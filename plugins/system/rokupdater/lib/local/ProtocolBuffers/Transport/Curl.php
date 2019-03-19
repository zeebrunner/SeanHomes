<?php
/**
 * @version   $Id: Curl.php 11337 2013-06-10 21:21:28Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * Class ProtocolBuffers_Transport_Curl
 */
class ProtocolBuffers_Transport_Curl extends ProtocolBuffers_Transport_AbstractTransport
{
	/**
	 * Constructor. CURLOPT_FOLLOWLOCATION must be disabled when open_basedir or safe_mode are enabled.
	 *
	 * @param   array $options  Client options array.
	 *
	 * @see     http://www.php.net/manual/en/function.curl-setopt.php
	 */
	public function __construct(array $options = array())
	{
		$this->options = $options;
		if (ini_get('open_basedir') || ini_get('safe_mode')) {
			$this->options['follow_location'] = false;
		}
	}

	/**
	 * Method to check if HTTP transport cURL is available for use
	 *
	 * @return boolean true if available, else false
	 *
	 * @since   12.1
	 */
	static public function isSupported()
	{
		return function_exists('curl_init') && is_callable('curl_init') && function_exists('curl_version') && curl_version();
	}

	/**
	 * @return bool is SSL supported
	 */
	protected function isSSLSupported()
	{
		$version       = curl_version();
	    return ($version['features'] & CURL_VERSION_SSL);
	}

	/**
	 * Send a request to the server and return a JHttpResponse object with the response.
	 *
	 * @param   RokUpdater_Uri $uri        The URI to the resource to request.
	 * @param   string         $data       Either an associative array or a string to be sent with the request.
	 * @param   array          $headers    An array of request headers to send with the request.
	 * @param   integer        $timeout    Read timeout in seconds.
	 * @param   string         $userAgent  The optional user agent string to send with the request.
	 *
	 * @throws ProtocolBuffers_Exception
	 * @throws Exception
	 * @return  string
	 */
	protected  function transportRequest(RokUpdater_Uri $uri, $data = null, array $headers = array(), $timeout = null, $userAgent = null)
	{
		// Setup the cURL handle.
		$ch = curl_init();

		// Initialize the certificate store
		$options[CURLOPT_CAINFO] = (array_key_exists('curl.certpath', $this->options)) ? $this->options['curl.certpath'] : dirname(__FILE__);
		$options[CURLOPT_CAINFO] .= '/cacert.pem';

		// Build the headers string for the request.
		$headerArray = array();
		if (isset($headers)) {
			foreach ($headers as $key => $value) {
				$headerArray[] = $key . ': ' . $value;
			}

			// Add the headers string into the stream context options array.
			$options[CURLOPT_HTTPHEADER] = $headerArray;
		}

		$options[CURLOPT_URL]  = $uri->getAbsoluteUri();
		$options[CURLOPT_POST] = true;

		$options[CURLOPT_POSTFIELDS] = 'message=' . urlencode($data);

		JLog::add(sprintf('%s post body is %s', get_class($this), $options[CURLOPT_POSTFIELDS]), JLog::DEBUG, 'rokupdater');

		// If an explicit timeout is given user it.
		if (isset($timeout)) {
			$options[CURLOPT_TIMEOUT]        = (int)$timeout;
			$options[CURLOPT_CONNECTTIMEOUT] = (int)$timeout;
		}

		// If an explicit user agent is given use it.
		if (isset($userAgent)) {
			$headers[CURLOPT_USERAGENT] = $userAgent;
		}

		// Return it... echoing it would be tacky.
		$options[CURLOPT_RETURNTRANSFER] = true;

		// Override the Expect header to prevent cURL from confusing itself in its own stupidity.
		// Link: http://the-stickman.com/web-development/php-and-curl-disabling-100-continue-header/
		$options[CURLOPT_HTTPHEADER][] = 'Expect:';

		// Follow redirects.
		$options[CURLOPT_FOLLOWLOCATION] = (array_key_exists('follow_location', $this->options)) ? (bool)$this->options['follow_location'] : true;

		if (array_key_exists('curl.options', $this->options) && is_array($this->options['curl.options'])) {
			foreach ($this->options['curl.options'] as $key => $value) {
				JLog::add(sprintf('%s setting overriden cURL option %s to  %s', get_class($this), $key, $value, $options[CURLOPT_POSTFIELDS]), JLog::INFO, 'rokupdater');
				$options[$key] = $value;
			}
		}

		// Set the cURL options.
		curl_setopt_array($ch, $options);

		// Execute the request and close the connection.
		$content = curl_exec($ch);

		JLog::add(sprintf('%s : Response is : %s', get_class($this), $content), JLog::DEBUG, 'rokupdater');

		if ($content === false) {
			JLog::add(sprintf('%s : request failed.', get_class($this), $options[CURLOPT_POSTFIELDS]), JLog::INFO, 'rokupdater');
			throw new Exception(curl_error($ch));
		}

		// Close the connection.
		curl_close($ch);

		return $content;
	}
}
