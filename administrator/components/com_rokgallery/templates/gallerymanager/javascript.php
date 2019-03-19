<?php
 /**
  * @version   $Id: javascript.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
 
$url = JURI::root(true) . '/administrator/index.php?option=com_rokgallery&task=ajax&format=raw';
echo "var RokGallery = {url: '".$url."'};";