<?php
/**
 * Class RokUpdater_Message_BundleAvailabilityStatus
 */
class RokUpdater_Message_BundleAvailabilityStatus extends ProtocolBuffers_Type_Enum
{
	/**
	 *
	 */
	const RESTRICTED_BLOCKED = 0;
	/**
	 *
	 */
	const RESTRICTED_ALLOWED = 1;
	/**
	 *
	 */
	const FREE = 2;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		$this->names = array(
			0 => "RESTRICTED_BLOCKED",
			1 => "RESTRICTED_ALLOWED",
			2 => "FREE"
		);
	}
}

/**
 * Class RokUpdater_Message_BundleData
 */
class RokUpdater_Message_BundleData extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_BundleData"]["1"]     = "RokUpdater_Message_BundleAvailabilityStatus";
		$this->values["1"]                                      = "";
		$this->values["1"]                                      = new RokUpdater_Message_BundleAvailabilityStatus();
		$this->values["1"]->value                               = RokUpdater_Message_BundleAvailabilityStatus::RESTRICTED_BLOCKED;
		self::$fieldNames["RokUpdater_Message_BundleData"]["1"] = "availability";
		self::$fields["RokUpdater_Message_BundleData"]["2"]     = "ProtocolBuffers_Type_String";
		$this->values["2"]                                      = array();
		self::$fieldNames["RokUpdater_Message_BundleData"]["2"] = "accessable_clubs";
	}

	/**
	 * @return RokUpdater_Message_BundleAvailabilityStatus
	 */
	function getAvailability()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param RokUpdater_Message_BundleAvailabilityStatus $value
	 *
	 * @return RokUpdater_Message_BundleData
	 */
	function setAvailability($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasAvailability()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return mixed
	 */
	function availability_string()
	{
		return $this->values["1"]->get_description();
	}

	/**
	 * @return string
	 */
	function getAccessableClubs($offset)
	{
		$v = $this->_get_arr_value("2", $offset);
		return $v->get_value();
	}

	/**
	 * @return int index for the string added/
	 */
	function addAccessableClubs($value)
	{
		$v = $this->_add_arr_value("2");
		$v->set_value($value);
		return $this;
	}

	/**
	 * @return RokUpdater_Message_BundleData
	 */
	function setAccessableClubs($index, $value)
	{
		$v = new self::$fields["RokUpdater_Message_BundleData"]["2"]();
		$v->set_value($value);
		$this->_set_arr_value("2", $index, $v);
		return $this;
	}

	/**
	 *
	 */
	function remove_last_accessable_clubs()
	{
		$this->_remove_last_arr_value("2");
	}

	/**
	 * @return int
	 */
	function getAccessableClubsCount()
	{
		return $this->_get_arr_size("2");
	}

	/**
	 * @return string[]
	 */
	function getAccessableClubss()
	{
		return $this->_get_value("2");
	}
}

/**
 * Class RokUpdater_Message_AccessTokenRequest
 */
