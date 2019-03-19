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
class RokgalleryViewGalleryManager extends RokGalleryLegacyJView
{

    protected $rtmodel;

    public function __construct($config = array())
    {
        $option = JFactory::getApplication()->input->getCmd('option');
        parent::__construct($config);

        $document = JFactory::getDocument();
        $session = JFactory::getSession();

        //$this->rtmodel = new RokGallery_Site_DetailModel();
    }

    function display($tpl = null)
    {
        JHtml::_('behavior.framework', true);
        JHtml::_('behavior.keepalive');

        $id = (int) JFactory::getApplication()->input->getInt('id');
        $force_fixed_size = JFactory::getApplication()->input->getBool('fixed',0);
        $name = JFactory::getApplication()->input->getString('name');

        $galleries = RokGallery_Model_GalleryTable::getAll();
        $current_gallery = false;
        if (null != $id){
            $current_gallery = RokGallery_Model_GalleryTable::getSingle($id);
        }

        if (null != $name) {
            $default_name = $name . rc__('ROKGALLERY_GALLERY_CREATE_DEFAULT_EXTENSION');
        } else {
            $default_name = rc__('ROKGALLERY') . rc__('ROKGALLERY_GALLERY_CREATE_DEFAULT_EXTENSION');
        }
		
        $this->assign('default_name', $default_name);
        $this->assign('current_gallery_id', $id);
        $this->assign('force_fixed_size', $force_fixed_size);
        $this->assignRef('galleries', $galleries);
        $this->assignRef('current_gallery', $current_gallery);
        $this->assign('context', 'rokgallery.gallerymanager');

        $this->setLayout('default');
        parent::display($tpl);
    }

    protected function _replaceMooTools(){
        $option = JFactory::getApplication()->input->getCmd('option');
        $document = JFactory::getDocument();


		// mootools
        $mootools_r = array();
        $mootools_r[] = JURI::root(true) .'/media/system/js/core.js';
        $mootools_r[] = JURI::root(true) .'/media/system/js/core-uncompressed.js';
        $mootools_r[] = JURI::root(true) .'/media/system/js/mootools-core.js';
        $mootools_r[] = JURI::root(true) .'/media/system/js/mootools-core-uncompressed.js';
        $mootools_r[] = JURI::root(true) .'/media/system/js/mootools-more.js';
        $mootools_r[] = JURI::root(true) .'/media/system/js/mootools-more-uncompressed.js';

        $mootools13 ='components/'.$option.'/assets/js/mootools.js';

		// modal
        $modal_r = array();
        $modal_r[] = JURI::root(true) .'/media/system/js/modal.js';
        $modal_r[] = JURI::root(true) .'/media/system/js/modal-uncompressed.js';

        $modal13 ='components/'.$option.'/assets/js/modal-1.3.js';

        $scripts = array();
        foreach ($document->_scripts as $key => $value) {
			if (in_array($key, $mootools_r)) $scripts[$mootools13] = $value;
			else if (in_array($key, $modal_r)) $scripts[$modal13] = $value;
			else { $scripts[$key] = $value; }
        }

        $document->_scripts = $scripts;
    }
}
