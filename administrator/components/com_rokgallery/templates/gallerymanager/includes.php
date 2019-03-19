<?php
 /**
  * @version   $Id: includes.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

RokCommon_Header::addStyle(RokCommon_Composite::get($that->context)->getUrl('gallerymanager.css') . '?version=2.0');
RokCommon_Header::addStyle(RokCommon_Composite::get($that->context)->getUrl('../../assets/styles/galleries.css') . '?version=2.0');
RokCommon_Header::addScript(RokCommon_Composite::get($that->context)->getUrl('../../assets/application/Scrollbar.js') . '?version=2.0');
RokCommon_Header::addScript(RokCommon_Composite::get($that->context)->getUrl('gallerymanager.js') . '?version=2.0');
RokCommon_Header::addInlineScript(RokCommon_Composite::get($that->context)->load('javascript.php', array('that'=>$that)));
