<?php
/*
 ****************************************************************
 Copyright (C) 2008-2015 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2015 Soft Ventures, Inc. All rights reserved.
 * @license	GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 * ABPro is distributed WITHOUT ANY WARRANTY, or implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 *
 ************************************************************
 The latest version of ABPro is available to subscribers at:
 http://www.appointmentbookingpro.com/
 ************************************************************
*/


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import CONTROLLER object class
jimport( 'joomla.application.component.controller' ); 


class backup_restoreController extends JControllerForm
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
		
		require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'rsappt_pro3.php';
		rsappt_pro3Helper::addSubmenu('backup_restore');
		
		// Register Extra tasks
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'backup', 'backup' );
		$this->registerTask( 'restore', 'restore' );
		
	}

	/** function edit
	*
	* Create a new item or edit existing item 
	* 
	* 1) set a custom VIEW layout to 'form'  
	* so expecting path is : [componentpath]/views/[$controller->_name]/'form.php';			
    * 2) show the view
    * 3) get(create) MODEL and checkout item
	*/
	function edit($key=null, $urlVar=null)
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set( 'view', 'backup_restore' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 0);


		parent::display();

	}
      
	function backup()
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set( 'view', 'backup_restore_results' );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'action', 'backup');

		parent::display();
	}


	function restore()
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set( 'view', 'backup_restore_results' );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'action', 'restore');

		parent::display();
	}

	
	function cancel($key=null)
	{
		$this->setRedirect( 'index.php?option=com_rsappt_pro3&controller=cpanel' );
	}	

}

