<?php
/**
 * @version   $Id: AccessTokenInfo.php 9276 2013-04-11 17:47:57Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokUpdater_AccessTokenInfo
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

	public static function createFromOAuthInfo(RokUpdater_Message_OAuthInfo $oauthinfo)
	{
		$ati = new self;
		$ati->setFromOAuthInfo($oauthinfo);
		return $ati;
	}

	public static function createFromJSON($json)
	{
		$ati = new self;
		$ati->setFromJSON($json);
		return $ati;
	}

	public function setFromOAuthInfo(RokUpdater_Message_OAuthInfo $oauthinfo, $notice = null)
	{
		$this->access_token  = $oauthinfo->getAccessToken();
		$this->expires       = $oauthinfo->getExpiresIn() + time();
		$this->token_type    = $oauthinfo->getTokenType();
		$this->scope         = $oauthinfo->getScope();
		$this->refresh_token = $oauthinfo->getRefreshToken();
		$this->notice        = $notice;
	}

	public function clearAccessInfo($notice = null)
	{
		$this->access_token  = null;
		$this->expires       = null;
		$this->token_type    = null;
		$this->scope         = null;
		$this->refresh_token = null;
		$this->notice        = $notice;
	}

	public function setFromJSON($json)
	{
		$json = json_decode($json);
		if (is_null($json)) {
			throw new RokUpdater_Exception('ROKUPDATER_INVALID_ACCESS_TOKEN_INFO_SAVED');
		}
		$fields = get_object_vars($json);
		foreach ($fields as $name => $value) {
			if (property_exists($this, $name)) {
				$this->$name = $value;
			}
		}
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
