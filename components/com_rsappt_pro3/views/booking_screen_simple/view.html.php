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
 
class booking_screen_simpleViewbooking_screen_simple extends JViewLegacy
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
	 $context = 'simple_booking_screen.';
 
 	 parent::__construct( $config );
	}
 

   
	function display($tpl = null)
	{
		global $context;
		
		$uri = JFactory::getURI()->toString();
		$user = JFactory::getUser();
		
		$this->assignRef('user', $user );	
		$this->assignRef('request_url',	$uri );

		$frompage  = 'simple_booking_screen';
		$this->assignRef('frompage',	$frompage);


//		if(!class_exists('Mobile_Detect')) {
//			require_once JPATH_SITE.DS."components".DS."com_rsappt_pro3".DS."Mobile_Detect.php";
//		}
//		$detect = new Mobile_Detect();
//		$layout = null;
//		//$layout = ($detect->isMobile() ? ($detect->isTablet() ? null/*'tablet'*/ : 'mobile') : null);
//		$layout = ($detect->isMobile() ? 'mobile' : null);
//		//echo $layout;
		$appWeb      = new JApplicationWeb;
		$layout = ($appWeb->client->mobile ? 'mobile' : null);
		$agent = $appWeb->client->userAgent;
		$this->assignRef('agent',	$agent);
		// dev only hard code mobile view
		//$layout = 'mobile';
		
    	parent::display($layout);
  }
}

?>
