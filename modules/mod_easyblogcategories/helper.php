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

require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');

class modEasyBlogCategoriesHelper
{
	public static function getCategories(&$params)
	{
		//blog privacy setting
		$db = EasyBlogHelper::db();
		$my = JFactory::getUser();
		$top_level		= 1;

		$onlyTheseCatIds	= $params->get('catid', '');

		$mainframe		= JFactory::getApplication();
		$order			= $params->get('order', 'popular');
		$sort			= $params->get('sort', 'desc');
		$count			= (INT)trim($params->get('count', 0));
		$hideEmptyPost 	= $params->get('hideemptypost', '0');

		$query	= 'SELECT a.`id`, a.`title`, a.`parent_id`, a.`alias`, a.`avatar`, COUNT(b.`id`) AS `cnt`'
				. ' , ' . $db->quote($top_level) . ' AS level'
				. ' FROM ' . $db->nameQuote( '#__easyblog_category' ) . ' AS `a`'
				. ' LEFT JOIN '. $db->nameQuote( '#__easyblog_post' ) . ' AS b'
				. ' ON a.`id` = b.`category_id`'
				. ' AND b.`published` = ' . $db->Quote('1');

		$menu 			= JFactory::getApplication()->getMenu()->getActive();

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$menuParams			= new JRegistry();
			$menuParams->loadString( $menu->params );
		}
		else
		{
			$menuParams 		= new JParameter( $menu->params );
		}

		$isBloggerMode  = $menuParams->get('standalone_blog');

		if( $isBloggerMode )
		{
			$uid 	= $menu->query['id'];

			$query 	.= ' AND a.`created_by`=' . $db->Quote( $uid );
		}


		if($my->id == 0)
		{
		    $query .= ' AND b.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			// @rule: When language filter is enabled, we need to detect the appropriate contents
			$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();

			if( $filterLanguage )
			{
				$query	.= ' AND (';
				$query	.= ' b.`language`=' . $db->Quote( JFactory::getLanguage()->getTag() );
				$query	.= ' OR b.`language`=' . $db->Quote( '' );
				$query	.= ' OR b.`language`=' . $db->Quote( '*' );
				$query	.= ' )';
			}
		}

		$query	.= ' WHERE a.`published` = 1';

		// get all private categories id
		$excludeCats	= EasyBlogHelper::getPrivateCategories();
		$queryExclude   = '';
		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`id` NOT IN (';

			for( $i = 0; $i < count( $excludeCats ); $i++ )
			{
				$queryExclude	.= $db->Quote( $excludeCats[ $i ] );

				if( next( $excludeCats ) !== false )
				{
					$queryExclude .= ',';
				}
			}
			$queryExclude	.= ')';
		}

		$query      .= ' AND a.`parent_id`=' . $db->Quote( 0 );

		if( !empty( $onlyTheseCatIds ) )
		{
		    $filterStr  = '';
		    $filterCats = explode( ',', $onlyTheseCatIds);
		    foreach( $filterCats as $cat)
		    {
                $filterStr  .= ( empty( $filterStr ) ) ? $db->Quote( trim($cat) ) : ',' . $db->Quote( trim($cat) );
		    }

		    $query  .= ' AND a.`id` IN ( ' . $filterStr . ' )';
		}

		$query	.= $queryExclude;

		if(!$hideEmptyPost)
		{
			$query	.= ' GROUP BY a.`id`';
		}
		else
		{
			$query	.= ' GROUP BY a.`id` HAVING (COUNT(b.`id`) > 0)';
		}

