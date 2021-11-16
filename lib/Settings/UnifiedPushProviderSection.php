<?php
namespace OCA\UnifiedPushProvider\Settings;

use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class UnifiedPushProviderSection implements IIconSection {
	/**
	 * @var IURLGenerator
	 */
	private $url;
	/**
	 * @param IURLGenerator $url
	 */
	public function __construct(IURLGenerator $url) {
		$this->url = $url;
	}
	/**
	 * returns the ID of the section. It is supposed to be a lower case string,
	 * e.g. 'ldap'
	 *
	 * @returns string
	 */
	public function getID() {
		return "uppush";
	}
	/**
	 * returns the translated name as it should be displayed, e.g. 'LDAP / AD
	 * integration'. Use the L10N service to translate it.
	 *
	 * @return string
	 */
	public function getName() {
		return "UnifiedPush Provider";
	}
	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the settings navigation. The sections are arranged in ascending order of
	 * the priority values. It is required to return a value between 0 and 99.
	 *
	 * E.g.: 70
	 */
	public function getPriority() {
		return 70;
	}
	/**
	 * {@inheritdoc}
	 */
	public function getIcon() {
		return $this->url->imagePath("uppush", 'app.svg');
	}
	
}
