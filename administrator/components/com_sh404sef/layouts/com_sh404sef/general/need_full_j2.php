<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2015
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.2.3180
 * @date		2015-12-23
 */

defined('_JEXEC') or die;

/**
 * This layout displays message or error, insde a bootstrap alert box
 */

if (!empty($displayData['message']))
{
	echo $displayData['message'];
}
