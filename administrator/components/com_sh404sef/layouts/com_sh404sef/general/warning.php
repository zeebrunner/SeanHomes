<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This layout displays a warning, insde a bootstrap alert box
 */

if (!empty($displayData['warning']))
{
	echo ShlHtmlBs_Helper::alert($displayData['warning'], $type = 'warning', $dismiss = true);
}
