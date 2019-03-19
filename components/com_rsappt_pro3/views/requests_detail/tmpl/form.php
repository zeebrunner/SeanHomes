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

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

//	include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );
//  	setSessionStuff("request");

	$showform= true;
	$mainframe = JFactory::getApplication();
	$jinput = JFactory::getApplication()->input;
//	$params = $mainframe->getPageParameters('com_rsappt_pro3');
	$itemid = $jinput->getInt( 'Itemid', '' ); // menu id of Front Desk
	$menu = $mainframe->getMenu();
	$params = $menu->getParams($itemid);
	//echo $params;
	$err = "";
	
	$fd_show_contact_info = true;
	if($params->get('fd_show_contact_info') == 'No'){
		$fd_show_contact_info = false;
	}
	$fd_allow_show_seats = true;
	if($params->get('fd_allow_show_seats') == 'No'){
		$fd_allow_show_seats = false;
	}
	$fd_show_udfs = true;
	if($params->get('fd_show_udfs') == 'No'){
		$fd_show_udfs = false;
	}
	$fd_show_extras = true;
	if($params->get('fd_show_extras') == 'No'){
		$fd_show_extras = false;
	}
	$fd_show_financials = true;
	if($params->get('fd_show_financials') == 'No'){
		$fd_show_financials = false;
	}
	$fd_edit_status_only = false;
	$readonly = "";
	$disablelist = "";
	$disabledropdown = "";
	if($params->get('fd_edit_status_only') == 'Yes'){
		$fd_edit_status_only = true;
		$readonly = " readonly=readonly class=\"sv_readonly_background\"";
		$disablelist = " disabled=true class=\"sv_readonly_background\"";
		$disabledropdown = " disabled=true class=\"sv_readonly_background admin_dropdown\"";
	}
	
	$listpage = $jinput->getString('listpage', 'list');
	
	if($listpage == 'list'){
		$savepage = 'save';
	} else if($listpage == "front_desk"){
		setSessionStuff("front_desk");
		$savepage = 'save_front_desk';
	} else {
		$savepage = 'save_adv_admin';
	}

	$session = JSession::getInstance($handler=null, $options=null);
	$session->set("status_filter", $jinput->getString('filter', ''));
	$session->set("request_resourceFilter", $jinput->getString('resourceFilter', ''));

	$request = $jinput->getInt( 'id', '' );
	$itemid = $jinput->getInt( 'Itemid', '' );
	$option = $jinput->getString( 'option', '' );

	$user = JFactory::getUser();
	if($user->guest){
		echo "<font color='red'>".JText::_('RS1_ADMIN_SCRN_NO_LOGIN')."</font>";
		$showform = false;
	} else {
		$database = JFactory::getDBO(); 
		// get request details
		$user = JFactory::getUser();
		
		if($this->detail->id_requests==""){
			echo "<font color='red'>".JText::_('RS1_ADMIN_SCRN_NO_ACCESS')."</font>";
			$showform = false;
		}
		
		// get config stuff
		$database = JFactory::getDBO();
		$sql = 'SELECT * FROM #__sv_apptpro3_config';
		try{
			$database->setQuery($sql);
			$apptpro_config = NULL;
			$apptpro_config = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		

		$div_cal = "";
		if($apptpro_config->use_div_calendar == "Yes"){
			$div_cal = "'testdiv1'";
		}

		// get udfs
		$database = JFactory::getDBO(); 
		//$sql = 'SELECT * FROM #__sv_apptpro3_udfs WHERE published=1 ORDER BY ordering';
		$sql = "SELECT ".
		"#__sv_apptpro3_udfs.udf_label, #__sv_apptpro3_udfs.udf_type, ".
		"#__sv_apptpro3_udfvalues.udf_value, #__sv_apptpro3_udfvalues.id as value_id, ".
		"#__sv_apptpro3_udfvalues.request_id ".
		"FROM ".
		"#__sv_apptpro3_udfvalues INNER JOIN ".
		"#__sv_apptpro3_udfs ON #__sv_apptpro3_udfvalues.udf_id = ".
		"#__sv_apptpro3_udfs.id_udfs ".
		"WHERE ".
		"#__sv_apptpro3_udfvalues.request_id = ".$this->detail->id_requests. " ".
		"ORDER BY ordering ";
		try{
			$database->setQuery($sql);
			$udf_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
		
		// get extras data
		$database = JFactory::getDBO(); 
		$sql = "SELECT extras_id, extras_label, extras_qty, extras_tooltip, max_quantity FROM ".
		" #__sv_apptpro3_extras_data INNER JOIN #__sv_apptpro3_extras ".
		"   ON #__sv_apptpro3_extras_data.extras_id = #__sv_apptpro3_extras.id_extras ".
		" WHERE #__sv_apptpro3_extras_data.request_id = ".$this->detail->id_requests. " ".
		" ORDER BY ordering ";
		try{
			$database->setQuery($sql);
			$extras_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		

		// get data for dropdownlist
		
		// get seat types
		$database = JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_seat_types WHERE published=1 '.
		' AND (scope = "" OR scope LIKE "%|'.$this->detail->resource.'|%") ORDER BY ordering';
		try{
			$database->setQuery($sql);
			$seat_type_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
	
		// get seat values
		$sql = "SELECT seat_type_id, seat_type_label, seat_type_qty FROM ".
		" #__sv_apptpro3_seat_counts INNER JOIN #__sv_apptpro3_seat_types ".
		"   ON #__sv_apptpro3_seat_counts.seat_type_id = #__sv_apptpro3_seat_types.id_seat_types ".
		" WHERE #__sv_apptpro3_seat_counts.request_id = ".$this->detail->id_requests. " ".
		" ORDER BY ordering ";
		try{
			$database->setQuery($sql);
			$seat_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
		
		
		//global $database;
		$sql = "(SELECT 0 as id, '".JText::_('RS1_INPUT_SCRN_RESOURCE_PROMPT')."' as name, '".
		JText::_('RS1_INPUT_SCRN_RESOURCE_PROMPT')."' as description, ".
		"0 as ordering, '' as cost) ".
		"UNION (SELECT id_resources,name,description,ordering,CONCAT(' - ', cost) as cost ".
		"FROM #__sv_apptpro3_resources ".
		"WHERE resource_admins LIKE '%|".$user->id."|%' )".
		"ORDER BY ordering;";
		try{
			$database->setQuery($sql);
			$res_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
	
		$sql = "SELECT #__sv_apptpro3_services.* ".
			"FROM #__sv_apptpro3_services LEFT JOIN #__sv_apptpro3_resources ".
			"ON #__sv_apptpro3_services.resource_id = #__sv_apptpro3_resources.id_resources ".
			"WHERE #__sv_apptpro3_services.published = 1 AND #__sv_apptpro3_resources.published = 1 ".
			"AND #__sv_apptpro3_services.resource_id = ".$this->detail->resource." ORDER BY name ";	
		try{
			$database->setQuery( $sql );
			$srv_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
	
		$database = JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_config';
		try{
			$database->setQuery($sql);
			$apptpro_config = NULL;
			$apptpro_config = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
	
		// get statuses
		$sql = "SELECT * FROM #__sv_apptpro3_status ORDER BY ordering ";
		try{
			$database->setQuery($sql);
			$statuses = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "requests_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		

		$sql = "SELECT * FROM #__sv_apptpro3_payment_status ORDER BY ordering ";
		try{
			$database->setQuery($sql);
			$pay_statuses = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "admin_tmpl_default", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
		
	}
	
	$display_startdate = $this->detail->startdate;	
	$startdate = $this->detail->startdate;
	if($display_startdate != JText::_('RS1_INPUT_SCRN_DATE_PROMPT')){
		switch ($apptpro_config->date_picker_format) {
			case "yy-mm-dd":
				$display_startdate = date("Y-m-d", strtotime($this->detail->startdate));
				break;
			case "dd-mm-yy":
				$display_startdate = date("d-m-Y", strtotime($this->detail->startdate));
				break;
			case "mm-dd-yy":
				$display_startdate = date("m-d-Y", strtotime($this->detail->startdate));
				break;
			default:	
				$display_startdate = date("Y-m-d", strtotime($this->detail->startdate));
				break;
		}
	}

	
?>
<?php if($showform){?>

<?php $document = JFactory::getDocument();
$document->addStyleSheet( JURI::base( true )."/components/com_rsappt_pro3/sv_apptpro.css");
?>
<script language="JavaScript" src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/date.js"></script>
<script language="JavaScript" src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/script.js"></script>
<?php 
$document = JFactory::getDocument();
$document->addStyleSheet( "//code.jquery.com/ui/1.8.2/themes/smoothness/jquery-ui.css");
?>
<script src="//code.jquery.com/ui/1.8.2/jquery-ui.js"></script>
<script language="JavaScript" src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/datepicker_locale/datepicker-<?php echo PICKER_LANG?>.js"></script>
<script language="JavaScript">
	jQuery(function() {
  		jQuery( "#display_startdate" ).datepicker({
			showOn: "button",
	 		dateFormat: "<?php echo $apptpro_config->date_picker_format;?>",
			firstDay: <?php echo $apptpro_config->popup_week_start_day ?>, 
			buttonImage: "<?php echo JURI::base( true );?>/components/com_rsappt_pro3/icon_cal_gr.png",
			buttonImageOnly: true,
			buttonText: "<?php echo JText::_('RS1_INPUT_SCRN_DATE_PROMPT');?>",
			altField: "#startdate,#enddate",
			altFormat: "yy-mm-dd" //DO NOT CHANGE 			
    	});
	});
	
	function getTomorrow(){
		var tomorrow = new Date();
		tomorrow.setDate(tomorrow.getDate()+1);
		var tomstr = '' + tomorrow.getFullYear() + "-" + (tomorrow.getMonth()+1) + "-" +tomorrow.getDate();
		//alert(tomstr);
		return(tomstr);
	}
		
	function doCancel(){
		Joomla.submitform("cancel");
	}		

	function doClose(){
		Joomla.submitform("req_close");
	}		
	
	function doSave(){
		if(document.getElementById("require_validation").value === "Yes"){
			result = validateFormEdit();
			//alert("|"+result+"|");
			if(result.indexOf('<?php echo JText::_('RS1_INPUT_SCRN_VALIDATION_OK');?>')==-1){		
				//alert(result);
				return false;
			}
		}

		if(document.getElementById('name').value == ""){
			alert('<?php echo JText::_('RS1_ADMIN_SCRN_NAME_ERR');?>');
			return(false);
		}
		Joomla.submitform("save");		
	}

	function calcSeatTotal(){
		if(document.getElementById("seat_type_count") != null && document.getElementById("seat_type_count").value > 0 ){
			var seat_count = 0; 
			rate = 0.00;
			for(i=0; i<parseInt(document.getElementById("seat_type_count").value); i++){
				seat_name_cost = "seat_type_cost_"+i;
				seat_name = "seat_"+i;
				group_seat_name = "seat_group_"+i;
				seat_count += parseInt(document.getElementById(seat_name).value);
			}
			document.getElementById("booked_seats_div").innerHTML = seat_count;
			document.getElementById("booked_seats").value = seat_count;
		}
		document.getElementById("require_validation").value = "Yes";

	}
	
	function setstarttime(){
		document.getElementById("starttime").value = document.getElementById("starttime_hour").value + ":" + document.getElementById("starttime_minute").value + ":00";
		document.getElementById("require_validation").value = "Yes";
	}
	
	function setendtime(){
		document.getElementById("endtime").value = document.getElementById("endtime_hour").value + ":" + document.getElementById("endtime_minute").value + ":00";
		document.getElementById("require_validation").value = "Yes";
	}

	function changeStartdate(){
		document.getElementById("enddate").value = document.getElementById("startdate").value;
		document.getElementById("require_validation").value = "Yes";
		changeDatePicker();
	}
	
	function setstatus(){
		if(document.getElementById("request_status").value == "accepted" && document.getElementById("old_status").value != "accepted"){
			document.getElementById("require_validation").value = "Yes";
		}		
	}
    </script>
<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm" class="sv_adminForm">
<div id="sv_apptpro_fe_detail">
<table width="100%" >
    <tr>
      <td align="left" colspan="2"> <h3><?php echo JText::_('RS1_ADMIN_SCRN_TITLE').JText::_('RS1_ADMIN_SCRN_BOOKING_DETAIL_TITLE');?></h3></td>
    </tr>
</table>
  <table class="table table-striped" >
    <tr>
      <td colspan="3"  style="text-align:right" height="40px" class="fe_header_bar">
      <?php if($this->lock_msg != ""){?>
	      <?php echo $this->lock_msg?>
    	  &nbsp;|&nbsp;&nbsp;<a href="#" id="closeLink" onclick="doClose();return false;"><?php echo JText::_('RS1_ADMIN_SCRN_BTN_CLOSE');?></a>&nbsp;&nbsp;</td>
      <?php } else { ?>
      	<a href="#" id="saveLink" onclick="doSave();return(false);"><?php echo JText::_('RS1_ADMIN_SCRN_BTN_SAVE');?></a>
      	&nbsp;|&nbsp;&nbsp;<a href="#" id="closeLink" onclick="doCancel();return(false);"><?php echo JText::_('RS1_ADMIN_SCRN_BTN_CANCEL');?></a>&nbsp;&nbsp;</td>
      <?php } ?>
    </tr>
    <tr>
      <td colspan="3"><div id="errors" class="sv_apptpro_errors"><?php echo $err ?></div></td>
	</tr>
    <tr>
      <td width="12%"><?php echo JText::_('RS1_ADMIN_SCRN_REQ_ID_COL_HEAD');?>: </td>
      <td width="40%"><?php echo $this->detail->id_requests; ?></td>
    </tr>  
    <tr>
      <td width="17%"><?php echo JText::_('RS1_ADMIN_SCRN_NAME');?>:<span class="style1">*</span> </td>
      <td><input type="text" size="40" maxsize="100" name="name" id="name" <?php echo $readonly ?> value="<?php echo stripslashes($this->detail->name); ?>" />
      <input type="hidden" name="user_id" id="user_id" value="<?php echo $this->detail->user_id; ?>" /></td>
      <td rowspan="11" valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_GENERAL_INFO');?></td>
    </tr>
	<?php if($fd_show_contact_info){?>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_PHONE');?>:</td>
      <td><input type="text" size="20" maxsize="20" name="phone" id="phone" <?php echo $readonly ?> value="<?php echo $this->detail->phone; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_EMAIL');?>: </td>
      <td><input type="text" size="40" maxsize="80" name="email" id="email" <?php echo $readonly ?> value="<?php echo $this->detail->email; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_USE_SMS_COL_HEAD');?>:</td>
      <td><select name="sms_reminders" <?php echo $disablelist ?> >
          <option value="Yes" <?php if( $this->detail->sms_reminders == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
          <option value="No" <?php if( $this->detail->sms_reminders == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
    </select>&nbsp;</td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_SMS_PHONE_COL_HEAD');?>:</td>
      <td><input type="text" size="20" maxsize="20" name="sms_phone" <?php echo $readonly ?> value="<?php echo $this->detail->sms_phone; ?>" />&nbsp;</td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_SMS_DIAL_CODE_COL_HEAD');?>:</td>
      <td><input type="text" size="3" maxsize="20" name="sms_dial_code" <?php echo $readonly ?> value="<?php echo $this->detail->sms_dial_code; ?>" />&nbsp;</td>
    </tr>
	<?php } ?>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_CATEGORY_COL_HEAD');?>:</td>
      <td><?php echo JText::_($this->detail->category_name); ?></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_RESOURCE');?>:</td>
    <?php if($fd_edit_status_only){?>
          <td><select name="xresource" id="xresource" <?php echo $disablelist ?> class="sv_apptpro3_requests_dropdown"  onchange="changeResourceFE();">
              <?php
        $k = 0;
        for($i=0; $i < count( $res_rows ); $i++) {
        $res_row = $res_rows[$i];
        ?>
              <option value="<?php echo $res_row->id; ?>" <?php if($this->detail->resource == $res_row->id){echo " selected='selected' ";} ?>><?php echo JText::_(stripslashes($res_row->name)); ?></option>
              <?php $k = 1 - $k; 
        } ?>
            </select>
	    <input type="hidden" size="20" maxsize="20" name="resource" <?php echo $readonly ?> value="<?php echo $this->detail->resource; ?>" /></td>
	<?php } else {?>
          <td><select name="resource" id="resource" class="sv_apptpro3_requests_dropdown"  onchange="changeResourceFE();">
              <?php
        $k = 0;
        for($i=0; $i < count( $res_rows ); $i++) {
        $res_row = $res_rows[$i];
        ?>
              <option value="<?php echo $res_row->id; ?>" <?php if($this->detail->resource == $res_row->id){echo " selected='selected' ";} ?>><?php echo JText::_(stripslashes($res_row->name)); ?></option>
              <?php $k = 1 - $k; 
        } ?>
            </select></td>
    <?php } ?>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_SERVICE_COL_HEAD');?>: </td>
    <?php if($fd_edit_status_only){?>
      <td><select name="xservice" id="xservice" <?php echo $disablelist ?> >
          <?php
			$k = 0;
			for($i=0; $i < count( $srv_rows ); $i++) {
			$srv_row = $srv_rows[$i];
			?>
          <option value="<?php echo $srv_row->id_services; ?>" <?php if($this->detail->service == $srv_row->id_services){echo " selected='selected' ";} ?>><?php echo JText::_(stripslashes($srv_row->name)); ?></option>
          <?php $k = 1 - $k; 
			} ?>
        </select>&nbsp;
	    <input type="hidden" size="20" maxsize="20" name="service" <?php echo $readonly ?> value="<?php echo $this->detail->service; ?>" /></td>
	<?php } else {?>
      <td><select name="service" id="service" >
          <?php
			$k = 0;
			for($i=0; $i < count( $srv_rows ); $i++) {
			$srv_row = $srv_rows[$i];
			?>
          <option value="<?php echo $srv_row->id_services; ?>" <?php if($this->detail->service == $srv_row->id_services){echo " selected='selected' ";} ?>><?php echo JText::_(stripslashes($srv_row->name)); ?></option>
          <?php $k = 1 - $k; 
			} ?>
        </select>&nbsp;</td>
    <?php } ?>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_STARTDATE');?>: </td>
      <td>
	<?php if(!$fd_edit_status_only){?>
            <input readonly="readonly" name="startdate" id="startdate" type="hidden" 
              class="sv_date_box" size="10" maxlength="10" value="<?php echo $startdate ?>" />
    
            <input type="text" readonly="readonly" id="display_startdate" name="display_startdate" class="sv_date_box" size="10" maxlength="10" 
                value="<?php echo $display_startdate ?>">
    <?php } else {?>
            <input readonly="readonly" name="startdate" id="startdate" type="text" 
              class="sv_date_box" size="10" maxlength="10" value="<?php echo $display_startdate ?>" />
    <?php } ?>
		<input type="hidden" id="enddate" name="enddate" value="<?php echo $this->detail->enddate; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_STARTTIME');?>:</td>
        <td width="40%">
        <div style="display: table-cell; padding-left:0px;"><select name="starttime_hour" id="starttime_hour" <?php echo $disabledropdown ?> 
        onchange="setstarttime();" class="admin_dropdown">
                <?php 
                for($x=0; $x<24; $x+=1){
                    if($x<10){
                        $x = "0".$x;
                    }
                    echo "<option value=".$x; if(substr($this->detail->starttime,0,2) == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
                }
                ?>
                </select></div>
		         <div style="display: table-cell; padding-left:5px;">:</div>
        		 <div style="display: table-cell; padding-left:5px;"><select name="starttime_minute" id="starttime_minute" <?php echo $disabledropdown ?> 
                 onchange="setstarttime();" class="admin_dropdown" >
                <?php
                for($x=0; $x<59; $x+=1){
                    if($x<10){
                        $x = "0".$x;
                    }
                    echo "<option value=".$x; if(substr($this->detail->starttime,3,2) == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
                }
                ?>
                </select></div>
		         <div style="display: table-cell; padding-left:5px;">(hh:mm)</div>
                 <input type="hidden" name="starttime" id="starttime" value="<?php echo $this->detail->starttime ?>" />              
                </td>        
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_ENDTIME');?>:</td>
	  <td width="40%">
      	<div style="display: table-cell; padding-left:0px;"><select name="endtime_hour" id="endtime_hour" <?php echo $disabledropdown ?> onchange="setendtime();" class="admin_dropdown">
      	<?php 
		for($x=0; $x<24; $x+=1){

			if($x<10){
				$x = "0".$x;
			}
			echo "<option value=".$x; if(substr($this->detail->endtime,0,2) == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
		}
		?>
        </select></div>
        <div style="display: table-cell; padding-left:5px;">:</div> 
		<div style="display: table-cell; padding-left:5px;"><select name="endtime_minute" id="endtime_minute" <?php echo $disabledropdown ?> onchange="setendtime();" class="admin_dropdown" >
		<?php
		for($x=0; $x<59; $x+=1){
			if($x<10){
				$x = "0".$x;
			}
			echo "<option value=".$x; if(substr($this->detail->endtime,3,2) == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
		}
		?>
        </select></div>
        <div style="display: table-cell; padding-left:5px;">(hh:mm)</div>
         <input type="hidden" name="endtime" id="endtime" value="<?php echo $this->detail->endtime ?>" />
		</td>              
    </tr>

    </tr>
    <?php if($fd_allow_show_seats){?>
    <tr>
      <td width="12%"><?php echo JText::_('RS1_ADMIN_SCRN_BOOKED_SEATS');?>:</td>
      <td width="40%"><div id="booked_seats_div"><?php echo $this->detail->booked_seats; ?></div><input type="hidden" size="2" maxsize="3" name="booked_seats" id="booked_seats" value="<?php echo $this->detail->booked_seats; ?>" /></td>
    </tr>
	<?php 
	$si = 0; 
	if(count($seat_type_rows)>0){ ?>
		<tr>
		  <td valign="top" ></td>
		  <td >
                <table border="0" cellpadding="2" cellspacing="1" >
	<?php foreach($seat_type_rows as $seat_type_row){ 
			$thiscount = 0;
	        for($i=0; $i < count( $seat_rows ); $i++) {
    	    	if($seat_type_row->id_seat_types == $seat_rows[$i]->seat_type_id){
					$thiscount = $seat_rows[$i]->seat_type_qty;
				}
			}  ?>

			<tr>
			  <td><?php echo JText::_($seat_type_row->seat_type_label)?>:</td>
			  <td colspan="3" valign="top">
			  <select name="seat_<?php echo $si ?>" <?php echo $disablelist ?> id="seat_<?php echo $si?>" onChange="calcSeatTotal();" class="sv_apptpro3_requests_dropdown" 
				title="<?php echo $seat_type_row->seat_type_tooltip ?>"  />
				<?php for($i=0; $i<=$seat_type_row->seat_group_max; $i++){ ?>
						<option value="<?php echo $i ?>" <?php echo ($i == $thiscount?'selected':'') ?>><?php echo $i ?></option>	        
				<?php } ?>
			   </select> 
				&nbsp;
			    <?php if($fd_edit_status_only){?>
				<input type="hidden" name="seat_<?php echo $si?>" id="seat_<?php echo $si?>" value="<?php echo $thiscount ?>"/>  
                <?php }?>
				<input type="hidden" name="seat_type_cost_<?php echo $si?>" id="seat_type_cost_<?php echo $si?>" value="<?php echo $seat_type_row->seat_type_cost ?>"/>  
				<input type="hidden" name="seat_type_id_<?php echo $si?>" id="seat_type_id_<?php echo $si?>" value="<?php echo $seat_type_row->id_seat_types ?>"/>  
				<input type="hidden" name="seat_group_<?php echo $si?>" id="seat_group_<?php echo $si?>" value="<?php echo $seat_type_row->seat_group ?>"/>  
				<input type="hidden" name="seat_type_org_qty_<?php echo $si?>" id="seat_type_org_qty_<?php echo $si?>" value="<?php echo $thiscount ?>"/>  
			  </td>
			</tr>
			<?php $si += 1; 
		} ?>
        </table></td></tr>
	<?php } ?>    
	<?php } else {
		// need a hidden field or the totals will be zeroed out on save?>    
        <input type="hidden" size="2" maxsize="3" name="booked_seats" id="booked_seats" value="<?php echo $this->detail->booked_seats; ?>" />
	<?php } ?>    

	<?php 
	$ei = 0; 
	if(count($extras_rows)>0){ ?>
        <tr>
          <td width="12%"><?php echo JText::_('RS1_INPUT_SCRN_EXTRAS_LABEL');?>:</td>
          <td width="40%"></td>
        </tr>

		<tr>
		  <td width="12%" valign="top" ></td>
		  <td colspan="2">
                <table border="0" cellpadding="2" cellspacing="1" >
	<?php foreach($extras_rows as $extras_row){ ?>
			<tr>
			  <td><?php echo JText::_($extras_row->extras_label)?>:</td>
			  <td colspan="3" valign="top"><?php echo $extras_row->extras_qty ?>
				&nbsp;
			  </td>
			</tr>
			<?php $ei += 1; 
		} ?>
        </table></td></tr>
	<?php } ?>    
    <tr>
      <td colspan="2" valign="top"><u><?php echo JText::_('RS1_ADMIN_SCRN_UDF');?></u></td>
      <td></td>
    </tr>
    <?php if(count($udf_rows > 0)){?>
		<tr >
		  <td width="12%" valign="top"></td>
		  <td colspan="2">
                <table border="0" cellpadding="2" cellspacing="1" >
                  <tr>
                    <td style="font-weight:bold; border-bottom:#999999 solid 1px"><?php echo JText::_('RS1_ADMIN_SCRN_UDF_LABEL');?></td>
                    <td style="font-weight:bold; border-bottom:#999999 solid 1px"><?php echo JText::_('RS1_ADMIN_SCRN_UDF_VALUE');?></td>
<!--                    <td style="font-weight:bold; border-bottom:#999999 solid 1px">Type</td>
-->                  </tr>
        <?php 
		$k = 0;
        for($i=0; $i < count( $udf_rows ); $i++) {
        	$udf_row = $udf_rows[$i];
        	?>
                  <tr>
                    <td ><?php echo JText::_(stripslashes($udf_row->udf_label))?>:</td>
             <?php if($udf_row->udf_type == 'Content'){?>
                    <td valign="top"><label><?php echo substr(strip_tags($udf_row->udf_value), 0, 50);?>... </label>
               	    <td valign="top"><input type="hidden" size="60" name=udf_value_<?php echo $i?> value='<?php echo $udf_row->udf_value?>'/>
           <?php } else if($udf_row->udf_type == 'Textarea'){?>
                    	<td valign="top"><textarea size="60" name=udf_value_<?php echo $i?> ><?php echo str_replace("'", "`", $udf_row->udf_value)?></textarea>
		    <?php } else { ?>
                   	<td valign="top"><input type="text" size="60" name=udf_value_<?php echo $i?> <?php echo $readonly ?> value='<?php echo str_replace("'", "`", $udf_row->udf_value)?>'/>
   		    <?php } ?>
                    <input type="hidden" name=udf_id_<?php echo $i?> value='<?php echo $udf_row->value_id?>'/>
                    </td>
<!--                    <td valign="top"><?php echo stripslashes($udf_row->udf_type)?></td>
-->                  </tr>
          <?php $k = 1 - $k; 
		} ?>
                </table>
          </td>
        </tr>
    <?php }?>
	
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td style=" border-top:#999999 solid 1px"><?php echo JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS');?>: </td>
      <td style=" border-top:#999999 solid 1px"><select name="request_status" id="request_status" class="sv_apptpro3_requests_dropdown"
      onchange="setstatus();">
		<?php foreach($statuses as $status_row){ ?>
            <option value="<?php echo $status_row->internal_value ?>" <?php if($this->detail->request_status == $status_row->internal_value){echo " selected='selected' ";} ?>><?php echo JText::_($status_row->status);?></option>        
        <?php } ?>
        </select><input type="hidden" id="old_status" name="old_status" value="<?php echo $this->detail->request_status;?>" /></td>
      <td style=" border-top:#999999 solid 1px" valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_HELP');?>&nbsp;</td>
    </tr>
    <?php if($fd_show_financials){?>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_PAY_STATUS');?>:</td>
      <td><select name="payment_status" <?php echo $disablelist ?> class="sv_apptpro3_requests_dropdown">
		<?php foreach($pay_statuses as $pay_status_row){ ?>
            <option value="<?php echo $pay_status_row->internal_value ?>" <?php if($this->detail->payment_status == $pay_status_row->internal_value){echo " selected='selected' ";} ?>><?php echo JText::_($pay_status_row->status);?></option>        
        <?php } ?>
        </select>      </td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_PAY_STATUS_HELP');?> </td>
	</tr>
    <tr>
      <td ><?php echo JText::_('RS1_ADMIN_SCRN_INVOICE_NUMBER');?>: </td>
      <td ><div id="svlabel"><?php echo $this->detail->invoice_number;?></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_INVOICE_NUMBER_HELP');?></td>
    </tr>
    <tr>
      <td ><?php echo JText::_('RS1_ADMIN_SCRN_BOOKING_TOTAL');?> :</td>
      <td ><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>&nbsp;<input type="text" size="5" maxsize="10" name="booking_total" <?php echo $readonly ?> value="<?php echo $this->detail->booking_total; ?>" style="text-align:right; width:50px;" /></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BOOKING_TOTAL_HELP');?></td>
    </tr>
    <tr>
      <td ><?php echo JText::_('RS1_ADMIN_SCRN_BOOKING_DEPOSIT');?> :</td>
      <td ><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>&nbsp;<input type="text" size="5" maxsize="10" name="booking_deposit" <?php echo $readonly ?> value="<?php echo $this->detail->booking_deposit; ?>" style="text-align:right; width:50px;" /></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BOOKING_DEPOSIT_HELP');?></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BOOKING_DUE');?> :</td>
      <td><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>&nbsp;<input type="text" size="5" maxsize="10" name="booking_due" <?php echo $readonly ?> value="<?php echo $this->detail->booking_due; ?>" style="text-align:right; width:50px;" /></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_BOOKING_DUE_HELP');?></td>
    </tr>
    <tr>
      <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_MAUNAL_PAYMENT_COLLECTED');?> :</td>
      <td valign="top"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>&nbsp;<input type="text" size="5" maxsize="10" name="manual_payment_collected" <?php echo $readonly ?> value="<?php echo $this->detail->manual_payment_collected; ?>" style="text-align:right; width:50px;" /></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_MAUNAL_PAYMENT_COLLECTED_HELP');?></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_CREDIT_USED');?> :</td>
      <td><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>&nbsp;<?php echo $this->detail->credit_used; ?><input type="hidden" size="5" name="credit_used" value="<?php echo $this->detail->credit_used; ?>" style="text-align:right; width:50px;"/></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_CREDIT_USED_HELP');?></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_COUPON_CODE');?> :</td>
      <td><?php echo $this->detail->coupon_code; ?></td>
      <td></td>
    </tr>
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_GIFT_CERT');?> :</td>
      <td><?php echo $this->detail->gift_cert; ?></td>
      <td></td>
    </tr>
    <tr>
      <td width="12%"><?php echo JText::_('RS1_ADMIN_SCRN_PP_TXN_DETAIL_ID');?> </td>
      <td><?php echo stripslashes($this->detail->txnid); ?></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_PP_TXN_DETAIL_ID_HELP');?> </td>
    </tr>   
    </tr>
	<?php } else {?>
    <input type="hidden" name="payment_status" value="<?php echo $this->detail->payment_status ?>" />
    <input type="hidden" name="booking_total" value="<?php echo $this->detail->booking_total ?>" />
    <input type="hidden" name="booking_due" value="<?php echo $this->detail->booking_due ?>" />
    <input type="hidden" name="manual_payment_collected" value="<?php echo $this->detail->manual_payment_collected ?>" />
    <input type="hidden" name="credit_used" value="<?php echo $this->detail->credit_used ?>" />
    <?php } ?>
    <input type="hidden" name="coupon_code" value="<?php echo $this->detail->coupon_code ?>" />
    <input type="hidden" name="gift_cert" value="<?php echo $this->detail->gift_cert ?>" />
    <?php if($apptpro_config->which_calendar != "None"){ ?>
    <tr >
      <td><?php echo JText::_('RS1_ADMIN_SCRN_CURRENT_CALENDAR');?>:</td>
      <td><?php echo $apptpro_config->which_calendar ?></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <?php if($apptpro_config->which_calendar == "Google"){ ?>
    <tr>
      <td>&nbsp;</td>
      <td><?php echo $this->detail->google_event_id ?><input type="hidden" name="google_event_id" id="google_event_id" value="<?php echo $this->detail->google_event_id ?>" />
			<input type="hidden" name="google_calendar_id" value="<?php echo $this->detail->google_calendar_id; ?>" />
      </td>
    </tr>
    <?php } ?> 
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_POST_TO_CALENDAR');?>:</td>
      <td>
	  <select name="show_on_calendar" class="sv_apptpro3_requests_dropdown" >
	      <option value="Yes" <?php if( $this->detail->show_on_calendar == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
	      <option value="No" <?php if( $this->detail->show_on_calendar == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
    </select></td>
      <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_POST_TO_CALENDAR_HELP');?>&nbsp;</td>       
    </tr>
<!--    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_CALENDAR_CATEGORY');?>:</td>
      <td><select name="calendar_category"  class="sv_apptpro3_requests_dropdown">
          <?php
			$k = 0;
			for($i=0; $i < count( $cal_cat_rows ); $i++) {
			$cal_cat_row = $cal_cat_rows[$i];
			?>
          <option value="<?php echo $cal_cat_row->id; ?>"
          <?php if($cal_cat_row->id == $this->detail->calendar_category){ echo "selected";}?>
          ><?php echo $cal_cat_row->title; ?></option>
          <?php $k = 1 - $k; 
	} ?>
        </select></td>
        <td><span style="color:#FF0000"><?php echo $noCats?></span><?php echo JText::_('RS1_ADMIN_SCRN_CALENDAR_CATEGORY_HELP');?>        </td>
    </tr>
--><!--    <tr>
      <td width="12%"><?php echo JText::_('RS1_ADMIN_SCRN_CALENDAR_CALENDAR');?></td>
      <td valign="top"><select name="calendar_calendar" >
          <?php
			$k = 0;
			for($i=0; $i < count( $cal_cal_rows ); $i++) {
			$cal_cal_row = $cal_cal_rows[$i];
			?>
          <option value="<?php echo $cal_cal_row->id; ?>"
          <?php if($cal_cal_row->id == $this->detail->calendar_calendar){ echo "selected";}?>
          ><?php echo $cal_cal_row->title; ?></option>
          <?php $k = 1 - $k; 
	     } ?>
        </select></td>
      <td><span style="color:#FF0000"><?php echo $noCats?></span> </td>
    </tr>
--><!--   <tr>
      <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_CALENDAR_COMMENT');?>:</td>
      <td ><textarea  name="calendar_comment" class="sv_apptpro3_requests_text" rows="4" cols="40"><?php echo $this->detail->calendar_comment; ?></textarea></td>
      <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_CALENDAR_COMMENT_HELP');?></td>
    </tr>
-->	<?php } // end of if($apptpro_config->which_calendar != "None") ?>
    <tr>
      <td valign="top" ><?php echo JText::_('RS1_ADMIN_SCRN_ADMINCOMMENT');?>:</td>
      <td ><textarea name="admin_comment" class="sv_apptpro3_requests_text" rows="4" cols="40" ><?php echo stripslashes($this->detail->admin_comment); ?></textarea></td>
      <td valign="top" ><?php echo JText::_('RS1_ADMIN_SCRN_ADMINCOMMENT_HELP');?></td>
    </tr>
    <tr>
      <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_TIMESTAMP');?>: 
      <?php if($this->detail->operator_id != null){echo " (".$this->detail->operator_id.")";}?>
      </td>
      <td valign="top"><?php echo $this->detail->created; ?></td>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_TIMESTAMP_HELP');?></td>
    </tr>
  </table>
  <input type="hidden" name="cancellation_id" id="cancellation_id" value="<?php echo $this->detail->cancellation_id; ?>" />
  <input type="hidden" name="id_requests" id="id_requests" value="<?php echo $this->detail->id_requests; ?>" />
  <input type="hidden" name="option" value="<?php echo $option; ?>" />
  <input type="hidden" name="controller" value="admin_detail" />
  <input type="hidden" name="id" value="<?php echo $this->user_id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="seat_type_count" id="seat_type_count" value="<?php echo count($seat_type_rows) ?>"/>  
  <input type="hidden" name="udf_rows_count" id="udf_rows_count" value="<?php echo count($udf_rows) ?>"/>  
  <input type="hidden" name="frompage" value="<?php echo $listpage ?>" />
  <input type="hidden" name="frompage_item" value="<?php echo $itemid ?>" />
  <input type="hidden" name="fromtab" value="0" />
	<input type="hidden" name="alt_cal_pos" id="alt_cal_pos" value="<?php echo $apptpro_config->cal_position_method; ?>" />

  <input type="hidden" name="operator_id" value="<?php echo $this->user_id; ?>" />
  <input type="hidden" id="require_validation" value="No" />
  <input type="hidden" id="screen_type" name="screen_type" value="non-gad" />			             
  <input type="hidden" name="date_picker_format" id="date_picker_format" value="<?php echo  $apptpro_config->date_picker_format;?>" />    
 
  <br /> 

  <?php if($apptpro_config->hide_logo == 'No'){ ?>
  <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
  <?php } ?>
 </div>
</form>
<?php } ?>