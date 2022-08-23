<?php
namespace OCA\UnifiedPushProvider\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Diagnostics\IEventLogger;
use OCP\IDBConnection;
use OCP\Settings\ISettings;
use OC\RedisFactory;
use OC\SystemConfig;

class AdminSettings implements ISettings {

	private $db;
	private $redisFactory;

	public function __construct(SystemConfig $config, IEventLogger $eventLogger, IDBConnection $db){
		$this->db = $db;
		$this->redisFactory = new RedisFactory($config, $eventLogger);
	}

	private function checkRedis() {
		if (!$this->redisFactory->isAvailable()) {
			return "Redis support is not available";
		}
		try {
			$this->redisFactory->getInstance();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
		return "";
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
