<?php
/**
 * @version   $Id: JoomlaAuthenticate.php 11338 2013-06-10 21:22:45Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * Class RokUpdater_Ajax_Model_JoomlaAuthenticate
 */
class RokUpdater_Ajax_Model_JoomlaAuthenticate implements RokUpdater_Ajax_IModel
{
	/**
	 *
	 */
	public function run()
	{
		$container = RokUpdater_ServiceProvider::getInstance();

		$user = JFactory::getUser();

		$result          = new stdClass();
		$result->status  = "error";
		$result->message = "";

		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rokupdater', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$lang->load('plg_system_rokupdater', JPATH_ADMINISTRATOR, null, false, false);
		$lang->load('plg_system_rokupdater', JPATH_PLUGINS.'/system/rokupdater', $lang->getDefault(), false, false);
		$lang->load('plg_system_rokupdater', JPATH_PLUGINS.'/system/rokupdater', null, false, false);

		try {
			// Check for
			if ($user->id == 0 || !$user->authorise('core.manage', 'com_installer')) {
				throw new RokUpdater_Exception('ROKUPDATER_INVALID_ACCESS');
			}
			$input = JFactory::getApplication()->input;

			$username = $input->post->get('userid', null,'STRING');
			$password = $input->post->get('pswrd', null,'STRING');
			if (empty($username) || empty($password)) {
				throw new RokUpdater_Exception('ROKUPDATER_ERROR_NEED_USERNAME_AND_PASSSWORD');
			}
			$result = $container->messageservice->requestAccessToken($username, $password, $container->site_id);

			if ($result->getStatus() !== RokUpdater_Message_RequestStatus::SUCCESS) {
				throw new RokUpdater_Exception($result->getMessage());
			}

			// store the access token info to the extension data
			$container->storageservice->storeSubscriberInfo(RokUpdater_Subscriber_Factory::createFromOAuthAccessTokenResponse($result));

			// update the update_site urls with the current access token
			$container->storageservice->updateAccessToken($container->site_id,$result->getOauthInfo()->getAccessToken());

			// refresh the updates
			$container->storageservice->forceUpdatesRefresh();
			$result->status  = 'success';
			$result->message = JText::_('ROKUPDATER_SUCCESS_LOGIN_MESSAGE');

		} catch (RokUpdater_Exception $roe) {
			$result->status  = 'error';
			$result->message = JText::_($roe->getMessage());
		}
		return json_encode($result);
	}
}
