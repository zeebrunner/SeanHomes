<?php
/**
 * @version   $Id: AbstractService.php 9276 2013-04-11 17:47:57Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

abstract class RokUpdater_AbstractService
{
	/**
	 * @var RokUpdater_ServiceProvider
	 */
	protected $container;

	function __construct(RokUpdater_ServiceProvider $container)
	{
		$this->container = $container;
	}
}
