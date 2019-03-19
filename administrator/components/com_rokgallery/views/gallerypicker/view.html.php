<?php
 /**
 * @version   $Id: view.html.php 10868 2013-05-30 04:05:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
include_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/legacy_class.php');

/**
 * HTML View class for the rokgallery component
 */
class RokgalleryViewGalleryPicker extends RokGalleryLegacyJView
{

    protected $rtmodel;

    public function __construct($config = array())
    {
        $option = JFactory::getApplication()->input->getCmd('option');
        parent::__construct($config);

        $document = JFactory::getDocument();
        $session = JFactory::getSession();
    }

    function display($tpl = null)
    {
        $show_menuitems = JFactory::getApplication()->input->getBool('show_menuitems', 1);
        $path = (JFactory::getApplication()->isSite()) ? JPATH_COMPONENT_ADMINISTRATOR : JPATH_COMPONENT;

        require_once ($path.'/helpers/rokgallery.php');

        RokGalleryHelper::setupGalleryPicker($this);

        $db		= JFactory::getDBO();
        $query	= $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            'm.id, m.title AS link_name, m.alias, m.published, m.access, m.language, l.title AS lang_title, ag.title AS access_group, mt.title AS menu_name'.
            ', CONCAT(m.link, "&Itemid=", m.id) AS menu_link'
        );
        $query->from('#__menu AS m');
        $query->join('LEFT', '#__languages AS l ON l.lang_code = m.language');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = m.access');
        $query->join('LEFT', '#__menu_types AS mt ON mt.menutype = m.menutype');
        $query->join('LEFT', '#__extensions AS ex ON ex.extension_id = m.component_id');
        $query->where('ex.element = "com_rokgallery"');
        $query->where('m.client_id = "0"');
        $query->where('m.menutype != ""');
        $query->where('m.published != "-2"');
        $query->order('m.menutype ASC, m.title ASC');

        $db->setQuery($query);

		$menuitems = $db->loadObjectList();

        if ($this->galleries === false) $this->galleries = array();
        if ($this->files === false) $this->files = array();

        $this->assign('images_path', $this->images_url);

        $this->assign('total_items_in_filter', $this->pager->getNumResults());
        $this->assign('items_to_be_rendered', $this->pager->getResultsInPage());
        $this->assign('next_page', $this->next_page);
        $this->assign('items_per_page', $this->items_per_page);
        $this->assign('items_per_row', $this->items_per_row);
        $this->assign('currently_shown_items', $this->pager->getLastIndice());
        $this->assign('totalFilesCount', $this->pager->getNumResults());

        $this->assign('show_menuitems', $show_menuitems);

        $this->assignRef('files', $this->files);
        $this->assignRef('galleries', $this->galleries);
        $this->assignRef('menuitems', $menuitems);

        $this->assign('gallery_id', $this->gallery_id);
        $this->assign('file_id', $this->file_id);
        $this->assign('textarea', $this->textarea);
        $this->assign('inputfield', $this->inputfield);
        $this->assign('context', 'rokgallery.gallerypicker');
        
        $this->setLayout('default');
        parent::display($tpl);
    }
}
