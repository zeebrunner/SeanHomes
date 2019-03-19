<?php
/**
 * @version   $Id: Subscription.php 10050 2013-05-03 20:00:03Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokUpdater_Subscriber_Subscription
{

	/** @var  string */
	public $club;

	/** @var  bool */
	public $active;

	/**
	 * @param $club
	 * @param $active
	 */
	public function __construct($club, $active)
	{
		$this->active = $active;
		$this->club   = $club;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}

	/**
	 * @return boolean
	 */
	public function getActive()
	{
		return $this->active;
	}

	/**
	 * @param string $club
	 */
	public function setClub($club)
	{
		$this->club = $club;
	}

	/**
	 * @return string
	 */
	public function getClub()
	{
		return $this->club;
	}


}
