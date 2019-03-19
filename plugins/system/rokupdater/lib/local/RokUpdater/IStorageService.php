<?php
/**
 * @version   $Id: IStorageService.php 10262 2013-05-14 04:27:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

interface RokUpdater_IStorageService
{
	/**
	 * @param $token
	 *
	 * @throws RokUpdater_Exception
	 */
	public function updateAccessToken($siteId, $token = null);

	/**
	 * @param RokUpdater_AccessTokenInfo $subscriber_info
	 */
	public function storeSubscriberInfo(RokUpdater_Subscriber_Info $subscriber_info);


	/**
	 * @return RokUpdater_Subscriber_Info|null
	 */
	public function getSubscriberInfo();


	/**
	 *
	 */
	public function removeSubscriberInfo();

	/**
	 *
	 */
	public function forceUpdatesRefresh();


	/**
	 * @return mixed
	 */
	public function mergeUpdateSites();
}
