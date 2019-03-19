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
 * rsappt_pro3  Controller
 */
 
class ajaxController extends JControllerForm
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
		
	
		// Register tasks
		$this->registerTask( 'list_bookings', 'list_bookings' );
		$this->registerTask( 'cancel_booking', 'cancel_booking' );
		$this->registerTask( 'delete_booking', 'delete_booking' );
		$this->registerTask( 'ajax_calview', 'ajax_calview' );

		$this->registerTask( 'ajax', 'generic_ajax' );
		$this->registerTask( 'ajax_validate', 'ajax_validate' );
		$this->registerTask( 'ajax_validate_edit', 'ajax_validate_edit' );

		$this->registerTask( 'ajax_gad', 'ajax_gad' );
		$this->registerTask( 'ajax_gad2', 'ajax_gad2' );

		$this->registerTask( 'ajax_check_overlap', 'ajax_check_overlap' );

		$this->registerTask( 'ajax_fetch', 'ajax_fetch' );

		$this->registerTask( 'ajax_who_booked', 'ajax_who_booked' );

		$this->registerTask( 'ajax_user_search', 'ajax_user_search' );

		$this->registerTask( 'ajax_get_rate_overrides', 'ajax_get_rate_overrides' );
		$this->registerTask( 'ajax_get_rate_adjustments', 'ajax_get_rate_adjustments' );

		$this->registerTask( 'get_gw_token', 'gw_token' );
		$this->registerTask( 'gw_fail', 'gw_fail' );
		$this->registerTask( 'gw_wrapit', 'gw_wrapit' );

		$this->registerTask( 'ajax_check_overrun', 'ajax_check_overrun' );

		$this->registerTask( 'ajax_quick_status_change', 'ajax_quick_status_change' );
	}

	function list_bookings()
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set( 'view', 'backup_restore' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 0);


		parent::display();

	}
      
	function cancel_booking()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/ajax/fe_cancel.php');
	}


	function delete_booking()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/ajax/fe_delete.php');
	}

	function ajax_calview()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/ajax/calview_ajax.php');
	}

	function generic_ajax()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/getSlots.php');
	}

	function ajax_validate()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/fe_val.php');
	}

	function ajax_validate_edit()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/fe_val_edit.php');
	}

	function ajax_gad()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/gad_ajax.php');
	}
	
	function ajax_gad2()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/gad_ajax2.php');
	}

	function ajax_check_overlap()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/fe_overlap.php');
	}

	function ajax_fetch()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/fe_fetch.php');
	}

	function ajax_who_booked()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/ajax/who_booked.php');
	}

	function ajax_user_search()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/ajax/user_search.php');
	}

	
	/** function cancel
	*
	* Check in the selected detail 
	* and set Redirection to the list of items	
	* 		
	* @return set Redirection
	*/
	function cancel($key=null)
	{
		$this->setRedirect( 'index.php?option=com_rsappt_pro3&controller=ajax',$msg );
	}	


	function ajax_get_rate_overrides()
	{
		include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );
		$user_id = $jinput->getString( 'id', '0' );
		
		// get resource rates
		$database =JFactory::getDBO(); 
		$sql = 'SELECT id_resources,rate,rate_unit,deposit_amount,deposit_unit FROM #__sv_apptpro3_resources';
		try{
			$database->setQuery($sql);
			$res_rates = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "controllers/ajax", "", "");
			echo JText::_('RS1_SQL_ERROR');
			jExit();
		}		
		$rateArrayString = "";
		$base_rate = "0.00";
		for($i=0; $i<count($res_rates); $i++){
			$base_rate = getOverrideRate("resource", $res_rates[$i]->id_resources, $res_rates[$i]->rate, $user_id, "rate");
			$rateArrayString = $rateArrayString.$res_rates[$i]->id_resources.":".$base_rate."";
			if($i<count($res_rates)-1){
				$rateArrayString = $rateArrayString.",";
			}
		}
		
		echo json_encode($rateArrayString);
		jExit();
	}

	function ajax_get_rate_adjustments()
	{
		$jinput = JFactory::getApplication()->input;

		$res_adjustments  = null;
		include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );
		$ent = $jinput->getWord( 'ent', '0' );
		$ent_id = $jinput->getInt( 'ent_id', '0' );
		$bkg_date = $jinput->getString( 'bkg_date', '0' );
		$bkg_start = $jinput->getString( 'bkg_start', '0' );
		$bkg_end = $jinput->getString( 'bkg_end', '0' );
		
		$day_adjustment = "";
		$day_adjustment_unit = "";
		$time_adjustment = "";
		$time_adjustment_unit = "";
		
		// get resource adjustments
		$database =JFactory::getDBO(); 
		$sql = "SELECT * FROM #__sv_apptpro3_rate_adjustments WHERE ".
		" entity_type = '".$database->escape($ent)."'".
		" AND entity_id = ".$database->escape($ent_id).
		" AND published = 1".
		" AND (start_publishing IS NULL OR start_publishing = '0000-00-00' OR '".$database->escape($bkg_date)."' >= start_publishing ) ".
		" AND (end_publishing IS NULL OR start_publishing = '0000-00-00' OR '".$database->escape($bkg_date)."' <= end_publishing ) ";
		try{
			$database->setQuery($sql);
			$res_adjustments = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "controllers/ajax", "", "");
			echo JText::_('RS1_SQL_ERROR');
			jExit();
		}	
		
		if(count($res_adjustments) == 0){
			// no adjustmemnt required
			echo json_encode(0);
			jExit();
			
		} else {
			foreach($res_adjustments as $res_adjustment){
				// are the found adjustments by-day
				if($res_adjustment->by_day_time == "DayOnly"){
					// Does the booking day match and enabled adjustment day
					$weekday = date( "w", strtotime($bkg_date));
					switch($weekday){
						case 0:
							if($res_adjustment->adjustSunday == "Yes"){
								$day_adjustment = $res_adjustment->rate_adjustment;
								$day_adjustment_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 1:
							if($res_adjustment->adjustMonday == "Yes"){
								$day_adjustment = $res_adjustment->rate_adjustment;
								$day_adjustment_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 2:
							if($res_adjustment->adjustTuesday == "Yes"){
								$day_adjustment = $res_adjustment->rate_adjustment;
								$day_adjustment_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 3:
							if($res_adjustment->adjustWednesday == "Yes"){
								$day_adjustment = $res_adjustment->rate_adjustment;
								$day_adjustment_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 4:
							if($res_adjustment->adjustThursday == "Yes"){
								$day_adjustment = $res_adjustment->rate_adjustment;
								$day_adjustment_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 5:
							if($res_adjustment->adjustFriday == "Yes"){
								$day_adjustment = $res_adjustment->rate_adjustment;
								$day_adjustment_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 6:
							if($res_adjustment->adjustSaturday == "Yes"){
								$day_adjustment = $res_adjustment->rate_adjustment;
								$day_adjustment_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
															
					}
				}

				if($res_adjustment->by_day_time == "TimeOnly"){
					// does booking start or end fall in range
					$temp = strtotime($bkg_start)+1;
					if(($temp > strtotime($res_adjustment->timeRangeStart) && $temp < strtotime($res_adjustment->timeRangeEnd)) || 
					( $temp > strtotime($res_adjustment->timeRangeStart) && $temp < strtotime($res_adjustment->timeRangeEnd))){
						$time_adjustment = $res_adjustment->rate_adjustment;
						$time_adjustment_unit = $res_adjustment->rate_adjustment_unit;
					}
					$temp = strtotime($bkg_end)-1;
					if(($temp > strtotime($res_adjustment->timeRangeStart) && $temp < strtotime($res_adjustment->timeRangeEnd)) || 
					( $temp > strtotime($res_adjustment->timeRangeStart) && $temp < strtotime($res_adjustment->timeRangeEnd))){
						$time_adjustment = $res_adjustment->rate_adjustment;
						$time_adjustment_unit = $res_adjustment->rate_adjustment_unit;
					}
				}
				
				if($res_adjustment->by_day_time == "DayAndTime"){
					// only adjust is both date and time match
					$weekday = date( "w", strtotime($bkg_date));
					$day_match = false;
					$time_match = false;
					$temp_day_adjust = "";
					$temp_time_adjust = "";
					$temp_day_adjust_unit = "";
					$temp_time_adjust_unit = "";
					switch($weekday){
						case 0:
							if($res_adjustment->adjustSunday == "Yes"){
								$day_match = true;
								$temp_day_adjust = $res_adjustment->rate_adjustment;
								$temp_day_adjust_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 1:
							if($res_adjustment->adjustMonday == "Yes"){
								$day_match = true;
								$temp_day_adjust = $res_adjustment->rate_adjustment;
								$temp_day_adjust_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 2:
							if($res_adjustment->adjustTuesday == "Yes"){
								$day_match = true;
								$temp_day_adjust = $res_adjustment->rate_adjustment;
								$temp_day_adjust_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 3:
							if($res_adjustment->adjustWednesday == "Yes"){
								$day_match = true;
								$temp_day_adjust = $res_adjustment->rate_adjustment;
								$temp_day_adjust_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 4:
							if($res_adjustment->adjustThursday == "Yes"){
								$day_match = true;
								$temp_day_adjust = $res_adjustment->rate_adjustment;
								$temp_day_adjust_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 5:
							if($res_adjustment->adjustFriday == "Yes"){
								$day_match = true;
								$temp_day_adjust = $res_adjustment->rate_adjustment;
								$temp_day_adjust_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
						case 6:
							if($res_adjustment->adjustSaturday == "Yes"){
								$day_match = true;
								$temp_day_adjust = $res_adjustment->rate_adjustment;
								$temp_day_adjust_unit = $res_adjustment->rate_adjustment_unit;
							}
							break;
															
					}
					if($day_match){
						// there is a da_adjustment but we only want to pass back a required adjustment if the time matches also
						$temp = strtotime($bkg_start)+1;
						if(($temp > strtotime($res_adjustment->timeRangeStart) && $temp < strtotime($res_adjustment->timeRangeEnd)) || 
						( $temp > strtotime($res_adjustment->timeRangeStart) && $temp < strtotime($res_adjustment->timeRangeEnd))){
							$time_match = true;
							$temp_time_adjust = $res_adjustment->rate_adjustment;
							$temp_time_adjust_unit = $res_adjustment->rate_adjustment_unit;
						}
						$temp = strtotime($bkg_end)-1;
						if(($temp > strtotime($res_adjustment->timeRangeStart) && $temp < strtotime($res_adjustment->timeRangeEnd)) || 
						( $temp > strtotime($res_adjustment->timeRangeStart) && $temp < strtotime($res_adjustment->timeRangeEnd))){
							$time_match = true;
							$temp_time_adjust = $res_adjustment->rate_adjustment;
							$temp_time_adjust_unit = $res_adjustment->rate_adjustment_unit;
						}
					}
					if($day_match && $time_match){
						$day_adjustment = ""; // if date/time adjustment, clear day
						$day_adjustment_unit = "";
						$time_adjustment = $temp_time_adjust;
						$time_adjustment_unit = $temp_time_adjust_unit;
					}
				}
			}
			
			
			
		}
//		echo json_encode("day~".$day_adjustment.","."time~".$time_adjustment);
		$ret_val = array(
   			'day' => $day_adjustment,
   			'day_unit' => $day_adjustment_unit,
			'time' => $time_adjustment,
			'time_unit' => $time_adjustment_unit
	    );
	    echo json_encode( $ret_val );				
		jExit();
	}


	function gw_token()
	{
		include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );
		include_once( JPATH_SITE."/components/com_rsappt_pro3/payment_processors/google_wallet/JWT.php" );
		// get google_wallet settings
		$database =JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_google_wallet_settings;';
		try{
			$database->setQuery($sql);
			$google_wallet_settings = NULL;
			$google_wallet_settings = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "gw_token", "", "");
			echo json_encode(JText::_('RS1_SQL_ERROR'));
			jExit();
		}
		$name = $jinput->getString( 'gw_name', 'Google Purchase' );
		$description = $jinput->getString( 'gw_description', 'Google Purchase' );
		$price = $jinput->getString( 'gw_price', '0.00' );
		$req_id = $jinput->getString( 'gw_req_id', '-1' );
		if($req_id != "cart"){
			$description = processTokens($req_id, $description);
		}

		$payload = array(
		  "iss" => $google_wallet_settings->google_wallet_seller_id,
		  "aud" => "Google",
		  "typ" => "google/payments/inapp/item/v1",
		  "exp" => time() + 3600,
		  "iat" => time(),
		  "request" => array (
			"name" => $name,
			"description" => $description,
			"price" => $price,
			"currencyCode" => "USD",
			"sellerData" => $req_id
		  )
		);
		$gwToken = JWT::encode($payload, $google_wallet_settings->google_wallet_seller_secret);		
		echo json_encode($gwToken);
		jExit();
	}
	
	function gw_fail()
	{
		include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );
		// get google_wallet settings
		$database =JFactory::getDBO(); 
		$req_id = $jinput->getString( 'req_id', '-1' );
		$sql = "UPDATE #__sv_apptpro3_requests set payment_processor_used='GoogleWallet', request_status='deleted', ".
		" admin_comment='Google Wallet transaction failed or was cancelled by user'".
		" WHERE id_requests=".$req_id;
		try{				
			$database->setQuery($sql);
			$database->execute();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "gw_fail", "", "");
		}
		
				
		echo json_encode("Canceled");
		jExit();
	}

	function gw_wrapit()	
	{
		if($jinput->getString( 'req_id', '-1' ) == "cart"){
			$cart = "Yes";
		} else {
			$cart = "No";
		}
		include_once(JPATH_SITE.'/components/com_rsappt_pro3/payment_processors/google_wallet/google_wallet_process_payment.php');		
	}

	function ajax_check_overrun()
	{
		// check to see if new booking, adjusted for service, extras, etc, overruns an exiting booking.	
		$jinput = JFactory::getApplication()->input;
		$resource = $jinput->getInt( 'res', '0' );
		$startdate = $jinput->getString( 'bk_date', '' );
		$starttime = $jinput->getString( 'bk_start', '' );
		$endtime = $jinput->getString( 'bk_end', '' );
	
		$database = JFactory::getDBO(); 

		// if max_seats > 1 no need to check as all booking for a slot must be the same duration (no sercie based duration or extras durtion allowed.
		$sql = 'SELECT max_seats FROM #__sv_apptpro3_resources WHERE id_resources = '.(int)$resource;
		try{
			$database->setQuery($sql);
			$max_seats = $database -> loadResult();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "chk_overrun", "", "");
			echo JText::_('RS1_SQL_ERROR');
			jExit();
		}		
		if($max_seats > 1){
			jExit();
		}
		
		$sql = 'SELECT * FROM #__sv_apptpro3_config';
		try{
			$database->setQuery($sql);
			$apptpro_config = NULL;
			$apptpro_config = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "chk_overrun", "", "");
			echo JText::_('RS1_SQL_ERROR');
			jExit();
		}		

		$mystartdatetime = "STR_TO_DATE('".$startdate ." ". $starttime ."', '%Y-%m-%d %T')+ INTERVAL 1 SECOND";
		$myenddatetime = "STR_TO_DATE('".$startdate ." ". $endtime ."', '%Y-%m-%d %T')- INTERVAL 1 SECOND";
		$gap = 0;
		$sql = "SELECT id_requests FROM #__sv_apptpro3_requests "
			." WHERE (resource = '". $resource ."')"
			." and (request_status = 'accepted' or request_status = 'pending' ".($apptpro_config->block_new=="Yes"?"OR request_status='new'":"")." )"
			." and ((". $mystartdatetime ." >= CONCAT(startdate, ' ', starttime) and ". $mystartdatetime ." <= DATE_ADD( CONCAT(enddate, ' ', endtime), INTERVAL ".$gap." MINUTE))"
			." or (". $myenddatetime ." >= CONCAT(startdate, ' ', starttime) and ". $myenddatetime ." <= CONCAT(enddate, ' ', endtime))"
			." or ( CONCAT(startdate, ' ', starttime) >= ". $mystartdatetime ." and CONCAT(startdate, ' ', starttime) <= ". $myenddatetime ." )"
			." or ( DATE_ADD( STR_TO_DATE(CONCAT(enddate, ' ', endtime), '%Y-%m-%d %T'), INTERVAL ".$gap." MINUTE) >= ". $mystartdatetime ." and DATE_ADD( STR_TO_DATE(CONCAT(enddate, ' ', endtime), '%Y-%m-%d %T'), INTERVAL ".$gap." MINUTE) <= ". $myenddatetime ."))";	
		//logIt($sql, "chk_overrun", "", "");
		try{
			$database->setQuery($sql);
			$overruns = $database->loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "chk_overrun", "", "");
			echo JText::_('RS1_SQL_ERROR');
			jExit();
		}		
		if(count($overruns) > 0){
			echo JText::_('RS1_INPUT_SCRN_CONFLICT_ERR')."|".JText::_('RS1_WARNING');
		}
		jExit();
	}

	function ajax_quick_status_change()
	{
		$jinput = JFactory::getApplication()->input;
		$id_requests = $jinput->getInt( 'bk', '0' );
		$new_status = $jinput->getString( 'new_stat', '' );
	
		$database = JFactory::getDBO(); 

		require_once(JPATH_COMPONENT.DS.'models'.DS.'requests_detail.php');
		$model = new admin_detailModelrequests_detail;
		$model->setId($id_requests);
		$detail	= $model->getData();
		//echo $detail->request_status;
		$detail->request_status = $new_status;
	
		// run the staff validation to ensure this change will not create a conflict
		include_once( JPATH_SITE."/components/com_rsappt_pro3/fe_val_edit_pt2.php" );
		$err = do_staff_edit_validation($detail->id_requests,$detail->request_status,$detail->name,$detail->phone,$detail->email,$detail->resource,
			$detail->startdate,$detail->starttime,$detail->enddate,$detail->endtime,$detail->booked_seats,$detail->user_id);
			
		if( $err!=JText::_('RS1_INPUT_SCRN_VALIDATION_OK')){
			echo $err;
		} else {			
			if($result = $model->store($detail)){
				echo "OK";
			}
		}
		
	
	jExit();
	}

}
?>

