<?php
/**
 * @version   $Id: FOpenStream.php 11337 2013-06-10 21:21:28Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * Class ProtocolBuffers_Transport_Stream
 */
class ProtocolBuffers_Transport_FOpenStream extends ProtocolBuffers_Transport_AbstractPHPStreamTransport
{

	/**
	 * Constructor.
	 *
	 * @param   array $options  Client options object.
	 *
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
		return function_exists('fopen') && is_callable('fopen') && ini_get('allow_url_fopen');
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
	 * @return  string
	 */
	protected function transportRequest(RokUpdater_Uri $uri, $data = null, array $headers = array(), $timeout = null, $userAgent = null)
	{
		// Create the stream context options array with the required method offset.
		$options = array('method' => strtoupper('POST'));

		// If data exists let's encode it and make sure our Content-type header is set.
		if (isset($data)) {

			$options['content'] = 'message=' . urlencode($data);
			JLog::add(sprintf('%s sending body %s', get_class($this), $options['content']), JLog::DEBUG, 'rokupdater');

			if (!isset($headers['Content-type'])) {
				$headers['Content-type'] = 'application/x-www-form-urlencoded';
			}

			$headers['Content-length'] = strlen($options['content']);
		}

		// Build the headers string for the request.
		$headerString = null;
		if (isset($headers)) {
			foreach ($headers as $key => $value) {
				$headerString .= $key . ': ' . $value . "\r\n";
			}

			// Add the headers string into the stream context options array.
			$options['header'] = trim($headerString, "\r\n");
		}

		// If an explicit timeout is given user it.
		if (isset($timeout)) {
			$options['timeout'] = (int)$timeout;
		}

		// If an explicit user agent is given use it.
		if (isset($userAgent)) {
			$options['user_agent'] = $userAgent;
		}

		// Ignore HTTP errors so that we can capture them.
		$options['ignore_errors'] = 1;

		// Create the stream context for the request.
		$context = stream_context_create(array('http' => $options));

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



		// Open the stream for reading.
		$stream = fopen($uri->getAbsoluteUri(), 'r', false, $context);


		// Get the contents from the stream.
		$content = stream_get_contents($stream);

		JLog::add(sprintf('%s : Response is : %s', get_class($this), $content), JLog::DEBUG, 'rokupdater');

		// Close the stream.
		fclose($stream);

		return $content;
	}
}
