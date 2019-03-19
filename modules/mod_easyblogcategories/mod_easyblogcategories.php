<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

if( !JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'easyblog.php'))
{
	return;
}

// Include the syndicate functions only once
require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.php');
require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php');
require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'router.php');
require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'image.php');
require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'tags.php');

$document	= JFactory::getDocument();
$document->addStyleSheet( rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/css/module.css' );

$categories			= modEasyBlogCategoriesHelper::getCategories($params);

$view 		= JRequest::getVar('view', '', 'REQUEST');
$layout 	= JRequest::getVar('layout', '', 'REQUEST');
$selected	= '';

if( $view=='categories' && $layout=='listings')
{
	$selected = JRequest::getVar('id', '', 'REQUEST');
}

require(JModuleHelper::getLayoutPath('mod_easyblogcategories'));
