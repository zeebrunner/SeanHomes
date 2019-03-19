<?php
/**
 * @version   $Id: AbstractMessageService.php 10262 2013-05-14 04:27:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

abstract class RokUpdater_AbstractMessageService extends RokUpdater_AbstractService implements RokUpdater_IMessageService
{
	/**
	 * Requests the access token
	 *
	 * @param $username
	 * @param $password
	 *
	 * @param $sideId
	 *
	 * @return RokUpdater_Message_AccessTokenResponse
	 */
	public function requestAccessToken($username, $password, $sideId)
	{
		$request = new RokUpdater_Message_AccessTokenRequest();
		$request->setUsername($username)->setPassword($password)->setSiteId($sideId);
		$result = new RokUpdater_Message_AccessTokenResponse();
		$request->Send($this->container->auth_message_register_uri->getAbsoluteUri(), $result);
		return $result;
	}

	/**
	 * Requests the access token
	 *
	 * @param string $refresh_token
	 *
	 * @param        $siteId
	 *
	 * @return RokUpdater_Message_RefreshTokenResponse
	 */
	public function requestAccessTokenRefresh($refresh_token, $siteId)
	{
		$request = new RokUpdater_Message_RefreshTokenRequest();
		$request->setRefreshToken($refresh_token)->setSiteId($siteId);
		$result = new RokUpdater_Message_RefreshTokenResponse();
		$request->Send($this->container->auth_message_refresh_uri->getAbsoluteUri(), $result);
		return $result;
	}

	/**
	 * Requests the access token
	 *
	 * @param string $username
	 * @param string $access_token
	 * @param string $refresh_token
	 *
	 * @param        $siteId
	 *
	 * @return RokUpdater_Message_LogoutResponse
	 */
	public function requestLogout($username, $access_token, $refresh_token, $siteId)
	{
		$request = new RokUpdater_Message_LogoutRequest();
		$request->setUsername($username);
		$request->setAccessToken($access_token);
		$request->setRefreshToken($refresh_token);
		$request->setSiteId($siteId);
		$result = new RokUpdater_Message_LogoutResponse();
		$uri    = $this->container->auth_message_logout_uri;
		$uri->addQueryParam('access_token', $access_token);
		$request->Send($uri->getAbsoluteUri(), $result);
		return $result;
	}
}
