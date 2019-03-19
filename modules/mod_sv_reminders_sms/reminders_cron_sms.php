<?php
/*
 ****************************************************************
 Copyright (C) 2008-2014 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2014 Soft Ventures, Inc. All rights reserved.
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
 
 
/* Initialize Joomla framework */
define( '_JEXEC', 1 );


$path = dirname(__FILE__);
$path = str_replace('\\', '/', $path);
$jpath = str_replace('modules/mod_sv_reminders_sms', '', $path);

define('JPATH_BASE', $jpath );

define( 'DS', DIRECTORY_SEPARATOR );

/* Required Files */
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

/* Create the Application */
$mainframe = JFactory::getApplication('site');
$err = "";
$message = "";
$status = '';

$database = JFactory::getDBO(); 

jimport( 'joomla.application.module.helper' );
$module = JModuleHelper::getModule( 'mod_sv_reminders_sms');
$params = new JRegistry($module->params);
//print_r($params);
	if($params->get("test_mode", "") == "1"){
		echo "Running in Test Mode. <br/>Don't forget to turn off Test Mode before going into production.<br/>";
	} else {
		if($_SERVER['SERVER_NAME'] != ""){
		  die('This tool can only be executed from cron');
		} 
	}

	$hours_before = $params->get("hours_before", "");
	$component = $params->get("component", "");
	$version = $params->get("version", "");
	$no_call_before = $params->get("no_call_before");
	$no_call_after = $params->get("no_call_after");
	if($hours_before == ""){ $err .= "No hours before specified. <br>";}
	if($component == ""){ $err .= "No component specified. <br>";}
//	if($version == ""){ $err .= "No version specified. <br>";}
 
	//echo $no_call_before."<br>";
 	//echo $no_call_after."<br>";
	

 	$ary_hours_before = explode(",", $hours_before);
	if(count($ary_hours_before) == 0){ $err .= "Unable to interpret hours before entry. <br>";}
	foreach($ary_hours_before as $hours){
		if(!is_numeric($hours)){$err .= "Non-numeric value in hours before entry. <br>";}
		if(intval($hours) < 0){$err .= "hours before must be 0 or greater. <br>";}
		//$err .= $hours;
	}

	$language = JFactory::getLanguage();

	// get IDs of accepted bookings that start $hours_before now/
	if($component == "ABProJ30"){
		require_once ( JPATH_BASE .DS.'administrator'.DS.'components'.DS.'com_rsappt_pro3'.DS.'functions_pro2.php' );
		require_once ( JPATH_BASE .DS.'administrator'.DS.'components'.DS.'com_rsappt_pro3'.DS.'sendmail_pro2.php' );
		$language->load('com_rsappt_pro3', JPATH_SITE, null, true);
		$request_table = "#__sv_apptpro3_requests";
		$resource_table = "#__sv_apptpro3_resources";
		$config_table = "#__sv_apptpro3_config";
	} else {
		echo "No valid component";
		exit;
	}

	// get config info
	$sql = 'SELECT * FROM '.$config_table;
	$database->setQuery($sql);
	$config_data = NULL;
	$config_data = $database -> loadObject();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
	}
	

