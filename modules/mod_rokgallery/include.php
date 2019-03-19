<?php
 /**
 * @version   $Id: include.php 10868 2013-05-30 04:05:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


if (!defined('JOOMLA_ROKGALLERYMODULE_LIB')) {
    define('JOOMLA_ROKGALLERYMODULE_LIB', 'JOOMLA_ROKGALLERYMODULE_LIB');
    $include_file = @realpath(realpath(ROKGALLERYMODULE_ROOT . '/lib/include.php'));
    $included_files = get_included_files();
    if (!in_array($include_file, $included_files) && ($loaderrors = require_once($include_file)) !== 'ROKGALLERYMODULE_LIB_INCLUDED') {
        return $loaderrors;
    }
    RokGallery_Doctrine::addModelPath(JPATH_SITE . '/components/com_rokgallery/lib');
    RokCommon_Composite::addPackagePath('mod_rokgallery', JPATH_SITE . '/modules/mod_rokgallery/templates');
    RokCommon_Composite::addPackagePath('mod_rokgallery_global_assets', JPATH_SITE . '/components/com_rokgallery/assets');

    $container = RokCommon_Service::getContainer();
    /** @var $header RokCommon_IHeader */
    $header = $container->header;

    $header->addScriptPath(JPATH_SITE . '/components/com_rokgallery/assets/js');
    $header->addScriptPath(JPATH_SITE . '/modules/mod_rokgallery/templates/showcase');
    $header->addScriptPath(JPATH_SITE . '/modules/mod_rokgallery/templates/showcase_responsive');
    $header->addScriptPath(JPATH_SITE . '/modules/mod_rokgallery/templates/slideshow');
}
return 'JOOMLA_ROKGALLERYMODULE_LIB_LOADED';
