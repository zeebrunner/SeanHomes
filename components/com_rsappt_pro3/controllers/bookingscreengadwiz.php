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

	include_once( JPATH_SITE."/administrator/components/com_rsappt_pro3/sendmail_pro2.php" );

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import CONTROLLER object class
jimport( 'joomla.application.component.controller' );


/**
 * rsappt_pro3  Controller
 */
 
class bookingscreengadwizController extends JControllerForm
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		// if you want to force redirect to the login screen uncomment the following
//		$user = JFactory::getUser();
//		if($user->guest){
//			$return = JURI::getInstance()->toString();
//			$url    = 'index.php?option=com_users&view=login';
//			$url   .= '&return='.base64_encode($return);
//			$this->setRedirect($url, 'You must login first');
//		}
		
		// Register Extra tasks	
		$this->registerTask( 'process_booking_request', 'process_booking_request' );
		$this->registerTask( 'show_confirmation', 'show_confirmation' );
		$this->registerTask( 'show_in_progress', 'show_in_progress' );
		$this->registerTask( 'pp_return', 'pp_return' );
		$this->registerTask( 'pp_return_cart', 'pp_return_cart' );
		$this->registerTask( 'authnet_return', 'authnet_return' );
		$this->registerTask( 'authnet_return_cart', 'authnet_return_cart' );
		$this->registerTask( 'twoco_return', 'twoco_return' );
		$this->registerTask( 'twoco_return_cart', 'twoco_return_cart' );
	}


	function cancel($key=null)
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		
		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$this->setRedirect( JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item ));
		} else {
			$this->setRedirect( 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item );
		}
	}	

	function process_booking_request(){
	
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$facebook = $jinput->getString('facebook', ''); // Yes if booking comes from facebook
		$err="";
		$message = "";
		$message_admin = "";
		$this->json = new stdClass();
		
		include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );

	//=========================================================================
	//	require_once('recaptchalib.php');
	//	$privatekey = "...";
	//	$resp = recaptcha_check_answer ($privatekey,
	//									$_SERVER["REMOTE_ADDR"],
	//									$_POST["recaptcha_challenge_field"],
	//									$_POST["recaptcha_response_field"]);
	//	
	//	if (!$resp->is_valid) {
	//	  die ("The reCAPTCHA wasn't entered correctly. Go back and try it again.");// .
	//	}
	//=========================================================================

		$pay_proc_submit = $jinput->getString('ppsubmit', '0');
	
		$name = $jinput->getString('name');
		$user_id = $jinput->getString('user_id');
		$unit_number = $jinput->getString('unitnumber');
		$phone = $jinput->getString('phone');
		$email = $jinput->getString('email');
		$sms_reminders = $jinput->getString('sms_reminders', "No");
		$sms_phone = $jinput->getString('sms_phone');
		$sms_dial_code = $jinput->getString('sms_dial_code');
		$resource = $jinput->getString('resource');
		$service_name = $jinput->getString('service_name');
		$startdate = $jinput->getString('startdate');
		$starttime = $jinput->getString('starttime');
		$enddate = $jinput->getString('enddate');
		$endtime = $jinput->getString('endtime');
		$comment = $jinput->getString('comment');
		$copyme = $jinput->getString('cbCopyMe');
		$str_udf_count = $jinput->getString('udf_count', "0");
		$str_res_udf_count = $jinput->getString('res_udf_count', "0");
		$int_udf_count = intval($str_udf_count) + intval($str_res_udf_count);

		$applied_credit = $jinput->getString('applied_credit', 0.00);
		$uc_used = $jinput->getString('uc_used', 0.00);
		$gc_used = $jinput->getString('gc_used', 0.00);
		$grand_total = $jinput->getString('grand_total', 0);
		$ammount_due = $grand_total;
		$coupon_code = $jinput->getString('coupon_code','');
		$booked_seats = $jinput->getString('booked_seats', 1);	
		$seat_type_count = $jinput->getString('seat_type_count', -1);
		$extras_count = $jinput->getString('extras_count', -1);
		$admin_comment = $jinput->getString('admin_comment', '');		
		$gift_cert = $jinput->getString('gift_cert', '');		

		$deposit_amount = $jinput->getString('deposit_amount', 0);
		$category = $jinput->getString('category_id');
		$sub_category_id = $jinput->getString('sub_category_id', -1);
		if($sub_category_id != -1){
			$category = $sub_category_id;			
		}
				
		if($resource == ""){
			$resource = $jinput->getInt('resources', "");
		}

		if($resource == ""){
			$resource = $jinput->getInt('selected_resource_id', 0);
			//$resource = $_POST['selected_resource_id'];
		}
		
		if($resource == "" or $resource < 1){
			// should never happen
			die("No Access (2)");
		}

		// get config info
		$database = JFactory::getDBO();
		$sql = 'SELECT * FROM #__sv_apptpro3_config';
		try{
			$database->setQuery($sql);
			$apptpro_config = NULL;
			$apptpro_config = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "bookingscreengadwiz", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
		$user = JFactory::getUser();
		if($apptpro_config->requireLogin == "Yes" && $user->guest){
			die("No Access (3)");
		}
		
		// get resource info for the selected resource
		$sql = 'SELECT * FROM #__sv_apptpro3_resources where id_resources = '.(int)$resource;
		try{
			$database->setQuery($sql);
			$res_detail = NULL;
			$res_detail = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "bookingscreengadwiz", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		

		if ($err == ""){
		
		/* ----------------------------------------------------------------------------------- 
		/*		Save order to database 
		/* -------------------------------------------------------------------------------------*/

		$request_status = "new";
		if($res_detail->auto_accept == "Global"){
			$auto_accept = $apptpro_config->auto_accept;
		} else {
			$auto_accept = $res_detail->auto_accept;
		}
		
		switch ($pay_proc_submit) {
		    case "0":
				// non-pay button				
				if($auto_accept == "Yes"){
					// auto-accept
					// note: if payment due, but if we got here display and block was not set so let it through						
					$request_status = "accepted";
				}
    	    	break;

			case "1":
				// PayPal button OR non-pay with DAB set
				if($apptpro_config->non_pay_booking_button == "DAB"){
					//Display and Block set but since we got this far the user credit must cover the booking
					if($auto_accept == "Yes"){
						$request_status = "accepted";
					}
				} else {
					// going to go to PayPal
					if($auto_accept == "Yes"){
						$request_status = "pending";
					}
					if($auto_accept == "Yes" && floatval($grand_total) == 0){
						$request_status = "accepted";
					}					
				}
				
				break;
				
			case "2":
			case "3":
				// AuthNet button OR non-pay with DAB set
				if($apptpro_config->non_pay_booking_button == "DAB"){
					//Display and Block set but since we got this far the user credit must cover the booking
					if($auto_accept == "Yes"){
						$request_status = "accepted";
					}
				} else {
					// going to go to AuthNet
					if($auto_accept == "Yes"){
						$request_status = "pending";
					}
					if($auto_accept == "Yes" && floatval($grand_total) == 0){
						$request_status = "accepted";
					}					
				}				
				break;
				
			case "4":
				// add to cart, add booking as Pending
				$request_status = "pending";			
 				break;

			default:
				// going to a payment processor
				if($apptpro_config->non_pay_booking_button == "DAB"){
					//Display and Block set but since we got this far the user credit must cover the booking
					if($auto_accept == "Yes"){
						$request_status = "accepted";
					}
				} else {
					if($auto_accept == "Yes"){
						$request_status = "pending";
					}
					if($auto_accept == "Yes" && floatval($grand_total) == 0){
						$request_status = "accepted";
					}					
				}
				break;				
		}

		// a booking can have some user credit and some gift certificate adding up to total applied_credit
		// pass as "applied_credit|uc_used|gc_used"
		
		$credit_data = $applied_credit."|".$uc_used."|".$gc_used;
  		// save to db
 		$last_id = NULL;
		$cancel_code = md5(uniqid(rand(), true));
		$last_id = saveToDB($name, $user_id ,$phone, $email, $sms_reminders, $sms_phone, $sms_dial_code, $resource, $category,
			$service_name, $startdate, $starttime, $enddate, $endtime, $request_status, $cancel_code, $grand_total,
			$ammount_due, $deposit_amount, $coupon_code, $booked_seats, $credit_data, $comment, $admin_comment, "", $gift_cert);	
		if($last_id->last_id == -1){
			exit;
		}		

		if($apptpro_config->which_calendar != 'None' and $apptpro_config->which_calendar != "Google"){
			// need to set request to resource's defaults
			$cat_id = NULL;
			$cal_id = NULL;
			getDefaultCalInfo($apptpro_config->which_calendar, $res_detail, $cat_id, $cal_id);
			if($cat_id != NULL){
				if($apptpro_config->which_calendar == "JCalPro2"){
					$sql = "UPDATE #__sv_apptpro3_requests SET calendar_category=".strval($cat_id).", ".
					"calendar_calendar = ".strval($cal_id)." WHERE id_requests = ".$last_id->last_id;
				} else {
					$sql = "UPDATE #__sv_apptpro3_requests SET calendar_category=".strval($cat_id)." WHERE id_requests = ".$last_id->last_id;
				}								
				$database->setQuery($sql);
				$database->execute();
			}
		}
		
		// add seat counts to seat_counts table if in use
		if($seat_type_count > 0){
			for($stci=0; $stci<$seat_type_count; $stci++){

				$seat_type_id = $jinput->getInt('seat_type_id_'.$stci,"?");
				$seat_type_qty = $jinput->getInt('seat_'.$stci, 0);
				if($seat_type_qty > 0){
					$sSql = sprintf("INSERT INTO #__sv_apptpro3_seat_counts (seat_type_id, request_id, seat_type_qty) VALUES(%d, %d, '%s')",
							$seat_type_id,
							$last_id->last_id,
							$database->escape($seat_type_qty));
					try{
						$database->setQuery($sSql);
						$database->execute();
					} catch (RuntimeException $e) {
						logIt($e->getMessage(), "ctrl_bookingscreengadwiz", "", "");
						echo JText::_('RS1_SQL_ERROR');
						exit;
					}
				}
			}
		}

		// add extras to extras_data table if in use
		if($extras_count > 0){
			for($ei=0; $ei<$extras_count; $ei++){

				$extras_id = $jinput->getInt('extras_id_'.$ei,"?");
				$extras_qty = $jinput->getString('extra_'.$ei, -1);
				if($extras_qty === "on"){$extras_qty = 1;} // extra was displayed as a checkbox
				if($extras_qty > -1){
					$sSql = sprintf("INSERT INTO #__sv_apptpro3_extras_data (extras_id, request_id, extras_qty) VALUES(%d, %d, '%s')",
							$extras_id,
							$last_id->last_id,
							$database->escape($extras_qty));
					try{
						$database->setQuery($sSql);
						$database->execute();
					} catch (RuntimeException $e) {
						logIt($e->getMessage(), "ctrl_bookingscreengadwiz", "", "");
						echo JText::_('RS1_SQL_ERROR');
						exit;
					}
				}
			}
		}


		// add udf values to udf_values table
		//echo "str_udf_count=".$str_udf_count;
		//echo "int_udf_count=".$int_udf_count;
		if($int_udf_count > 0){
			for($i=0; $i<$int_udf_count; $i++){

				$udf_type = $jinput->getString('user_field'.$i.'_type', "");
				if($udf_type == "Content"){
					// leave HTML as this comes from admin not the customer
//					$udf_value = $jinput->getString('user_field'.$i.'_value', null, 'default', 'none', JREQUEST_ALLOWHTML);
					$udf_value = $jinput->get('user_field'.$i.'_value', '', 'RAW');
					$sSql = sprintf("INSERT INTO #__sv_apptpro3_udfvalues (udf_id, request_id, udf_value) VALUES(%d, %d, '%s')",
						$jinput->getString('user_field'.$i.'_udf_id', ""),
//						$_POST['user_field'.$i.'_udf_id'],
						$last_id->last_id,
						$database->escape($udf_value));
				} else {
					$udf_value = $jinput->getString('user_field'.$i.'_value');
					$sSql = sprintf("INSERT INTO #__sv_apptpro3_udfvalues (udf_id, request_id, udf_value) VALUES(%d, %d, '%s')",
						$jinput->getString('user_field'.$i.'_udf_id', ""),
//						$_POST['user_field'.$i.'_udf_id'],
						$last_id->last_id,
						$database->escape($udf_value));
				}
				try{
					$database->setQuery($sSql);
					$database->execute();
				} catch (RuntimeException $e) {
					logIt($e->getMessage(), "ctrl_bookingscreengadwiz", "", "");
					echo JText::_('RS1_SQL_ERROR');
					exit;
				}
			}
		}
	
		
		// if "accepted", add to calendar
		if($request_status == "accepted"){
			addToCalendar($last_id->last_id, $apptpro_config);
			addToEmailMarketing($last_id->last_id, $apptpro_config);
		}	
		
		if(($pay_proc_submit != "0" && $pay_proc_submit != "4") && floatval($grand_total) > 0){
			/* ----------------------------------------------------------------------------------- 
			/*		go to Payment Processor
			/* -------------------------------------------------------------------------------------*/

			$payment_required = $grand_total;
			if($deposit_amount != "0"){
				$payment_required = $deposit_amount;
			}
			
			if($pay_proc_submit != "0" && $pay_proc_submit != "4"){
				// not pay later or cart
				$from_screen = "booking_screen_gad";
				$request_id = $last_id->last_id;
				$mobile_order = "No";
				// drop in the appropriate goto code..
				include JPATH_COMPONENT.DS."payment_processors".DS.$pay_proc_submit.DS.$pay_proc_submit."_goto.php";
				
				// for payment gateways, messages are sent from ipn/ins/etc
			}
		
		} else if($pay_proc_submit == "4"){			
			$payment_required = $grand_total;
			if($deposit_amount != "0"){
				$payment_required = $deposit_amount;
			}
			// add to cart only
			// is there a cart for his session?
			include ( JPATH_SITE."/components/com_rsappt_pro3/controllers/cart.php" );
			$cart = new cartController;

			if($cart->add($last_id->last_id, $payment_required)){
				// yes, just add item
				$this->json->msg = JText::_('RS1_ADDED_TO_CART');
			} else {
				// creat cart first
				$this->json->msg = JText::_('RS1_ERROR_NOT_ADDED_CART');
			}
			
			echo json_encode($this->json);
			jExit();
			
		} else {		
			// dev only
			//ini_set ( "SMTP", "shawmail.cg.shawcable.net" ); 

			$message_attachment = "";
			// send form		
			$mailer = JFactory::getMailer();
			$mailer->setSender($apptpro_config->mailFROM);

			if($request_status == "accepted"){
				$temp = buildMessage(strval($last_id->last_id), "confirmation", "No", "", "No", "Yes");
				$message .= $temp[0];
				if($temp[1] != ""){
					$message_attachment = JPATH_BASE.$temp[1];
				}				
				$message_admin .= buildMessage(strval($last_id->last_id), "confirmation_admin", "No");
			} else {
				$message .= buildMessage(strval($last_id->last_id), "in_progress", "No");			
				$message_admin .= buildMessage(strval($last_id->last_id), "in_progress_admin", "No");			
				//$message_admin = $message;
			}
			
			if($apptpro_config->html_email != "Yes"){
				$message = str_replace("<br>", "\r\n", $message);
				$message_admin = str_replace("<br>", "\r\n", $message_admin);
			}

			$array = array($last_id->last_id);
			$ics = buildICSfile($array);


			$sql = 'SELECT * FROM #__sv_apptpro3_mail WHERE id_mail = '.($res_detail->mail_id ==1 ||$res_detail->mail_id == null?"1":$res_detail->mail_id);
			try{
				$database->setQuery($sql);
				$messages_to_use = $database -> loadObject();
			} catch (RuntimeException $e) {
				logIt($e->getMessage(), "be_sendmail", "", "");
				echo JText::_('RS1_SQL_ERROR').$e->getMessage();
				exit;
			}
			
			// email to customer
			if($jinput->getString('email') != ""){
				$to = $jinput->getString('email');

				if($apptpro_config->html_email == "Yes"){
					$mailer->IsHTML(true);
				}

				if($messages_to_use->attach_ics_customer == "Yes" && $request_status == "accepted"){
					$mailer->AddStringAttachment($ics, "appointment_".strval($last_id->last_id).".ics");
				}

				if($message_attachment != ""){
					$mailer->addAttachment($message_attachment);
				}

				$mailer->addRecipient($to);
				$mailer->setSubject(JText::_($apptpro_config->mailSubject));
				$mailer->setBody($message);
				if($mailer->send() != true){
					logIt("Error sending email: ".$mailer->ErrorInfo);
				}
				// reset for next
				$mailer = null;
				$mailer = JFactory::getMailer();
				$mailer->setSender($apptpro_config->mailFROM);

			}
			
			// email to admin
			if($apptpro_config->mailTO != ""){
				$to = $apptpro_config->mailTO;

				if($apptpro_config->html_email == "Yes"){
					$mailer->IsHTML(true);
				}

				if($messages_to_use->attach_ics_admin == "Yes" && $request_status == "accepted"){
					$mailer->AddStringAttachment($ics, "appointment_".strval($last_id->last_id).".ics");
				}

				$mailer->addRecipient(explode(",", $to));
				$mailer->setSubject(JText::_($apptpro_config->mailSubject));
				$mailer->setBody($message_admin);
				if($mailer->send() != true){
					logIt("Error sending email: ".$mailer->ErrorInfo);
				}

				// reset for next
				$mailer = null;
				$mailer = JFactory::getMailer();
				$mailer->setSender($apptpro_config->mailFROM);
			}
			
			// email to resource
			if($res_detail->resource_email != ""){
				$to = $res_detail->resource_email;

				if($apptpro_config->html_email == "Yes"){
					$mailer->IsHTML(true);
				}

				if($messages_to_use->attach_ics_resource == "Yes" && $request_status == "accepted"){
					$mailer->AddStringAttachment($ics, "appointment_".strval($last_id->last_id).".ics");
				}

				$mailer->addRecipient(explode(",", $to));
				$mailer->setSubject(JText::_($apptpro_config->mailSubject));
				$mailer->setBody($message_admin);
				if($mailer->send() != true){
					logIt("Error sending email: ".$mailer->ErrorInfo);
				}
			}


			// SMS to resource
			if($apptpro_config->enable_clickatell == "Yes" || $apptpro_config->enable_eztexting == "Yes" || $apptpro_config->enable_twilio == "Yes"){
				$config = JFactory::getConfig();
				$tzoffset = $config->get('offset');      
			$tz = new DateTimeZone($tzoffset);
			$offsetdate = new JDate("now", $tz);
				$reminder_log_time_format = "Y-m-d H:i:s";
				$user = JFactory::getUser();
				if(!$user->guest){
					$bookingUser = $user->id;
				} else {
					$bookingUser = -1;
				}
				$returnCode = "";
	
				if($request_status == "accepted"){
					sv_sendSMS($last_id->last_id, "confirmation", $returnCode, $toResource="Yes");			
				} else {
					sv_sendSMS($last_id->last_id, "in_progress", $returnCode, $toResource="Yes");			
				}
				logReminder("New booking: ".$returnCode, $last_id->last_id, $bookingUser, $name, $offsetdate->format($reminder_log_time_format, true, true));
			}
		
			if($request_status == "accepted"){
				// unique $cancel_code required so confimration screen cannot be called directly
				$next_view="show_confirmation&cc=".$cancel_code;
			} else {
				$next_view="show_in_progress&cc=".$cancel_code;
			}

			// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
			$config = JFactory::getConfig();
			$seo = $config->get( 'sef' );
			$tmpl = $jinput->getString('tmpl', "");
			if($frompage_item == ""){
				// from popup caller, no item id
				$frompage_item = 1; // need a place holder for router parsing
			}
			if($facebook == "Yes"){				
				$this->setRedirect( 'index.php?option=com_rsappt_pro3&view=booking_screen_gad&format=fb&Itemid='.$frompage_item.'&task='.$next_view.'&req_id='.$last_id->last_id );
			} else {		
				if($seo == "1"){							
					$this->setRedirect( JRoute::_('index.php?option=com_rsappt_pro3&view=bookingscreengadwiz&Itemid='.$frompage_item.'&task='.$next_view.'&req_id='.$last_id->last_id.'&frompage='.$frompage.($tmpl!=""?"&tmpl=".$tmpl:"") ));
				} else {
					$this->setRedirect( 'index.php?option=com_rsappt_pro3&view=bookingscreengadwiz&Itemid='.$frompage_item.'&task='.$next_view.'&req_id='.$last_id->last_id.'&frompage='.$frompage.($tmpl!=""?"&tmpl=".$tmpl:"") );
				}
			}
			
			}
		}
	}


	function show_confirmation()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'wiz_confirmation' );
		$jinput->set( 'frompage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'req_id', $jinput->getString( 'req_id'));
		$jinput->set( 'which_message', 'confirmation');
		$cc = $jinput->getString( 'cc', '' );
		$jinput->set( 'cc', $cc);
		if($cc==""){
			echo "No Access (1)";
			exit;
		}

		parent::display();
	}

	function show_in_progress()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'wiz_confirmation' );
		$jinput->set( 'frompage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'req_id', $jinput->getString( 'req_id'));
		$jinput->set( 'which_message', 'in_progress');
		$cc = $jinput->getString( 'cc', '' );
		$jinput->set( 'cc', $cc);
		if($cc==""){
			echo "No Access (1)";
			exit;
		}

		parent::display();
	}
	

	function pp_return(){
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'wiz_paypal_return' );
		$jinput->set( 'frompage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'req_id', $jinput->getString( 'req_id'));

		parent::display();
	}

	function pp_return_cart(){
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'wiz_paypal_return' );
		$jinput->set( 'frompage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'req_id', $jinput->getString( 'req_id'));
		$jinput->set( 'cart', 'yes');

		parent::display();
	}

	function authnet_return(){
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'wiz_authnet_return' );
		$jinput->set( 'frompage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'req_id', $jinput->getString( 'req_id'));

		parent::display();
	}

	function authnet_return_cart(){
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'wiz_authnet_return' );
		$jinput->set( 'frompage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'req_id', $jinput->getString( 'req_id'));
		$jinput->set( 'cart', 'yes');

		parent::display();
	}

	function twoco_return(){
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'wiz_2co_return' );
		$jinput->set( 'frompage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'req_id', $jinput->getString( 'req_id'));

		parent::display();
	}

	function twoco_return_cart(){
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'wiz_2co_return' );
		$jinput->set( 'frompage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'req_id', $jinput->getString( 'req_id'));
		$jinput->set( 'cart', 'yes');

		parent::display();
	}

}
?>
