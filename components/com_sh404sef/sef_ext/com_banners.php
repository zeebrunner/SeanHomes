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

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$sefConfig = & Sh404sefFactory::getConfig(); 

$shName = shGetComponentPrefix($option);
$title[] = empty($shName) ? 'banners':$shName;

$title[] = '/';

$title[] = (empty($task) ? '' : $task) . (empty($id) ? '' : (int) $id);


if (count($title) > 0) $string = sef_404::sefGetLocation($string, $title,null);

