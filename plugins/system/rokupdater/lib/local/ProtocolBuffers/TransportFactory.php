<?php
/**
 * @version   $Id: TransportFactory.php 11337 2013-06-10 21:21:28Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * Class ProtocolBuffers_TransportFactory
 */
abstract class ProtocolBuffers_TransportFactory
{

	/**
	 *
	 */
	const PROTOCOL_BUFFERS_ERROR_NO_VALID_TRANSPORT = 1;
	/**
	 *
	 */
	const PROTOCOL_BUFFERS_ERROR_SSL_STREAM_NOT_REGISTERED = 2;

	/**
	 * @param array $options
	 *
	 * @throws ProtocolBuffers_Exception
	 * @return ProtocolBuffers_TransportInterface
	 */
	public static function factory(array $options = array())
	{
		$transtport = null;

		if (ProtocolBuffers_Transport_Curl::isSupported())
		{
			JLog::add('Using cURL as Protocol Buffers Transport', JLog::INFO, 'rokupdater');
			$transtport = new ProtocolBuffers_Transport_Curl($options);
		}
		else if (ProtocolBuffers_Transport_StreamSocket::isSupported())
		{
			JLog::add('Using SocketStream as Protocol Buffers Transport', JLog::INFO, 'rokupdater');
			$transtport = new ProtocolBuffers_Transport_StreamSocket($options);
		}
		else if (ProtocolBuffers_Transport_FOpenStream::isSupported())
		{
			JLog::add('Using FOpenStream as Protocol Buffers Transport', JLog::INFO, 'rokupdater');
			$transtport = new ProtocolBuffers_Transport_FOpenStream($options);
		}

		if ($transtport == null)
		{
			JLog::add('Unable to find valid transport for Protocol Buffers direct communication', JLog::WARNING, 'rokupdater');
			throw new ProtocolBuffers_Exception("No valid transport found", self::PROTOCOL_BUFFERS_ERROR_NO_VALID_TRANSPORT);
		}
		return $transtport;
	}
}
