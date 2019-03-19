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
 
class front_deskViewfront_desk extends JViewLegacy
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
	 $context = 'front_desk.';
 
 	 parent::__construct( $config );
	}
 

   
	function display($tpl = null)
	{
		global $context;
	  	$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		
		$menu = JFactory::getApplication()->getMenu(); 
		$menu_id = $jinput->getString( 'menu_id', '' ); // passed from normal view on 'print'
		if($menu_id == ""){
			$active = $menu->getActive(); 
			$menu_id = $active->id;
		}
		$params = $menu->getParams($menu_id);
		$start_screen_view = "month";
		if($params->get('fd_start_screen') != ''){
			$start_screen_view = $params->get('fd_start_screen');
			//echo $start_screen_view;
		}
		
		// get filters
		$front_desk_view	= $mainframe->getUserStateFromRequest( $context.'front_desk_view', 'front_desk_view', $start_screen_view);
		$front_desk_resource_filter	= $mainframe->getUserStateFromRequest( $context.'front_desk_resource_filter', 'front_desk_resource_filter', '');
		$front_desk_category_filter	= $mainframe->getUserStateFromRequest( $context.'front_desk_category_filter', 'front_desk_category_filter', '');
		$front_desk_status_filter	= $mainframe->getUserStateFromRequest( $context.'front_desk_status_filter', 'front_desk_status_filter', '');
		$front_desk_payment_status_filter	= $mainframe->getUserStateFromRequest( $context.'front_desk_payment_status_filter', 'front_desk_payment_status_filter', '');
		$front_desk_user_search	= $mainframe->getUserStateFromRequest( $context.'front_desk_user_search', 'front_desk_user_search', '');

		$front_desk_cur_week_offset = $mainframe->getUserState('front_desk_cur_week_offset');
		$front_desk_cur_day = $mainframe->getUserState('front_desk_cur_day');
		$front_desk_cur_month = $mainframe->getUserState('front_desk_cur_month');
		$front_desk_cur_year = $mainframe->getUserState('front_desk_cur_year');


		$uri = JFactory::getURI()->toString();
		$user = JFactory::getUser();
		
		$this->assignRef('user', $user );	
		$this->assignRef('request_url',	$uri );

		$frompage  = 'front_desk';
		$this->assignRef('frompage',	$frompage);
		$this->assignRef('front_desk_view', $front_desk_view);
		$this->assignRef('front_desk_resource_filter', $front_desk_resource_filter);
		$this->assignRef('front_desk_category_filter', $front_desk_category_filter);
		$this->assignRef('front_desk_status_filter', $front_desk_status_filter);
		$this->assignRef('front_desk_payment_status_filter', $front_desk_payment_status_filter);
		$this->assignRef('front_desk_user_search', $front_desk_user_search);

		$this->assignRef('front_desk_cur_week_offset', $front_desk_cur_week_offset);
		$this->assignRef('front_desk_cur_day', $front_desk_cur_day);
		$this->assignRef('front_desk_cur_month', $front_desk_cur_month);
		$this->assignRef('front_desk_cur_year', $front_desk_cur_year);


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
