<?php
/**
 * @version   $Id: Info.php 10589 2013-05-22 20:05:08Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokUpdater_Subscriber_Info
{
	/**
	 * @var string
	 */
	public $access_token;
	/**
	 * @var int
	 */
	public $expires;
	/**
	 * @var string
	 */
	public $scope;
	/**
	 * @var string
	 */
	public $refresh_token;
	/**
	 * @var string
	 */
	public $token_type;
	/**
	 * @var string
	 */
	public $notice;

	/**
	 * @var string
	 */
	public $username;

	/** @var RokUpdater_Subscriber_Subscription[] */
	public $subscriptions = array();

	/**
	 * @param array $subscriptions
	 */
	public function setSubscriptions($subscriptions)
	{
		$this->subscriptions = $subscriptions;
	}

	/**
	 * @return array
	 */
	public function getSubscriptions()
	{
		return $this->subscriptions;
	}

	public function addSubscription(RokUpdater_Subscriber_Subscription $subscription)
	{
		$this->subscriptions[] = $subscription;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}


	/**
	 * @return string
	 */
	public function getAccessToken()
	{
		return $this->access_token;
	}

	/**
	 * @param string $access_token
	 */
	public function setAccessToken($access_token)
	{
		$this->access_token = $access_token;
	}

	/**
	 * @return int
	 */
	public function getExpires()
	{
		return $this->expires;
	}

	/**
	 * @param int $expires
	 */
	public function setExpires($expires)
	{
		$this->expires = $expires;
	}

	/**
	 * @return string
	 */
	public function getNotice()
	{
		return $this->notice;
	}

	/**
	 * @param string $notice
	 */
	public function setNotice($notice)
	{
		$this->notice = $notice;
	}

	/**
	 * @return string
	 */
	public function getRefreshToken()
	{
		return $this->refresh_token;
	}

	/**
	 * @param string $refresh_token
	 */
	public function setRefreshToken($refresh_token)
	{
		$this->refresh_token = $refresh_token;
	}

	/**
	 * @return string
	 */
	public function getScope()
	{
		return $this->scope;
	}

	/**
	 * @param string $scope
	 */
	public function setScope($scope)
	{
		$this->scope = $scope;
	}

	/**
	 * @return string
	 */
	public function getTokenType()
	{
		return $this->token_type;
	}

	/**
	 * @param string $token_type
	 */
	public function setTokenType($token_type)
	{
		$this->token_type = $token_type;
	}
}
