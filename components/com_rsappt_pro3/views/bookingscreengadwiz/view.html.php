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
 
class bookingscreengadwizViewbookingscreengadwiz extends JViewLegacy
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
	 $context = 'gad_booking_screen.';
 
 	 parent::__construct( $config );
	}
 

   
	function display($tpl = null)
	{
		global $context;
		
		$uri = JFactory::getURI()->toString();		
		$user=JFactory::getUser();
		$this->assignRef('user', $user);	
		$this->assignRef('request_url',	$uri );

		$frompage  = 'bookingscreengadwiz';
		$this->assignRef('frompage',	$frompage);

		$layout = null;
		$appWeb      = new JApplicationWeb;
		$layout = ($appWeb->client->mobile ? 'mobile' : null);
		$agent = $appWeb->client->userAgent;
		$this->assignRef('agent',	$agent);
		if($layout == "mobile"){
			// get config stuff
			$database =JFactory::getDBO(); 
			$sql = 'SELECT * FROM #__sv_apptpro3_config';
			try{
				$database->setQuery($sql);
				$apptpro_config = NULL;
				$apptpro_config = $database -> loadObject();
			} catch (RuntimeException $e) {
				logIt($e->getMessage(), "gad_tmpl_default", "", "");
				echo JText::_('RS1_SQL_ERROR');
				return false;
			}		
			if($apptpro_config->mobile_show_simple == "Yes"){
				$layout = 'mobile_simple';
			}
		}

		/* 
			There is no guaranteed way to detect the device from the user agent. In general if the agent contains:
				'Android' and 'Mobile' = phone
				'Android' only = tablet
			Note: This is not universal among manufactures, some use Android Mobile for tablets ;-(
			Also, iPad reports as 'mobile'.
		*/

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
		//$layout = 'mobile_simple';

		$this->assignRef('device', $device);

    	parent::display($layout);
  }
}

?>