		switch($order)
		{
			case 'ordering' :
				$orderBy	= ' ORDER BY `lft` ';
				break;
			case 'popular' :
				$orderBy	= ' ORDER BY `cnt` ';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` ';
				break;
		}
		$query	.= $orderBy.$sort;

		if(!empty($count))
		{
			$query	.= ' LIMIT ' . $count;
		}

		// echo $query;exit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$categories = array();
		modEasyBlogCategoriesHelper::getChildCategories( $result , $params , $categories , ++$top_level );

		// Since running the iteration will invert the ordering, we'll need to reverse it back.
		// $categories		= array_reverse( $categories );

		return $categories;
	}

	public static function getChildCategories( &$result , $params , &$categories, $level = 1 )
	{
	    $db     = EasyBlogHelper::db();
	    $my     = JFactory::getUser();
		$mainframe		= JFactory::getApplication();
		$order			= $params->get('order', 'popular');
		$sort			= $params->get('sort', 'desc');
		$count			= (INT)trim($params->get('count', 0));
		$hideEmptyPost 	= $params->get('hideemptypost', '0');

	    foreach($result as $row )
	    {

	        if( $row->parent_id == 0 )
	        {
	            $categories[ $row->id ] = $row;
	            $categories[ $row->id ]->childs = array();
			}
			else
			{
			    $categories[ $row->id ]  = $row;
			    $categories[ $row->id ]->childs  = array();
			}


			$query	= 'SELECT a.`id`, a.`title`, a.`parent_id`, a.`alias`, a.`avatar`, COUNT(b.`id`) AS `cnt`'
					. ' , ' . $db->quote($level) . ' AS level'
					. ' FROM ' . $db->nameQuote( '#__easyblog_category' ) . ' AS `a`'
					. ' LEFT JOIN '. $db->nameQuote( '#__easyblog_post' ) . ' AS b'
					. ' ON a.`id` = b.`category_id`'
					. ' AND b.`published` = ' . $db->Quote('1');

			$query	.= ' WHERE a.`published` = 1';
			$query  .= ' AND a.`parent_id`=' . $db->Quote( $row->id );

			if(!$hideEmptyPost)
			{
				$query	.= ' GROUP BY a.`id`';
			}
			else
			{
				$query	.= ' GROUP BY a.`id` HAVING (COUNT(b.`id`) > 0)';
			}

			switch($order)
			{
				case 'ordering' :
					$orderBy	= ' ORDER BY `lft` ';
					break;
				case 'popular' :
					$orderBy	= ' ORDER BY `cnt` ';
					break;
				case 'alphabet' :
					$orderBy = ' ORDER BY a.`title` ';
					break;
				case 'latest' :
				default	:
					$orderBy = ' ORDER BY a.`created` ';
					break;
			}
			$query	.= $orderBy.$sort;

			$db->setQuery( $query );

			$records	= $db->loadObjectList();

			if( $records )
			{
			    modEasyBlogCategoriesHelper::getChildCategories( $records , $params , $categories[ $row->id ]->childs, ++$level );

				foreach( $records as $childrec)
				{
					$categories[ $row->id ]->cnt    += $childrec->cnt;
				}
			}
		}
	}

	public static function getAvatar($category)
	{
		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );

		$categorytable = EasyBlogHelper::getTable('Category', 'Table');
		$categorytable->bind($category);

		return $categorytable->getAvatar();
	}

	public static function _getMenuItemId(&$params)
	{
		$itemId                 = '';
		$routeTypeCategory		= false;
		$routeTypeBlogger		= false;
		$routeTypeTag			= false;

		$routingType            = $params->get( 'routingtype', 'default' );

		if( $routingType != 'default' )
		{
			switch ($routingType)
			{
				case 'menuitem':
					$itemId					= $params->get( 'menuitemid' ) ? '&Itemid=' . $params->get( 'menuitemid' ) : '';
					break;
				default:
					break;
			}
		}

		return $itemId;
	}

	function accessNestedCategories( &$categories , $selected , $params , $level = null )
	{
		$menuItemId = modEasyBlogCategoriesHelper::_getMenuItemId($params);

		foreach($categories as $category)
		{
			if( is_null( $level ) )
			{
				$level 	= 0;
			}
			
			$css = '';

			if($category->id == $selected)
			{
				$css = 'font-weight: bold;';
			}
			
			if( $params->get( 'layouttype' ) == 'tree' )
			{
				// $category->level	-= 1;
				$padding	= $level * 30;
			}

			require(JModuleHelper::getLayoutPath('mod_easyblogcategories', 'item'));

			if( $params->get( 'layouttype' ) == 'tree' || $params->get( 'layouttype' ) == 'flat' )
			{
				if( isset( $category->childs ) && is_array( $category->childs ) )
				{
				    modEasyBlogCategoriesHelper::accessNestedCategories( $category->childs , $selected, $params ,  $level + 1 );
				}
			}
		}
	}
}
