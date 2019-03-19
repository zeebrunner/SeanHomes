<?php
/**
 * @version   $Id: AbstractTransport.php 11337 2013-06-10 21:21:28Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

abstract class ProtocolBuffers_Transport_AbstractTransport implements ProtocolBuffers_TransportInterface
{
	/**
	 *
	 */
	const HTTPS_PORT = 443;
	/**
	 *
	 */
	const HTTP_PORT = 80;
	/**
	 *
	 */
	const HTTPS_SCHEME = 'https';
	/**
	 *
	 */
	const HTTP_SCHEME = 'http';
	/**
	 * @var array   array of options
	 */
	protected $options;

	/**
	 * wrapper to send a request to the server and return a string with the response body.
	 *
	 * @param   RokUpdater_Uri $uri        The URI to the resource to request.
	 * @param   string         $data       Either an associative array or a string to be sent with the request.
	 * @param   array          $headers    An array of request headers to send with the request.
	 * @param   integer        $timeout    Read timeout in seconds.
	 * @param   string         $userAgent  The optional user agent string to send with the request.
	 *
	 * @throws ProtocolBuffers_Exception
	 * @throws Exception
	 * @return string
	 */
	public function request(RokUpdater_Uri $uri, $data = null, array $headers = array(), $timeout = null, $userAgent = null)
	{
		JLog::add(sprintf('%s request called for %s', get_class($this), $uri->getAbsoluteUri()), JLog::DEBUG, 'rokupdater');
		JLog::add(sprintf('%s request data passed %s', get_class($this), $data), JLog::DEBUG, 'rokupdater');

		if ($uri->getScheme() == self::HTTPS_SCHEME && !$this->isSSLSupported() && array_key_exists('http_fallback', $this->options)) {
			JLog::add(sprintf('%s : SSL is not supported in cURL and fallback is set', get_class($this)), JLog::DEBUG, 'rokupdater');
			if ($this->options['http_fallback']) {
				$uri->setScheme(self::HTTP_SCHEME);
				if ((int)$uri->getPort() == self::HTTPS_PORT) {
					$uri->setPort(self::HTTP_PORT);
				}
			} else {
				JLog::add('SSL is not supported in cURL and fallback not set', JLog::INFO, 'rokupdater');
				throw new ProtocolBuffers_Exception(sprintf('%s Transport implmentation does not support SSL', get_class($this)), ProtocolBuffers_TransportFactory::PROTOCOL_BUFFERS_ERROR_SSL_STREAM_NOT_REGISTERED);
			}
		}
		JLog::add(sprintf('%s sending request to %s', get_class($this), $uri->getAbsoluteUri()), JLog::INFO, 'rokupdater');
		return $this->transportRequest($uri, $data, $headers, $timeout, $userAgent);
	}

	/**
	 * Check to see if ssl is supported by the transport
	 * @return bool
	 */
	abstract protected function isSSLSupported();

	/**
	 * Send a request to the server and return a string with the response body.
	 *
	 * @param   RokUpdater_Uri $uri        The URI to the resource to request.
	 * @param   string         $data       Either an associative array or a string to be sent with the request.
	 * @param   array          $headers    An array of request headers to send with the request.
	 * @param   integer        $timeout    Read timeout in seconds.
	 * @param   string         $userAgent  The optional user agent string to send with the request.
	 *
	 * @throws ProtocolBuffers_Exception
	 * @throws Exception
	 * @return string
	 */
	abstract protected function transportRequest(RokUpdater_Uri $uri, $data = null, array $headers = array(), $timeout = null, $userAgent = null);

}
