<?php
/**
 * @version   $Id: ServiceProvider.php 11003 2013-05-31 04:44:11Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * Class RokUpdater_ServiceProvider
 *
 * @property-read RokUpdater_IMessageService         $messageservice
 * @property-read RokUpdater_IStorageService         $storageservice
 *
 *
 * @property RokUpdater_Uri                          $auth_message_register_uri
 * @property RokUpdater_Uri                          $auth_message_refresh_uri
 * @property RokUpdater_Uri                          $auth_message_logout_uri
 * @property string                                  $updates_server_hostname
 * @property string                                  $updaters_server_regex_pattern
 * @property string                                  $site_id
 *
 *
 */
class RokUpdater_ServiceProvider extends RokUpdater_Container
{
	const BASE_AUTH_URL      = 'https://updates.rockettheme.com/';
	const BASE_UPDATE_URL    = 'http://updates.rockettheme.com/';
	const AUTH_REGISTER_PATH = '/auth/register';
	const AUTH_REFRESH_PATH  = '/auth/refresh';
	const AUTH_LOGOUT_PATH   = '/auth/logout';
	/**
	 * @var RokUpdater_Container
	 */
	protected static $instance;
	protected $auth_server_uri;
	protected $update_server_uri;
	protected $overrides;

	public function __construct()
	{
		$this->setFactory('auth_message_register_uri', array($this, 'getAuthMessageRegisterUri'));
		$this->setFactory('auth_message_refresh_uri', array($this, 'getAuthMessageRefreshUri'));
		$this->setFactory('auth_message_logout_uri', array($this, 'getAuthMessageLogoutUri'));

		$this->setFactory('updates_server_hostname', array($this, 'getUpdatesServerName'));
		$this->setValue('updaters_server_regex_pattern', '%' . $this->updates_server_hostname . '%');

		$this->setFactory('curl_options', array($this, 'getCurlOptions'));
		$this->setFactory('streamsocket_options', array($this, 'getStreamSocketOptions'));

		$this->setFactory('messageservice', array($this, 'getMessageService'));
		$this->setFactory('storageservice', array($this, 'getStorageService'));
		$this->setClassName('ajax_handler', 'RokUpdater_Ajax', true);
		$this->setClassName('ajax_model_authenticate', 'RokUpdater_Ajax_Model_JoomlaAuthenticate', false);
		$this->setClassName('ajax_model_logout', 'RokUpdater_Ajax_Model_Logout', false);
		$this->setClassName('ajax_model_getpage', 'RokUpdater_Ajax_Model_GetPage', true);
		$this->setFactory('site_id', array($this, 'getSiteId'));
		$this->setFactory('auth_http_fallback',array($this,'getAuthHttpFallback'));
	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new RokUpdater_ServiceProvider();
		}
		return self::$instance;
	}

	/**
	 * Logger factory
	 *
	 * @param \Elgg_ServiceProvider|\RokUpdater_ServiceProvider $c Dependency injection container
	 *
	 * @return ElggLogger
	 */
	protected function getStorageService(RokUpdater_ServiceProvider $c)
	{
		$jversion = new JVersion();
		if (version_compare($jversion->getShortVersion(), '3.0', '<')) {
			return new RokUpdater_Joomla25StorageService($c);
		} else {
			return new RokUpdater_Joomla30StorageService($c);
		}
	}

	protected function  getMessageService(RokUpdater_ServiceProvider $c)
	{
		return new RokUpdater_MessageService($c);
	}

	protected function getAuthMessageRegisterUri(RokUpdater_ServiceProvider $c)
	{
		$uri = $this->getOverrideServerInfo('auth_server', self::BASE_AUTH_URL);
		return $uri->setPath($uri->getPath() . self::AUTH_REGISTER_PATH);
	}

	protected function getAuthMessageRefreshUri(RokUpdater_ServiceProvider $c)
	{
		$uri = $this->getOverrideServerInfo('auth_server', self::BASE_AUTH_URL);
		return $uri->setPath($uri->getPath() . self::AUTH_REFRESH_PATH);
	}

	protected function getAuthMessageLogoutUri(RokUpdater_ServiceProvider $c)
	{
		$uri = $this->getOverrideServerInfo('auth_server', self::BASE_AUTH_URL);
		return $uri->setPath($uri->getPath() . self::AUTH_LOGOUT_PATH);
	}

	protected function getUpdatesServerName(RokUpdater_ServiceProvider $c)
	{
		$uri = $this->getOverrideServerInfo('update_server', self::BASE_UPDATE_URL);
		return $uri->getHost();
	}

	protected function getStreamSocketOptions(RokUpdater_ServiceProvider $c)
	{
		$streamsocket_options = array();
		$this->loadOverrides();
		if (isset($this->overrides->streamsocket_options) && is_array($this->overrides->streamsocket_options) && !empty($this->overrides->streamsocket_options)) {
			foreach ($this->overrides->streamsocket_options as $wrapper_entries) {
				foreach (get_object_vars($wrapper_entries) as $wrapper_name => $wrapper_settings) {
					foreach (get_object_vars($wrapper_settings) as $wrapper_setting_name => $wrapper_setting_value) {
						$streamsocket_options[$wrapper_name][$wrapper_setting_name] = $wrapper_setting_value;
					}
				}
			}

		}
		return $streamsocket_options;
	}

	protected function getCurlOptions(RokUpdater_ServiceProvider $c)
	{
		$curl_options = array();
		$this->loadOverrides();
		if (isset($this->overrides->curl_options) && is_array($this->overrides->curl_options) && !empty($this->overrides->curl_options)) {
			foreach ($this->overrides->curl_options as $key => $object) {
				foreach (get_object_vars($object) as $option_key => $option_val) {
					$curl_options[$option_key] = $option_val;
				}
			}
		}
		return $curl_options;
	}

	protected function getOverrideServerInfo($override_name, $base_url)
	{
		$this->loadOverrides();
		$server_uri = new RokUpdater_Uri($base_url);
		if (isset($this->overrides->$override_name)) {
			$server_uri = new RokUpdater_Uri($this->overrides->$override_name);
		}
		return $server_uri;
	}

	protected function getSiteId()
	{
		if (class_exists('JFactory')) {
			return md5(JFactory::getConfig()->get('secret'));
		}
		return '';
	}

	protected function loadOverrides()
	{
		if (defined('ROKUPDATER_ROOT_PATH') && !isset($this->overrides)) {
			if (file_exists(ROKUPDATER_ROOT_PATH . '/overrides.json')) {
				$this->overrides = json_decode(file_get_contents(ROKUPDATER_ROOT_PATH . '/overrides.json'));
			}
		}
	}

	protected function getAuthHttpFallback(RokUpdater_ServiceProvider $c)
	{
		$plugin = JPluginHelper::getPlugin('system', 'rokupdater');
		$params = new JRegistry();
		$params->loadString($plugin->params);
		return $params->get('fallback_to_http_for_auth', false);
	}
}
