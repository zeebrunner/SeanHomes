<?php
 /**
 * @version   $Id: include.php 10868 2013-05-30 04:05:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!defined('JOOMLA_ROKGALLERY_LIB')) {
    define('JOOMLA_ROKGALLERY_LIB', 'JOOMLA_ROKGALLERY_LIB');

    if (!defined('ROKCOMMON_LIB_PATH')) define('ROKCOMMON_LIB_PATH', JPATH_SITE . '/libraries/rokcommon');

    $include_file = @realpath(realpath(JPATH_SITE . '/components/com_rokgallery/lib/include.php'));
    $included_files = get_included_files();
    if (!in_array($include_file, $included_files) && ($loaderrors = require_once($include_file)) !== 'ROKGALLERY_LIB_INCLUDED') {
        if (!defined('ROKGALLERY_ERROR_MISSING_LIBS')) define('ROKGALLERY_ERROR_MISSING_LIBS', true);
        return $loaderrors;
    }
    if (($loaderrors = require_once(dirname(__FILE__) . '/requirements.php')) !== true) {
        if (!defined('ROKGALLERY_ERROR_MISSING_LIBS')) define('ROKGALLERY_ERROR_MISSING_LIBS', true);
        return $loaderrors;
    }
    RokGallery_Doctrine::addModelPath(JPATH_SITE . '/components/com_rokgallery/lib');
    RokCommon_Composite::addPackagePath('mod_rokgallery', JPATH_SITE . '/modules/mod_rokgallery/templates');

    $container = RokCommon_Service::getContainer();
    /** @var $header RokCommon_IHeader */
    $header = $container->header;

    $header->addScriptPath(JPATH_SITE . '/components/com_rokgallery/assets/js');
    $header->addScriptPath(JPATH_SITE . '/components/com_rokgallery/assets/application');
    $header->addStylePath(JPATH_SITE . '/components/com_rokgallery/assets/styles');
}
return 'JOOMLA_ROKGALLERY_LIB_INCLUDED';