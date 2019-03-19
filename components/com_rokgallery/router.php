<?php
/**
 * Joomla! 1.5 component rokgallery
 *
 * @version $Id: router.php 10868 2013-05-30 04:05:27Z btowles $
 * @author RocketTheme
 * @package Joomla
 * @subpackage rokgallery
 * @license GNU/GPL
 *
 *
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Function to convert a system URL to a SEF URL
 */
function RokgalleryBuildRoute(&$query)
{

    $session_namespace = 'com_rokgallery.site';
    jimport('joomla.filter.output');

    $session = JFactory::getSession();

    $segments = array();

    // Get the menu items for this component.
    $jsite = new JSite();
    $menu = $jsite->getMenu();
    if (empty($query['Itemid'])) {
        $menuItem = $menu->getActive();
    } else {
        $menuItem = $menu->getItem($query['Itemid']);
    }
    if (empty($menuItem->id)) {
        $params = new JRegistry();
    } else {
        $params = $menu->getParams($menuItem->id);
    }

    if (!isset($query['view'])) {
        $query['view'] = 'gallery';
    }

    switch ($query['view']) {
        case 'detail':
	        RokGallery_Doctrine::getConnection();
            $slice = RokGallery_Model_SliceTable::getSingle((int)$query['id']);
            array_unshift($segments, (int)$query['id'].'-'.JFilterOutput::stringURLSafe($slice->slug));
            unset($query['id']);

        case 'gallery':
//            if (isset($query['page'])) {
//                array_unshift($segments, $query['page']);
//                unset($query['page']);
//            }
        default:
            unset($query['view']);
    }

    if (isset($query['layout'])) {
        if ($query['layout'] == $session->get('layout', $params->get('default_layout'), $session_namespace)) {
            unset($query['layout']);
        }
    }

    if (isset($query['style'])) {
        if ($query['style'] == $session->get('style', $params->get('default_style'), $session_namespace)) {
            unset($query['style']);
        }
    }

    if (isset($query['sort_by'])) {
        if ($query['sort_by'] == $session->get('sort_by', $params->get('default_sort_by'), $session_namespace)) {
            unset($query['sort_by']);
        }
    }

    if (isset($query['sort_direction'])) {
        if ($query['sort_direction'] == $session->get('sort_direction', $params->get('default_sort_direction'), $session_namespace)) {
            unset($query['sort_direction']);
        }
    }

    return $segments;
}

/*
* Function to convert a SEF URL back to a system URL
*/
function RokgalleryParseRoute($segments)
{

    $app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
    if (empty($item))
    {
        $item = $menu->getDefault();
        $menu->setActive($item->id);
    }
    $vars = array();
    if (count($segments)) {
        list($vars['id'],$vars['slug']) = explode(':',$segments[0]);
        $vars['view'] = 'detail';
    }
    return $vars;
}