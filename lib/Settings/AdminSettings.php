<?php
namespace OCA\UnifiedPushProvider\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IDBConnection;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IUserSession;
use Redis;

class AdminSettings implements ISettings {

	private $config;
	private $db;
	private $userSession;
	private $groupManager;

	function checkRedis() {
		$redis_db = 1;

		if (!class_exists('Redis')) return "Redis is not installed.";

		$redis_config = $this->config->getSystemValue("redis");
		if (!is_array($redis_config)) return "Redis is not configured in nextcloud config.php.";
		if (array_key_exists("dbindex", $redis_config)) $redis_db = $redis_config['dbindex'] + 1;

		$redis = new Redis();
		if (!$redis->connect($redis_config['host'], $redis_config['port'])) {
			return "Cannot connect to Redis.";
		}
		if (array_key_exists('password', $redis_config) && $redis_config['password'] !== '') {
			$redis->auth($redis_config['password']);
		}
		if(!$redis->select($redis_db)) {
			return "Cannot select in Redis.";
		}

		return "";
	}

	public function __construct(IConfig $config, IDBConnection $db, IUserSession $userSession, IGroupManager $groupManager){
		$this->config = $config;
		$this->db = $db;
		$this->userSession = $userSession;
		$this->groupManager = $groupManager;
	}

	public function getForm() {
		$parameters = array();
		$redis_error = $this->checkRedis();
		if ($redis_error !== "") {
			$parameters['error'] = $redis_error;
			return new TemplateResponse("uppush", "admin-redis-error", $parameters);
		}
		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from('uppush_config')
			->where($query->expr()->eq('parameter', $query->createNamedParameter("keepalive")));
		$result = $query->execute();
		$keepalive = "300";
		while ($row = $result->fetch()){
			$keepalive = $row['value'];
		}
		$result->closeCursor();
		$parameters['keepalive'] = $keepalive;
		return new TemplateResponse("uppush", 'admin-settings', $parameters);
	}

	public function getSection() {
		return "uppush";
	}

	public function getPriority() {
		return 0;
	}
}
