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

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );

/**
 [controller]View[controller]
 */
 
class front_deskViewbooking_screen_fd extends JViewLegacy
{
	/**
	 * Custom Constructor
	 */
	function __construct( $config = array())
	{
	 /** set up global variable for sorting etc.
	  * $context is used in VIEW abd in MODEL
	  **/	  
	 
 	 global $context;
	 $context = 'booking_screen_fd.';
 
 	 parent::__construct( $config );
	}
 

   
	function display($tpl = null)
	{
		global $context;
	  	$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		
		$uri = JFactory::getURI()->toString();
		$user = JFactory::getUser();
		
		$this->assignRef('user', $user );	
		$this->assignRef('request_url',	$uri );

		$frompage  = 'booking_screen_fd';
		$frompage_item = $jinput->getString('frompage_item');
		$this->assignRef('frompage',	$frompage);
		$this->assignRef('Itemid',	$frompage_item);



		$appWeb      = new JApplicationWeb;
		$layout = ($appWeb->client->mobile ? 'mobile' : null);
		$agent = $appWeb->client->userAgent;
		$this->assignRef('agent',	$agent);
		// dev only hard code mobile view
		$device = "";

		// if you want iPad to display a simple booking screen, comment out the line below.
		if(strpos($agent, "iPad") !== false ){ 
			$layout = null; 
			$device = "iPad";
			}

		// if you want Android tablets to display a desktop booking screen, comment out the line below.
		if(strpos($agent, "Android") !== false && strpos($agent, "Mobile") === false ){ 
			$layout = null; 
			$device = "tablet";
			}

		// dev only hard code mobile view
		//$device = "tablet";
		//$layout = 'mobile';

		$this->assignRef('device', $device);
	
    	parent::display($layout);
  }
}

?>
