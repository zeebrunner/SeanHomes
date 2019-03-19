<?php
/**
 * @version   $Id: StreamSocket.php 11337 2013-06-10 21:21:28Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * Class ProtocolBuffers_Transport_Socket
 */
class ProtocolBuffers_Transport_StreamSocket extends ProtocolBuffers_Transport_AbstractPHPStreamTransport
{

	/**
	 *
	 */
	const SSL_SOCKET_TYPE = 'ssl://';
	/**
	 *
	 */
	const TCP_SOCKET_TYPE = 'tcp://';
	/**
	 * @var    array  Reusable socket connections.
	 * @since  11.3
	 */
	protected $connections;

	/**
	 * Constructor.
	 *
	 * @param   array $options  Client options object.
	 */
	public function __construct(array $options = array())
	{
		$this->options = $options;
	}

	/**
	 * method to check if http transport layer available for using
	 *
	 * @return bool true if available else false
	 *
	 * @since   12.1
	 */
	static public function isSupported()
	{
		return !function_exists('stream_socket_client') && is_callable('stream_socket_client');
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
	 * @throws Exception
	 * @return  JHttpResponse
	 *
	 * @since   11.3
	 */
	protected function transportRequest(RokUpdater_Uri $uri, $data = null, array $headers = array(), $timeout = null, $userAgent = null)
	{
		$connection = $this->connect($uri, $timeout);

		// Make sure the connection is alive and valid.
		if (is_resource($connection)) {
			// Make sure the connection has not timed out.
			$meta = stream_get_meta_data($connection);
			if ($meta['timed_out']) {
				throw new Exception('Server connection timed out.');
			}
		} else {
			throw new Exception('Not connected to server.');
		}

		// Get the request path from the URI object.
		$path = $uri->getRelativeUri();

		// If we have data to send make sure our request is setup for it.
		if (!empty($data)) {
			// If the data is not a scalar value encode it to be sent with the request.
			if (!is_scalar($data)) {
				$data = http_build_query($data);
			}

			// Add the relevant headers.
			$headers['Content-Type']   = 'application/x-www-form-urlencoded';
			$headers['Content-Length'] = strlen($data);
		}

		// Build the request payload.
		$request   = array();
		$request[] = strtoupper('POST') . ' ' . ((empty($path)) ? '/' : $path) . ' HTTP/1.0';
		$request[] = 'Host: ' . $uri->getHost();

		// If an explicit user agent is given use it.
		if (isset($userAgent)) {
			$headers['User-Agent'] = $userAgent;
		}

		// If there are custom headers to send add them to the request payload.
		if (is_array($headers)) {
			foreach ($headers as $k => $v) {
				$request[] = $k . ': ' . $v;
			}
		}

		// If we have data to send add it to the request payload.
		if (!empty($data)) {
			$request[] = null;
			$request[] = 'message=' . urlencode($data);
		}

		$request_content = implode("\r\n", $request) . "\r\n\r\n";
		JLog::add(sprintf('%s : Sending request: %s', get_class($this), $request_content), JLog::DEBUG, 'rokupdater');

		// Send the request to the server.
		fwrite($connection, $request_content);

		// Get the response data from the server.
		$content = '';

		while (!feof($connection)) {
			$content .= fgets($connection, 4096);
		}

		JLog::add(sprintf('%s : Response is : %s', get_class($this), $content), JLog::DEBUG, 'rokupdater');

		if (strlen($content) <= 0) {
			JLog::add(sprintf('%s : Request failed empty content returned.', get_class($this)), JLog::INFO, 'rokupdater');
			throw new Exception('Request failed empty content returned.');
		}
		return $this->getResponse($content);
	}

	protected function connect(RokUpdater_Uri $uri, $timeout = null)
	{
		// Initialize variables.
		$errno = null;
		$err   = null;


		$host = ($uri->getScheme() == self::HTTPS_SCHEME) ? self::SSL_SOCKET_TYPE : self::TCP_SOCKET_TYPE;
		$host .= $uri->getHost() . ':' . $uri->getPort();

		JLog::add(sprintf('%s : Stream socket url is %s.', get_class($this), $host), JLog::DEBUG, 'rokupdater');

		// Build the connection key for resource memory caching.
		$key = md5($host);

		// If the connection already exists, use it.
		if (!empty($this->connections[$key]) && is_resource($this->connections[$key])) {
			// Make sure the connection has not timed out.
			$meta = stream_get_meta_data($this->connections[$key]);
			if (!$meta['timed_out']) {
				return $this->connections[$key];
			}
		}

		if (null == $timeout) {
			$timeout = ini_get("default_socket_timeout");
		}


		$context = stream_context_create();
		if ($uri->getScheme() == self::HTTPS_SCHEME) {
			$cacert_path = dirname(__FILE__) . '/cacert.pem';
			JLog::add(sprintf('%s : setting ssl:cacert option to %s', get_class($this), $cacert_path), JLog::INFO, 'rokupdater');
			stream_context_set_option($context, 'ssl', 'cafile', $cacert_path);
			stream_context_set_option($context, 'ssl', 'verify_peer', true);

		}
		if (array_key_exists('streamsocket.options', $this->options) && is_array($this->options['streamsocket.options'])) {
			foreach ($this->options['streamsocket.options'] as $wrapper => $settings) {
				foreach ($settings as $setting_name => $setting_value) {
					JLog::add(sprintf('%s : Stream socket override option set %s:%s:%s.', get_class($this), $wrapper, $setting_name, $setting_value), JLog::INFO, 'rokupdater');
					stream_context_set_option($context, $wrapper, $setting_name, $setting_value);
				}
			}
		}

		// Attempt to connect to the server.
		$connection = stream_socket_client($host, $errno, $err, $timeout, STREAM_CLIENT_CONNECT, $context);
		if (!$connection) {
			throw new Exception($err, $errno);
		}

		// Since the connection was successful let's store it in case we need to use it later.
		$this->connections[$key] = $connection;

		// If an explicit timeout is set, set it.
		if (isset($timeout)) {
			stream_set_timeout($this->connections[$key], (int)$timeout);
		}

		return $this->connections[$key];
	}

	/**
	 * Method to get a response object from a server response.
	 *
	 * @param   string $content  The complete server response, including headers.
	 *
	 * @return  string
	 *
	 * @since   11.3
	 * @throws  UnexpectedValueException
	 */
	protected function getResponse($content)
	{
		// Split the response into headers and body.
		$response = explode("\r\n\r\n", $content, 2);
		return $response[1];
	}

}
