<?php
/**
 * @version   $Id: TransportInterface.php 10995 2013-05-30 22:43:58Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

interface ProtocolBuffers_TransportInterface
{
	/**
	 * Constructor.
	 *
	 * @param   array $options  Client options object.
	 *
	 * @since   11.3
	 */
	public function __construct(array $options = array());

	/**
	 * method to check if http transport layer available for using
	 *
	 * @return bool true if available else false
	 *
	 * @since   12.1
	 */
	static public function isSupported();

	/**
	 * Send a request to the server and return a JHttpResponse object with the response.
	 *
	 * @param   RokUpdater_Uri  $uri        The URI to the resource to request.
	 * @param   string  $data       Either an associative array or a string to be sent with the request.
	 * @param   array   $headers    An array of request headers to send with the request.
	 * @param   integer $timeout    Read timeout in seconds.
	 * @param   string  $userAgent  The optional user agent string to send with the request.
	 *
	 * @return  string
	 */
	public function request(RokUpdater_Uri $uri, $data = null, array $headers = array(), $timeout = null, $userAgent = null);
}
