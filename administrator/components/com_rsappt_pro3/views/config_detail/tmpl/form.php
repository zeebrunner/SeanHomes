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

	$editor =JFactory::getEditor();
	$edit_params = array( 'html_height'=> '200' );
	
	$auto_resource_groups_groups = "";
				 
	// get config stuff
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_config_detail_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		

	$div_cal = "";
	if($apptpro_config->use_div_calendar == "Yes"){
		$div_cal = "'testdiv1'";
	}

	$tables = JFactory::getDbo()->getTableList();
	// get cb profile columns
	if(in_array($database->replacePrefix('#__comprofiler_fields'), $tables)){
		try{
			$database->setQuery("SELECT * FROM #__comprofiler_fields WHERE #__comprofiler_fields.table = '#__comprofiler' and (type='text' or type='predefined') ORDER BY name" );
			$cb_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_config_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
	}
	
	// get joomla profile columns
	// note bug in J3.1.1
	//	http://forum.joomla.org/viewtopic.php?f=706&t=802997
//	if(in_array($database->replacePrefix('#__user_profiles'), $tables)){
		try{
			$database->setQuery("SELECT DISTINCT profile_key FROM #__user_profiles ORDER BY ordering" );
			$profile_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_config_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
//	}
	
	// get js profile columns
	if(in_array($database->replacePrefix('#__community_fields'), $tables)){
		try{
			$database->setQuery("SELECT * FROM #__community_fields WHERE type!='group' ORDER BY name" );
			$js_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_config_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
	}
	
	// get groups
	if(in_array($database->replacePrefix('#__usergroups'), $tables)){
		try{
			$database->setQuery("SELECT title, id FROM #__usergroups WHERE id>2 ORDER BY title" );
			$user_groups = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_config_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
	}
	
 	// get data for dropdowns
	$database = JFactory::getDBO();
	try{
		$database->setQuery("SELECT * FROM #__sv_apptpro3_pp_currency ORDER BY description" );
		$currency_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_config_detail_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		

	$sql = 'SELECT * FROM #__sv_apptpro3_udfs WHERE (udf_type="Textbox" or udf_type="List" or udf_type="Radio") and published=1 ORDER BY ordering';
	try{
		$database->setQuery($sql);
		$udf_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_config_detail_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		

	// get dialing codes
	try{	
		$database->setQuery("SELECT * FROM #__sv_apptpro3_dialing_codes ORDER BY country" );
		$dial_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_config_detail_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		

	// get auto-resource groups
	$groups = str_replace("||", ",", $this->detail->auto_resource_groups);
	$groups = str_replace("|", "", $groups);
	//echo $groups;
	//exit;
	if($groups != ""){
		$sql = "SELECT id as groupid, title as title FROM #__usergroups WHERE ".
			"id IN (".$groups.")";
		try{
			$database->setQuery($sql);
			$auto_resource_group_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_resources_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
	}
	try{
		$database->setQuery("SELECT * FROM #__usergroups WHERE id > 1 ORDER BY title" ); // exclude Public
		$user_group_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_resources_detail_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		
	try{
		$database->setQuery("SELECT * FROM #__sv_apptpro3_categories WHERE published = 1 ORDER BY ordering" );
		$cat_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_resources_detail_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
	}		
	// get auto_resource_category assignments 
	$auto_resource_category = "";
	if (strlen($this->detail->auto_resource_category) > 0 ){
		$category_scope_assignments = str_replace("||", ",", $this->detail->auto_resource_category);
		$category_scope_assignments = str_replace("|", "", $category_scope_assignments);
		//echo $category_scope_assignments;
		//exit;
		$sql = "SELECT id_categories, name FROM #__sv_apptpro3_categories WHERE ".
  			"id_categories IN (".$category_scope_assignments.")";
		try{
			$database->setQuery($sql);
			$category_scope_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_resources_detail_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
	}	

	?>

<div id="testdiv1" style="VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: white; layer-background-color: white; z-index:99999"> </div>
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/calStyles.css" rel="stylesheet">
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">
<link href="<?php echo JURI::root( true );?>/administrator/components/com_rsappt_pro3/abpro_admin.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo JURI::root( true );?>/administrator/components/com_rsappt_pro3/overlib_mini.js"></script> 
<script language="JavaScript" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/CalendarPopup.js"></script> 
<script language="JavaScript">
	var now = new Date();
	var cal = new CalendarPopup( <?php echo $div_cal ?>);
	cal.setCssPrefix("TEST");
	cal.setWeekStartDay(<?php echo $apptpro_config->popup_week_start_day ?>);
	
	function doAddAutoResourceGroup(){
		var groupid = document.getElementById("user_groups").value;
		var cur_user_groups = document.getElementById("auto_resource_groups_id").value;
		var x = document.getElementById("auto_resource_groups_list");
		for (i=0;i<x.length;i++){
			if(x[i].value == groupid) {
				alert("<?php echo JText::_('RS1_ALREADY_SELECTED');?>");
				return false;
			}			
		}
	
		var opt = document.createElement("option");
        // Add an Option object to Drop Down/List Box
        document.getElementById("auto_resource_groups_list").options.add(opt); 
        opt.text = document.getElementById("user_groups").options[document.getElementById("user_groups").selectedIndex].text;
        opt.value = document.getElementById("user_groups").options[document.getElementById("user_groups").selectedIndex].value;
		cur_user_groups = cur_user_groups + "|" + groupid + "|";
		document.getElementById("auto_resource_groups_id").value = cur_user_groups;
	}

	function doRemoveAutoResourceGroup(){
		if(document.getElementById("auto_resource_groups_list").selectedIndex == -1){
			alert("<?php echo JText::_('RS1_NO_GROUP_SELECTED');?>");
			return false;
		}
		var user_to_go = document.getElementById("auto_resource_groups_list").options[document.getElementById("auto_resource_groups_list").selectedIndex].value;
		document.getElementById("auto_resource_groups_list").remove(document.getElementById("auto_resource_groups_list").selectedIndex);
		
		var cur_user_groups = document.getElementById("auto_resource_groups_id").value;

		cur_user_groups = cur_user_groups.replace("|" + user_to_go + "|", "");
		document.getElementById("auto_resource_groups_id").value = cur_user_groups;
	}
	
	function doAddCategoryScope(){
		var catid = document.getElementById("categories").value;
		var selected_categories = document.getElementById("auto_resource_category_id").value;
		var x = document.getElementById("selected_categories");
		for (i=0;i<x.length;i++){
			if(x[i].value == catid) {
				alert("Already selected");
				return false;
			}			
		}
	
		var opt = document.createElement("option");
        // Add an Option object to Drop Down/List Box
        document.getElementById("selected_categories").options.add(opt); 
        opt.text = document.getElementById("categories").options[document.getElementById("categories").selectedIndex].text;
        opt.value = document.getElementById("categories").options[document.getElementById("categories").selectedIndex].value;
		selected_categories = selected_categories + "|" + catid + "|";
		document.getElementById("auto_resource_category_id").value = selected_categories;
	}

	function doRemoveCategoryScope(){
		if(document.getElementById("selected_categories").selectedIndex == -1){
			alert("No Category selected for Removal");
			return false;
		}
		var cat_to_go = document.getElementById("selected_categories").options[document.getElementById("selected_categories").selectedIndex].value;
		document.getElementById("selected_categories").remove(document.getElementById("selected_categories").selectedIndex);
		
		var selected_categories = document.getElementById("auto_resource_category_id").value;

		selected_categories = selected_categories.replace("|" + cat_to_go + "|", "");
		document.getElementById("auto_resource_category_id").value = selected_categories;
	}
	
</script>
<table class="adminheading">
  <tr>
    <th class="config"><?php echo JText::_('RS1_ADMIN_CONFIG');?></th>
  </tr>
</table>
<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">
  <fieldset class="adminform">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#panel1" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_CONFIG_TAB');?></a></li>
      <li><a href="#panel2" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_TAB');?></a></li>
      <li><a href="#panel3" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_TAB');?></a></li>
      <li><a href="#panel7" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_CONFIG_FRONT_TAB');?></a></li>
      <li><a href="#panel8" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_CONFIG_CART_TAB');?></a></li>
      <li><a href="#panel9" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_CONFIG_COLUMNS');?></a></li>
      <li><a href="#panel6" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_CONFIG_STAFF');?></a></li>
    </ul>
    <div class="tab-content">
    <div id="panel1" class="tab-pane active">
      <table class="table table-striped" >
        <tr >
          <td width="20%"><?php echo JText::_('RS1_ADMIN_CONFIG_EMAIL_TO');?>:</td>
          <td><input style="width:90%" type="text" size="50" maxsize="255" name="mailTO" value="<?php echo $this->detail->mailTO; ?>" /></td>
          <td width="50%"><?php echo JText::_('RS1_ADMIN_CONFIG_EMAIL_TO_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_EMAIL_FROM');?>: </td>
          <td><input style="width:90%" type="text" size="50" maxsize="80" name="mailFROM" value="<?php echo $this->detail->mailFROM; ?>" /></td>
          <td></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_EMAIL_SUBJECT');?>: </td>
          <td><input style="width:90%" type="text" size="50" maxsize="50" name="mailSubject" value="<?php echo $this->detail->mailSubject; ?>" /></td>
          <td></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_HTML_EMAIL');?>: </td>
          <td><select name="html_email">
              <option value="Yes" <?php if($this->detail->html_email == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->html_email == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_HTML_EMAIL_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_LOGIN_REQUIRED');?>:</td>
          <td><select name="requireLogin">
              <option value="Yes" <?php if($this->detail->requireLogin == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->requireLogin == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_LOGIN_REQUIRED_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SCRN_NAME_READONLY');?></td>
          <td><select name="name_read_only">
              <option value="Yes" <?php if($this->detail->name_read_only == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->name_read_only == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_SCRN_NAME_READONLY_HELP');?></td>
        </tr>
        <tr >
          <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_PHONE');?>: </td>
          <td valign="top"><select name="requirePhone">
              <option value="Yes" <?php if($this->detail->requirePhone == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_PHONE_REQUIRED');?></option>
              <option value="No" <?php if($this->detail->requirePhone == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_PHONE_OPTIONAL');?></option>
              <option value="Hide" <?php if($this->detail->requirePhone == "Hide"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_PHONE_HIDE');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_PHONE_REQUIRED_HELP');?></td>
        </tr>
        <tr >
          <td valign="top"></td>
          <td valign="top"><div><?php echo JText::_('RS1_ADMIN_READ_ONLY');?> </div>
            <select name="phone_read_only">
              <option value="Yes" <?php if($this->detail->phone_read_only == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->phone_read_only == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_READ_ONLY_HELP');?></td>
        </tr>
        <tr >
          <td valign="top"></td>
          <td valign="top"><div><?php echo JText::_('RS1_ADMIN_CONFIG_PHONE_PROFILE');?></div>
            <select name="phone_profile_mapping" id="phone_profile_mapping" >
              <option value=""><?php echo JText::_('RS1_ADMIN_SELECT_PROFILE_VALUE');?></option>
              <?php
				$k = 0;
				for($i=0; $i < count( $profile_rows ); $i++) {
				$profile_row = $profile_rows[$i];
				?>
              <option value="<?php echo $profile_row->profile_key; ?>" <?php if($this->detail->phone_profile_mapping == $profile_row->profile_key){echo " selected='selected' ";} ?>><?php echo stripslashes($profile_row->profile_key); ?></option>
              <?php $k = 1 - $k; 
				} ?>
            </select></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_PHONE_PROFILE_HELP');?></td>
        </tr>
        <tr >
          <td valign="top"></td>
          <td valign="top"><div><?php echo JText::_('RS1_ADMIN_CONFIG_PHONE_CB');?></div>
            <select name="phone_cb_mapping" id="phone_cb_mapping" >
              <option value=""><?php echo JText::_('RS1_ADMIN_SELECT_CB_VALUE');?></option>
              <?php
				$k = 0;
				for($i=0; $i < count( $cb_rows ); $i++) {
				$cb_row = $cb_rows[$i];
				?>
              <option value="<?php echo $cb_row->name; ?>" <?php if($this->detail->phone_cb_mapping == $cb_row->name){echo " selected='selected' ";} ?>><?php echo stripslashes($cb_row->name); ?></option>
              <?php $k = 1 - $k; 
				} ?>
            </select></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_PHONE_CB_HELP');?></td>
        </tr>
        <tr >
          <td valign="top"></td>
          <td valign="top"><div><?php echo JText::_('RS1_ADMIN_CONFIG_PHONE_JS');?></div>
            <select name="phone_js_mapping" id="phone_js_mapping" >
              <option value=""><?php echo JText::_('RS1_ADMIN_SELECT_JS_VALUE');?></option>
              <?php
				$k = 0;
				for($i=0; $i < count( $js_rows ); $i++) {
				$js_row = $js_rows[$i];
				?>
              <option value="<?php echo $js_row->fieldcode; ?>" <?php if($this->detail->phone_js_mapping == $js_row->fieldcode){echo " selected='selected' ";} ?>><?php echo stripslashes($js_row->name); ?></option>
              <?php $k = 1 - $k; 
				} ?>
            </select></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_PHONE_JS_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SCRN_EMAIL');?>: </td>
          <td><select name="requireEmail">
              <option value="Yes" <?php if($this->detail->requireEmail == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_EMAIL_REQUIRED');?></option>
              <option value="No" <?php if($this->detail->requireEmail == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_EMAIL_OPTIONAL');?></option>
              <option value="Hide" <?php if($this->detail->requireEmail == "Hide"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_EMAIL_HIDE');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_EMAIL_REQUIRED_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_HIDE_LOGO');?>: </td>
          <td><select name="hide_logo">
              <option value="Yes" <?php if($this->detail->hide_logo == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->hide_logo == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_HIDE_LOGO_HELP');?></td>
        </tr>
        <!--      <tr >
        <td><?php echo JText::_('RS1_ADMIN_CONFIG_USE_DIV_CAL');?>: </td>
        <td><select name="use_div_calendar">
            <option value="Yes" <?php if($this->detail->use_div_calendar == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
            <option value="No" <?php if($this->detail->use_div_calendar == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
          </select></td>
        <td><?php echo JText::_('RS1_ADMIN_CONFIG_USE_DIV_CAL_HELP');?></td>    
      </tr>
      <tr >
        <td><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_POSITIONING');?>: </td>
        <td><select name="cal_position_method">
            <option value="1" <?php if($this->detail->cal_position_method == "1"){echo " selected='selected' ";} ?>>1</option>
            <option value="2" <?php if($this->detail->cal_position_method == "2"){echo " selected='selected' ";} ?>>2</option>
          </select></td>
        <td><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_POSITIONING_HELP');?></td>    
      </tr>
-->
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_POPUP_START_DAY');?>: </td>
          <td><select name="popup_week_start_day">
              <option value="0" <?php if($this->detail->popup_week_start_day == "0"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_SUNDAY');?></option>
              <option value="1" <?php if($this->detail->popup_week_start_day == "1"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_MONDAY');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_POPUP_START_DAY_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_DATE_PICKER_FORMAT');?>: </td>
          <td><select name="date_picker_format">
              <option value="yy-mm-dd" <?php if($this->detail->date_picker_format == "yy-mm-dd"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_YYYY-MM-DD');?></option>
              <option value="dd-mm-yy" <?php if($this->detail->date_picker_format == "dd-mm-yy"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_DD-MM-YYYY');?></option>
              <option value="mm-dd-yy" <?php if($this->detail->date_picker_format == "mm-dd-yy"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_MM-DD-YYYY');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_DATE_PICKER_FORMAT_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_LIMIT_BOOKINGS');?>: </td>
          <td><div style="display: table-cell">
              <input style="width:20px; text-align: center" type="text" size="3" maxsize="3" name="limit_bookings" value="<?php echo $this->detail->limit_bookings; ?>"/>
            </div>
            <div style="display: table-cell; padding-left:10px;"><?php echo JText::_('RS1_ADMIN_CONFIG_IN');?></div>
            <div style="display: table-cell; padding-left:10px;">
              <input style="width:20px; text-align: center" type="text" size="3" maxsize="3" name="limit_bookings_days" value="<?php echo $this->detail->limit_bookings_days; ?>"/>
            </div>
            <div style="display: table-cell; padding-left:10px;"><?php echo JText::_('RS1_ADMIN_CONFIG_DAYS');?></div></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_LIMIT_BOOKINGS_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_AUTO_ACCEPT');?>: </td>
          <td><select name="auto_accept">
              <option value="Yes" <?php if($this->detail->auto_accept == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->auto_accept == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_AUTO_ACCEPT_HELP');?></td>
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_BLOCK_NEW');?>: </td>
          <td><select name="block_new">
              <option value="Yes" <?php if($this->detail->block_new == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->block_new == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_BLOCK_NEW_HELP');?></td>
        </tr>
        <tr >
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_ALLOW_CANCEL');?>: </td>
          <td><select name="allow_cancellation">
              <option value="Yes" <?php if($this->detail->allow_cancellation == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->allow_cancellation == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <option value="BEO" <?php if($this->detail->allow_cancellation == "BEO"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_CANCEL_BACK_END_ONLY');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_ALLOW_CANCEL_HELP');?></td>
        </tr>
        <tr >
          <td valign="top"></td>
          <td><div style="display: table-cell;"><?php echo JText::_('RS1_ADMIN_CONFIG_ALLOW_CANCEL_UPTO');?></div>
            <div style="display: table-cell; padding-left:10px; ">
              <select style="width:60px;" name="hours_before_cancel">
                <option value="0" <?php if($this->detail->hours_before_cancel == "0"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_0');?></option>
                <option value="1" <?php if($this->detail->hours_before_cancel == "1"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_1');?></option>
                <option value="2" <?php if($this->detail->hours_before_cancel == "2"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_2');?></option>
                <option value="4" <?php if($this->detail->hours_before_cancel == "4"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_4');?></option>
                <option value="6" <?php if($this->detail->hours_before_cancel == "6"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_6');?></option>
                <option value="8" <?php if($this->detail->hours_before_cancel == "8"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_8');?></option>
                <option value="12" <?php if($this->detail->hours_before_cancel == "12"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_12');?></option>
                <option value="24" <?php if($this->detail->hours_before_cancel == "24"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_24');?></option>
                <option value="48" <?php if($this->detail->hours_before_cancel == "48"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_48');?></option>
              </select>
            </div>
            <div style="display: table-cell; padding-left:10px; "><?php echo JText::_('RS1_ADMIN_CONFIG_ALLOW_CANCEL_HOURS_BEFORE');?></div></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_ALLOW_CANCEL_HOURS_BEFORE2');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_ALLOW_CREDIT_REFUND');?>:</td>
          <td><select name="allow_user_credit_refunds">
              <option value="Yes" <?php if($this->detail->allow_user_credit_refunds == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->allow_user_credit_refunds == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_ALLOW_CREDIT_REFUND_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_TIME_FORMAT');?>:</td>
          <td><select name="timeFormat">
              <option value="12" <?php if($this->detail->timeFormat == "12"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_12_HOUR');?></option>
              <option value="24" <?php if($this->detail->timeFormat == "24"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_24_HOUR');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_TIME_FORMAT_HELP');?></td>
        </tr>
        <tr >
          <td height="" ><?php echo JText::_('RS1_ADMIN_CONFIG_USE_JQ_TOOLTIPS');?>:</td>
          <td><select name="use_jquery_tooltips">
              <option value="Yes" <?php if($this->detail->use_jquery_tooltips == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->use_jquery_tooltips == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_USE_JQ_TOOLTIPS_HELP');?></td>
        </tr>
        <tr >
          <td height="" ><?php echo JText::_('RS1_ADMIN_CONFIG_ENABLE_EB_DISCOUNT');?>:</td>
          <td><select name="enable_eb_discount">
              <option value="Yes" <?php if($this->detail->enable_eb_discount == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->enable_eb_discount == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_ENABLE_EB_DISCOUNT_HELP');?></td>
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_SCRN_GAP');?></td>
          <td><input type="text" size="5" maxsize="2" name="gap" style="width:30px; text-align: center" value="<?php echo $this->detail->gap; ?>" />
            &nbsp;&nbsp;</td>
          <td><?php echo JText::_('RS1_ADMIN_SCRN_GAP_HELP');?></td>
        </tr>
        <tr >
          <td height="" ><?php echo JText::_('RS1_ADMIN_CONFIG_JIT_SUBMIT');?>:</td>
          <td><select name="jit_submit">
              <option value="Yes" <?php if($this->detail->jit_submit == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->jit_submit == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_JIT_SUBMIT_HELP');?></td>
        </tr>
        <tr >
          <td height="" ><?php echo JText::_('RS1_ADMIN_CONFIG_DDSLICK_ENABLE');?>:</td>
          <td><select name="enable_ddslick">
              <option value="Yes" <?php if($this->detail->enable_ddslick == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->enable_ddslick == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_DDSLICK_ENABLE_HELP');?></td>
        </tr>
        <tr >
          <td height="" ><?php echo JText::_('RS1_ADMIN_CONFIG_AUTO_RESOURCE_ENABLE');?>:</td>
          <td><select name="enable_auto_resource">
              <option value="Yes" <?php if($this->detail->enable_auto_resource == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->enable_auto_resource == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_AUTO_RESOURCE_ENABLE_HELP');?></td>
        </tr>
      <tr>
        <td><?php echo JText::_('RS1_ADMIN_SCRN_AUTO_RESOURCE_GROUPS');?></td>
        <td><table width="95%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="33%"><select style="width:auto" name="user_groups" id="user_groups">
              <?php
                $k = 0;
                for($i=0; $i < count( $user_group_rows ); $i++) {
                $user_group_row = $user_group_rows[$i];
                ?>
              <option value="<?php echo $user_group_row->id; ?>"><?php echo $user_group_row->title; ?></option>
              <?php $k = 1 - $k; 
                } ?>
            </select></td>
            <td width="34%" valign="top" align="center"><input type="button" name="btnAddAutoResourceGroup" id="btnAddAutoResourceGroup" size="10" value="<?php echo JText::_('RS1_ADMIN_SCRN_RES_ADMINS_ADD');?>" onclick="doAddAutoResourceGroup()" />
              <br />
              &nbsp;<br />
              <input type="button" name="btnRemoveAutoResourceGroup" id="btnRemoveAutoResourceGroup" size="10"  onclick="doRemoveAutoResourceGroup()" value="<?php echo JText::_('RS1_ADMIN_SCRN_RES_ADMINS_REMOVE');?>" /></td>
            <td width="33%"><div class="sv_select"><select name="auto_resource_groups_list" id="auto_resource_groups_list" size="4" multiple="multiple" >
              <?php
                $k = 0;
                for($i=0; $i < count( $auto_resource_group_rows ); $i++) {
                $auto_resource_group_row = $auto_resource_group_rows[$i];
                ?>
              <option value="<?php echo $auto_resource_group_row->groupid; ?>"><?php echo $auto_resource_group_row->title; ?></option>
              <?php 
                    $auto_resource_groups_groups = $auto_resource_groups_groups."|".$auto_resource_group_row->groupid."|";
                    $k = 1 - $k; 
                } ?>
            </select></div></td>
          </tr>
        </table></td>
        <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_AUTO_RESOURCE_GROUPS_HELP');?></td>
      </tr>
      <tr>
        <td><?php echo JText::_('RS1_ADMIN_SCRN_AUTO_RESOURCE_CATEGORY');?></td>
        <td><table class="table table-striped" >
          <tr>
            <td width="33%"><select style="width:auto" name="categories" id="categories">
              <?php
                $k = 0;
                for($i=0; $i < count( $cat_rows ); $i++) {
                    $cat_row = $cat_rows[$i];
                ?>
              <option value="<?php echo $cat_row->id_categories; ?>"><?php echo $cat_row->name;?></option>
              <?php $k = 1 - $k; 
                } ?>
            </select></td>
            <td width="34%" valign="top" align="center"><input type="button" name="btnAddCategoryScope" id="btnAddCategoryScope" size="10" value="<?php echo JText::_('RS1_ADMIN_SCRN_RES_ADMINS_ADD');?>" onclick="doAddCategoryScope()" />
              <br />
              &nbsp;<br />
              <input type="button" name="btnRemoveCategoryScope" id="btnRemoveCategoryScope" size="10"  onclick="doRemoveCategoryScope()" value="<?php echo JText::_('RS1_ADMIN_SCRN_RES_ADMINS_REMOVE');?>" /></td>
            <td width="33%"><div class="sv_select"><select name="selected_categories" id="selected_categories" multiple="multiple" size="4" >
              <?php
                $k = 0;
                for($i=0; $i < count( $category_scope_rows ); $i++) {
                $category_scope_row = $category_scope_rows[$i];
                ?>
              <option value="<?php echo $category_scope_row->id_categories; ?>"><?php echo $category_scope_row->name; ?></option>
              <?php 
                    $auto_resource_category = $auto_resource_category."|".$category_scope_row->id_categories."|";
                    $k = 1 - $k; 
                } ?>
            </select></div></td>
          </tr>
        </table></td>
        <td><?php echo JText::_('RS1_ADMIN_SCRN_AUTO_RESOURCE_CATEGORY_HELP');?>&nbsp;</td>
      </tr>
        <tr >
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_HEADER_TEXT');?>: </td>
          <td><textarea style="width:90%" name="headerText" rows="3" cols="60"><?php echo stripslashes($this->detail->headerText); ?></textarea></td>
          <td></td>
        </tr>
        <tr >
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_FOOTER_TEXT');?>: </td>
          <td ><textarea style="width:90%" name="footerText" rows="3" cols="60"><?php echo stripslashes($this->detail->footerText); ?></textarea></td>
          <td></td>
        </tr>
<!--        <tr >
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_SITE_ACCESS_CODE');?>: </td>
          <td><input type="text" size="20" maxsize="20" name="site_access_code" value="<?php echo $this->detail->site_access_code; ?>" /></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SITE_ACCESS_CODE_HELP');?></td>
        </tr>-->
      </table>
    </div>
    <div id="panel2" class="tab-pane"> 
      <script language="javascript">
                function cal_pick(){
                    if(document.getElementById('NoCal').checked == true){
                        document.getElementById('which_calendar_id').value = "None";
                        }
                    if(document.getElementById('Google').checked == true){
                        document.getElementById('which_calendar_id').value = "Google";
                        }
                    }
            </script>
      <link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/calStyles.css" rel="stylesheet">
      <?php echo JText::_('RS1_ADMIN_CONFIG_CAL_INTRO');?>
      <table class="table table-striped" >
        <tr>
          <td colspan="2" align="center"><input name="rbcalendar" type="radio" id="NoCal" value="None"  onclick="cal_pick()" 
                        <?php if($this->detail->which_calendar == 'None'){ echo 'checked="checked"';} ?>/>
            &nbsp;&nbsp;<?php echo JText::_('RS1_ADMIN_CONFIG_CAL_NONE');?></td>
          <td width="50%"></td>
        </tr>
        <!--  <tr>
                      <td align="center" valign="top"><input type="radio" name="rbcalendar" id="EventList" value="EventList" onclick="cal_pick()" 
                                <?php if($this->detail->which_calendar == 'EventList'){ echo 'checked="checked"';} ?>/></td>
                      <td><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_EVENTLIST');?> <?php echo JText::_('RS1_ADMIN_CONFIG_CAL_EVENTLIST_LINK');?> <?php echo JText::_('RS1_ADMIN_CONFIG_CAL_EVENTLIST_HELP');?></td>
             </tr>-->
        <tr>
          <td colspan="2" align="center" valign="top"><input type="radio" name="rbcalendar" id="Google" value="Google" onclick="cal_pick()" 
                                <?php if($this->detail->which_calendar == 'Google'){ echo 'checked="checked"';} ?>/>
            &nbsp;&nbsp; <?php echo JText::_('RS1_ADMIN_CONFIG_CAL_GOOGLE');?> <?php echo JText::_('RS1_ADMIN_CONFIG_CAL_GOOGLE_LINK');?></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_GOOGLE_HELP');?></td>
        </tr>
      </table>
      <hr />
      <?php echo JText::_('RS1_ADMIN_CONFIG_CAL_FIELDS');?>
      </td>
      <table class="table table-striped" >
        <tr>
          <td width="20%" valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_TITLE');?>:</td>
          <td valign="top"><select name="calendar_title">
              <option value="resource.name" <?php if($this->detail->calendar_title == "resource.name"){echo " selected='selected' ";} ?>>resource.name</option>
              <option value="request.name" <?php if($this->detail->calendar_title == "request.name"){echo " selected='selected' ";} ?>>request.name</option>
              <?php
                    $k = 0;
                    for($i=0; $i < count( $udf_rows ); $i++) {
                    $udf_row = $udf_rows[$i];
                    ?>
              <option value="<?php echo $udf_row->id_udfs; ?>" <?php if($this->detail->calendar_title == $udf_row->id_udfs){echo " selected='selected' ";} ?>><?php echo $udf_row->udf_label?></option>
              <?php $k = 1 - $k; 
                    } ?>
            </select></td>
          <td width="50%" ><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_TITLE_HELP');?></td>
        </tr>
        <tr>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_BODY');?>:</td>
          <td><textarea style="width:100%" name="calendar_body2" rows="4" cols="70"><?php echo stripslashes($this->detail->calendar_body2); ?></textarea></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_CAL_BODY_HELP');?></td>
        </tr>
        <tr>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_DST');?>:</td>
          <td valign="top"><select name="daylight_savings_time">
              <option value="Yes" <?php if($this->detail->daylight_savings_time == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->daylight_savings_time == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td rowspan="3" ><?php echo JText::_('RS1_ADMIN_CONFIG_DST_HELP');?></td>
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_SCRN_DST_START_DATE');?></td>
          <td><input type="text" class="sv_date_box" size="12" maxsize="10" readonly="readonly" name="dst_start_date" id="dst_start_date" value="<?php echo $this->detail->dst_start_date; ?>" />
            <a href="#" id="anchor1" onclick="cal.select(document.forms['adminForm'].dst_start_date,'anchor1','yyyy-MM-dd'); return false;"
                         name="anchor1"><img height="15" hspace="2" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/icon_cal.gif" width="16" border="0"></a></td>
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_SCRN_DST_END_DATE');?></td>
          <td><input type="text" class="sv_date_box" size="12" maxsize="10" readonly="readonly" name="dst_end_date" id="dst_end_date" value="<?php echo $this->detail->dst_end_date; ?>" />
            <a href="#" id="anchor2" onclick="cal.select(document.forms['adminForm'].dst_end_date,'anchor2','yyyy-MM-dd'); return false;"
                         name="anchor2"><img height="15" hspace="2" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/icon_cal.gif" width="16" border="0"></a> &nbsp; </td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <?php echo JText::_('RS1_ADMIN_CONFIG_CAL_NOTE');?>
      </td>
      <input type="hidden" name="which_calendar" id="which_calendar_id" value=<?php echo $this->detail->which_calendar ?> />
    </div>
    <div id="panel3" class="tab-pane"> 
      <script language="javascript">
            function set_gad_grid_start_day_radios(){		
                switch(document.getElementById('gad_grid_start_day').value)
                {
                case "Today":
                  document.getElementById('rb_gad_grid_start_day_today').checked = true;
                  break;    
                case "XDays":
                  document.getElementById('rb_gad_grid_start_day_xdays').checked = true;
                  break;    
                case "Tomorrow":
                  document.getElementById('rb_gad_grid_start_day_tomorrow').checked = true;
                  break;    
                default:
                  document.getElementById('rb_gad_grid_start_day_specific').checked = true;
                  break;    
                }
            }
            
            function setTomorrow(){
                document.getElementById('gad_grid_start_day').value = "Tomorrow";
            }
        
            function setMonday(){
                document.getElementById('gad_grid_start_day').value = "Monday";
            }
        
            function setToday(){
                document.getElementById('gad_grid_start_day').value = "Today";
            }
        
            function setNotSet(){
                document.getElementById('gad_grid_start_day').value = "Not Set";
            }
            
            function setXDays(){
                document.getElementById('gad_grid_start_day').value = "XDays";
            }
        
            
        </script>
      <link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">
      <?php echo JText::_('RS1_ADMIN_CONFIG_GAD_INTRO');?>
      <table class="table table-striped" >
        <tr >
          <td width="20%"><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_START_TIME');?>:</td>
          <td><select name="def_gad_grid_start" class="admin_dropdown" >
              <?php 
                for($x=0; $x<24; $x+=1){
                    $x.=":00";
                    echo "<option value=".$x; if($this->detail->def_gad_grid_start == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
                }
                ?>
            </select></td>
          <td width="50%">&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_END_TIME');?>:</td>
          <td><select name="def_gad_grid_end" class="admin_dropdown">
              <?php 
                for($x=0; $x<=24; $x+=1){
                    $x.=":00";
                    echo "<option value=".$x; if($this->detail->def_gad_grid_end == $x) {echo " selected='selected' ";} echo ">".$x." </option>";  
                }
                ?>
            </select></td>
          <td>&nbsp;</td>
        <tr >
          <td width="20%"><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_HIDE_STARTSTOP');?>:</td>
          <td><select name="gad_grid_hide_startend">
              <option value="Yes" <?php if($this->detail->gad_grid_hide_startend == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->gad_grid_hide_startend == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td width="50%"><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_HIDE_STARTSTOP_HELP');?>&nbsp;</td>
        </tr>
        <tr>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_START_DAY');?>:</td>
          <td ><table width="100%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td><input type="radio" name="rb_gad_grid_start_day" id="rb_gad_grid_start_day_today" value="rb_gad_grid_start_day_today"
                        <?php echo ($this->detail->gad_grid_start_day == "Today" ? "checked='checked'" : "");?> onclick="setToday();" />
                  &nbsp; <?php echo JText::_('RS1_ADMIN_CONFIG_GAD_TODAY');?>&nbsp;</td>
              </tr>
              <tr>
                <td><input type="radio" name="rb_gad_grid_start_day" id="rb_gad_grid_start_day_tomorrow" value="rb_gad_grid_start_day_tomorrow"
                        <?php echo ($this->detail->gad_grid_start_day == "Tomorrow" ? "checked='checked'" : "");?> onclick="setTomorrow();" />
                  &nbsp; <?php echo JText::_('RS1_ADMIN_CONFIG_GAD_TOMORROW');?>&nbsp;</td>
              </tr>
              <tr>
                <td><input type="radio" name="rb_gad_grid_start_day" id="rb_gad_grid_start_day_monday" value="rb_gad_grid_start_day_monday"
                        <?php echo ($this->detail->gad_grid_start_day == "Monday" ? "checked='checked'" : "");?> onclick="setMonday();" />
                  &nbsp; <?php echo JText::_('RS1_ADMIN_CONFIG_GAD_MONDAY');?>&nbsp;</td>
              </tr>
              <tr>
                <td><input type="radio" name="rb_gad_grid_start_day" id="rb_gad_grid_start_day_xdays" value="rb_gad_grid_start_day_xdays"
                        <?php echo ($this->detail->gad_grid_start_day == "XDays" ? "checked='checked'" : "");?> onclick="setXDays();" />
                  &nbsp;
                  <input type="text" style="width:20px" size="2" name="gad_grid_start_day_days" id="gad_grid_start_day_days" value="<?php echo $this->detail->gad_grid_start_day_days?>" />
                  <?php echo JText::_('RS1_ADMIN_CONFIG_GAD_DAYS');?>&nbsp;</td>
              </tr>
              <tr>
                <td><input type="radio" name="rb_gad_grid_start_day" id="rb_gad_grid_start_day_specific" value="rb_gad_grid_start_day_specific" 
                        <?php echo (($this->detail->gad_grid_start_day != "Tomorrow" AND $this->detail->gad_grid_start_day != "Today" AND $this->detail->gad_grid_start_day != "Monday" AND $this->detail->gad_grid_start_day != "XDays")? "checked='checked'" : "");?>/>
                  &nbsp; <?php echo JText::_('RS1_ADMIN_CONFIG_GAD_SPECIFIC');?>:
                  <input type="text" style="width:80px" name="gad_grid_start_day" id="gad_grid_start_day" size="12" readonly="readonly" value="<?php echo $this->detail->gad_grid_start_day; ?>" 
                        onchange="set_gad_grid_start_day_radios();" />
                  <a href="#" id="anchor3" onclick="cal.select(document.forms['adminForm'].gad_grid_start_day,'anchor3','yyyy-MM-dd'); return false;"
                                     name="anchor3"><img height="15" hspace="2" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/icon_cal.gif" width="16" border="0"></a>&nbsp;</td>
              </tr>
            </table></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_START_DAY_HELP');?></td>
        </tr>
        <tr >
          <td ><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_WIDTH');?>:</td>
          <td><input type="text" style="width:30px; text-align: center" size="10" maxsize="20" name="gad_grid_width" value="<?php echo $this->detail->gad_grid_width; ?>" /></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_WIDTH_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_NAME_WIDTH');?>:</td>
          <td ><input type="text" style="width:30px; text-align: center" size="10" maxsize="20" name="gad_name_width" value="<?php echo $this->detail->gad_name_width; ?>" /></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_NAME_WIDTH_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_AVAILABLE_IMAGE');?>:</td>
          <td><input type="text" size="60" maxsize="80" name="gad_available_image" value="<?php echo $this->detail->gad_available_image; ?>" />
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_AVAILABLE_IMAGE_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_BOOKED_IMAGE');?>:</td>
          <td><input type="text" size="60" maxsize="80" name="gad_booked_image" value="<?php echo $this->detail->gad_booked_image; ?>" />
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_BOOKED_IMAGE_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_DATE_FORMAT');?>:</td>
          <td><input type="text" size="20" maxsize="20" name="gad_date_format" value="<?php echo $this->detail->gad_date_format; ?>" /></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_DATE_FORMAT_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_LONG_DATE_FORMAT');?>:</td>
          <td><input type="text" size="20" maxsize="20" name="long_date_format" value="<?php echo $this->detail->long_date_format; ?>" /></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_LONG_DATE_FORMAT_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_DAYS_TO_SHOW');?>:</td>
          <td><input type="text" style="width:30px; text-align: center" size="1" maxsize="2" name="gad_grid_num_of_days" value="<?php echo $this->detail->gad_grid_num_of_days; ?>" /></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_DAYS_TO_SHOW_HELP');?></td>
        </tr>
        <tr>
          <td width="20%"><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_GAD2');?>:</td>
          <td><select name="use_gad2" class="admin_dropdown">
              <option value="Yes" <?php if($this->detail->use_gad2 == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->use_gad2 == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td width="50%" rowspan="2"><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_GRID_GAD2_HELP');?>&nbsp;</td>
        </tr>
        <tr>
          <td ><?php echo JText::_('RS1_ADMIN_CONFIG_GAD2_ROW_HEIGHT');?>:</td>
          <td><input type="text" style="width:30px; text-align: center" size="3" maxsize="3" name="gad2_row_height" value="<?php echo $this->detail->gad2_row_height; ?>" /></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_SHOW_AVAILABLE_SEATS');?>:</td>
          <td><select name="show_available_seats">
              <option value="Yes" <?php if($this->detail->show_available_seats == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->show_available_seats == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_GAD_SHOW_AVAILABLE_SEATS_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_WHO_BOOKED');?>:</td>
          <td><select name="gad_who_booked">
              <option value="Yes" <?php if($this->detail->gad_who_booked == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->gad_who_booked == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_WHO_BOOKED_HELP');?></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_MOBILE_SHOW_SIMPLE');?>:</td>
          <td><select name="mobile_show_simple">
              <option value="Yes" <?php if($this->detail->mobile_show_simple == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->mobile_show_simple == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_MOBILE_SHOW_SIMPLE_HELP');?></td>
        </tr>
      </table>
    </div>
    <div id="panel7" class="tab-pane">
      <table class="table table-striped" >
        <tr >
          <td width="25%"><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_RESOURCES');?>:</td>
          <td><select name="adv_admin_show_resources">
              <option value="Yes" <?php if($this->detail->adv_admin_show_resources == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->adv_admin_show_resources == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <?php
                        $k = 0;
                        for($i=0; $i < count( $user_groups ); $i++) {
                        $user_group = $user_groups[$i];
                        ?>
              <option value="<?php echo $user_group->id; ?>"  <?php if($this->detail->adv_admin_show_resources == $user_group->id){echo " selected='selected' ";} ?>><?php echo $user_group->title ?></option>
              <?php $k = 1 - $k; 
                        } ?>
            </select></td>
          <td width="50%">&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_SERVICES');?>:</td>
          <td><select name="adv_admin_show_services">
              <option value="Yes" <?php if($this->detail->adv_admin_show_services == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->adv_admin_show_services == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <?php
                        $k = 0;
                        for($i=0; $i < count( $user_groups ); $i++) {
                        $user_group = $user_groups[$i];
                        ?>
              <option value="<?php echo $user_group->id; ?>"  <?php if($this->detail->adv_admin_show_services == $user_group->id){echo " selected='selected' ";} ?>><?php echo $user_group->title ?></option>
              <?php $k = 1 - $k; 
                        } ?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_TIMESLOTS');?>:</td>
          <td><select name="adv_admin_show_timeslots">
              <option value="Yes" <?php if($this->detail->adv_admin_show_timeslots == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->adv_admin_show_timeslots == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <?php
                        $k = 0;
                        for($i=0; $i < count( $user_groups ); $i++) {
                        $user_group = $user_groups[$i];
                        ?>
              <option value="<?php echo $user_group->id; ?>"  <?php if($this->detail->adv_admin_show_timeslots == $user_group->id){echo " selected='selected' ";} ?>><?php echo $user_group->title ?></option>
              <?php $k = 1 - $k; 
                        } ?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_BOOKOFFS');?>:</td>
          <td><select name="adv_admin_show_bookoffs">
              <option value="Yes" <?php if($this->detail->adv_admin_show_bookoffs == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->adv_admin_show_bookoffs == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <?php
                        $k = 0;
                        for($i=0; $i < count( $user_groups ); $i++) {
                        $user_group = $user_groups[$i];
                        ?>
              <option value="<?php echo $user_group->id; ?>"  <?php if($this->detail->adv_admin_show_bookoffs == $user_group->id){echo " selected='selected' ";} ?>><?php echo $user_group->title ?></option>
              <?php $k = 1 - $k; 
                        } ?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_COUPONS');?>:</td>
          <td><select name="adv_admin_show_coupons">
              <option value="Yes" <?php if($this->detail->adv_admin_show_coupons == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->adv_admin_show_coupons == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <?php
                        $k = 0;
                        for($i=0; $i < count( $user_groups ); $i++) {
                        $user_group = $user_groups[$i];
                        ?>
              <option value="<?php echo $user_group->id; ?>"  <?php if($this->detail->adv_admin_show_coupons == $user_group->id){echo " selected='selected' ";} ?>><?php echo $user_group->title ?></option>
              <?php $k = 1 - $k; 
                        } ?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_EXTRAS');?>:</td>
          <td><select name="adv_admin_show_extras">
              <option value="Yes" <?php if($this->detail->adv_admin_show_extras == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->adv_admin_show_extras == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <?php
                        $k = 0;
                        for($i=0; $i < count( $user_groups ); $i++) {
                        $user_group = $user_groups[$i];
                        ?>
              <option value="<?php echo $user_group->id; ?>"  <?php if($this->detail->adv_admin_show_extras == $user_group->id){echo " selected='selected' ";} ?>><?php echo $user_group->title ?></option>
              <?php $k = 1 - $k; 
                        } ?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_RATE_ADJ');?>:</td>
          <td><select name="adv_admin_show_rate_adj">
              <option value="Yes" <?php if($this->detail->adv_admin_show_rate_adj == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->adv_admin_show_rate_adj == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <?php
                        $k = 0;
                        for($i=0; $i < count( $user_groups ); $i++) {
                        $user_group = $user_groups[$i];
                        ?>
              <option value="<?php echo $user_group->id; ?>"  <?php if($this->detail->adv_admin_show_rate_adj == $user_group->id){echo " selected='selected' ";} ?>><?php echo $user_group->title ?></option>
              <?php $k = 1 - $k; 
                        } ?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_SEAT_ADJ');?>:</td>
          <td><select name="adv_admin_show_seat_adj">
              <option value="Yes" <?php if($this->detail->adv_admin_show_seat_adj == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->adv_admin_show_seat_adj == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <?php
                        $k = 0;
                        for($i=0; $i < count( $user_groups ); $i++) {
                        $user_group = $user_groups[$i];
                        ?>
              <option value="<?php echo $user_group->id; ?>"  <?php if($this->detail->adv_admin_show_seat_adj == $user_group->id){echo " selected='selected' ";} ?>><?php echo $user_group->title ?></option>
              <?php $k = 1 - $k; 
                        } ?>
            </select></td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <?php echo JText::_('RS1_ADMIN_CONFIG_SHOW_PAY_PROCS');?> </div>
    <div id="panel8" class="tab-pane">
      <table class="table table-striped" >
        <tr>
          <td colspan="3"><?php echo JText::_('RS1_ADMIN_CONFIG_CART_INTRO');?><br /></td>
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_CART_ENABLE');?>: </td>
          <td><select name="cart_enable">
              <option value="Yes" <?php if($this->detail->cart_enable == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_CART_YES');?></option>
              <option value="Public" <?php if($this->detail->cart_enable == "Public"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_CART_PUBLIC_ONLY');?></option>
              <option value="Staff" <?php if($this->detail->cart_enable == "Staff"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_CART_STAFF_ONLY');?></option>
              <option value="No" <?php if($this->detail->cart_enable == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_CART_NO');?></option>
            </select>
            &nbsp;&nbsp; <br/>
            <?php echo JText::_('RS1_ADMIN_CART_LIMITS');?>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_CART_ENABLE_HELP');?></td>
        </tr>
        <tr>
          <td width="10%" valign="top"><?php echo JText::_('RS1_ADMIN_CART_HEADER');?>:</td>
          <td width="55%" valign="top"><?php echo $editor->display( 'cart_msg_header',  $this->detail->cart_msg_header, '100%', '200', '75', '20', false, null, null, null, $edit_params ) ;?></td>
          <td with="35%" valign="top"><?php echo JText::_('RS1_ADMIN_CART_HEADER_HELP');?></td>
        </tr>
        <tr>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CART_BODY_CONFIRM');?>:</td>
          <td valign="top"><?php echo $editor->display( 'cart_msg_confirm',  $this->detail->cart_msg_confirm , '100%', '200', '75', '20', false ) ;?></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CART_BODY_CONFIRM_HELP');?></td>
        </tr>
        <tr>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CART_BODY_INPROG');?>:</td>
          <td valign="top"><?php echo $editor->display( 'cart_msg_inprogress',  $this->detail->cart_msg_inprogress , '100%', '200', '75', '20', false ) ;?></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CART_BODY_INPROG_HELP');?></td>
        </tr>
        <tr>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CART_FOOTER');?>:</td>
          <td valign="top"><?php echo $editor->display( 'cart_msg_footer',  $this->detail->cart_msg_footer , '100%', '200', '75', '20', false ) ;?></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CART_FOOTER_HELP');?></td>
        </tr>
        <tr>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CART_PAYPAL_ITEM');?>:</td>
          <td valign="top"><textarea name="cart_paypal_item" rows="3" cols="70"><?php echo stripslashes($this->detail->cart_paypal_item); ?></textarea></td>
          <td valign="top"><?php echo JText::_('RS1_ADMIN_CART_PAYPAL_ITEM_HELP');?></td>
        </tr>
        <tr>
          <td colspan="3"><?php echo JText::_('RS1_ADMIN_CART_LIMITS');?><br /></td>
        </tr>
      </table>
    </div>
    <div id="panel9" class="tab-pane"> <?php echo JText::_('RS1_ADMIN_CONFIG_COLUMNS_INTRO');?>
      <table class="table table-striped" >
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SHOW_EMAIL');?>:</td>
          <td><select name="admin_show_email">
              <option value="Yes" <?php if($this->detail->admin_show_email == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->admin_show_email == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td width="50%">&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SHOW_CATEGORY');?>:</td>
          <td><select name="admin_show_category">
              <option value="Yes" <?php if($this->detail->admin_show_category == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->admin_show_category == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SHOW_RESOURCE');?>:</td>
          <td><select name="admin_show_resource">
              <option value="Yes" <?php if($this->detail->admin_show_resource == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->admin_show_resource == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SHOW_SERVICE');?>:</td>
          <td><select name="admin_show_service">
              <option value="Yes" <?php if($this->detail->admin_show_service == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->admin_show_service == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SHOW_SEATS');?>:</td>
          <td><select name="admin_show_seats">
              <option value="Yes" <?php if($this->detail->admin_show_seats == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->admin_show_seats == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SHOW_PAY_ID');?>:</td>
          <td><select name="admin_show_pay_id">
              <option value="Yes" <?php if($this->detail->admin_show_pay_id == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->admin_show_pay_id == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_SHOW_PAY_STATS');?>:</td>
          <td><select name="admin_show_pay_stat">
              <option value="Yes" <?php if($this->detail->admin_show_pay_stat == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->admin_show_pay_stat == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </div>
    <div id="panel6" class="tab-pane">
      <table class="table table-striped" >
        <tr>
          <td colspan="3"><?php echo JText::_('RS1_ADMIN_CONFIG_STAFF_INTRO');?><br /></td>
        </tr>
        <tr >
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_STAFF_BOOK_IN_PAST');?>:</td>
          <td><input type="text" style="width:30px; text-align: center" size="3" maxsize="3" name="staff_booking_in_the_past" value="<?php echo $this->detail->staff_booking_in_the_past; ?>" /></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_STAFF_BOOK_IN_PAST_HELP');?></td>
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_ENABLE_QUICK_STAT_CHANGE');?>:</td>
          <td><select name="status_quick_change">
              <option value="Yes" <?php if($this->detail->status_quick_change == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->status_quick_change == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_ENABLE_QUICK_STAT_CHANGE_HELP');?></td>
        </tr>
      </table>
      <hr />
      <table class="table table-striped" >
        <tr>
          <td colspan="3"><?php echo JText::_('RS1_ADMIN_CONFIG_CCINVOICE_SETTINGS');?><br /></td>
        </tr>
        <tr >
          <td width="20%"><?php echo JText::_('RS1_ADMIN_CONFIG_CCINVOICE_ITEM_NAME');?>:</td>
          <td><input type="text" name="ccinvoice_item_name" value="<?php echo $this->detail->ccinvoice_item_name; ?>" /></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_CCINVOICE_ITEM_NAME_HELP');?></td>
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_CCINVOICE_ITEM_DESC');?>:</td>
          <td><textarea style="width:90%" name="ccinvoice_item_description" rows="3" cols="60"><?php echo stripslashes($this->detail->ccinvoice_item_description); ?></textarea></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_CCINVOICE_ITEM_DESC_HELP');?></td>
        </tr>
      </table>
      
    </div>
  </fieldset>
  <input type="hidden" name="id_config" value="<?php echo $this->detail->id_config; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="config_detail" />
  <input type="hidden" name="auto_resource_groups" id="auto_resource_groups_id" value="<?php echo $auto_resource_groups_groups; ?>" />
  <input type="hidden" name="auto_resource_category" id="auto_resource_category_id" value="<?php echo $auto_resource_category; ?>" />
  <br />
  <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
</form>
