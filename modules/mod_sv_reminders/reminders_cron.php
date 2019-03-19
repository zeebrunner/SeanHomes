<?php
/*
 ****************************************************************
 Copyright (C) 2008-2012 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2009 Soft Ventures, Inc. All rights reserved.
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
  
/* Initialize Joomla framework */
define( '_JEXEC', 1 );


$path = dirname(__FILE__);
$path = str_replace('\\', '/', $path);
$jpath = str_replace('modules/mod_sv_reminders', '', $path);

define('JPATH_BASE', $jpath );

define( 'DS', DIRECTORY_SEPARATOR );

/* Required Files */
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

/* Create the Application */
$mainframe = JFactory::getApplication('site');
$err = "";
$message = "";

$database = JFactory::getDBO(); 

jimport( 'joomla.application.module.helper' );
$module = JModuleHelper::getModule( 'mod_sv_reminders');
$params = new JRegistry($module->params);
//print_r($params);
	if($params->get("test_mode", "") == "1"){
		echo "Running in Test Mode. <br/>Don't forget to turn off Test Mode before going into production.<br/>";
	} else {
		if($_SERVER['SERVER_NAME'] != ""){
		  die('This tool can only be executed from cron');
		} 
	}
	
	$days_before = $params->get("days_before", "");
	$component = $params->get("component", "");
	$results_to = $params->get("mail_to", "");
	//echo $component;
	$version = $params->get("version", "");
	//echo $version;
	if($days_before == ""){ $err .= "No days before specified. <br>";}
	if($component == ""){ $err .= "No component specified. <br>";}
	if($version == ""){ $err .= "No version specified. <br>";}
 	$ary_days_before = explode(",", $days_before);
	if(count($ary_days_before) == 0){ $err .= "Unable to interpret days before entry. <br>";}
	foreach($ary_days_before as $days){
		if(!is_numeric($days)){$err .= "Non-numeric value in days before entry. <br>";}
		if(intval($days) < 1){$err .= "Days before must be 1 or greater. <br>";}
		//$err .= $days;
	}

	$language = JFactory::getLanguage();
	// get IDs of accepted bookings that start $days_before now/
	if($component == "ABProJ30"){
		require_once ( JPATH_BASE .DS.'administrator'.DS.'components'.DS.'com_rsappt_pro3'.DS.'sendmail_pro2.php' );
		$language->load('com_rsappt_pro3', JPATH_SITE, null, true);
		$request_table = "#__sv_apptpro3_requests";
		$resource_table = "#__sv_apptpro3_resources";
		$config_table = "#__sv_apptpro3_config";
		$request_id = "id_requests";
		$resource_id = "id_resources";
	} else if($component == "RBProJ30"){
		require_once ( JPATH_BASE .DS.'administrator'.DS.'components'.DS.'com_rsbook_pro3'.DS.'sendmail_pro2.php' );
		$language->load('com_rsbook_pro3', JPATH_SITE, null, true);
		$request_table = "#__sv_bookpro3_requests";
		$resource_table = "#__sv_bookpro3_resources";
		$config_table = "#__sv_bookpro3_config";
		$request_id = "id_requests";
		$resource_id = "id_resources";
	} else {
		echo "No valid component";
		exit;
	}

	// get config info
	$sql = 'SELECT * FROM '.$config_table;
	$database->setQuery($sql);
	$config = NULL;
	$config = $database -> loadObject();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
		exit;
	}
	

if($err != ""){
	echo $err;
	exit;
} else {
	// need current loacal time based on server time adjusted by Joomla time zone setting
	$site_config = JFactory::getConfig();
	$tzoffset = $site_config->get('offset');      
	$tz = new DateTimeZone($tzoffset);
	$offsetdate = new JDate("now", $tz);
	$reminder_log_time_format = "Y-m-d H:i:s";

	$status = '';
	foreach($ary_days_before as $days){
		
		$sql = "SELECT ".$request_table.".*, DATE_FORMAT(".$request_table.".startdate, '%W %M %e, %Y') as display_startdate, ".
			"DATE_FORMAT(".$request_table.".starttime, ' %l:%i %p') as display_starttime ,".
			$resource_table.".name AS resource_name ".
			"FROM (".$request_table." INNER JOIN ".$resource_table." ".
			" ON  ".$request_table.".resource = ".$resource_table.".".$resource_id." )". 
			" WHERE ".$request_table.".".$request_id." IN (".
			"  SELECT ".$request_id." FROM ".$request_table." WHERE request_status = 'accepted' ".
			"  AND DATE_ADD(CURDATE(),INTERVAL ".intval($days)." DAY) = startdate)";
		$database->setQuery($sql);
		$requests = NULL;
		$requests = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
		}
		//echo $sql."<br/>";	

		if(count($requests) == 0){
			$status .= "No bookings found for ".intval($days)." days out.<br>";
			logReminder($status, -1, -1, "", $offsetdate->format($reminder_log_time_format, true, true));
		} else {
			$subject = JText::_('RS1_REMINDER_EMAIL_SUBJECT');
			
			$k = 0;
	
			for($i=0; $i < count( $requests ); $i++) {
				$request = $requests[$i];
				$err = "";
				if($request->email == ""){
					// no email address
					$err .= "No email address, ";
				} else if($request->request_status != "accepted"){
					// is not 'accepted'?
					$err .= "Request status not 'Accepted', ";
				}
				if($err != ""){
					$line = "Recipient: ". $request->email ." - ". $err." *** NO REMINDER SENT *** ";											
					logReminder($line, $request->$request_id, $request->user_id, $request->name, $offsetdate->format($reminder_log_time_format, true, true));
					$status .= $line."<br>";
				} else {
					if(sendMail($request->email, $subject, "reminder", $request->$request_id)){					 
						$line = "Recipient: ". $request->email . ", ".stripslashes($request->name). ", ".stripslashes($request->resource_name).", ".$request->display_starttime. ", ".$request->display_startdate." - Ok";											
						logReminder($line, $request->$request_id, $request->user_id, $request->name, $offsetdate->format($reminder_log_time_format, true, true));
						$status .= $line."<br>";
					} else {
						$line = "Recipient: ". $request->email . ", ".stripslashes($request->name). ", ".stripslashes($request->resource_name).", ".$request->display_starttime. ", ".$request->display_startdate." - Failed";											
						logReminder($line, $request->$request_id, $request->user_id, $request->name, $offsetdate->format($reminder_log_time_format, true, true));
						$status .= $line."<br>";
					}			
					
				}
			}

		}
	}
}

if($results_to != ""){
	$mailer = JFactory::getMailer();
	
	$mailer->setSender($config->mailFROM);

	if($config->html_email == "Yes"){
		$mailer->IsHTML(true);
	}


	if($err==""){
		$message .= $status;
	} else {
		$message .= $err;
	}	

	$mailer->addRecipient($results_to);
	$mailer->setSubject("Reminder cron results");
	$mailer->setBody($message);
	if($mailer->send() != true){
		logIt("Error sending email");
	}
}

echo $message;
echo "Run completed";

exit;	
	
?>