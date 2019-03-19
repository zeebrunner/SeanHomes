<?php
/**
 * @version   $Id: legacy_class.php 10868 2013-05-30 04:05:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('_JEXEC') or die('Restricted access');

if (method_exists('JSession','checkToken')) {
	function rokgallery_checktoken($method = 'post')
	{
		if ($method == 'default')
		{
			$method = 'request';
		}
		return JSession::checkToken($method);
	}
} else {
	function rokgallery_checktoken($method = 'post')
	{
		return JRequest::checkToken($method);
	}
}

if (!class_exists('RokGalleryLegacyJView', false)) {
  $jversion = new JVersion();
  if (version_compare($jversion->getShortVersion(), '2.5.5', '>')) {
    class RokGalleryLegacyJView extends JViewLegacy
    {
    }

    class RokGalleryLegacyJController extends JControllerLegacy
    {
    }

    class RokGalleryLegacyJModel extends JModelLegacy
    {
    }
  } else {
    jimport('joomla.application.component.view');
    jimport('joomla.application.component.controller');
    jimport('joomla.application.component.model');
    class RokGalleryLegacyJView extends JView
    {
    }

    class RokGalleryLegacyJController extends JController
    {
    }

    class RokGalleryLegacyJModel extends JModel
    {
    }
  }
}
