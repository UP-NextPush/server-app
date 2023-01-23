<?php
declare(strict_types=1);

namespace OCA\UnifiedPushProvider\Controller;

use DateTime;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Diagnostics\IEventLogger;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IUserSession;
use OC\RedisFactory;
use OC\SystemConfig;
use OC_Util;
use Redis;

class UnifiedPushProviderController extends Controller {

	private $db;
	private $userSession;
	private $redisFactory;

	public function __construct(SystemConfig $config, IEventLogger $eventLogger, IDBConnection $db, IUserSession $userSession){
		$this->db = $db;
		$this->userSession = $userSession;
		$this->redisFactory = new RedisFactory($config, $eventLogger);
	}

	function err_die($message){
		error_log("Nextcloud/UnifiedPushProvider: $message");
		exit(1);
	}

	function uuid(){
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		  mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		  mt_rand(0, 0xffff),
		  mt_rand(0, 0x0fff) | 0x4000,
		  mt_rand(0, 0x3fff) | 0x8000,
		  mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	function checkDeviceId($deviceId){
		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from('uppush_devices')
			->where($query->expr()->eq('device_id', $query->createNamedParameter($deviceId)));

		$result = $query->execute();
		$resultId = null;
		if ($row = $result->fetch()){
			$resultId = $row['device_id'];
		}
		return ($resultId !== null);
	}

	function _push(string $token, string $message){
		$redis = $this->redisFactory->getInstance();

                $token = explode("/", $token)[0];
		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from('uppush_applications')
			->where($query->expr()->eq('token', $query->createNamedParameter($token)));
		$result = $query->execute();
		$deviceId = null;
		if ($row = $result->fetch()){
			$deviceId = $row['device_id'];
		}
		if ($deviceId === null) return false;

		$messageDict = [
			'type' => "message",
			'token' => $token,
			'message' => base64_encode($message)
		];

		$redis->lPush($deviceId, json_encode($messageDict));

		return true;
	}

	/**
	 * Set keepalive interval.
	 *
	 * @NoCSRFRequired
	 *
	 * @param string $keepalive
	 *
	 * @return JsonResponse
	 */
	public function setKeepalive(string $keepalive){
		$keepalive = filter_var($keepalive, FILTER_SANITIZE_NUMBER_INT);
		try {
			$query = $this->db->getQueryBuilder();
			$query->delete('uppush_config')
				->where($query->expr()->eq('parameter', $query->createNamedParameter('keepalive')));
			$query->execute();
		} catch (Exception $ex) {
		}
		$query = $this->db->getQueryBuilder();
		$query->insert('uppush_config')
			->values([
			'parameter' => $query->createNamedParameter('keepalive'),
			'value' => $query->createNamedParameter($keepalive)
			]);
		$query->execute();
		return new JSONResponse(['success' => true]);
	}

	/**
	 * Request to create a new deviceId.
	 *
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 *
	 * @param string $deviceName
	 *
	 * @return JsonResponse
	 */
	public function createDevice(string $deviceName){
		$deviceId = $this->uuid();
		$query = $this->db->getQueryBuilder();
		$query->insert('uppush_devices')
			->values([
				'user_id' => $query->createNamedParameter($this->userSession->getUser()->getUID()),
				'device_id' => $query->createNamedParameter($deviceId),
				'device_name' => $query->createNamedParameter($deviceName),
				'date' => $query->createNamedParameter(date(DATE_RFC2822))
			]);
		$query->execute();
		return new JSONResponse([
				'success' => true,
				'deviceId' => $deviceId
			]);
	}

	/**
	 * Request to get push messages.
	 * This is a public page since it has to be handle by the non-connected app
	 * (NextPush app and not Nextcloud-app)
	 *
	 * @NoCSRFRequired
	 * @PublicPage
	 *
	 * @param string $deviceId
	 */
	public function syncDevice(string $deviceId){
		if (!$this->checkDeviceId($deviceId)) return new JSONResponse(['success' => false], Http::STATUS_UNAUTHORIZED);

		set_time_limit(0);

		$redis = $this->redisFactory->getInstance();
		$redis->setOption(Redis::OPT_READ_TIMEOUT, -1);

		$redis->rPush($deviceId, "shutdown_".getmypid());

		// Record this sub in redis, but expire after 12 hours (43200 seconds)
		$timestamp = (new DateTime())->getTimestamp();
		$redis->setEx('subscribe_'.$deviceId.'_'.$timestamp, 43200, $timestamp);
		$subcount = count($redis->keys('subscribe_'.$deviceId.'_*'));

		OC_Util::obEnd();
		header('Cache-Control: no-cache');
		header('X-Accel-Buffering: no');
		header('Content-Type: text/event-stream');
		flush();

		echo 'event: start'.PHP_EOL;
		echo 'data: {"type":"start"}'.PHP_EOL;
		echo PHP_EOL;
		flush();

		if ($subcount > 144){
			echo 'event: warning'.PHP_EOL;
			echo 'data: {"type":"warning","message":"Connected '.$subcount.' times in 12 hours"}'.PHP_EOL;
			echo PHP_EOL;
			flush();
		}

		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from('uppush_config')
			->where($query->expr()->eq('parameter', $query->createNamedParameter("keepalive")));
		$result = $query->execute();
		$keepalive = "50";
		if ($row = $result->fetch()){
			$keepalive = $row['value'];
		}

		echo 'event: keepalive'.PHP_EOL;
		echo 'data: {"type":"keepalive","keepalive":'.$keepalive.'}'.PHP_EOL;
		echo PHP_EOL;
		flush();

		while (true){
			$element = $redis->brPop($deviceId, intval($keepalive));
			if (!is_null($element) && is_array($element) && array_key_exists(1, $element)){
				if (strpos($element[1], 'shutdown') === 0){
					if (getmypid() != explode("_", $element[1])[1]) {
						echo 'event: close'.PHP_EOL;
						echo 'data: {"type":"close"}'.PHP_EOL;
						echo PHP_EOL;
						flush();
						exit(0);
					}
					else continue;
				}
				echo 'event: '.json_decode($element[1])->type.PHP_EOL;
				echo 'data: '.$element[1].PHP_EOL;
				echo PHP_EOL;
				flush();
			} else {
				echo 'event: ping'.PHP_EOL;
				echo 'data: {"type":"ping"}'.PHP_EOL;
				echo PHP_EOL;
				flush();
			}
		}
	}

	/**
	 * Delete a device.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $deviceId
	 *
	 * @return JsonResponse
	 */
	public function deleteDevice(string $deviceId){
		if (!$this->checkDeviceId($deviceId)) return new JSONResponse(['success' => false], Http::STATUS_UNAUTHORIZED);

		$query = $this->db->getQueryBuilder();
		$query->delete('uppush_applications')
			->where($query->expr()->eq('device_id', $query->createNamedParameter($deviceId)));
		$query->execute();
		$query = $this->db->getQueryBuilder();
		$query->delete('uppush_devices')
			->where($query->expr()->eq('device_id', $query->createNamedParameter($deviceId)));
		$query->execute();
		return new JSONResponse(['success' => true]);
	}

	/**
	 * Create an authorization token for a new 3rd party service.
	 *
	 * @NoAdminRequired
	 * @CORS
	 * @NoCSRFRequired
	 *
	 * @param string $deviceId
	 * @param string $appName
	 *
	 * @return JsonResponse
	 */
	public function createApp(string $deviceId, string $appName){
		if (!$this->checkDeviceId($deviceId)) return new JSONResponse(['success' => false], Http::STATUS_UNAUTHORIZED);
		$token = $this->uuid();

		$query = $this->db->getQueryBuilder();
		$query->insert('uppush_applications')
			->values([
				'device_id' => $query->createNamedParameter($deviceId),
				'app_name' => $query->createNamedParameter($appName),
				'token' => $query->createNamedParameter($token),
				'date' => $query->createNamedParameter(date(DATE_RFC2822))
			]);
		$query->execute();
		return new JSONResponse([
				'success' => true,
				'token' => $token
			]);
	}

	/**
	 * Delete an authorization token.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $token
	 *
	 * @return JsonResponse
	 */
	public function deleteApp(string $token){
		$redis = $this->redisFactory->getInstance();

		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from('uppush_applications')
			->where($query->expr()->eq('token', $query->createNamedParameter($token)));
		$result = $query->execute();
		$deviceId = null;
		if ($row = $result->fetch()){
			$deviceId = $row['device_id'];
		}
		if ($deviceId === null) return new JSONResponse(['success' => false], Http::STATUS_UNAUTHORIZED);

		$query = $this->db->getQueryBuilder();
		$query->delete('uppush_applications')
			->where($query->expr()->eq('token', $query->createNamedParameter($token)));
		$query->execute();

		$messageDict = [
			'type' => "deleteApp",
			'token' => $token,
			'message' => ""
		];

		$redis->lPush($deviceId, json_encode($messageDict));

		return new JSONResponse(['success' => true]);
	}

	/**
	 * Receive notifications from 3rd parties.
	 *
	 * @PublicPage
	 * @CORS
	 * @NoCSRFRequired
	 *
	 * @param string $token
	 *
	 * @return JsonResponse
	 */
	public function push(string $token){
		$message = file_get_contents('php://input');

		if(!$this->_push($token, $message)){
			return new JSONResponse(['success' => false], Http::STATUS_UNAUTHORIZED);
		}
		return new JSONResponse(['success' => true]);
	}

	/**
	 * Unifiedpush discovery
	 * Following specifications
	 *
	 * @CORS
	 * @PublicPage
	 * @NoCSRFRequired
	 *
	 * @return JsonResponse
	 */
	public function unifiedpushDiscovery(){
		return new JSONResponse([
				'unifiedpush' => [
					'version' => 1
				]
			]);
	}

	/**
	 * GATEWAYS
	 */

	/**
	 * Matrix Gateway discovery
	 *
	 * @CORS
	 * @PublicPage
	 * @NoCSRFRequired
	 *
	 * @return JsonResponse
	 */
	public function gatewayMatrixDiscovery(){
		return new JSONResponse([
				'unifiedpush' => [
					'gateway' => 'matrix'
				]
			]);
	}

	/**
	 * Matrix Gateway
	 *
	 * @CORS
	 * @PublicPage
	 * @NoCSRFRequired
	 *
	 * @return JsonResponse
	 */
	public function gatewayMatrix(){
		$message = json_decode(file_get_contents('php://input'));
		$rejected = [];
		$devices = $message->notification->devices;
		unset($message->notification->devices);
		foreach ($devices as $device) {
			$pushkey = $device->pushkey;
			$exploded_pushkey = explode('/', $pushkey);
			$token = end($exploded_pushkey);
			if(!$this->_push($token, json_encode($message))){
				array_push($rejected, $pushkey);
			}
		}
		return new JSONResponse([
				'rejected' => $rejected
			]);
	}
}
