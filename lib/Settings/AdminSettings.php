<?php
namespace OCA\UnifiedPushProvider\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IUserSession;

class AdminSettings implements ISettings {

	private $db;
	private $userSession;
	private $groupManager;

	public function __construct(IDBConnection $db, IUserSession $userSession, IGroupManager $groupManager){
		$this->db = $db;
		$this->userSession = $userSession;
		$this->groupManager = $groupManager;
	}

	public function getForm() {
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
		$parameters = array();
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
