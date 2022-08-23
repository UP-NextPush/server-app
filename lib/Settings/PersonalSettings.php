<?php
namespace OCA\UnifiedPushProvider\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IDBConnection;
use OCP\IUserSession;

class PersonalSettings implements ISettings {

	private $db;
	private $userSession;

	public function __construct(IDBConnection $db, IUserSession $userSession){
		$this->db = $db;
		$this->userSession = $userSession;
	}

	public function getForm() {
		$query = $this->db->getQueryBuilder();
		$query->select('*')
			->from('uppush_devices')
			->where($query->expr()->eq('user_id', $query->createNamedParameter($this->userSession->getUser()->getUID())));
		$deviceResult = $query->execute();
		$devices = array();
		while ($row = $deviceResult->fetch()) {
			$query = $this->db->getQueryBuilder();
			$query->select('*')
				->from('uppush_applications')
				->where($query->expr()->eq('device_id', $query->createNamedParameter($row['device_id'])));
			$appResult = $query->execute();
			$apps = array();
        		while ($row2 = $appResult->fetch()) {
            			$apps[] = [
				'name' => $row2['app_name'],
				'date' => $row2['date'],
				'token' => $row2['token'],
                		];
        		}
	    		$appResult->closeCursor();

			$devices[] = [
				'name' => $row['device_name'],
				'date' => $row['date'],
				'token' => $row['device_id'],
				'apps' => $apps,
			];
		}
		$deviceResult->closeCursor();
		$parameters = array();
		$parameters['devices'] = $devices;
		return new TemplateResponse("uppush", 'personal-settings', $parameters);
	}

	public function getSection() {
		return "uppush";
	}

	public function getPriority() {
		return 0;
	}
}
