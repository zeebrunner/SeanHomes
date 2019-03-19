<?php
 /**
  * @version   $Id: includes.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
$browser = new RokCommon_Browser();
if ($browser->getShortName() == 'ie7'){

    RokCommon_Header::addStyle(RokCommon_Composite::get($that->context)->getUrl('rokgallery-ie7.css'));
}
RokCommon_Header::addScript(RokCommon_Composite::get($that->context)->getUrl('loves'.RokGallery_Helper::getJSVersion().'.js'));
 
