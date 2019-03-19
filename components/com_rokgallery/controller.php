<?php
 /**
  * @version   $Id: controller.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
include_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/legacy_class.php');



/**
 * rokgallery Component Controller
 */
class RokGalleryController extends RokGalleryLegacyJController {

    function __construct($config = array())
	{
		// RokGallery image picker proxying:
		if (JFactory::getApplication()->input->getCmd('view') === 'gallerypicker') {
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
            RokCommon_Composite::addPackagePath('rokgallery',JPATH_COMPONENT_ADMINISTRATOR.'/templates');
		}

		parent::__construct($config);
	}
    
    function display($cachable = false, $urlparams = false) {
        // Make sure we have a default view
        if( !JFactory::getApplication()->input->getCmd( 'view' )) {
		    JFactory::getApplication()->input->set('view', 'gallery' );
        }
		parent::display();
	}

    public function ajax()
    {
        try
        {
            RokCommon_Ajax::addModelPath(JPATH_SITE . '/components/com_rokgallery/lib/RokGallery/Site/Ajax/Model', 'RokGallerySiteAjaxModel');
            $model = JFactory::getApplication()->input->getString('model');
            $action = JFactory::getApplication()->input->getString('action');
            $params = JFactory::getApplication()->input->getString('params');

            echo RokCommon_Ajax::run($model, $action, $params);
        }
        catch (Exception $e)
        {
            $result = new RokCommon_Ajax_Result();
            $result->setAsError();
            $result->setMessage($e->getMessage());
            echo json_encode($result);
        }
    }
}
