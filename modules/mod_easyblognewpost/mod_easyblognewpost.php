<?php
/*
 * @package		mod_easyblognewpost
 * @copyright	Copyright (C) 2010 StackIdeas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$helper		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';

jimport( 'joomla.filesystem.file' );

if( !JFile::exists( $helper ) )
{
	return;
}
require_once( $helper );
require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'acl.php' );
$useracl	= EasyBlogACLHelper::getRuleSet();

EasyBlogHelper::loadModuleCss();

// @task: If user doesn't have any privileges to post a new blog post, let's just skip this.
if( !$useracl->rules->add_entry )
{
	return;
}

$itemId		= '';
$routeType	= $params->get( 'routingtype' , 'default' );

if( $routeType != 'default' && $routeType == 'menuitem' )
{
	$itemId	= $params->get( 'menuitemid' ) ? '&Itemid=' . $params->get( 'menuitemid' ) : '';
}

// @task: Load languages
JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

// @task: Add css file to headers
$doc 	= JFactory::getDocument();

require( JModuleHelper::getLayoutPath( 'mod_easyblognewpost' ) );
