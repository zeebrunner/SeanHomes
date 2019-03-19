<?php
/**
 * @copyright      Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
define('DEV', false);

/**
 * Plugin class for logout redirect handling.
 *
 * @package        Joomla.Plugin
 * @subpackage     System.logout
 */
class plgSystemRokUpdater extends JPlugin
{


	/** @var RokUpdater_ServiceProvider */
	protected $container;

	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		if (!defined('ROKUPDATER_ROOT_PATH')) {
			define('ROKUPDATER_ROOT_PATH', JPATH_PLUGINS . '/system/rokupdater/');
		}
		require_once(dirname(__FILE__) . '/lib/include.php');
		JLog::addLogger(array('text_file' => 'rokupdater.php'), $this->params->get('debugloglevel', 63), array('rokupdater'));

		$this->container = RokUpdater_ServiceProvider::getInstance();

		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rokupdater', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$lang->load('plg_system_rokupdater', JPATH_ADMINISTRATOR, null, false, false);
		$lang->load('plg_system_rokupdater', dirname(__FILE__), $lang->getDefault(), false, false);
		$lang->load('plg_system_rokupdater', dirname(__FILE__), null, false, false);
	}

	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		if (!$app->isAdmin()) return;
		$option = JFactory::getApplication()->input->getWord('option', '');
		$view   = JFactory::getApplication()->input->getWord('view', '');
		try {
			if ($option == 'com_installer' && $view == 'update') {
				$subscriber_info = $this->container->storageservice->getSubscriberInfo();
				if (is_null($subscriber_info)) {
					$this->container->storageservice->removeSubscriberInfo();
					$subscriber_info = new RokUpdater_Subscriber_Info();
				}

				// check if the access token has expired
				if ($subscriber_info->refresh_token != null && $subscriber_info->expires < time()) {
					$refresh_result = $this->container->messageservice->requestAccessTokenRefresh($subscriber_info->refresh_token, $this->container->site_id);
					if ($refresh_result->getStatus() == RokUpdater_Message_RequestStatus::SUCCESS) {
						$this->container->storageservice->storeSubscriberInfo(RokUpdater_Subscriber_Factory::createFromOAuthAccessTokenResponse($refresh_result));
						$this->container->storageservice->updateAccessToken($this->container->site_id, $refresh_result->getOauthInfo()->getAccessToken());
					} else {
						$this->container->storageservice->removeSubscriberInfo();
						$this->container->storageservice->updateAccessToken($this->container->site_id);
					}
					$this->container->storageservice->forceUpdatesRefresh();
				}
			}
		} catch (Exception $e) {
			$this->_subject->setError($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * @return mixed
	 */
	public function onBeforeRender()
	{
		$app = JFactory::getApplication();
		if (!$app->isAdmin()) return;
		$option = JFactory::getApplication()->input->getWord('option', '');
		$view   = JFactory::getApplication()->input->getWord('view', '');

		if ($option == 'com_installer' && $view == 'update') {
			// LESS compile only in DEV mode
			if (DEV) {
				require_once('vendors/leafo/lessc.inc.php');
				$less = new lessc;
				$less->compileFile(dirname(__FILE__) . '/assets/less/j25.less', dirname(__FILE__) . '/assets/css/rokupdater-j25.css');
				$less->compileFile(dirname(__FILE__) . '/assets/less/j30.less', dirname(__FILE__) . '/assets/css/rokupdater-j30.css');
			}

			$document = JFactory::getDocument();
			$jversion = new JVersion();
			if (version_compare($jversion->getShortVersion(), '3.0.0', '>')) {
				JHtml::_('behavior.modal', 'a.modal');
				$document->addStylesheet(JURI::root(true) . '/plugins/system/rokupdater/assets/css/rokupdater-j30.css');
			} else {
				$document->addStylesheet(JURI::root(true) . '/plugins/system/rokupdater/assets/css/rokupdater-j25.css');
			}
			$document->addStylesheet(JURI::root(true) . '/plugins/system/rokupdater/assets/css/hint.min.css');
			$document->addScript(JURI::root(true) . '/plugins/system/rokupdater/fields/assets/ajax/js/Logout.js');
			//$document->addScriptDeclaration("window.addEvent('load', function(){ $$('.btn.btn-warning.modal')[0].fireEvent('click'); });");
			JHtml::_('behavior.framework');
		}
	}

	public function onAfterRender()
	{
		$app = JFactory::getApplication();
		if (!$app->isAdmin()) return;

		$option = JFactory::getApplication()->input->getWord('option', '');
		$view   = JFactory::getApplication()->input->getWord('view', '');

		if ($option == 'com_installer' && $view == 'update') {

			require_once(dirname(__FILE__) . '/lib/include.php');

			$document = JFactory::getDocument();
			$doctype  = $document->getType();
			if ($doctype == 'html') {
				/** @var $rtupdates RokUpdater_Message_BundleData[] */
				$rtupdates = $this->getRTUpdates();

				$body = JResponse::getBody();
				$pq   = phpQuery::newDocument($body);

				$jversion = new JVersion();
				$is_j30   = version_compare($jversion->getShortVersion(), '3.0.0', '>');

				$subscriber_info = $this->container->storageservice->getSubscriberInfo();

				$auth_required_txt = JText::_('ROKUPDATER_LABEL_AUTH_NEEDED_MESSAGE');
				$auth_button_txt   = sprintf('<a class="btn btn-warning modal" href="%1s" type="button" rel="{handler: \'iframe\', size: {x: 400, y: 500}, onClose: function() {}}"><i class="rok-lock"></i> %2s</a>', JURI::root(true) . '/plugins/system/rokupdater/ajax.php?ajax_model=getpage&page=rockettheme_login', JText::_('ROKUPDATER_LABEL_LOGIN_BUTTON'));

				$status_msg_txt    = JText::sprintf('ROKUPDATER_LABEL_LOGOUT_MESSAGE', $subscriber_info->getUsername());
				$logout_button_txt = sprintf('<a class="btn btn-info" type="button" href="' . JURI::root(true) . '/plugins/system/rokupdater/ajax.php?ajax_model=logout' . '" data-rokupdater-logout><i class="rok-lock"></i> %1s</a>', JText::_('ROKUPDATER_LABEL_LOGOUT_BUTTON'));

				$clubs = '';
				foreach ($subscriber_info->getSubscriptions() as $club) {
					$clubs .= sprintf('<span class="%1s rok-badge">%2s</span> ', ($club->getActive()) ? 'rok-active' : 'rok-inactive', $club->getClub());
				}

				$noaccess_lbl  = sprintf('<span class="rok-noaccess rok-label hint--right hint--rounded" data-hint="%s"><i class="rok-minus"></i></span>', JText::_('ROKUPDATER_LABEL_RESTRICTED_BLOCKED'));
				$hasaccess_lbl = sprintf('<span class="rok-hasaccess rok-label hint--right hint--rounded" data-hint="%s"><i class="rok-checkmark"></i></span>', JText::_('ROKUPDATER_LABEL_RESTRICTED_ALLOWED'));
				$free_lbl      = sprintf('<span class="rok-free rok-label hint--right hint--rounded" data-hint="%s"><i class="rok-plus"></i></span>', JText::_('ROKUPDATER_LABEL_FREE_EXTENSION'));


				if ($this->params->get('show_auth_on_updates', true)) {
					if ($subscriber_info != null && $subscriber_info->expires > time()) {
						if ($is_j30) {
							pq('#system-message-container')->after('<div class="rokupdater info j30"><i class="rok-rocketlogo"></i>' . $status_msg_txt . $clubs . ' ' . $logout_button_txt . '</div>');
						} else {
							pq('#system-message-container')->after('<div class="rokupdater info j25"><i class="rok-rocketlogo"></i>' . $status_msg_txt . $clubs . ' ' . $logout_button_txt . '</div>');
						}
					} else {
						if ($is_j30) {
							pq('#system-message-container')->after('<div class="rokupdater auth j30"><i class="rok-rocketlogo"></i>' . $auth_required_txt . ' ' . $auth_button_txt . '</div>');
						} else {
							pq('#system-message-container')->after('<div class="rokupdater auth j25"><i class="rok-rocketlogo"></i>' . $auth_required_txt . ' ' . $auth_button_txt . '</div>');
						}
					}
				}

				foreach ($rtupdates as $id => $rtupdate) {
					switch ($rtupdate->getAvailability()) {
						case RokUpdater_Message_BundleAvailabilityStatus::FREE:
							$label = $free_lbl;
							break;
						case RokUpdater_Message_BundleAvailabilityStatus::RESTRICTED_ALLOWED:
							$label = $hasaccess_lbl;
							break;
						default:
							$label = $noaccess_lbl;
							break;
					}

					if ($this->params->get('hide_unavailable', false) && $rtupdate->getAvailability() == RokUpdater_Message_BundleAvailabilityStatus::RESTRICTED_BLOCKED) {
						if ($is_j30) {
							pq('#installer-update .table .tr input[value="' . $id . '"]')->parent()->parent()->remove();
						} else {
							pq('#installer-update .adminlist input[value="' . $id . '"]')->parent()->parent()->remove();
						}
					} else {
						if ($is_j30) {
							pq('#installer-update .table input[value="' . $id . '"]')->parent()->next()->wrapInner('<div style="position: relative;"></div>')->find('div:first')->append($label);
						} else {
							pq('#installer-update .adminlist input[value="' . $id . '"]')->parent()->next()->wrapInner('<div style="position: relative;"></div>')->find('div:first')->append($label);
						}

						if ($rtupdate->getAvailability() == RokUpdater_Message_BundleAvailabilityStatus::RESTRICTED_BLOCKED) {
							if ($is_j30) {
								pq('#installer-update .table input[value="' . $id . '"]')->remove();
							} else {
								pq('#installer-update .adminlist input[value="' . $id . '"]')->remove();
							}

						}
					}
				}
			}
			$body = $pq->getDocument()->htmlOuter();
			JResponse::setBody($body);
		} elseif ($option == 'com_cpanel') {
			$jversion = new JVersion();
			JHtml::_('behavior.modal', 'a.modal');
			$body          = JResponse::getBody();
			$pq            = phpQuery::newDocument($body);
			$authpage_link = JURI::root(true) . '/plugins/system/rokupdater/ajax.php?ajax_model=getpage&page=rockettheme_login';
			$authpage_rel  = '{handler: \'iframe\', size: {x: 400, y: 500}, onClose: function() {}}';
			pq('#plg_system_rokupdater-off a')->attr('href', $authpage_link)->attr('rel', $authpage_rel)->addClass('modal');
			$body = $pq->getDocument()->htmlOuter();
			JResponse::setBody($body);
		}
	}

	protected function getRTUpdates()
	{
		$rtupdates = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('update_id, data');
		$query->from('#__updates as u');
		$query->leftJoin('#__update_sites as us ON u.update_site_id=us.update_site_id');
		$query->where('us.location like ' . $query->quote($this->container->updaters_server_regex_pattern));

		$db->setQuery($query);

		$updates = $db->loadObjectList('update_id');

		foreach ($updates as $id => &$update) {
			if (!empty($update->data)) {
				$message = new RokUpdater_Message_BundleData();
				$message->ParseFromString(hex2bin($update->data));
				$rtupdates[$id] = $message;
			}
		}

		return $rtupdates;
	}

	/**
	 * Returns an icon definition for an icon which looks for extensions updates
	 * via AJAX and displays a notification when such updates are found.
	 *
	 * @param  $context  The calling context
	 *
	 * @return array A list of icon definition associative arrays, consisting of the
	 *                 keys link, image, text and access.
	 *
	 * @since          2.5
	 */
	public function onGetIcons($context)
	{
		if ($context != $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_installer')) {
			return;
		}

		// LESS compile only in DEV mode
		if (DEV) {
			require_once('vendors/leafo/lessc.inc.php');
			$less = new lessc;
			$less->compileFile(dirname(__FILE__) . '/assets/less/j25.less', dirname(__FILE__) . '/assets/css/rokupdater-j25.css');
			$less->compileFile(dirname(__FILE__) . '/assets/less/j30.less', dirname(__FILE__) . '/assets/css/rokupdater-j30.css');
		}

		$document = JFactory::getDocument();

		$jversion = new JVersion();
		if (version_compare($jversion->getShortVersion(), '3.0.0', '>')) {
			JHtml::_('behavior.modal', 'a.modal');
			$document->addStylesheet(JURI::root(true) . '/plugins/system/rokupdater/assets/css/rokupdater-j30.css');
			$image = 'rocketlogo';
			$j30   = true;
		} else {
			$document->addStylesheet(JURI::root(true) . '/plugins/system/rokupdater/assets/css/rokupdater-j25.css');
			$image = 'admin/blank.png';
			$badge = '';
			$j30   = false;
		}

		JHtml::_('behavior.modal', 'a.modal');
		$subscriber_info = $this->container->storageservice->getSubscriberInfo();
		if ($subscriber_info == null || $subscriber_info->getAccessToken() == null) {
			$text  = "Not Authenticated w/ RocketTheme";
			$id    = "plg_system_rokupdater-off";
			$badge = ' <span class="label label-important"><i class="rok-close"></i></span>';
			$link  = 'ROKUPDATER_AUTHPAGE';
		} else {
			$text  = "Authenticated w/ Rockettheme";
			$id    = "plg_system_rokupdater-on";
			$badge = ' <span class="label label-success"><i class="rok-checkmark"></i></span>';
			$link  = JURI::root(true) . '/administrator/index.php?option=com_installer&view=update';
		}

		return array(
			array(
				'link'  => $link,
				'image' => $image,
				'text'  => JText::_($text) . ($j30 ? $badge : ''),
				'id'    => $id
			)
		);
	}


	/**
	 * Handle post extension install update sites
	 *
	 * @param    JInstaller     Installer object
	 * @param    int            Extension Identifier
	 *
	 * @since    1.6
	 */
	public function onExtensionAfterInstall($installer, $eid)
	{
		if ($eid) {
			$access_token = null;
			$subscriber_info = $this->container->storageservice->getSubscriberInfo();
			if (null != $subscriber_info)
			{
				$access_token = $subscriber_info->getAccessToken();
			}
			$this->container->storageservice->mergeUpdateSites();
			$this->container->storageservice->updateAccessToken($this->container->site_id, $access_token);
		}
	}

	/**
	 * After update of an extension
	 *
	 * @param    JInstaller     Installer object
	 * @param    int            Extension identifier
	 *
	 * @since    1.6
	 */
	public function onExtensionAfterUpdate($installer, $eid)
	{
		if ($eid) {
			$access_token = null;
			$subscriber_info = $this->container->storageservice->getSubscriberInfo();
			if (null != $subscriber_info)
			{
				$access_token = $subscriber_info->getAccessToken();
			}
			$this->container->storageservice->mergeUpdateSites();
			$this->container->storageservice->updateAccessToken($this->container->site_id, $access_token);
		}
	}

	protected function getPluginId()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('type = ' . $db->quote('plugin'));
		$query->where('folder = ' . $db->quote('system'));
		$query->where('element = ' . $db->quote('rokupdater'));
		$db->setQuery($query);
		return $db->loadResult();
	}
}


if (!function_exists('hex2bin')) {
	function hex2bin($hexstr)
	{
		$n    = strlen($hexstr);
		$sbin = "";
		$i    = 0;
		while ($i < $n) {
			$a = substr($hexstr, $i, 2);
			$c = pack("H*", $a);
			if ($i == 0) {
				$sbin = $c;
			} else {
				$sbin .= $c;
			}
			$i += 2;
		}
		return $sbin;
	}
}
