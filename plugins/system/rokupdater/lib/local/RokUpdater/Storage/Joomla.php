<?php
/**
 * @version   $Id: Joomla.php 8934 2013-03-29 19:17:23Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokUpdater_Storage_Joomla implements OAuth2_Storage_AuthorizationCodeInterface, OAuth2_Storage_AccessTokenInterface, OAuth2_Storage_ClientCredentialsInterface, OAuth2_Storage_UserCredentialsInterface, OAuth2_Storage_RefreshTokenInterface
{
	/**
	 * @var JDatabase
	 */
	protected $db;

	public function __construct(JDatabase $db)
	{
		$this->db = $db;
	}

	/**
	 * Look up the supplied oauth_token from storage.
	 * We need to retrieve access token data as we create and verify tokens.
	 *
	 * @param $oauth_token
	 * oauth_token to be check with.
	 *
	 * @return array|null An associative array as below, and return NULL if the supplied oauth_token@ingroup oauth2_section_7
	 */
	public function getAccessToken($oauth_token)
	{
		$ret = null;
		/** @var $token RokOAuthAccessToken */
		$token = JTable::getInstance('AccessToken', 'RokOAuth');
		if (($token_id = $token->find(array('access_token' => $oauth_token))) != null) {
			$token->load($token_id);
			$ret = array(
				'oauth_token' => $token->access_token,
				'client_id'   => $token->client->client_id,
				'user_id'     => $token->user->username,
				'expires'     => JDate::getInstance($token->expires)->toUnix(),
				'scope'       => $token->scope
			);
		}
		return $ret;
	}

	/**
	 * Store the supplied access token values to storage.
	 *
	 * We need to store access token data as we create and verify tokens.
	 *
	 * @param $oauth_token
	 * oauth_token to be stored.
	 * @param $client_id
	 * Client identifier to be stored.
	 * @param $user_id
	 * User identifier to be stored.
	 * @param $expires
	 * Expiration to be stored.
	 * @param $scope
	 * (optional) Scopes to be stored in space-separated string.
	 *
	 * @ingroup oauth2_section_4
	 */
	public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null)
	{
		/** @var $client RokOAuthClient */
		$client = JTable::getInstance('Client', 'RokOauth');
		$client->load($client->find(array('client_id' => $client_id)));

		/** @var $user RokUpdaterUser */
		$user = JTable::getInstance('User', 'RokUpdater');
		$user->load($user->find(array('username' => $user_id)));

		/** @var $access_token RokOAuthAccessToken */
		$access_token               = JTable::getInstance('AccessToken', 'RokOauth');
		$access_token->access_token = $oauth_token;
		$access_token->user         = $user->id;
		$access_token->client       = $client->id;
		$access_token->expires      = JDate::getInstance($expires)->toSql();
		$access_token->store();
	}

	/**
	 * Fetch authorization code data (probably the most common grant type).
	 *
	 * Retrieve the stored data for the given authorization code.
	 *
	 * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
	 *
	 * @param $code
	 * Authorization code to be check with.
	 *
	 * @return void An associative array as below, and NULL if the code is invalid:@see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.1
	 *
	 * @ingroup oauth2_section_4
	 */
	public function getAuthorizationCode($code)
	{
		$ret = null;
		/** @var $authorization_code RokOAuthAuthorizationCode */
		$authorization_code = JTable::getInstance('AuthorizationCode', 'RokOAuth');
		if (($code_id = $authorization_code->find(array('authorization_code' => $code))) != null) {
			$authorization_code->load($code_id);
			$ret = array(
				'code'         => $authorization_code->authorization_code,
				'client_id'    => $authorization_code->client->client_id,
				'user_id'      => $authorization_code->user->username,
				'redirect_uri' => $authorization_code->redirect_uri,
				'expires'      => JDate::getInstance($authorization_code->expires)->toUnix(),
				'scope'        => $authorization_code->scope
			);
		}
		return $ret;
	}

	/**
	 * Take the provided authorization code values and store them somewhere.
	 *
	 * This function should be the storage counterpart to getAuthCode().
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
	 *
	 * @param $code
	 * Authorization code to be stored.
	 * @param $client_id
	 * Client identifier to be stored.
	 * @param $user_id
	 * User identifier to be stored.
	 * @param $redirect_uri
	 * Redirect URI to be stored.
	 * @param $expires
	 * Expiration to be stored.
	 * @param $scope
	 * (optional) Scopes to be stored in space-separated string.
	 *
	 * @ingroup oauth2_section_4
	 */
	public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null)
	{
		/** @var $client RokOAuthClient */
		$client = JTable::getInstance('Client', 'RokOauth');
		$client->load($client->find(array('client_id' => $client_id)));

		/** @var $user RokUpdaterUser */
		$user = JTable::getInstance('User', 'RokUpdater');
		$user->load($user->find(array('username' => $user_id)));

		/** @var $code RokOAuthAuthorizationCode */
		$code                     = JTable::getInstance('AuthorizationCode', 'RokOauth');
		$code->authorization_code = $code;
		$code->user               = $user->id;
		$code->client             = $client->id;
		$code->redirect_uri       = $redirect_uri;
		$code->expires            = JDate::getInstance($expires)->toSql();
		$code->scope              = $scope;
		$code->store();
	}

	/**
	 * Make sure that the client credentials is valid.
	 *
	 * @param $client_id
	 * Client identifier to be check with.
	 * @param $client_secret
	 * (optional) If a secret is required, check that they've given the right one.
	 *
	 * @return bool TRUE if the client credentials are valid, and MUST return FALSE if it isn't.@endcode
	 *
	 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-3.1
	 *
	 * @ingroup oauth2_section_3
	 */
	public function checkClientCredentials($client_id, $client_secret = null)
	{
		/** @var $client RokOAuthClient */
		$client = JTable::getInstance('Client', 'RokOauth');
		if (($id = $client->find(array('client_id' => $client_id))) != null) {
			$client->load($id);
			if ($client->active && $client->client_secret == $client_secret) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check restricted grant types of corresponding client identifier.
	 *
	 * If you want to restrict clients to certain grant types, override this
	 * function.
	 *
	 * @param $client_id
	 * Client identifier to be check with.
	 * @param $grant_type
	 * Grant type to be check with
	 *
	 * @return void TRUE if the grant type is supported by this client identifier, and@ingroup oauth2_section_4
	 */
	public function checkRestrictedGrantType($client_id, $grant_type)
	{
		return true;
	}

	/**
	 * Get client details corresponding client_id.
	 *
	 * OAuth says we should store request URIs for each registered client.
	 * Implement this function to grab the stored URI for a given client id.
	 *
	 * @param $client_id
	 * Client identifier to be check with.
	 *
	 * @return array
	 * Client details. Only mandatory item is the "registered redirect URI", and MUST
	 * return FALSE if the given client does not exist or is invalid.
	 *
	 * @ingroup oauth2_section_4
	 */
	public function getClientDetails($client_id)
	{
		// TODO: Implement getClientDetails() method.
	}

	/**
	 * Grant refresh access tokens.
	 *
	 * Retrieve the stored data for the given refresh token.
	 *
	 * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
	 *
	 * @param $refresh_token
	 * Refresh token to be check with.
	 *
	 * @return void An associative array as below, and NULL if the refresh_token is@see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-6
	 *
	 * @ingroup oauth2_section_6
	 */
	public function getRefreshToken($refresh_token)
	{
		$ret = null;
		/** @var $token RokOAuthRefreshToken */
		$token = JTable::getInstance('RefreshToken', 'RokOAuth');
		if (($token_id = $token->find(array('refresh_token' => $refresh_token))) != null) {
			$token->load($token_id);
			$ret = array(
				'refresh_token' => $token->refresh_token,
				'client_id'     => $token->client->client_id,
				'user_id'       => $token->user->username,
				'expires'       => JDate::getInstance($token->expires)->toUnix(),
				'scope'         => $token->scope
			);
		}
		return $ret;
	}

	/**
	 * Take the provided refresh token values and store them somewhere.
	 *
	 * This function should be the storage counterpart to getRefreshToken().
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
	 *
	 * @param $refresh_token
	 * Refresh token to be stored.
	 * @param $client_id
	 * Client identifier to be stored.
	 * @param $user_id
	 * @param $expires
	 * expires to be stored.
	 * @param $scope
	 * (optional) Scopes to be stored in space-separated string.
	 *
	 * @return void
	 * @ingroup oauth2_section_6
	 */
	public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
	{
		/** @var $client RokOAuthClient */
		$client = JTable::getInstance('Client', 'RokOauth');
		$client->load($client->find(array('client_id' => $client_id)));

		/** @var $user RokUpdaterUser */
		$user = JTable::getInstance('User', 'RokUpdater');
		$user->load($user->find(array('username' => $user_id)));

		/** @var $token RokOAuthRefreshToken */
		$token                = JTable::getInstance('RefreshToken', 'RokOauth');
		$token->refresh_token = $refresh_token;
		$token->user          = $user->id;
		$token->client        = $client->id;
		$token->expires       =  JDate::getInstance($expires)->toSql();
		$token->store();
	}

	/**
	 * Expire a used refresh token.
	 *
	 * This is not explicitly required in the spec, but is almost implied.
	 * After granting a new refresh token, the old one is no longer useful and
	 * so should be forcibly expired in the data store so it can't be used again.
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * @param $refresh_token
	 * Refresh token to be expirse.
	 *
	 * @ingroup oauth2_section_6
	 */
	public function unsetRefreshToken($refresh_token)
	{
		// TODO: Implement unsetRefreshToken() method.
	}

	/**
	 * Grant access tokens for basic user credentials.
	 *
	 * Check the supplied username and password for validity.
	 *
	 * You can also use the $client_id param to do any checks required based
	 * on a client, if you need that.
	 *
	 * Required for OAuth2::GRANT_TYPE_USER_CREDENTIALS.
	 *
	 * @param $username
	 * Username to be check with.
	 * @param $password
	 * Password to be check with.
	 *
	 * @return bool TRUE if the username and password are valid, and FALSE if it isn't.@code
	 *      return array(
	 * 'scope' => <stored scope values (space-separated string)>,
	 * );
	 * @endcode
	 *
	 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.3
	 *
	 * @ingroup oauth2_section_4
	 */
	public function checkUserCredentials($username, $password)
	{
		/** @var $user RokUpdaterUser */
		$user = JTable::getInstance('User', 'RokUpdater');
		if (($user_id = $user->find(array('username' => $username))) == null) {
			return false;
		}
		$user->load($user_id);

		if (!$user->block && isset($user->password)) {
			$parts     = explode(':', $user->password);
			$crypt     = $parts[0];
			$salt      = @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($password, $salt);
			if ($crypt == $testcrypt) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * @param $username
	 *
	 * @return void ARRAY the associated "scope" or "user_id" values if applicable, or an empty array
	 */
	public function getUserDetails($username)
	{
		return array('user_id' => $username);
	}

	/**
	 * once an Authorization Code is used, it must be exipired
	 *
	 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-31#section-4.1.2
	 *
	 *    The client MUST NOT use the authorization code
	 *    more than once.  If an authorization code is used more than
	 *    once, the authorization server MUST deny the request and SHOULD
	 *    revoke (when possible) all tokens previously issued based on
	 *    that authorization code
	 *
	 */
	public function expireAuthorizationCode($code)
	{
		// TODO: Implement expireAuthorizationCode() method.
	}
}
