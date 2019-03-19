<?php
/**
 * @version   $Id: Logout.php 11035 2013-05-31 10:07:02Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokUpdater_Ajax_Model_Logout implements RokUpdater_Ajax_IModel
{
	public function run()
	{
		$container = RokUpdater_ServiceProvider::getInstance();

		$user = JFactory::getUser();
		$conf = JFactory::getConfig();

		$result          = new stdClass();
		$result->status  = "error";
		$result->message = "";

		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rokupdater', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$lang->load('plg_system_rokupdater', JPATH_ADMINISTRATOR, null, false, false);
		$lang->load('plg_system_rokupdater', dirname(__FILE__) . '/../', $lang->getDefault(), false, false);
		$lang->load('plg_system_rokupdater', dirname(__FILE__) . '/../', null, false, false);

		try {
			// Check for
			if ($user->id == 0 || !$user->authorise('core.manage', 'com_installer')) {
				throw new RokUpdater_Exception('ROKUPDATER_INVALID_ACCESS');
			}

			// store the access token info to the extension data
			$subscriber_info = $container->storageservice->getSubscriberInfo();
			if (null != $subscriber_info) {
				try{
					$container->messageservice->requestLogout($subscriber_info->getUsername(), $subscriber_info->getAccessToken(), $subscriber_info->getRefreshToken(), $container->site_id);
				}
				catch(Exception $e)
				{}
			}
			$container->storageservice->updateAccessToken($container->site_id);
			$container->storageservice->removeSubscriberInfo();
			$container->storageservice->forceUpdatesRefresh();
			$result->status = 'success';
		} catch (RokUpdater_Exception $roe) {
			$result->status  = 'error';
			$result->message = JText::_($roe->getMessage());
		}
		echo json_encode($result);
	}

}