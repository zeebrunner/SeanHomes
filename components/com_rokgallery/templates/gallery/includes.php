<?php
 /**
  * @version   $Id: includes.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
// load up style sheets
$browser = new RokCommon_Browser();
RokCommon_Header::addStyle(RokCommon_Composite::get($that->context)->getUrl('gallery.css'));
if ($browser->getEngine() == 'gecko') RokCommon_Header::addStyle(RokCommon_Composite::get($that->context)->getUrl('gallery-gecko.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get($that->style_context)->getUrl('style.css'));
RokCommon_Header::addInlineScript(RokCommon_Composite::get($that->style_context)->load('js-settings.php', array('that'=>$that)));

