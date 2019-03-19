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



defined( '_JEXEC' ) or die( 'Restricted access' );
	include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );


//			JFactory::getDocument()->setMimeEncoding( 'text/html' );
	JFactory::getDocument()->setMimeEncoding( 'text/html' );
	$jinput = JFactory::getApplication()->input;
	
	// what this module does..
	// recives the user's selected resource and date
	// determine what day the date is
	// select timeslots for that day
	// select bookings for that date & resource
	// return a dataset of timeslot | availability
	// ex:
	//	08:00-09:30 | available
	//	09:30-11:00 | booked
	//	etc
	// OR
	// if caldays=yes, get the available days for a resource
	// if serv=yes, get the services for a resource
	
	
	// recives the user's selected resource and date
	$resource = $jinput->getInt('res');
	$cat = $jinput->getInt('cat',-1);
	$startdate = $jinput->getString('startdate');
	$browser = $jinput->getString('browser');
	$gad = $jinput->getWord('gad');
	$reg = $jinput->getWord('reg', 'No');
	$mobile = $jinput->getWord('mobile', 'No');
	$getcoup = $jinput->getWord('getcoup', 'No');
	$coupon_code = $jinput->getString('cc', '');
	$parent_cat_id = $jinput->getInt('cat', '');
	$service = $jinput->getInt('srv', '');
	$fd = $jinput->getWord('fd', 'No');
	$preset_service = $jinput->getInt('preset_service', '');
	$bk_date = $jinput->getString('bk_date');
	$element_name = $jinput->getWord('el_name', '');
	$getcert = $jinput->getWord('getcert', 'No');
	$gift_cert_code = $jinput->getString('gc', '');

	$database = JFactory::getDBO(); 
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		echo JText::_('RS1_SQL_ERROR');
		logIt($e->getMessage(), "getSlots", "", "");
		return false;
	}		

	if($jinput->getString('caldays') == "yes"){
		// ************************************
		// get calendar days for the resource
		// ************************************
		$ret_val = "";
		$database = JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_resources where id_resources = '.$resource;
		try{
			$database->setQuery($sql);
			$res_detail = NULL;
			$res_detail = $database -> loadObject();
		} catch (RuntimeException $e) {
			$ret_val .= JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		

		// clearDisabledDates added to CalendarPopup.js by rob, not in standard verison
		//$ret_val .= "cal.clearDisabledDates();"; 

		//$ret_val .= "cal.setWeekStartDay(".$apptpro_config->popup_week_start_day.");";
		$ret_val .= "jQuery( \"#".$element_name."\" ).datepicker( \"option\", \"firstDay\", ".$apptpro_config->popup_week_start_day."  );";

		// build list of days to disable on calendar
		$disableDays = "";
		if(	$res_detail->allowSunday=="No" ) $disableDays = $disableDays.("0");
		if(	$res_detail->allowMonday=="No" ) {
			if( $disableDays != "") $disableDays = $disableDays.",";
			$disableDays = $disableDays.("1");
		}
		if(	$res_detail->allowTuesday=="No" ) {
			if( $disableDays != "") $disableDays = $disableDays.",";
			$disableDays = $disableDays.("2");
		}
		if(	$res_detail->allowWednesday=="No" ) {
			if( $disableDays != "") $disableDays = $disableDays.",";
			$disableDays = $disableDays.("3");
		}
		if(	$res_detail->allowThursday=="No" ) {
			if( $disableDays != "") $disableDays = $disableDays.",";
			$disableDays = $disableDays.("4");
		}
		if(	$res_detail->allowFriday=="No" ) {
			if( $disableDays != "") $disableDays = $disableDays.",";
			$disableDays = $disableDays.("5");
		}
		if(	$res_detail->allowSaturday=="No" ) {
			if( $disableDays != "") $disableDays = $disableDays.",";
			$disableDays = $disableDays.("6");
		}
		
//		$ret_val .= "cal.setDisabledWeekDays(".$disableDays.");";
		$ret_val .= "non_booking_days = [".$disableDays."];";

		// check for book-offs
		$sql = "SELECT * FROM #__sv_apptpro3_bookoffs where resource_id = ".$resource.
		" AND off_date >= CURDATE() AND full_day='Yes' AND Published=1";
		try{
			$database->setQuery($sql);
			$bookoffs = NULL;
			$bookoffs = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			$ret_val .= JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}	
		$bo_dates = "";	
		for($i=0; $i < count( $bookoffs ); $i++) {
			$bookoff = $bookoffs[$i];
			//$ret_val .= "cal.addDisabledDates('".$bookoff->off_date."');"; 
			$bo_dates .= "'".$bookoff->off_date."',";
		}
		$ret_val .= "bookoff_dates = [".$bo_dates."];\n";

		$mindate = 999;
		$maxdate = 999;		
		if($res_detail->disable_dates_before != "Tomorrow" AND $res_detail->disable_dates_before != "Today" AND $res_detail->disable_dates_before != "XDays"){
			// use specific date
			// cal function actually disables up to the date, not date before
//			$day = strtotime($res_detail->disable_dates_before);
//			$day = $day - 86400;
//			$ret_val .= "cal.addDisabledDates(null,'".strftime("%Y-%m-%d", $day)."');"; 
			$now = time(); 
			$spec_date = strtotime($res_detail->disable_dates_before);
			$datediff = $spec_date - $now;
			$mindate = floor($datediff/(60*60*24))+1;			
		}
		if($res_detail->disable_dates_before == "XDays"){
//			$ret_val .= "var now = new Date();";
//			$ret_val .= "now.setDate(now.getDate()+".strval($res_detail->disable_dates_before_days).");";  
//			$ret_val .= "cal.addDisabledDates(null,formatDate(now,'yyyy-MM-dd'));"; 
			$mindate = $res_detail->disable_dates_before_days;			
		}
		if($res_detail->disable_dates_before == "Tomorrow"){
//			$ret_val .= "var now = new Date();";
//			$ret_val .= "cal.addDisabledDates(null,formatDate(now,'yyyy-MM-dd'));"; 
			$mindate = 1;
		}
		if($res_detail->disable_dates_before == "Today"){
//			$ret_val .= "var now = new Date();";
//			$ret_val .= "now.setDate(now.getDate()-1);";  
//			$ret_val .= "cal.addDisabledDates(null,formatDate(now,'yyyy-MM-dd'));"; 
			$mindate = 0;			
		}
		$ret_val .= "jQuery( \"#".$element_name."\" ).datepicker( \"option\", \"minDate\", ".$mindate." );";
		
		// set disable after as required
		if($res_detail->disable_dates_after != "Not Set" && $res_detail->disable_dates_after != "XDays"){
//			$day = strtotime($res_detail->disable_dates_after);
//			$day = $day + 86400;
//			$ret_val .= "cal.addDisabledDates('".strftime("%Y-%m-%d", $day)."', null);"; 
			$now = time(); 
			$spec_date = strtotime($res_detail->disable_dates_after);
			$datediff = $spec_date - $now;
			$maxdate = floor($datediff/(60*60*24))+1;						
		}
		if($res_detail->disable_dates_after == "XDays"){
//			$day = strtotime("now");
//			$day = $day + (86400*$res_detail->disable_dates_after_days);
//			$ret_val .= "cal.addDisabledDates('".strftime("%Y-%m-%d", $day)."', null);"; 
			$maxdate = $res_detail->disable_dates_after_days;
		}
		$ret_val .= "jQuery( \"#".$element_name."\" ).datepicker( \"option\", \"maxDate\", ".$maxdate." );";
		
		$ret_val .= "jQuery( \"#display_startdate\" ).val(\"".JText::_('RS1_INPUT_SCRN_DATE_PROMPT')."\");";
		 
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		$data = array(
   			'msg' => $ret_val
	    );
	    echo json_encode( $data );				
		jExit();

		
	} else if($jinput->getString('res') == "yes"){
		// ************************************
		// get resources for a category
		// ************************************
		$database = JFactory::getDBO(); 
		if($reg=='No'){
			//$andClause = " AND access != 'registered_only' ";
			$andClause = " AND access LIKE '%|1|%' ";
		} else {
			$andClause = " AND access != 'public_only' ";
		}

		$user = JFactory::getUser();		
		if($jinput->getString('fd', 'No') == "Yes"){
			// only resources for which user is res admin
			$andClause .= " AND resource_admins LIKE '%|".$user->id."|%' ";
		}
		$safe_search_string = '%|' . $database->escape( $cat, true ) . '|%' ;							
		$res_top_row = ( $gad=="Yes" ? JText::_('RS1_GAD_SCRN_RESOURCE_DROPDOWN'): JText::_('RS1_INPUT_SCRN_RESOURCE_PROMPT'));
		$sql = '(SELECT 0 as id_resources, \''.$res_top_row.'\' as name, \''.
		$res_top_row.'\' as description, 0 as ordering, "" as cost, \'|-1|\' as access, 0 as gap, "" as ddslick_image_path, "" as ddslick_image_text) '.
		'UNION (SELECT id_resources,name,description,ordering,cost,access,gap,ddslick_image_path,ddslick_image_text '.
		'FROM #__sv_apptpro3_resources WHERE published=1 '.$andClause.' AND category_scope LIKE '.$database->quote( $safe_search_string, false ).' ) ORDER BY ordering';
//		'FROM #__sv_apptpro3_resources WHERE published=1 '.$andClause.' AND category_scope LIKE "%|'.$cat.'|%" ) ORDER BY ordering';
//		'FROM #__sv_apptpro3_resources WHERE published=1 '.$andClause.' AND category_id = '.$cat.' ) ORDER BY ordering';
		try{
			$database->setQuery($sql);
			$res_rows = NULL;
			$res_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		echo '<select name="resources" id="resources" class="sv_apptpro_request_dropdown" onchange="changeResource()" '.
				($apptpro_config->enable_ddslick == "Yes"?" style=\"visibility:hidden; display:none\"":"") .
	  	    	'title="'.(blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_('RS1_INPUT_SCRN_RESOURCE_TOOLTIP')).'">';
					$k = 0;
					for($i=0; $i < count( $res_rows ); $i++) {
					$res_row = $res_rows[$i];
						if(display_this_resource($res_row, $user)){					
				          	echo '<option value='.$res_row->id_resources.'>'.JText::_(stripslashes($res_row->name));  echo ($res_row->cost==""?"":" - "); echo JText::_(stripslashes($res_row->cost)).'</option>\n';
						}
          			$k = 1 - $k; 
					} 
        echo '</select>';
		if($apptpro_config->enable_ddslick == "Yes"){
			echo '<select id="resources_slick" >';
			$k = 0;
			for($i=0; $i < count( $res_rows ); $i++) {
				$res_row = $res_rows[$i];
			
	            echo '<option value="'.$res_row->id_resources.'"'.
    	            ' data-imagesrc="'.($res_row->ddslick_image_path!=""?getResourceImageURL($res_row->ddslick_image_path):"").'" '.
                    ' data-description="'.$res_row->ddslick_image_text.'"> ';
                echo JText::_(stripslashes($res_row->name)).($res_row->cost==""?"":" - ").JText::_(stripslashes($res_row->cost)).'</option>';
				$k = 1 - $k; 
			 }
            echo '</select>';
      	}
		
	} else if($jinput->getString('getsubcats') == "yes"){
		// ************************************
		// get subcategory for a category
		// ************************************
		$database = JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_categories WHERE parent_category = '.$parent_cat_id.'  order by ordering';
		try{
			$database->setQuery($sql);
			$res_cats = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		if(count($res_cats) == 0){
			echo "";
		} else {
			echo "<select name=\"sub_category_id\" id=\"sub_category_id\" class=\"sv_apptpro_request_dropdown\" onchange=\"changeSubCategory('".$fd."');\" ".
			($apptpro_config->enable_ddslick == "Yes"?" style=\"visibility:hidden; display:none\"":"") .
	      	"title=\"".(blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_('RS1_INPUT_SCRN_RESOURCE_SUB_CATEGORIES_TOOLTIP'))."\" >\n ".
          	"<option value=\"0\">".JText::_('RS1_INPUT_SCRN_RESOURCE_SUB_CATEGORIES_PROMPT')."</option>\n";
			$k = 0;
			for($i=0; $i < count( $res_cats ); $i++) {
				$res_cat = $res_cats[$i];
          		echo "<option value=\"".$res_cat->id_categories."\" >".JText::_(stripslashes($res_cat->name))."</option>\n";
          		$k = 1 - $k; 
			}
        	echo "</select>\n";
			if($apptpro_config->enable_ddslick == "Yes"){
				echo '<select id="sub_category_id_slick" >';
	          	echo "<option value=\"0\">".JText::_('RS1_INPUT_SCRN_RESOURCE_SUB_CATEGORIES_PROMPT')."</option>";
				$k = 0;
				for($i=0; $i < count( $res_cats ); $i++) {
					$res_cat = $res_cats[$i];				
					echo '<option value="'.$res_cat->id_categories.'"'.
						' data-imagesrc="'.($res_cat->ddslick_image_path!=""?getResourceImageURL($res_cat->ddslick_image_path):"").'" '.
						' data-description="'.$res_cat->ddslick_image_text.'"> ';
					echo JText::_(stripslashes($res_cat->name)).'</option>';
					$k = 1 - $k; 
				 }
				echo '</select>';
			}
			
			echo "<div align=\"right\"></div>\n"; 
		}	


	} else if($jinput->getString('serv') == "yes"){
		// ************************************
		// get services for the resource
		// ************************************
		$user_id = $jinput->getString('uid', "");
		if($user_id == "" ){
			$user = JFactory::getUser();
			$user_id = $user->id;		
		}
		$ret_val = "";
			
		$database = JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_services where published = 1 ';
		if($fd == 'No'){
			$sql .= ' AND staff_only = "No"';
		}
		if($preset_service != ""){
			$sql .= ' AND id_services = '.$preset_service.' ';
		}
		if($cat > -1){
			$safe_search_string = '%|' . $database->escape( $cat, true ) . '|%' ;
			$sql .= ' AND (category_scope = \'\' OR category_scope LIKE '.$database->quote( $safe_search_string, false ).')';
		}
		$sql .= ' AND resource_id = '.$resource.' ORDER BY ordering' ;
		try{
			$database->setQuery($sql);
			$service_rows = NULL;
			$service_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			$ret_val = JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		if(count($service_rows) == 0){
			$ret_val .= "<input type='hidden' id='has_services' value='no' />";
		} else {
			$ret_val .= '<select name="service_name" id="service_name" class="sv_apptpro_request_dropdown'.($mobile=="Yes"?"_mobile":"").'" onchange="setDuration();calcTotal()"'.
					($apptpro_config->enable_ddslick == "Yes"?" style=\"visibility:hidden; display:none\"":"") .
					'title="'.(blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_('RS1_INPUT_SCRN_SERVICE_TOOLTIP')).'">';
						$k = 0;
						//echo '<option value="-1">Select a Service</option>';
						for($i=0; $i < count( $service_rows ); $i++) {
						$service_row = $service_rows[$i];
							if($preset_service != ""){
								$ret_val .= '<option value='.$service_row->id_services.' '.($preset_service==$service_row->id_services?' selected ':'').'>'.JText::_(stripslashes($service_row->name)).'</option>';
							} else {
								$ret_val .= '<option value='.$service_row->id_services.'>'.JText::_(stripslashes($service_row->name)).'</option>';
							}
						$k = 1 - $k; 
						} 
			$ret_val .= '</select>';
			if($apptpro_config->enable_ddslick == "Yes"){
				$ret_val .=  '<select id="service_name_slick" >';
				$k = 0;
				for($i=0; $i < count( $service_rows ); $i++) {
					$service_row = $service_rows[$i];
				
					$ret_val .=  '<option value="'.$service_row->id_services.'"'.
						' data-imagesrc="'.($service_row->ddslick_image_path!=""?getResourceImageURL($service_row->ddslick_image_path):"").'" '.
						' data-description="'.$service_row->ddslick_image_text.'"> ';
					$ret_val .=  JText::_(stripslashes($service_row->name)).'</option>';
					$k = 1 - $k; 
				 }
				$ret_val .=  '</select>';
			}			
		}	
	 			
		// get service rates and durations
		$database = JFactory::getDBO(); 
//		$sql = 'SELECT id,service_rate,service_rate_unit,resource_id FROM #__sv_apptpro3_services WHERE resource_id = '.$resource;
		$sql = 'SELECT id_services,service_rate,service_rate_unit,service_duration,service_duration_unit,resource_id,'.
		'service_eb_discount,service_eb_discount_unit,service_eb_discount_lead FROM #__sv_apptpro3_services WHERE resource_id = '.$resource;
		try{
			$database->setQuery($sql);
			$service_rates = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			$ret_val = JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		$serviceRatesArrayString = "<input type='hidden' id='service_rates' value='";
		$serviceDurationsArrayString = "<input type='hidden' id='service_durations' value='";
		$serviceEBDiscountArrayString = "<input type='hidden' id='service_eb_discount' value='";
		$base_rate = "0.00";
		for($i=0; $i<count($service_rates); $i++){
			if($apptpro_config->enable_overrides == "Yes"){
				$base_rate = getOverrideRate("service", $service_rates[$i]->id_services, $service_rates[$i]->service_rate, $user_id, "rate");
			} else {
				$base_rate = $service_rates[$i]->service_rate;
			}
			$serviceRatesArrayString = $serviceRatesArrayString.$service_rates[$i]->id_services.":".$base_rate.":".$service_rates[$i]->service_rate_unit."";
			if($i<count($service_rates)-1){
				$serviceRatesArrayString = $serviceRatesArrayString.",";
			}

			$serviceDurationsArrayString = $serviceDurationsArrayString.$service_rates[$i]->id_services.":".$service_rates[$i]->service_duration.":".$service_rates[$i]->service_duration_unit."";
			if($i<count($service_rates)-1){
				$serviceDurationsArrayString = $serviceDurationsArrayString.",";
			}

			if($apptpro_config->enable_eb_discount == "No"){
				$serviceEBDiscountArrayString = $serviceEBDiscountArrayString.$service_rates[$i]->id_services.":0.00:".$service_rates[$i]->service_eb_discount_unit.":".$service_rates[$i]->service_eb_discount_lead."";				
			} else {
				$serviceEBDiscountArrayString = $serviceEBDiscountArrayString.$service_rates[$i]->id_services.":".$service_rates[$i]->service_eb_discount.":".$service_rates[$i]->service_eb_discount_unit.":".$service_rates[$i]->service_eb_discount_lead."";
			}
			if($i<count($service_rates)-1){
				$serviceEBDiscountArrayString = $serviceEBDiscountArrayString.",";
			}

		}
		$serviceRatesArrayString = $serviceRatesArrayString."'>";
		$ret_val .= $serviceRatesArrayString."\n";
		$serviceDurationsArrayString = $serviceDurationsArrayString."'>";
		$ret_val .= $serviceDurationsArrayString."\n";
		$serviceEBDiscountArrayString = $serviceEBDiscountArrayString."'>";
		$ret_val .= $serviceEBDiscountArrayString."\n";

		echo $ret_val;
		jExit();

	} else if($jinput->getString('res_udfs') == "yes"){
		// ************************************
		// get udfs for the resource
		// ************************************
		$udf_help_icon = "<img alt=\"\" src='".getImageSrc("help_udf2.png")."' class='sv_help_icon' ";
		$out = "<table width='95%' cellpadding='0' cellspacing='0' class='table table-striped'>";
		$div_cal = "";
		if($apptpro_config->use_div_calendar == "Yes"){
			$div_cal = "'testdiv1'";
		}
		$udf_date_picker_format = "";
		switch ($apptpro_config->date_picker_format) {
			case "yy-mm-dd":
				$udf_date_picker_format = "yyyy-MM-dd";
				break;
			case "dd-mm-yy":
				$udf_date_picker_format = "dd-MM-yyyy";
				break;
			case "mm-dd-yy":
				$udf_date_picker_format = "MM-dd-yyyy";
				break;
			default:	
				$udf_date_picker_format = "yyyy-MM-dd";
				break;
		}

		$database = JFactory::getDBO(); 
		$safe_search_string = '%|' . $database->escape( $resource, true ) . '|%' ;							
		$sql = 'SELECT * FROM #__sv_apptpro3_udfs WHERE published=1'.
		' AND udf_show_on_screen="Yes" '.
		' AND scope LIKE '.$database->quote( $safe_search_string, false ).' ';
//		' AND scope LIKE \'%|'.$resource.'|%\' ';
		if($fd != "Yes"){ 
			$sql .= ' AND staff_only != "Yes" ';
		}		
		$sql .=	' ORDER BY ordering';
		try{
			$database->setQuery($sql);
			$udf_rows = NULL;
			$udf_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		if(count($udf_rows) == 0){
			echo "";
		} else {
			// these are res specific udfs, there may be global ones above these so we need to adjust the start count
			$sql = 'SELECT count(*) FROM #__sv_apptpro3_udfs WHERE published=1 AND udf_show_on_screen="Yes" ';
			if($fd != "Yes"){ 
				$sql .= ' AND staff_only != "Yes" ';
			}		
			$sql .= ' AND scope = "" ';
			try{
				$database->setQuery($sql);
				$udf_offset = $database -> loadResult();
			} catch (RuntimeException $e) {
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		

			$k = 0;
			for($i=0; $i < count( $udf_rows ); $i++) {
				$udf_row = $udf_rows[$i];
				$udf_number = $i + intval($udf_offset);
				// if cb_mapping value specified, fetch the cb data
				$user = JFactory::getUser();
				if($user->guest == false and $udf_row->cb_mapping != "" and $jinput->getString('user_field'.$udf_number.'_value', '') == ""){
					$udf_value = getCBdata($udf_row->cb_mapping, $user->id);
				} else if($user->guest == false and $udf_row->profile_mapping != "" and $jinput->getString('user_field'.$i.'_value', '') == ""){
					$udf_value = getProfiledata($udf_row->profile_mapping, $user->id);
				} else if($user->guest == false and $udf_row->js_mapping != "" and $jinput->getString('user_field'.$udf_number.'_value', '') == ""){
					$udf_value = getJSdata($udf_row->js_mapping, $user->id);
				} else {
					$udf_value = $jinput->getString('user_field'.$udf_number.'_value', '');
				}
					
				$out .= "<tr>";
				if($mobile=="Yes"){
					$out .= "<td><label id=user_field".$udf_number."_label  class=\"sv_apptpro_request_text\">".JText::_(stripslashes($udf_row->udf_label))."</label>";
				} else {
					$out .= "<td class=\"sv_apptpro_request_label\" valign=\"top\" width=\"20%\"><label id=user_field".$udf_number."_label  class=\"sv_apptpro_request_text\">".JText::_(stripslashes($udf_row->udf_label))."</label></td>".
							"<td valign=\"top\">";
				}
					if($udf_row->udf_type == "Textbox"){ 
						$out .= "<input name=\"user_field".$udf_number."_value\" id=\"user_field".$udf_number."_value\" type=\"text\" value=\"".$udf_value."\"". 
						"size=\"".$udf_row->udf_size."\" maxlength=\"255\"";
                     				if($udf_row->udf_placeholder_text != ""){$out.=" placeholder='".$udf_row->udf_placeholder_text."' ";} 						
						if($udf_row->read_only == "Yes" && $udf_row->cb_mapping != "" && $user->guest == false){$out.=" readonly=\"readonly\" ";}
						$out .= " class=\"sv_apptpro_request_text\" title=\"".JText::_(stripslashes($udf_row->udf_tooltip))."\"/>".
						"<input type=\"hidden\" name=\"user_field".$udf_number."_is_required\" id=\"user_field".$udf_number."_is_required\" value=\"".$udf_row->udf_required."\" />";
					} else if($udf_row->udf_type == "Textarea"){
						$out .= "<textarea name=\"user_field".$udf_number."_value\" id=\"user_field".$udf_number."_value\""; 
                     				if($udf_row->udf_placeholder_text != ""){$out.=" placeholder='".$udf_row->udf_placeholder_text."' ";} 						
						if($udf_row->read_only == "Yes" && $udf_row->cb_mapping != "" && $user->guest == false){$out.=" readonly=\"readonly\" ";}
						$out.=" rows=\"".$udf_row->udf_rows."\" cols=\"".$udf_row->udf_cols."\" ". 
						" class=\"sv_apptpro_request_text\" title=\"".JText::_(stripslashes($udf_row->udf_tooltip))."\"/>".$udf_value."</textarea> ".
						" <input type=\"hidden\" name=\"user_field".$udf_number."_is_required\" id=\"user_field".$udf_number."_is_required\" value=\"".$udf_row->udf_required."\" />";
					} else if($udf_row->udf_type == "Radio"){ 
						$col_count = 0;
						$aryButtons = explode(",", JText::sprintf("%s",stripslashes($udf_row->udf_radio_options)));
						$out .="<table class='sv_udf_radio_table'><tr><td>";
						foreach ($aryButtons as $button){ 
							$col_count++; 
							$out .="<input name=\"user_field".$udf_number."_value\" type=\"radio\" id=\"user_field".$udf_number."_value\""; 
							if(strpos($button, "(d)")>-1){
								$out .=	" checked=checked ";
								$button = str_replace("(d)","", $button);
							}
							$out .= " value=\"".stripslashes(trim($button))."\" title=\"".JText::_(stripslashes($udf_row->udf_tooltip))."\"/> ";
							$out .= "<span class='sv_udf_radio_text'>".JText::_(stripslashes(trim($button)))."</span>";
                            if($col_count >= $udf_row->udf_cols){$col_count = 0; $out .= "</td></tr><tr><td>";}else{$out .= "</td><td>";}
							//JText::_(stripslashes(trim($button)))."<br /> ";
						}
                        $out .="</tr></table>";
						$out .= " <input type=\"hidden\" name=\"user_field".$udf_number."_is_required\" id=\"user_field".$udf_number."_is_required\" value=\"".$udf_row->udf_required."\" />";
					} else if($udf_row->udf_type == "List"){ 
							$aryOptions = explode(",", JText::sprintf("%s",stripslashes($udf_row->udf_radio_options)));
							$out .= " <select name=\"user_field".$udf_number."_value\" id=\"user_field".$udf_number."_value\" class=\"sv_apptpro_request_dropdown\" ".
							"title=\"".(blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_(stripslashes($udf_row->udf_tooltip)))."\"> "; 
							foreach ($aryOptions as $option){
								$out .= "<option value=\"".$option."\"";
								if(strpos($option, "(d)")>-1){
									$out .= " selected=true ";
									$option = str_replace("(d)","", $option);
								}
								$out .= ">".JText::_(stripslashes($option))."</option>";
							}              
							$out .= "</select>";                 
					} else if($udf_row->udf_type == 'Date'){
						$out .= "<script>";
						$out .= "	var cal".$udf_number." = new CalendarPopup(".$div_cal.");";
						$out .= "	cal".$udf_number.".showYearNavigation();";
						$out .= "	cal".$udf_number.".setCssPrefix(\"TEST\");";
						$out .= "</script>";
						$out .= "<input readonly=\"readonly\" name=\"user_field".$udf_number."_value\" id=\"user_field".$udf_number."_value\" type=\"text\"";
			                        $out .= "  class=\"sv_date_box\" size=\"10\" maxlength=\"10\" value=\"\"/>";
			                        $out .= "  &nbsp;<a href=\"#\" id=\"anchor10".$udf_number."\" onclick=\"cal".$udf_number.".select(document.forms['frmRequest'].user_field".$udf_number."_value,'anchor10".$udf_number."','".$udf_date_picker_format."'); return false;\"";
			                        $out .= " name=\"anchor10".$udf_number."\"><img height='15' hspace='2' src='".JURI::base()."components/com_rsappt_pro3/icon_cal.gif' width='16' border='0'></a> ";
   						$out .= " <input type=\"hidden\" name=\"user_field".$udf_number."_is_required\" id=\"user_field".$udf_number."_is_required\" value=\"".$udf_row->udf_required."\" />";
						 
					} else if($udf_row->udf_type == 'Content'){ 
	                    $out .= "<label>".JText::_($udf_row->udf_content)."</label>";
                    	$out .= "<input type=\"hidden\" name=\"user_field".$udf_number."_value\" id=\"user_field".$udf_number."_value\" value=\"".JText::_(htmlentities($udf_row->udf_content, ENT_QUOTES, "UTF-8"))."\"> ";
	   					$out .= "<input type=\"hidden\" name=\"user_field".$udf_number."_type\" id=\"user_field".$udf_number."_type\" value='Content'> ";
					} else {
						$out .= "<input name=\"user_field".$udf_number."_value\" id=\"user_field".$udf_number."_value\" type=\"checkbox\" value=\"Checked\" ".
						" title=\"".JText::_(stripslashes($udf_row->udf_tooltip))."\"/>".
						" <input type=\"hidden\" name=\"user_field".$udf_number."_is_required\" id=\"user_field".$udf_number."_is_required\" ".
						" value=\"".$udf_row->udf_required."\" /> ";
					}    
					$out .= " <input type=\"hidden\" name=\"user_field".$udf_number."_udf_id\" id=\"user_field".$udf_number."_udf_id\" ".
					"value=\"".$udf_row->id_udfs."\" /> ";

					if($udf_row->udf_help != "" && $udf_row->udf_help_as_icon == "Yes" ){      
						//$out.= $udf_help_icon." title='".JText::_(stripslashes($udf_row->udf_help))."'>";
						$out .= $udf_help_icon." id='opener".$udf_number."' title='".JText::_('RS1_INPUT_SCRN_CLICK_FOR_HELP')."'>";		
						$out .= "<div id=\"udf_help".$udf_number."\" title=\"".JText::_(stripslashes($udf_row->udf_label))."\">".JText::_(stripslashes($udf_row->udf_help))."</div>";	
							$out .= "<script>";
							$out .= "jQuery( \"#udf_help".$udf_number."\" ).dialog({ autoOpen: false, ";
							$out .= "  position:{";
							$out .= "    my: \"left+10 top+5\",";
							$out .= "    of: \"#opener".$udf_number."\",";
							$out .= "    collision: \"fit\"";
							$out .= "  }";
							$out .= "});";							
							$out .= "jQuery( \"#opener".$udf_number."\" ).click(function() { ";
							$out .= "   jQuery( \"#udf_help".$udf_number."\" ).dialog( \"open\" );";
							if($udf_row->udf_help_format == "Link"){					
								$out .= "jQuery( \"#udf_help".$udf_number."\" ).load(\"".JText::_(stripslashes($udf_row->udf_help))."\", function() {});";
							}
							$out .= "});";	
							$out .= "</script>";
					} 	
					
				$out .= "</td>".	
				"</tr>";
            	if($udf_row->udf_help_as_icon == "No" && $udf_row->udf_help != ""){ 				
					$out .=	"<tr>".
						"<td></td><td colspan=\"3\" valign=\"top\" class=\"sv_apptpro_request_helptext\">".JText::_(stripslashes($udf_row->udf_help))."</td>".
					"</tr>";
				}
				$k = 1 - $k; 
			}	
	 	}
		if($out == "<table>"){
			$out="";
		} else {
			$out .= "</table>";
			$out .= "<input type=\"hidden\" id=\"res_udf_count\" name=\"res_udf_count\" value=\"".count($udf_rows)."\" />";
		}
		echo $out;				
		jExit();

	} else if($jinput->getString('res_seats') == "yes"){
		// ************************************
		// get seat types for the resource
		// ************************************
		$user_id = $jinput->getInt('uid', "");
		if($user_id == "" ){
			$user = JFactory::getUser();
			$user_id = $user->id;		
		}
		$out = "<table>\n";

		// get seat types
		$database = JFactory::getDBO(); 
		$safe_search_string = '%|' . $database->escape( $resource, true ) . '|%' ;							
		$sql = 'SELECT * FROM #__sv_apptpro3_seat_types WHERE published=1 AND (scope = "" OR scope LIKE '.$database->quote( $safe_search_string, false ).') ORDER BY ordering';
		try{
			$database->setQuery($sql);
			$seat_type_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		$si = 0; 
		$base_rate = "0.00";
		$user = JFactory::getUser();		
		foreach($seat_type_rows as $seat_type_row){
			if($apptpro_config->enable_overrides == "Yes"){
				$base_rate = getOverrideRate("seat", $seat_type_row->id_seat_types, $seat_type_row->seat_type_cost, $user_id, "rate");
			} else {
				$base_rate = $seat_type_row->seat_type_cost;
			}
			$out .= "<tr class=\"seats_block\"> \n".
				"<td class=\"sv_apptpro_request_label\">".JText::_($seat_type_row->seat_type_label).":</td>\n".
			  	"<td colspan=\"3\" valign=\"top\">\n".
			  	"<select name=\"seat_".$si."\" id=\"seat_".$si."\" onChange=\"calcTotal();\" class=\"sv_apptpro_request_dropdown\" ". 
				"title=\"".(blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_($seat_type_row->seat_type_tooltip))."\" style=\"width:auto; min-width:50px; text-align:center\"/>\n";
				for($i=$seat_type_row->default_quantity; $i<=$seat_type_row->seat_group_max; $i++){ 
					$out .=	"<option value=".$i.">".$i."</option>\n";	        
				}
			   $out .= "</select>\n". 
				"&nbsp;".JText::_($seat_type_row->seat_type_help)." \n".
				"<input type=\"hidden\" name=\"seat_type_cost_".$si."\" id=\"seat_type_cost_".$si."\" value=\"".$base_rate."\"/>\n".  
				"<input type=\"hidden\" name=\"seat_type_id_".$si."\" id=\"seat_type_id_".$si."\" value=\"".$seat_type_row->id_seat_types."\"/>\n".  
				"<input type=\"hidden\" name=\"seat_group_".$si."\" id=\"seat_group_".$si."\" value=\"".$seat_type_row->seat_group."\"/>\n".  
			  " </td>\n".
			"</tr>\n";
			$si += 1; 
		} 
		if($si>0){  
			$out .= "<tr class=\"seats_block\">\n".
			  "<td class=\"sv_apptpro_request_label\">".JText::_('RS1_INPUT_SCRN_TOTAL_SEATS').":</td>\n".
			  "<td colspan=\"3\" valign=\"top\"><div id=\"booked_seats_div\" name=\"booked_seats_div\" style=\"padding-left:5px\"></div>\n".
			  "<input type=\"hidden\" name=\"booked_seats\" id=\"booked_seats\" value=\"1\"/>  </td>\n".
			"</tr>\n";
		}

		if($out == "<table>\n"){
			$out="";
		} else {
			$out .= "</table>\n";
			$out .= "<input type=\"hidden\" name=\"seat_type_count\" id=\"seat_type_count\" value=\"".count($seat_type_rows)."\">\n";
		}
		echo $out;				
		jExit();
		

	} else if($jinput->getString('extras') == "yes"){
		// ************************************
		// get extras for the resource
		// ************************************
		$user_id = $jinput->getInt('uid', "");
		if($user_id == "" ){
			try{
				$user = JFactory::getUser();
				$user_id = $user->id;		
			} catch (RuntimeException $e) {
				logIt($e->getMessage(), "getSlots, get extras, get user", "", "");
				echo json_encode("Error on getting user id");
				jExit();
			}			
		}

		$out = "<table cellspacing=\"4\" cellpadding=\"4\">\n";

		$database = JFactory::getDBO(); 
		$safe_search_string = '%|' . $database->escape( $resource, true ) . '|%' ;							
		$sql = 'SELECT * FROM #__sv_apptpro3_extras WHERE published=1 ';
		if($fd == 'No'){
			$sql .= ' AND staff_only = "No" ';
		}
		$sql .= 'AND (resource_scope = "" OR resource_scope LIKE '.$database->quote( $safe_search_string, false ).' ';
		$sql .= ") ORDER BY ordering";
		try{
			$database->setQuery($sql);
			$extras_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			jExit();
		}		

		$si = 0; 
		if(count($extras_rows)>0){  
			if($mobile=="Yes"){
				$out .= "<tr class=\"extras_block\">\n".
				  "<td class=\"sv_apptpro_request_label\" width=\"20%\">".JText::_('RS1_INPUT_SCRN_EXTRAS_LABEL')."\n</td>\n".
				"</tr>\n";
			} else {
				$out .= "<tr class=\"extras_block\">\n".
				  "<td class=\"sv_apptpro_request_label\" width=\"20%\">".JText::_('RS1_INPUT_SCRN_EXTRAS_LABEL')."</td>\n".
				  "<td colspan=\"3\" valign=\"top\"></td>\n".
				"</tr>\n";
			}
			$base_rate = "0.00";
			$user = JFactory::getUser();		
			foreach($extras_rows as $extras_row){
				if($apptpro_config->enable_overrides == "Yes"){
					$base_rate = getOverrideRate("extra", $extras_row->id_extras, $extras_row->extras_cost, $user_id, "rate");
				} else {
					$base_rate = $extras_row->extras_cost;
				}
				if($mobile=="Yes"){
					$out .= "<tr class=\"extras_block\"> \n".
						"<td class=\"sv_apptpro_request_label\" width=\"20%\"><div id=\"extras_label_".$si."\">".JText::_($extras_row->extras_label)."</div>\n";
				} else {
					$out .= "<tr class=\"extras_block\"> \n".
						"<td class=\"sv_apptpro_request_label\" width=\"20%\"><div id=\"extras_label_".$si."\">".JText::_($extras_row->extras_label)."</div></td>\n".
						"<td colspan=\"2\" valign=\"top\">\n";
				}
					if($extras_row->max_quantity == 1){
						// display as checkbox
						$out .= "<input type=\"checkbox\"  name=\"extra_".$si."\" id=\"extra_".$si."\" onChange=\"changeExtra();\" ". 
						($extras_row->default_quantity==1?" checked ":"").
						"title='".JText::_($extras_row->extras_tooltip)."'  />\n";
						
					} else {
						// display as dropdown list
						$out .= "<select name=\"extra_".$si."\" id=\"extra_".$si."\" onChange=\"changeExtra();\" class=\"sv_apptpro_request_dropdown\" ". 
						"title='".(blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_($extras_row->extras_tooltip))."' style=\"width:auto; min-width:50px; text-align:center\" />\n";
						for($i=$extras_row->min_quantity; $i<=$extras_row->max_quantity; $i++){ 
							$out .=	"<option value=".$i.($i==$extras_row->default_quantity?" selected":"").">".$i."</option>\n";	        
						}
					   $out .= "</select>\n";
					}
					$out .= "&nbsp;<span id=extras_help_".$si.">".JText::_($extras_row->extras_help)." </span>\n".
					"<input type=\"hidden\" name=\"extras_cost_".$si."\" id=\"extras_cost_".$si."\" value=\"".$base_rate."\"/>\n".  
					"<input type=\"hidden\" name=\"extras_cost_unit_".$si."\" id=\"extras_cost_unit_".$si."\" value=\"".$extras_row->cost_unit."\"/>\n".  
					"<input type=\"hidden\" name=\"extras_id_".$si."\" id=\"extras_id_".$si."\" value=\"".$extras_row->id_extras."\"/>\n".  
					"<input type=\"hidden\" name=\"extras_duration_".$si."\" id=\"extras_duration_".$si."\" value=\"".$extras_row->extras_duration."\"/>\n".  
					"<input type=\"hidden\" name=\"extras_duration_unit_".$si."\" id=\"extras_duration_unit_".$si."\" value=\"".$extras_row->extras_duration_unit."\"/>\n".  
					"<input type=\"hidden\" name=\"extras_duration_effect_".$si."\" id=\"extras_duration_effect_".$si."\" value=\"".$extras_row->extras_duration_effect."\"/>\n".  
				  " </td>\n".
				"</tr>\n";
				$si += 1; 
			} 
	
			if($out == "<table>\n"){
				$out="";
			} else {
				$out .= "</table>\n";
			}
			$out .= "<input type=\"hidden\" name=\"extras_count\" id=\"extras_count\" value=\"".count($extras_rows)."\">\n";
		    echo $out;				
		}
		jExit();


	} else if($jinput->getString('adminserv') == "yes"){
		// ************************************
		// get services for the resource (admin side)
		// ************************************
	
		$database = JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_services where published = 1 AND resource_id = '.$resource;
		$database->setQuery($sql);
		try{
			$service_rows = NULL;
			$service_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		if(count($service_rows) > 0){
			// new Option(text, value, defaultSelected, selected)
			$k = 0;
			for($i=0; $i < count( $service_rows ); $i++) {
				$service_row = $service_rows[$i];
					echo 'document.getElementById("service").options['.$i.']=new Option("'.stripslashes($service_row->name).'", "'.$service_row->id_services.'", false, false);';
				$k = 1 - $k; 
			} 
		}	
		exit;

	} else if($jinput->getString('getcoup') == "yes"){
		// ************************************
		// get coupon details
		// ************************************
		// also in json
		
		// To make coupon code case insensitive remove the BINARY from the three(3) queries below.
		$database = JFactory::getDBO(); 
		$sql = "SELECT *, DATE_FORMAT(expiry_date, '%Y-%m-%d') as expiry FROM #__sv_apptpro3_coupons where BINARY coupon_code = '".$coupon_code."' and published=1";
		try{
			$database->setQuery($sql);
			$coupon_detail = NULL;
			$coupon_detail = $database -> loadObject();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		$coupon_refused = false;
		
		// check scope
		if($coupon_detail != NULL && $coupon_detail->scope != ""){
			// one or more resources hae been specified
			if(strpos($coupon_detail->scope, '|'.$resource.'|') === false){
				// coupon not valis for this resource
				echo JText::_('RS1_INPUT_SCRN_COUPON_INVALID_4_RESOURCE')."|0|";
				$coupon_refused = true;
			}				 			
		}
		if($coupon_detail == NULL){
			echo JText::_('RS1_INPUT_SCRN_COUPON_INVALID')."|0|";
			$coupon_refused = true;
		} else if(!strncmp($coupon_detail->expiry, "0000-00-00", 10) != "0000-00-00" && strtotime("now") > strtotime($coupon_detail->expiry)){
			echo JText::_('RS1_INPUT_SCRN_COUPON_EXPIRED')."|0|";
			$coupon_refused = true;
		} else if($coupon_detail->valid_range_start != "" && $coupon_detail->valid_range_start != "0000-00-00" && strtotime($bk_date) < strtotime($coupon_detail->valid_range_start)){
				echo JText::_('RS1_INPUT_SCRN_COUPON_NOT_IN_RANGE')."|0|";
				$coupon_refused = true;
		} else if($coupon_detail->valid_range_end != "" && $coupon_detail->valid_range_end != "0000-00-00" && strtotime($bk_date) > strtotime($coupon_detail->valid_range_end)){
				echo JText::_('RS1_INPUT_SCRN_COUPON_NOT_IN_RANGE')."|0|";
				$coupon_refused = true;
		} else {		
			// Check for Max Total Usage
			if($coupon_detail->max_total_use > 0){
				// get total useage count
				$sql = "SELECT count(*) FROM #__sv_apptpro3_requests WHERE BINARY coupon_code = '".$coupon_code."' ".
					" AND (".
					"	request_status = 'accepted' ".
					" 	OR request_status = 'attended' ".
					" 	OR request_status = 'completed' ".
					")";
				try{
					$database->setQuery($sql);
					$coupon_count = NULL;
					$coupon_count = $database -> loadResult();
				} catch (RuntimeException $e) {
					echo JText::_('RS1_SQL_ERROR');
					logIt($e->getMessage(), "getSlots", "", "");
					return false;
				}		
				if($coupon_count >= $coupon_detail->max_total_use){
					echo JText::_('RS1_INPUT_SCRN_COUPON_MAXED_OUT')."|0|";
					$coupon_refused = true;
				}
			}		

			// Check for Max User Usage
			$user = JFactory::getUser();
			if($coupon_detail->max_user_use > 0 and $user->guest == false){
				if($jinput->getString('uid',"-1") != "-1"){
					// call is from the staff booking screen so we check the user_id passed in rather than than operator.
					$user_to_check = $jinput->getInt('uid', -1);
				} else {
					$user_to_check = $user->id;
				}
				// get total useage count
				$sql = "SELECT count(*) FROM #__sv_apptpro3_requests WHERE BINARY coupon_code = '".$coupon_code."' ".
					" AND user_id = ".$user_to_check." ".
					" AND (".
					"	request_status = 'accepted' ".
					" 	OR request_status = 'attended' ".
					" 	OR request_status = 'completed' ".
					")";
				try{
					$database->setQuery($sql);
					$coupon_count = NULL;
					$coupon_count = $database -> loadResult();
				} catch (RuntimeException $e) {
					echo JText::_('RS1_SQL_ERROR');
					logIt($e->getMessage(), "getSlots", "", "");
					return false;
				}		
				if($coupon_count >= $coupon_detail->max_user_use){
					echo JText::_('RS1_INPUT_SCRN_COUPON_MAXED_OUT')."|0|";
					$coupon_refused = true;
				}
			}		
			
		}
					
		if($coupon_refused == false){
			echo JText::_($coupon_detail->description)."|".$coupon_detail->discount."|".$coupon_detail->discount_unit;
		}
		exit;

	} else if($jinput->getString('getcert') == "yes"){
		// ************************************
		// get gift certificate balance
		// ************************************
		// not yet..also in json
		
		// To make coupon code case insensitive remove the BINARY from the three(3) queries below.
		$database = JFactory::getDBO(); 
		$sql = "SELECT balance, gift_cert_name FROM #__sv_apptpro3_user_credit where BINARY gift_cert = '".$gift_cert_code."' ";//and published=1";
		try{
			$database->setQuery($sql);
			$gift_cert_detail = NULL;
			$gift_cert_detail = $database -> loadObject();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		
		if($gift_cert_detail == NULL){
			echo JText::_('RS1_INPUT_SCRN_GIFT_CERT_INVALID')."|-1|";
		} else {
			echo JText::_('RS1_INPUT_SCRN_GIFT_CERT_BALANCE')."|".$gift_cert_detail->balance."|";
		}
		exit;

	} else {
		// ************************************
		// get slots
		// ************************************
		
		require_once( JPATH_CONFIGURATION.'/configuration.php' );
		$CONFIG = new JConfig();
		$timezone_identifier = $CONFIG->offset;
		$options = "";			
		
		// determine what day the date is
		$day = date("w", strtotime($startdate)); 
	
		$database = JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_config';
		try {
			$database->setQuery($sql);
			$apptpro_config = NULL;
			$apptpro_config = $database -> loadObject();	
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}		

		// get resource info for the selected resource
		$sql = 'SELECT * FROM #__sv_apptpro3_resources where id_resources = '.$resource;
		try{
			$database->setQuery($sql);
			$res_detail = NULL;
			$res_detail = $database -> loadObject();
		} catch (RuntimeException $e) {
			echo JText::_('RS1_SQL_ERROR');
			logIt($e->getMessage(), "getSlots", "", "");
			return false;
		}					
		
		// If Max Seats = 0 we can use th emobile app's logic to fetch slots - this suppors service based duration.
		if($res_detail->max_seats == 1){ 
			// code from mobile app
			$day_off = false;
			$sql = "SELECT id_timeslots as id, timeslot_starttime, timeslot_endtime from  #__sv_apptpro3_timeslots ".
			" WHERE published = 1 ";
			if($res_detail->timeslots == "Global"){
				$sql .= " AND resource_id = 0";
			} else {
				$sql .= " AND resource_id = ".$resource;
			}
			$sql .= " AND day_number = ".$day. 
			" AND (start_publishing is null OR start_publishing = '0000-00-00' OR start_publishing < '".$startdate."') ".
			" AND (end_publishing is null OR end_publishing = '0000-00-00' OR end_publishing > '".$startdate."')".
			" AND staff_only = 'No' ORDER BY timeslot_starttime";
			try{
				$database->setQuery($sql);
				$timeslot_list = $database->loadObjectList();
			} catch (RuntimeException $e) {
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		

			if(count($timeslot_list) == 0){
				echo JText::_('RS1_NO_TIMESLOTS');
				jExit();
				//exit;
			}
			
			// now get bookings
			$sql = "SELECT *, id_requests as id FROM #__sv_apptpro3_requests WHERE resource = ".$resource." AND (request_status = 'accepted' OR request_status = 'pending' ".($apptpro_config->block_new=="Yes"?"OR request_status='new'":"").") ".
				" AND startdate = '".$startdate."' ".
				" ORDER BY starttime";
			try
			{
				$database->setQuery($sql);
				$bookings_list = $database->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		
			//print_r($bookings_list);
			//exit;
	
			// now get book-offs
			$sql = "SELECT id_bookoffs as id, full_day, bookoff_starttime, bookoff_endtime FROM #__sv_apptpro3_bookoffs WHERE off_date = '".$startdate."' AND resource_id = ".$resource." AND published = 1 ".
				" ORDER BY bookoff_starttime";
			try
			{
				$database->setQuery($sql);
				$bookoff_list = $database->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}					
	
			if(count($bookoff_list) >0){
				foreach($bookoff_list as $bookoff){
					if($bookoff->full_day == "Yes"){
						// thats it we're outa here, its a full day book off
						$day_off = true;
						//echo "Unavailable Day";
						//return;
					}
				}
			}
			if(!$day_off){
				if(count($bookings_list) == 0 && count($bookoff_list) == 0){
					// no bookings or book-offs send all timeslots
					$sql = "SELECT id_timeslots as id, timeslot_starttime, timeslot_endtime, timeslot_description, day_number, ";
					if($apptpro_config->timeFormat == '12'){							
						$sql .=	"CONCAT( DATE_FORMAT(timeslot_starttime, '%l:%i%p'), ' - ', DATE_FORMAT(timeslot_endtime, '%l:%i%p') ) as startendtime, ".
						"DATE_FORMAT(timeslot_starttime, '%l:%i%p') as display_starttime, ".
						"DATE_FORMAT(timeslot_endtime, '%l:%i%p') as display_endtime, ";
					} else {
						$sql .=	"CONCAT( DATE_FORMAT(timeslot_starttime, '%H:%i'), ' - ', DATE_FORMAT(timeslot_endtime, '%H:%i') ) as startendtime, ".
						"DATE_FORMAT(timeslot_starttime, '%H:%i') as display_starttime, ".
						"DATE_FORMAT(timeslot_endtime, '%H:%i') as display_endtime, ";
					}					
					$sql .=	"'Available' as booked from #__sv_apptpro3_timeslots ".
					" WHERE published = 1 ".
					" AND day_number = ".$day;
					if($res_detail->timeslots == "Global"){
						$sql .= " AND resource_id = 0";
					} else {
						$sql .= " AND resource_id = ".$resource;
					}					
					$sql .= " AND (start_publishing is null OR start_publishing = '0000-00-00' OR start_publishing < '".$startdate."') ".
					" AND (end_publishing is null OR end_publishing = '0000-00-00' OR end_publishing > '".$startdate."')".
					"  AND staff_only = 'No' ORDER BY timeslot_starttime";
				} else {				
					// get bookoff blocked ids
					$ts_blocked_ids = "";
					foreach($timeslot_list as $time_slot){
						foreach($bookoff_list as $bookoff){
							if( strtotime($bookoff->bookoff_starttime) == strtotime($time_slot->timeslot_starttime) 
								&& strtotime($bookoff->bookoff_endtime) == strtotime($time_slot->timeslot_endtime)){
									// bkg start & end = ts start & end (bookoff = timeslot)
									$ts_blocked_ids .= $time_slot->id.",";
								} else if( strtotime($bookoff->bookoff_starttime) == strtotime($time_slot->timeslot_starttime)){
									// bkg starts at ts start
									$ts_blocked_ids .= $time_slot->id.",";
								} else if( strtotime($bookoff->bookoff_endtime) == strtotime($time_slot->timeslot_endtime)){
									// bkg end at ts end
									$ts_blocked_ids .= $time_slot->id.",";
								} else if( strtotime($bookoff->bookoff_starttime) > strtotime($time_slot->timeslot_starttime) 
									&& strtotime($bookoff->bookoff_starttime) < strtotime($time_slot->timeslot_endtime)){
									// bkg start > ts start and < ts end (bookoff starts in a timeslot)
									$ts_blocked_ids .= $time_slot->id.",";
								} else if( strtotime($bookoff->bookoff_endtime) > strtotime($time_slot->timeslot_starttime) 
									&& strtotime($bookoff->bookoff_endtime) < strtotime($time_slot->timeslot_endtime)){
									// bkg end > ts start and < ts end (bookoff ends in a timeslot)
									$ts_blocked_ids .= $time_slot->id.",";
								} else if( strtotime($bookoff->bookoff_starttime) < strtotime($time_slot->timeslot_starttime) 
									&& strtotime($bookoff->bookoff_endtime) > strtotime($time_slot->timeslot_endtime)){
									// bkg start < ts start and bkg end > ts end (bookoff covers a timeslot)
									$ts_blocked_ids .= $time_slot->id.",";
								}
							}						
						}
	
					// get booked ids
					if($res_detail->max_seats > 0){ // this version not compatible with max_seats > 1
						$ts_booked_ids = "";
						foreach($timeslot_list as $time_slot){
							foreach($bookings_list as $booking){
								if(fullyBooked($booking, $res_detail, $apptpro_config)){
									if( strtotime($booking->starttime) == strtotime($time_slot->timeslot_starttime) 
										&& strtotime($booking->endtime) == strtotime($time_slot->timeslot_endtime)){
											// bkg start & end = ts start & end (booking = timeslot)
											$ts_booked_ids .= $time_slot->id.",";
										} else if( strtotime($booking->starttime) == strtotime($time_slot->timeslot_starttime)){
											// bkg starts at ts start
											$ts_booked_ids .= $time_slot->id.",";
										} else if( strtotime($booking->endtime) == strtotime($time_slot->timeslot_endtime)){
											// bkg end at ts end
											$ts_booked_ids .= $time_slot->id.",";
										} else if( strtotime($booking->starttime) > strtotime($time_slot->timeslot_starttime) 
											&& strtotime($booking->starttime) < strtotime($time_slot->timeslot_endtime)){
											// bkg start > ts start and < ts end (booking starts in a timeslot)
											$ts_booked_ids .= $time_slot->id.",";
										} else if( strtotime($booking->endtime) > strtotime($time_slot->timeslot_starttime) 
											&& strtotime($booking->endtime) < strtotime($time_slot->timeslot_endtime)){
											// bkg end > ts start and < ts end (booking ends in a timeslot)
											$ts_booked_ids .= $time_slot->id.",";
										} else if( strtotime($booking->starttime) < strtotime($time_slot->timeslot_starttime) 
											&& strtotime($booking->endtime) > strtotime($time_slot->timeslot_endtime)){
											// bkg start < ts start and bkg end > ts end (booking covers a timeslot)
											$ts_booked_ids .= $time_slot->id.",";
										}
									}
								}
							}
					}
	
					if($ts_booked_ids != "" && $ts_blocked_ids != ""){
						// both booked and blocked
						$booked_exp = " IF(id_timeslots IN(".substr($ts_booked_ids,0,strlen($ts_booked_ids)-1)."),'Booked', IF(id_timeslots IN(".substr($ts_blocked_ids,0,strlen($ts_blocked_ids)-1)."),'Unavailable','Available')) as booked ";
					} else if($ts_booked_ids != "" && $ts_blocked_ids == ""){
						// only booked
						$booked_exp = " IF(id_timeslots IN(".substr($ts_booked_ids,0,strlen($ts_booked_ids)-1)."),'Booked', 'Available') as booked ";
					} else if($ts_booked_ids == "" && $ts_blocked_ids != ""){
						// only blocked
						$booked_exp = " IF(id_timeslots IN(".substr($ts_blocked_ids,0,strlen($ts_blocked_ids)-1)."),'Unavailable', 'Available') as booked ";
					} else {
						// neither
						$booked_exp = "'Available' as booked ";
					}
					$sql = "SELECT id_timeslots as id, timeslot_starttime, timeslot_endtime, timeslot_description, day_number, ";
					if($apptpro_config->timeFormat == '12'){							
						$sql .=	"CONCAT( DATE_FORMAT(timeslot_starttime, '%l:%i%p'), ' - ', DATE_FORMAT(timeslot_endtime, '%l:%i%p') ) as startendtime, ".
						"DATE_FORMAT(timeslot_starttime, '%l:%i%p') as display_starttime, timeslot_starttime as timeorder, ".
						"DATE_FORMAT(timeslot_endtime, '%l:%i%p') as display_endtime, ";
					} else {
						$sql .=	"CONCAT( DATE_FORMAT(timeslot_starttime, '%H:%i'), ' - ', DATE_FORMAT(timeslot_endtime, '%H:%i') ) as startendtime, ".
						"DATE_FORMAT(timeslot_starttime, '%H:%i') as display_starttime, timeslot_starttime as timeorder, ".
						"DATE_FORMAT(timeslot_endtime, '%H:%i') as display_endtime, ";
					}					
					$sql .=	$booked_exp."  from  #__sv_apptpro3_timeslots ".
					" WHERE published = 1 ".
					" AND day_number = ".$day;
					if($res_detail->timeslots == "Global"){
						$sql .= " AND resource_id = 0";
					} else {
						$sql .= " AND resource_id = ".$resource;
					}					
					$sql .= " AND (start_publishing is null OR start_publishing = '0000-00-00' OR start_publishing < '".$startdate."') ".
					" AND (end_publishing is null OR end_publishing = '0000-00-00' OR end_publishing > '".$startdate."')".
					" AND staff_only = 'No' ORDER BY timeslot_starttime";
				}
			}
			try{
				$database->setQuery($sql);
				$slot_rows = NULL;
				$slot_rows = $database -> loadObjectList();
			} catch (RuntimeException $e) {
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		
	
			// get get part day book-offs
			$sql = "SELECT * FROM #__sv_apptpro3_bookoffs ".
				" WHERE ".
				" ( resource_id='".$resource."' ) ".
				" AND ( (off_date = '".$startdate."' AND full_day='No') OR (rolling_bookoff != 'No') )".
				" AND published=1 ORDER BY bookoff_starttime";
			try{
				$database->setQuery($sql);
				$part_day_bookoffs = $database -> loadObjectList();
			} catch (RuntimeException $e) {
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		
	
	//		echo $sql;
	//		print_r($slot_rows);
	//		echo "<br/><br/>";
	//		print_r($part_day_bookoffs);
	//		exit;
			$actual_slots_available = 0;
			
			if(!$day_off){
				foreach($slot_rows as $slot_row){
					
					$ok_to_process_slot = true;
					if(count($part_day_bookoffs) > 0){
						// need to check each slot to see if blocked by book-off
						if(blocked_by_bookoff($slot_row, $part_day_bookoffs)){
							$ok_to_process_slot = false;
						}						
					}
					if($slot_row->booked == "Available" && $ok_to_process_slot){
						$options .=  "<option value=\"".$slot_row->timeslot_starttime.",".$slot_row->timeslot_endtime."\">".$slot_row->startendtime."</option>";
						$actual_slots_available ++;
					}
				}
			}
			
		// end code from mobile app		
		} else {
			// with max seats > 0 the code below shows slots and seats available - NOT compatible with servcie based duration
	
			// select timeslots for that day
			$database = JFactory::getDBO();
			$sql = "SELECT *, timeslot_starttime as non_display_timeslot_starttime, ";
			if($apptpro_config->timeFormat == "12"){
				$sql .= "TIME_FORMAT(timeslot_starttime,'%l:%i %p') as timeslot_starttime, timeslot_starttime as timeorder, ".
				"TIME_FORMAT(timeslot_starttime,'%k:%i') as starttime_24, TIME_FORMAT(timeslot_endtime,'%k:%i') as endtime_24, ".
				"TIME_FORMAT(timeslot_endtime,'%l:%i %p') as timeslot_endtime, TIME_FORMAT(timeslot_starttime,'%H:%i') as db_starttime_24 ";
			} else {
				$sql .= "TIME_FORMAT(timeslot_starttime,'%H:%i') as timeslot_starttime, timeslot_starttime as timeorder,  ".
				"TIME_FORMAT(timeslot_starttime,'%k:%i') as starttime_24, TIME_FORMAT(timeslot_endtime,'%k:%i') as endtime_24, ".
				"TIME_FORMAT(timeslot_endtime,'%H:%i') as timeslot_endtime, TIME_FORMAT(timeslot_starttime,'%H:%i') as db_starttime_24 ";
			}	
			// does the resource use global slots?
			
			if($res_detail->timeslots == "Global"){
				$sql .=	"FROM #__sv_apptpro3_timeslots WHERE published=1 AND (resource_id is null or resource_id = 0) AND day_number = ".$day.
					" AND (start_publishing IS NULL OR start_publishing = '0000-00-00' OR '".$startdate."' >= start_publishing ) ".
					" AND (end_publishing IS NULL OR start_publishing = '0000-00-00' OR '".$startdate."' <= end_publishing ) ".
					" AND staff_only = 'No' ORDER BY timeorder";
			} else {
				$sql .=	"FROM #__sv_apptpro3_timeslots WHERE published=1 AND resource_id = ".$resource." AND day_number = ".$day.
					" AND (start_publishing IS NULL OR start_publishing = '0000-00-00' OR '".$startdate."' >= start_publishing ) ".
					" AND (end_publishing IS NULL OR start_publishing = '0000-00-00' OR '".$startdate."' <= end_publishing ) ".
					" AND staff_only = 'No' ORDER BY timeorder";
			} 
		
			//echo $sql;
			try{
				$database->setQuery($sql);
				$slot_rows = $database -> loadObjectList();
			} catch (RuntimeException $e) {
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		
		
			// select bookings for that date & resource
			$sql = "SELECT starttime FROM #__sv_apptpro3_requests WHERE resource='".$resource."' AND startdate='".$startdate."' AND (request_status='accepted' OR request_status='pending' ".($apptpro_config->block_new=="Yes"?"OR request_status='new'":"").")";
			//echo $sql;
			try{
				$database->setQuery($sql);
				$booking_rows = $database -> loadColumn ();
			} catch (RuntimeException $e) {
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		

			// get get part day book-offs
			$sql = "SELECT * FROM #__sv_apptpro3_bookoffs ".
				" WHERE ".
				" ( resource_id='".$resource."' ) ".
				" AND ( (off_date = '".$startdate."' AND full_day='No') OR (rolling_bookoff != 'No') )".
				" AND published=1 ORDER BY bookoff_starttime";
			try{
				$database->setQuery($sql);
				$part_day_bookoffs = $database -> loadObjectList();
			} catch (RuntimeException $e) {
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		
			
			// get resource info for the selected resource
			$sql = 'SELECT * FROM #__sv_apptpro3_resources where id_resources = '.$resource;
			try{
				$database->setQuery($sql);
				$res_detail = NULL;
				$res_detail = $database -> loadObject();
			} catch (RuntimeException $e) {
				echo JText::_('RS1_SQL_ERROR');
				logIt($e->getMessage(), "getSlots", "", "");
				return false;
			}		
			$gotSlots = true;
			if(count( $slot_rows ) == 0) {
				$gotSlots = false;
			}
			
			// The problem now is that we won't know if there are no slots until we walk through the for loop below so we
			// do not know which top row to put in.
			// We will need to build the response as a big string then at the end we can tack the appropriate first option in.
	
			$actual_slots_available = 0;
	
			for($i=0; $i < count( $slot_rows ); $i++) {
									
				$slot_row = $slot_rows[$i];
				$ok_to_process_slot = true;
				$k = 0;
				
				date_default_timezone_set($timezone_identifier);
				$time_adjusted_for_lead = time() + ($res_detail->min_lead_time * 60 * 60);	
				if(strtotime($startdate." ".$slot_row->non_display_timeslot_starttime) < $time_adjusted_for_lead){
					$ok_to_process_slot = false;
				} else {
					$ok_to_process_slot = true;
				}
				
				if(count($part_day_bookoffs) > 0){
					// need to check each slot to see if blocked by book-off
					if(blocked_by_bookoff($slot_row, $part_day_bookoffs)){
						$ok_to_process_slot = false;
					}
				}
				
				if($ok_to_process_slot){
					$k=0;
					if($res_detail->max_seats != 0){ // a limit has been specified
							$currentcount = getCurrentSeatCount($startdate, $slot_row->db_starttime_24.":00", $slot_row->endtime_24.":00", $res_detail->id_resources);
							if ($currentcount >= $res_detail->max_seats){
							
								// dev only
								//echo "<option value=''>".count_values($slot_row->timeorder, $booking_rows)."</option>";
							
								// IE does not support 'disabled', do not show this slot
								if($browser != "Explorer"){
									//echo "<option value=".$slot_row->starttime_24.",".$slot_row->endtime_24." style='color:cccccc' disabled='disabled'>".$slot_row->timeslot_starttime." - ".$slot_row->timeslot_endtime."</option>";
								}
							} else {
								$options .= "<option value=\"".$slot_row->starttime_24.",".$slot_row->endtime_24."\">".$slot_row->timeslot_starttime." - ".$slot_row->timeslot_endtime;
								if($apptpro_config->show_available_seats == "Yes"){
									$adjusted_max_seats = getSeatAdjustments($startdate, $slot_row->timeslot_starttime, $slot_row->timeslot_endtime, $res_detail->id_resources, $res_detail->max_seats);								
									$options .= "  (".strval($res_detail->max_seats + $adjusted_max_seats - $currentcount).")";
									//$options .= "  (".strval($res_detail->max_seats - $currentcount).")";
								} 
								$options .="</option>";
								$actual_slots_available ++;
							}
					} else {
						// allow dupes
						$options .=  "<option value=\"".$slot_row->starttime_24.",".$slot_row->endtime_24."\">".$slot_row->timeslot_starttime." - ".$slot_row->timeslot_endtime."</option>";
						$actual_slots_available ++;
					}
				}
				
				$k = 1 - $k; 
			}
		}
		
		
		$options .= "</select>";

		$ret_val = "<select name=\"timeslots\" id=\"timeslots\" style=\"width:auto\" class=\"sv_apptpro_request_dropdown\" onchange=\"set_starttime();selectTimeslotSimple();setDuration();calcTotal();\">";

		if($actual_slots_available == 0){
			$ret_val .= "<option value=\"00:00,00:00\" >".JText::_('RS1_INPUT_SCRN_NO_TIMESLOTS_AVAILABLE')."</option>";	
		} else {
			$ret_val .= "<option value=\"00:00,00:00\" >".JText::_('RS1_INPUT_SCRN_TIMESLOT_PROMPT')."</option>";		
		}		
		$ret_val .= $options;
		JFactory::getDocument()->setMimeEncoding( 'text/html' );
	    echo $ret_val;				
		jExit();

	}

?>