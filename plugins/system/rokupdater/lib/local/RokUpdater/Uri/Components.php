<?php
/**
 * @version   $Id: Components.php 8934 2013-03-29 19:17:23Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * Specifies the parts of RokUpdater_Uri.
 */
final class RokUpdater_Uri_Components
{
	/**
	 * The RokUpdater_Uri::getScheme() data.
	 */
	const SCHEME = 1;

	/**
	 *
	 */
	const AUTHORITY_START = 2;

	/**
	 * The RokUpdater_Uri::getUserInfo() data.
	 */
	const USERINFO = 4;

	/**
	 * The RokUpdater_Uri::getHost() data.
	 */
	const HOST = 8;

	/**
	 * The RokUpdater_Uri::getPort() data. If there is no port or there is the default one,
	 * it is omitted.
	 */
	const PORT = 16;

	/**
	 * The RokUpdater_Uri::getPort() data.
	 */
	const STRONG_PORT = 32;


	/**
	 * The RokUpdater_Uri::getPath() data.
	 */
	const PATH = 64;

	/**
	 * The RokUpdater_Uri::getQuery() data.
	 */
	const QUERY = 128;

	/**
	 * The RokUpdater_Uri::getFragment() data.
	 */
	const FRAGMENT = 256;


	/**
	 * The RokUpdater_Uri::getHost() and RokUpdater_Uri::getPort() data.
	 */
	const HOST_AND_PORT = 24;

	/**
	 * The RokUpdater_Uri::getScheme(), RokUpdater_Uri::getHost() and RokUpdater_Uri::getPort() data.
	 */
	const SCHEME_AND_SERVER = 27;

	/**
	 * The RokUpdater_Uri::getUserInfo(), RokUpdater_Uri::getHost() and RokUpdater_Uri::getPort() data.
	 * If there is not port or there is the default one, it is omitted.
	 */
	const AUTHORITY = 28;

	/**
	 * The RokUpdater_Uri::getBaseUri() data.
	 */
	const BASEURI = 31;


	/**
	 * The RokUpdater_Uri::getUserInfo(), RokUpdater_Uri::getHost() and RokUpdater_Uri::getPort() data.
	 */
	const STRONG_AUTHORITY = 47;

	/**
	 * The RokUpdater_Uri::getPath() and RokUpdater_Uri::getQuery() data.
	 */
	const PATH_AND_QUERY = 192;

	/**
	 * The RokUpdater_Uri::getRelativeUri() data.
	 */
	const RELATIVE_URI = 448;

	/**
	 *
	 */
	const SCHEME_RELATIVE_URI = 478;

	/**
	 * The RokUpdater_Uri::getAbsoluteUri() data.
	 */
	const ABSOLUTE_URI = 479;
}