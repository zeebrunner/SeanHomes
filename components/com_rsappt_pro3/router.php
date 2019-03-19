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

function rsappt_pro3BuildRoute(&$query){
	$segments = array();
	$view = "";
	$controller = "";
	
	if(isset($query['view'])){
		$view = $query['view'];
		$segments[] = $query['view'];
		unset($query['view']);
	}
	
	if(isset($query['controller'])){
		$controller = $query['controller'];
		$segments[] = $query['controller'];
		unset($query['controller']);
	}
	
	switch ($view) {
		case 'booking_screen_gad';
		case 'booking_screen_fd';
		case 'booking_screen_simple';
		case 'bookingscreengadwiz';
			if(isset($query['Itemid'])){
				$segments[] = $query['Itemid'];
		        unset( $query['Itemid'] );
		    };
			if(isset($query['task'])){
				$segments[] = $query['task'];
				unset( $query['task'] );
			};
			if(isset($query['cc'])){
				$segments[] = $query['cc'];
				unset( $query['cc'] );
			};
			if(isset($query['req_id'])){
				$segments[] = $query['req_id'];
				unset( $query['req_id'] );
			};
			break;


		case 'mail';
		case 'front_desk';
			//$segments[] = 'front_desk'; 
			if(isset($query['Itemid'])){	
				$segments[] = $query['Itemid'];
			}
			break;

		case 'advadmin';
			$segments[] = 'advadmin';
			if(isset($query['Itemid'])){	
				$segments[] = $query['Itemid'];
			}
			if(isset($query['current_tab'])){	
				$segments[] = $query['current_tab'];
			}
			
			break;

		case 'admin';
		case 'admin_invoice';
			if(isset($query['task'])){
				$segments[] = $query['task'];
				unset( $query['task'] );
			};
			if(isset($query['frompage'])){
				$segments[] = $query['frompage'];
				unset( $query['frompage'] );
			};
			
			break;

	}

	switch ($controller) {
		
		case 'front_desk';
			//index.php?option=com_rsappt_pro3&controller=front_desk&task=add_booking&frompage=front_desk&Itemid='.$itemid
			if(isset($query['task'])){
				$segments[] = $query['task'];
				unset( $query['task'] );
			};
			if(isset($query['frompage'])){
				$segments[] = $query['frompage'];
				unset( $query['frompage'] );
			};
			if(isset($query['Itemid'])){
				$segments[] = $query['Itemid'];
				unset( $query['Itemid'] );
			};
			break;

		case 'mail_detail';
			if(isset($query['task'])){
				$segments[] = $query['task'];
				unset( $query['task'] );
			};
			if(isset($query['cid'])){
				// cid is array		
				if(is_array($query['cid'])){
					$segments[] = $query['cid'][0];
				} else {
					$segments[] = $query['cid'];
				}
				unset( $query['cid'] );
			} else {
				// dummy cid for positioning
				$segments[] = -1;
			};
			break;

		case 'admin_detail';
			//task=edit&cid[]='. $booking->id_requests.'&frompage=front_desk&Itemid='.$this->Itemid
			if(isset($query['task'])){
				$segments[] = $query['task'];
				unset( $query['task'] );
			};
			if(isset($query['cid'])){
				// cid is array		
				if(is_array($query['cid'])){
					$segments[] = $query['cid'][0];
				} else {
					$segments[] = $query['cid'];
				}
				unset( $query['cid'] );
			} else {
				// dummy cid for positioning
				$segments[] = -1;
			};
			if(isset($query['frompage'])){
				$segments[] = $query['frompage'];
				unset( $query['frompage'] );
			};
			if(isset($query['Itemid'])){
				$segments[] = $query['Itemid'];
				unset( $query['Itemid'] );
			};
			break;
			
		case 'mail';
		case 'admin';			
		case 'admin_invoice';			
			if(isset($query['task'])){
				$segments[] = $query['task'];
				unset( $query['task'] );
			};			
			if(isset($query['frompage'])){
				$segments[] = $query['frompage'];
				unset( $query['frompage'] );
			};
			break;

	}
  	return $segments;
}

function rsappt_pro3ParseRoute(&$segments) {
	$vars = array();
	
	switch($segments[0]){
		case 'booking_screen_gad':
		case 'booking_screen_fd';
		case 'booking_screen_simple';
		case 'bookingscreengadwiz';
			//index.php?option=com_rsappt_pro3&view=booking_screen_gad&Itemid='.$frompage_item.'&task='.$next_view.'&req_id=
			$vars['view'] = $segments[0];
			if(count($segments)>1){
				$vars['Itemid'] = $segments[1];		
			}
			if(count($segments)>2){
				$vars['task'] = $segments[2];
			}
			if(count($segments)>3){
				$vars['cc'] = $segments[3];
			}
			if(count($segments)>4){
				$vars['req_id'] = $segments[4];
			}
			break;		

		case 'front_desk':
			if(count($segments)>2){
				$vars['controller'] = 'front_desk';
				$vars['task'] = $segments[1];
				$vars['frompage'] = $segments[2];
				if(count($segments)>3){
					$vars['Itemid'] = $segments[3];
				}
			} else {
				$vars['view'] = 'front_desk';
				if(count($segments)>1){
					$vars['Itemid'] = $segments[1];
				}
			}
			break;

	   case 'mail_detail':
			$vars['controller'] = 'mail_detail';
			if(count($segments)>1){
				$vars['task'] = $segments[1];
			}
			if(count($segments)>2){
				$vars['cid'] = $segments[2];
			}
			if(count($segments)>3){
				$vars['frompage'] = $segments[3];
			}
			if(count($segments)>4){
				$vars['Itemid'] = $segments[4];
			}
			break;

	   case 'admin_detail':
			$vars['controller'] = 'admin_detail';
			$vars['task'] = $segments[1];
			$vars['cid'] = $segments[2];
			if(count($segments)>3){
				$vars['frompage'] = $segments[3];
			}
			if(count($segments)>4){
				$vars['Itemid'] = $segments[4];
			}
			break;
		   
	   case 'advadmin':
		   $vars['view'] = 'advadmin';
		   break;

	   case 'mail':
		   $vars['view'] = 'mail';
		   break;

		case 'admin':
			$vars['view'] = 'admin';
			if(count($segments)>1){
				if($segments[1] == "printer"){
					$vars['task'] = $segments[1];
					$vars['layout'] = 'default_prt';
					$vars['tmpl'] = 'component';
				}
			}
			break;

	   case 'admin_invoice':
			//  index.php?option=com_rsappt_pro3&controller=admin_invoice&task=create_invoice&frompage=advadmin
		   $vars['view'] = 'admin_invoice';
			$vars['task'] = $segments[1];
			if(count($segments)>2){
				$vars['frompage'] = $segments[2];
			}
		   break;
			
	}
  return $vars;
}
?>