class RokUpdater_Message_AccessTokenRequest extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_AccessTokenRequest"]["1"]     = "ProtocolBuffers_Type_String";
		$this->values["1"]                                              = "";
		self::$fieldNames["RokUpdater_Message_AccessTokenRequest"]["1"] = "username";
		self::$fields["RokUpdater_Message_AccessTokenRequest"]["2"]     = "ProtocolBuffers_Type_String";
		$this->values["2"]                                              = "";
		self::$fieldNames["RokUpdater_Message_AccessTokenRequest"]["2"] = "password";
		self::$fields["RokUpdater_Message_AccessTokenRequest"]["3"]     = "ProtocolBuffers_Type_String";
		$this->values["3"]                                              = "";
		self::$fieldNames["RokUpdater_Message_AccessTokenRequest"]["3"] = "siteId";
	}

	/**
	 * @return string
	 */
	function getUsername()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_AccessTokenRequest
	 */
	function setUsername($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasUsername()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return string
	 */
	function getPassword()
	{
		return $this->_get_value("2");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_AccessTokenRequest
	 */
	function setPassword($value)
	{
		$this->_set_value("2", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasPassword()
	{
		return $this->_is_set("2");
	}

	/**
	 * @return string
	 */
	function getSiteId()
	{
		return $this->_get_value("3");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_AccessTokenRequest
	 */
	function setSiteId($value)
	{
		$this->_set_value("3", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasSiteId()
	{
		return $this->_is_set("3");
	}
}

/**
 * Class RokUpdater_Message_RequestStatus
 */
class RokUpdater_Message_RequestStatus extends ProtocolBuffers_Type_Enum
{
	/**
	 *
	 */
	const ERROR = 0;
	/**
	 *
	 */
	const SUCCESS = 1;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		$this->names = array(
			0 => "ERROR",
			1 => "SUCCESS"
		);
	}
}

/**
 * Class RokUpdater_Message_OAuthInfo
 */
class RokUpdater_Message_OAuthInfo extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_OAuthInfo"]["1"]     = "ProtocolBuffers_Type_String";
		$this->values["1"]                                     = "";
		self::$fieldNames["RokUpdater_Message_OAuthInfo"]["1"] = "access_token";
		self::$fields["RokUpdater_Message_OAuthInfo"]["2"]     = "ProtocolBuffers_Type_Int";
		$this->values["2"]                                     = "";
		self::$fieldNames["RokUpdater_Message_OAuthInfo"]["2"] = "expires_in";
		self::$fields["RokUpdater_Message_OAuthInfo"]["3"]     = "ProtocolBuffers_Type_String";
		$this->values["3"]                                     = "";
		self::$fieldNames["RokUpdater_Message_OAuthInfo"]["3"] = "token_type";
		self::$fields["RokUpdater_Message_OAuthInfo"]["4"]     = "ProtocolBuffers_Type_String";
		$this->values["4"]                                     = "";
		self::$fieldNames["RokUpdater_Message_OAuthInfo"]["4"] = "scope";
		self::$fields["RokUpdater_Message_OAuthInfo"]["5"]     = "ProtocolBuffers_Type_String";
		$this->values["5"]                                     = "";
		self::$fieldNames["RokUpdater_Message_OAuthInfo"]["5"] = "refresh_token";
	}

	/**
	 * @return string
	 */
	function getAccessToken()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_OAuthInfo
	 */
	function setAccessToken($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasAccessToken()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return int
	 */
	function getExpiresIn()
	{
		return $this->_get_value("2");
	}

	/**
	 * @param int $value
	 *
	 * @return RokUpdater_Message_OAuthInfo
	 */
	function setExpiresIn($value)
	{
		$this->_set_value("2", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasExpiresIn()
	{
		return $this->_is_set("2");
	}

	/**
	 * @return string
	 */
	function getTokenType()
	{
		return $this->_get_value("3");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_OAuthInfo
	 */
	function setTokenType($value)
	{
		$this->_set_value("3", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasTokenType()
	{
		return $this->_is_set("3");
	}

	/**
	 * @return string
	 */
	function getScope()
	{
		return $this->_get_value("4");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_OAuthInfo
	 */
	function setScope($value)
	{
		$this->_set_value("4", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasScope()
	{
		return $this->_is_set("4");
	}

	/**
	 * @return string
	 */
	function getRefreshToken()
	{
		return $this->_get_value("5");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_OAuthInfo
	 */
	function setRefreshToken($value)
	{
		$this->_set_value("5", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasRefreshToken()
	{
		return $this->_is_set("5");
	}
}

/**
 * Class RokUpdater_Message_Subscription
 */
class RokUpdater_Message_Subscription extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_Subscription"]["1"]     = "ProtocolBuffers_Type_String";
		$this->values["1"]                                        = "";
		self::$fieldNames["RokUpdater_Message_Subscription"]["1"] = "club";
		self::$fields["RokUpdater_Message_Subscription"]["2"]     = "ProtocolBuffers_Type_Bool";
		$this->values["2"]                                        = "";
		self::$fieldNames["RokUpdater_Message_Subscription"]["2"] = "active";
	}

	/**
	 * @return string
	 */
	function getClub()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_Subscription
	 */
	function setClub($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasClub()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return bool
	 */
	function getActive()
	{
		return $this->_get_value("2");
	}

	/**
	 * @param bool $value
	 *
	 * @return RokUpdater_Message_Subscription
	 */
	function setActive($value)
	{
		$this->_set_value("2", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasActive()
	{
		return $this->_is_set("2");
	}
}

/**
 * Class RokUpdater_Message_SubscriberInfo
 */
class RokUpdater_Message_SubscriberInfo extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_SubscriberInfo"]["1"]     = "ProtocolBuffers_Type_String";
		$this->values["1"]                                          = "";
		self::$fieldNames["RokUpdater_Message_SubscriberInfo"]["1"] = "username";
		self::$fields["RokUpdater_Message_SubscriberInfo"]["2"]     = "RokUpdater_Message_Subscription";
		$this->values["2"]                                          = array();
		self::$fieldNames["RokUpdater_Message_SubscriberInfo"]["2"] = "subscription";
	}

	/**
	 * @return string
	 */
	function getUsername()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_SubscriberInfo
	 */
	function setUsername($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasUsername()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return RokUpdater_Message_Subscription
	 */
	function getSubscription($offset)
	{
		return $this->_get_arr_value("2", $offset);
	}

	/**
	 * @return int index for the RokUpdater_Message_Subscription added/
	 */
	function addSubscription()
	{
		return $this->_add_arr_value("2");
	}

	/**
	 * @return RokUpdater_Message_SubscriberInfo
	 */
	function setSubscription($index, $value)
	{
		$this->_set_arr_value("2", $index, $value);
		return $this;
	}

	/**
	 * @return RokUpdater_Message_SubscriberInfo
	 */
	function addAllSubscription($values)
	{
		$this->_set_arr_values("2", $values);
		return $this;
	}

	/**
	 *
	 */
	function remove_last_subscription()
	{
		$this->_remove_last_arr_value("2");
	}

	/**
	 * @return int
	 */
	function getSubscriptionCount()
	{
		return $this->_get_arr_size("2");
	}

	/**
	 * @return RokUpdater_Message_Subscription[]
	 */
	function getSubscriptions()
	{
		return $this->_get_value("2");
	}
}

/**
 * Class RokUpdater_Message_AccessTokenResponse
 */
class RokUpdater_Message_AccessTokenResponse extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_AccessTokenResponse"]["1"]     = "RokUpdater_Message_RequestStatus";
		$this->values["1"]                                               = "";
		self::$fieldNames["RokUpdater_Message_AccessTokenResponse"]["1"] = "status";
		self::$fields["RokUpdater_Message_AccessTokenResponse"]["2"]     = "ProtocolBuffers_Type_String";
		$this->values["2"]                                               = "";
		self::$fieldNames["RokUpdater_Message_AccessTokenResponse"]["2"] = "message";
		self::$fields["RokUpdater_Message_AccessTokenResponse"]["3"]     = "RokUpdater_Message_OAuthInfo";
		$this->values["3"]                                               = "";
		self::$fieldNames["RokUpdater_Message_AccessTokenResponse"]["3"] = "oauth_info";
		self::$fields["RokUpdater_Message_AccessTokenResponse"]["4"]     = "RokUpdater_Message_SubscriberInfo";
		$this->values["4"]                                               = "";
		self::$fieldNames["RokUpdater_Message_AccessTokenResponse"]["4"] = "subscriber_info";
	}

	/**
	 * @return RokUpdater_Message_RequestStatus
	 */
	function getStatus()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param RokUpdater_Message_RequestStatus $value
	 *
	 * @return RokUpdater_Message_AccessTokenResponse
	 */
	function setStatus($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasStatus()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return mixed
	 */
	function status_string()
	{
		return $this->values["1"]->get_description();
	}

	/**
	 * @return string
	 */
	function getMessage()
	{
		return $this->_get_value("2");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_AccessTokenResponse
	 */
	function setMessage($value)
	{
		$this->_set_value("2", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasMessage()
	{
		return $this->_is_set("2");
	}

	/**
	 * @return RokUpdater_Message_OAuthInfo
	 */
	function getOauthInfo()
	{
		return $this->_get_value("3");
	}

	/**
	 * @param RokUpdater_Message_OAuthInfo $value
	 *
	 * @return RokUpdater_Message_AccessTokenResponse
	 */
	function setOauthInfo($value)
	{
		$this->_set_value("3", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasOauthInfo()
	{
		return $this->_is_set("3");
	}

	/**
	 * @return RokUpdater_Message_SubscriberInfo
	 */
	function getSubscriberInfo()
	{
		return $this->_get_value("4");
	}

	/**
	 * @param RokUpdater_Message_SubscriberInfo $value
	 *
	 * @return RokUpdater_Message_AccessTokenResponse
	 */
	function setSubscriberInfo($value)
	{
		$this->_set_value("4", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasSubscriberInfo()
	{
		return $this->_is_set("4");
	}
}

/**
 * Class RokUpdater_Message_RefreshTokenRequest
 */
class RokUpdater_Message_RefreshTokenRequest extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_RefreshTokenRequest"]["1"]     = "ProtocolBuffers_Type_String";
		$this->values["1"]                                               = "";
		self::$fieldNames["RokUpdater_Message_RefreshTokenRequest"]["1"] = "refresh_token";
		self::$fields["RokUpdater_Message_RefreshTokenRequest"]["2"]     = "ProtocolBuffers_Type_String";
		$this->values["2"]                                               = "";
		self::$fieldNames["RokUpdater_Message_RefreshTokenRequest"]["2"] = "siteId";
	}

	/**
	 * @return string
	 */
	function getRefreshToken()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_RefreshTokenRequest
	 */
	function setRefreshToken($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasRefreshToken()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return string
	 */
	function getSiteId()
	{
		return $this->_get_value("2");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_RefreshTokenRequest
	 */
	function setSiteId($value)
	{
		$this->_set_value("2", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasSiteId()
	{
		return $this->_is_set("2");
	}
}

/**
 * Class RokUpdater_Message_RefreshTokenResponse
 */
class RokUpdater_Message_RefreshTokenResponse extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_RefreshTokenResponse"]["1"]     = "RokUpdater_Message_RequestStatus";
		$this->values["1"]                                                = "";
		self::$fieldNames["RokUpdater_Message_RefreshTokenResponse"]["1"] = "status";
		self::$fields["RokUpdater_Message_RefreshTokenResponse"]["2"]     = "ProtocolBuffers_Type_String";
		$this->values["2"]                                                = "";
		self::$fieldNames["RokUpdater_Message_RefreshTokenResponse"]["2"] = "message";
		self::$fields["RokUpdater_Message_RefreshTokenResponse"]["3"]     = "RokUpdater_Message_OAuthInfo";
		$this->values["3"]                                                = "";
		self::$fieldNames["RokUpdater_Message_RefreshTokenResponse"]["3"] = "oauth_info";
		self::$fields["RokUpdater_Message_RefreshTokenResponse"]["4"]     = "RokUpdater_Message_SubscriberInfo";
		$this->values["4"]                                                = "";
		self::$fieldNames["RokUpdater_Message_RefreshTokenResponse"]["4"] = "subscriber_info";
	}

	/**
	 * @return RokUpdater_Message_RequestStatus
	 */
	function getStatus()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param RokUpdater_Message_RequestStatus $value
	 *
	 * @return RokUpdater_Message_RefreshTokenResponse
	 */
	function setStatus($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasStatus()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return mixed
	 */
	function status_string()
	{
		return $this->values["1"]->get_description();
	}

	/**
	 * @return string
	 */
	function getMessage()
	{
		return $this->_get_value("2");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_RefreshTokenResponse
	 */
	function setMessage($value)
	{
		$this->_set_value("2", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasMessage()
	{
		return $this->_is_set("2");
	}

	/**
	 * @return RokUpdater_Message_OAuthInfo
	 */
	function getOauthInfo()
	{
		return $this->_get_value("3");
	}

	/**
	 * @param RokUpdater_Message_OAuthInfo $value
	 *
	 * @return RokUpdater_Message_RefreshTokenResponse
	 */
	function setOauthInfo($value)
	{
		$this->_set_value("3", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasOauthInfo()
	{
		return $this->_is_set("3");
	}

	/**
	 * @return RokUpdater_Message_SubscriberInfo
	 */
	function getSubscriberInfo()
	{
		return $this->_get_value("4");
	}

	/**
	 * @param RokUpdater_Message_SubscriberInfo $value
	 *
	 * @return RokUpdater_Message_RefreshTokenResponse
	 */
	function setSubscriberInfo($value)
	{
		$this->_set_value("4", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasSubscriberInfo()
	{
		return $this->_is_set("4");
	}
}

/**
 * Class RokUpdater_Message_LogoutRequest
 */
class RokUpdater_Message_LogoutRequest extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_LogoutRequest"]["1"]     = "ProtocolBuffers_Type_String";
		$this->values["1"]                                         = "";
		self::$fieldNames["RokUpdater_Message_LogoutRequest"]["1"] = "username";
		self::$fields["RokUpdater_Message_LogoutRequest"]["2"]     = "ProtocolBuffers_Type_String";
		$this->values["2"]                                         = "";
		self::$fieldNames["RokUpdater_Message_LogoutRequest"]["2"] = "access_token";
		self::$fields["RokUpdater_Message_LogoutRequest"]["3"]     = "ProtocolBuffers_Type_String";
		$this->values["3"]                                         = "";
		self::$fieldNames["RokUpdater_Message_LogoutRequest"]["3"] = "refresh_token";
		self::$fields["RokUpdater_Message_LogoutRequest"]["4"]     = "ProtocolBuffers_Type_String";
		$this->values["4"]                                         = "";
		self::$fieldNames["RokUpdater_Message_LogoutRequest"]["4"] = "siteId";
	}

	/**
	 * @return string
	 */
	function getUsername()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_LogoutRequest
	 */
	function setUsername($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasUsername()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return string
	 */
	function getAccessToken()
	{
		return $this->_get_value("2");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_LogoutRequest
	 */
	function setAccessToken($value)
	{
		$this->_set_value("2", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasAccessToken()
	{
		return $this->_is_set("2");
	}

	/**
	 * @return string
	 */
	function getRefreshToken()
	{
		return $this->_get_value("3");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_LogoutRequest
	 */
	function setRefreshToken($value)
	{
		$this->_set_value("3", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasRefreshToken()
	{
		return $this->_is_set("3");
	}

	/**
	 * @return string
	 */
	function getSiteId()
	{
		return $this->_get_value("4");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_LogoutRequest
	 */
	function setSiteId($value)
	{
		$this->_set_value("4", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasSiteId()
	{
		return $this->_is_set("4");
	}
}

/**
 * Class RokUpdater_Message_LogoutResponse
 */
class RokUpdater_Message_LogoutResponse extends RokUpdater_AbstractProtocolBuffersMessage
{
	/**
	 * @var int
	 */
	protected $wired_type = RokUpdater_AbstractProtocolBuffersMessage::WIRED_LENGTH_DELIMITED;

	/**
	 * @param null $reader
	 */
	public function __construct($reader = null)
	{
		parent::__construct($reader);
		self::$fields["RokUpdater_Message_LogoutResponse"]["1"]     = "RokUpdater_Message_RequestStatus";
		$this->values["1"]                                          = "";
		self::$fieldNames["RokUpdater_Message_LogoutResponse"]["1"] = "status";
		self::$fields["RokUpdater_Message_LogoutResponse"]["2"]     = "ProtocolBuffers_Type_String";
		$this->values["2"]                                          = "";
		self::$fieldNames["RokUpdater_Message_LogoutResponse"]["2"] = "message";
	}

	/**
	 * @return RokUpdater_Message_RequestStatus
	 */
	function getStatus()
	{
		return $this->_get_value("1");
	}

	/**
	 * @param RokUpdater_Message_RequestStatus $value
	 *
	 * @return RokUpdater_Message_LogoutResponse
	 */
	function setStatus($value)
	{
		$this->_set_value("1", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasStatus()
	{
		return $this->_is_set("1");
	}

	/**
	 * @return mixed
	 */
	function status_string()
	{
		return $this->values["1"]->get_description();
	}

	/**
	 * @return string
	 */
	function getMessage()
	{
		return $this->_get_value("2");
	}

	/**
	 * @param string $value
	 *
	 * @return RokUpdater_Message_LogoutResponse
	 */
	function setMessage($value)
	{
		$this->_set_value("2", $value);
		return $this;
	}

	/**
	 * @return bool
	 */
	function hasMessage()
	{
		return $this->_is_set("2");
	}
}
