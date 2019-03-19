<?php
/**
 * @version   $Id: AbstractPHPStreamTransport.php 11337 2013-06-10 21:21:28Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

abstract class ProtocolBuffers_Transport_AbstractPHPStreamTransport extends ProtocolBuffers_Transport_AbstractTransport
{
	/**
	 *
	 */
	const SSL_TRANSPORT = 'ssl';

	protected function isSSLSupported()
	{
		return in_array(self::SSL_TRANSPORT, stream_get_transports());
	}
}