if($err != ""){
	echo $err;
} else {

	// need current local time based on server time adjusted by Joomla time zone setting
	
	// change for J1.6 timezones
//	$config =& JFactory::getConfig();
//	$tzoffset = $config->getValue('config.offset');      

	require_once( JPATH_SITE.DS.'configuration.php' );
	$CONFIG = new JConfig();
	$tzoffset = $CONFIG->offset;
	

	$site_config = JFactory::getConfig();
	$tzoffset = $site_config->get('offset');      
	$tz = new DateTimeZone($tzoffset);
	$localTimebyCity = new JDate("now", $tz);

	$reminder_log_time_format = "Y-m-d H:i:s";
	//echo $localTimebyCity->format($reminder_log_time_format, true, true);
	

	// if outside call hours just exit
	if(intval($localTimebyCity->format('H', true, true)) < $no_call_before){
		// leave quietly
		echo "too early exit";
		exit;
	}
	if(intval($localTimebyCity->format('H', true, true)) >= $no_call_after){
		// leave quietly
		echo "too late exit";
		exit;
	}
	
	$status = '';
	foreach($ary_hours_before as $hours){

		// look for startdate = current day and current hour + $hours = starthour (ignore minutes)
		$target_hour = intval($localTimebyCity->format('H')) + $hours;
		
		if($component == "ABProJ30"){
				$sql = "SELECT ".$request_table.".*, DATE_FORMAT(".$request_table.".startdate, '%W %M %e, %Y') as display_startdate, ".
					"DATE_FORMAT(".$request_table.".starttime, ' %l:%i %p') as display_starttime ,".
					$resource_table.".name AS resource_name ".
					"FROM (".$request_table." INNER JOIN ".$resource_table." ".
					" ON  ".$request_table.".resource = ".$resource_table.".id_resources )". 
					" WHERE ".$request_table.".id_requests IN (".
					"  SELECT id_requests FROM ".$request_table." WHERE request_status = 'accepted' ".
					"    AND '".$localTimebyCity->format('Y-m-d H', true, true)."' = DATE_FORMAT(SUBTIME(CONCAT(startdate, CONCAT(\" \",starttime)),\"0 ".$hours.":0:0\"),\"%Y-%m-%d %H\")) ";
		} else {
			// RBPro
			
		}

		//echo $sql;
		//exit;
		$database->setQuery($sql);
		$requests = NULL;
		$requests = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
		}
		//echo $sql."<br/>";	

		if(count($requests) == 0){
			$status .= "No bookings found for ".intval($hours)." hours out.";
			logReminder($status, -1, -1, "", $localTimebyCity->format($reminder_log_time_format, true, true));
			$status .= "<br>";

		} else {
			$k = 0;
			// dev only
			//ini_set ( "SMTP", "shawmail.cg.shawcable.net" ); 
	
			for($i=0; $i < count( $requests ); $i++) {
				$request = $requests[$i];
				$err = "";
				
				if($request->sms_phone == ""){
					// no sms_phone
					$err .= "No sms_phone, ";
				} else if($request->request_status != "accepted"){
					// is not 'accepted'?
					$err .= "Request status not 'Accepted', ";
				}
				if($err != ""){
					$status .= "Recipient: ". $request->name ." - ". $err." *** NO REMINDER SENT *** <br>";											
				} else {
					if($config_data->enable_clickatell == "Yes" || $config_data->enable_eztexting == "Yes" || $config_data->enable_twilio == "Yes"){
						if($component == "ABProJ30"){
							// verison 3.0
							$returnCode = "";								
							if(sv_sendSMS($request->id_requests, "reminder", $returnCode )){
								$line = "SMS to Recipient: ".stripslashes($request->name). ", ".$request->display_starttime." - Ok - Return Code: ".$returnCode;
								logReminder($line, $request->id_requests, $request->user_id, $request->name, $localTimebyCity->format($reminder_log_time_format, true, true));
								$status .= $line."<br>";
							} else {
								$line = "SMS to Recipient: ".stripslashes($request->name). ", ".$request->display_starttime." - Failed - Return Code: ".$returnCode;											
								logReminder($line, $request->id_requests, $request->user_id, $request->name, $localTimebyCity->format($reminder_log_time_format, true, true));
								$status .= $line."<br>";
							}
						}
					} else {
							$line = "SMS to Recipient: ".stripslashes($request->name). ", ".$request->display_starttime." - SMS Disabled";											
							logReminder($line, $request->id_requests, $request->user_id, $request->name, $localTimebyCity->format($reminder_log_time_format, true, true));
							$status .= $line."<br>";
					}				
				}
			}
		}

	}
}

	// dev only
	//ini_set ( "SMTP", "shawmail.cg.shawcable.net" ); 

	// email $status and/or $err to admin
	$mailer = JFactory::getMailer();
	
	$mailer->setSender($config_data->mailFROM);

	if($config_data->html_email == "Yes"){
		$mailer->IsHTML(true);
	}
	
	$mailTO = $params->get("mail_to", "");
	if($mailTO == ""){
		$mailTO = $config_data->mailTO;
	}
	$mailer->addRecipient($mailTO);
	$mailer->setSubject("SMS Reminder cron results");

	if($err==""){
		$message .= $status;
	} else {
		$message .= $err;
	}	
	$mailer->setBody($message);
	
	if($mailer->send() != true){
		echo "Error sending email";
	}
	
	echo $status;
	
	echo "Run completed";


	exit;	
	
	
?>