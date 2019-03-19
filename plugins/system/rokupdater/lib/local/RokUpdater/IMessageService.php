<?php
/**
 * @version   $Id: IMessageService.php 10262 2013-05-14 04:27:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

interface RokUpdater_IMessageService
{

	/**
	 * Requests the access token
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @param string $siteId
	 *
	 * @return RokUpdater_Message_AccessTokenResponse
	 */
	public function requestAccessToken($username, $password, $siteId);

	/**
	 * Requests the access token
	 *
	 * @param string $refresh_token
	 *
	 * @param string $siteId
	 *
	 * @return RokUpdater_Message_RefreshTokenResponse
	 */
	public function requestAccessTokenRefresh($refresh_token, $siteId);

	/**
	 * Requests the access token
	 *
	 * @param string       $username
	 * @param string       $access_token
	 * @param string       $refresh_token
	 *
	 * @param string       $siteId
	 *
	 * @return RokUpdater_Message_LogoutResponse
	 */
	public function requestLogout($username, $access_token, $refresh_token, $siteId);
}
