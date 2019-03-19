<?php
/**
 * @version   $Id: Factory.php 10050 2013-05-03 20:00:03Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokUpdater_Subscriber_Factory
{
	public static function createFromJSON($json)
	{
		$ati  = new RokUpdater_Subscriber_Info();
		$json = json_decode($json);
		if (is_null($json)) {
			throw new RokUpdater_Exception('ROKUPDATER_INVALID_ACCESS_TOKEN_INFO_SAVED');
		}
		$fields = get_object_vars($json);
		foreach ($fields as $name => $value) {
			if (property_exists($ati, $name)) {
				if (is_array($value)) {
					if ($name == 'subscriptions') {
						foreach ($value as $subscription) {
							$ati->addSubscription(new RokUpdater_Subscriber_Subscription($subscription->club, $subscription->active));
						}
					}
				} else {
				$ati->$name = $value;
			}
		}

		}
		return $ati;
	}

	/**
	 * @param RokUpdater_Message_AccessTokenResponse|RokUpdater_Message_RefreshTokenResponse $access_token_resp
	 *
	 * @return RokUpdater_Subscriber_Info
	 */
	public static function createFromOAuthAccessTokenResponse($access_token_resp)
	{
		$ati = new RokUpdater_Subscriber_Info();
		if ($access_token_resp->hasOauthInfo()) {
			$oauthinfo = $access_token_resp->getOauthInfo();
			$ati->setAccessToken($oauthinfo->getAccessToken());
			$ati->setExpires($oauthinfo->getExpiresIn() + time());
			$ati->setTokenType($oauthinfo->getTokenType());
			$ati->setScope($oauthinfo->getScope());
			$ati->setRefreshToken($oauthinfo->getRefreshToken());
		}

		if ($access_token_resp->hasMessage()) {
			$ati->notice = $access_token_resp->getMessage();
		}

		if ($access_token_resp->hasSubscriberInfo())
		{
			$ati->setUsername($access_token_resp->getSubscriberInfo()->getUsername());
			foreach($access_token_resp->getSubscriberInfo()->getSubscriptions() as $subscription)
			{
				$ati->addSubscription(new RokUpdater_Subscriber_Subscription($subscription->getClub(),$subscription->getActive()));
			}
		}

		return $ati;
	}

	/**
	 * @param null $notice
	 *
	 * @return RokUpdater_Subscriber_Info
	 */
	public static function createEmpty($notice = null)
	{
		$ati = new RokUpdater_Subscriber_Info();
		$ati->notice        = $notice;
		return $ati;
	}
}
