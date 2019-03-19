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


/**
 * rsappt_pro2  Controller
 */
 
class ajaxController extends JControllerForm
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
		
		$this->registerTask( 'ajax_user_search', 'ajax_user_search' );
		$this->registerTask( 'ajax_set_rate_override_enable', 'ajax_set_rate_override_enable' );
		$this->registerTask( 'ajax_set_gift_cert_enable', 'ajax_set_gift_cert_enable' );


	}


	function ajax_user_search()
	{
		include_once(JPATH_SITE.'/administrator/components/com_rsappt_pro3/ajax/user_search.php');
	}


	function ajax_set_rate_override_enable()
	{
		$jinput = JFactory::getApplication()->input;
		$new_value = $jinput->getWord( 'nv', 'Np' );
		
		$database =JFactory::getDBO(); 
		$sql = 'UPDATE #__sv_apptpro3_config SET enable_overrides = "'.$new_value.'"';
		try{
			$database->setQuery($sql);
			$database->execute();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_ctrl_ajax", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			jExit();
		}		
		
		echo json_encode(JText::_('RS1_ADMIN_RATE_OVERRIDES_ENABLE_CHANGE'));
		jExit();
	}


	function ajax_set_gift_cert_enable()
	{
		$jinput = JFactory::getApplication()->input;
		$new_value = $jinput->getWord( 'nv', 'Np' );
		
		$database =JFactory::getDBO(); 
		$sql = 'UPDATE #__sv_apptpro3_config SET enable_gift_cert = "'.$new_value.'"';
		try{
			$database->setQuery($sql);
			$database->execute();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_ctrl_ajax", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			jExit();
		}		
		
		echo json_encode(JText::_('RS1_ADMIN_GIFT_CERT_ENABLE_CHANGE'));
		jExit();
	}

}

?>

