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

	JHTML::_('behavior.tooltip');
	//JHTML::_('behavior.modal');
	jimport( 'joomla.application.helper' );
	JHtml::_('jquery.framework');

	$mainframe = JFactory::getApplication();
	$session = JSession::getInstance($handler=null, $options=null);
	$jinput = JFactory::getApplication()->input;

	$option = $jinput->getString( 'option', '' );
	$user = JFactory::getUser();
	$itemId = $jinput->getInt('Itemid');

	include_once( JPATH_SITE."/administrator/components/com_rsappt_pro3/sendmail_pro2.php" );
	include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );

	// -----------------------------------------------------------------------
	// see if we need to switch into single-resource or single-category mode.
	$single_resource_mode = false;
	$single_resource_id = "";
	$single_category_mode = false;
	$single_category_id = "";
	$single_service_mode = false;
	$single_service_id = "";
	$default_resource_specified = false;
	$default_resource_id = "";
	$default_category_specified = false;
	$default_category_id = "";
	$single_service_resource = "";
	$res_cats = null;

	$params = $mainframe->getPageParameters('com_rsappt_pro3');
	if($params->get('res_or_cat') == 1 && $params->get('passed_id') != ""){
		// single resource mode on, set by menu parameter
		$single_resource_mode = true;
		$single_resource_id = $params->get('passed_id');
		//echo "single resource mode (menu), id=".$single_resource_id;
	}

	if($jinput->getInt('res','')!=""){
		// single resource mode on, set by menu parameter
		$single_resource_mode = true;
		$single_resource_id = $jinput->getInt('res','');
		//echo "single resource mode (querystring), id=".$single_resource_id;
	}

	if($params->get('res_or_cat') == 2 && $params->get('passed_id') != ""){
		// single category mode on, set by menu parameter
		$single_category_mode = true;
		$single_category_id = $params->get('passed_id');
		//echo "single category mode (menu), id=".$single_category_id;
	}

	if($jinput->getInt('cat','')!=""){
		// single category mode on, set by menu parameter
		$single_category_mode = true;
		$single_category_id = $jinput->getInt('cat','');
		//echo "single category mode (querystring), id=".$single_category_id;
	}

	if($params->get('res_or_cat') == 3 && $params->get('passed_id') != ""){
		// single service mode on, set by menu parameter
		$single_service_mode = true;
		$single_service_id = $params->get('passed_id');
		//echo "single resource mode (menu), id=".$single_resource_id;
	}

	if($jinput->getInt('srv','')!=""){
		// single service mode on, set by querystring arg
		// single service overrides all else, it will force single resource
		$single_service_mode = true;
		$single_service_id = $jinput->getInt('srv','');
		//echo "single service mode (querystring), id=".$single_service_id;
	}

	if($params->get('res_or_cat') == 4 && $params->get('passed_id') != ""){
		// default resource specified, set by menu parameter
		$default_resource_specified = true;
		$default_resource_id = $params->get('passed_id');
		//echo "default resource specified(menu), id=".$default_resource_id;
	}

	if($params->get('res_or_cat') == 5 && $params->get('passed_id') != ""){
		// default category specified, set by menu parameter
		$default_category_specified = true;
		$default_category_id = $params->get('passed_id');
		//echo "default category specified(menu), id=".$default_category_id;
	}

	// -----------------------------------------------------------------------

	$name = "";
	$email = "";
	$enddate = "";
	$starttime = "";
	$endtime = "";
	$grand_total = "0.00";
	$comment = "";
	$err = "";

	$api_login_id = "";
	$fingerprint = "";
	$amount = "0.00";
	$fp_timestamp = "";
	$fp_sequence = "";

	$showform= true;

	$required_symbol = "<span style='color:#F00'>*</span>";

	// get data for dropdownlist
	$database = JFactory::getDBO();

	$andClause = "";

	// if single service mode, find resource for the service and set single resource mode as well..
	if($single_service_mode){
		// get resource for the service
		$sql = 'SELECT resource_id FROM #__sv_apptpro3_services WHERE id_services = '.(int)$single_service_id.' AND published = 1;';
		try{
			$database->setQuery($sql);
			$single_service_resource = null;
			$single_service_resource = $database -> loadResult();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "sb_tmpl_default", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}
		if($single_service_resource != null){
			$single_resource_mode = true;
			$single_resource_id = $single_service_resource;
		}
	}

	if(!$single_resource_mode){
		// get resource categories
		$database = JFactory::getDBO();
		if($single_category_mode){
			$andClause .= " AND id_categories = ". (int)$single_category_id;
		} else {
			$andClause .= " AND (parent_category IS NULL OR parent_category = '') ";
		}
		$database = JFactory::getDBO();
		$sql = 'SELECT * FROM #__sv_apptpro3_categories WHERE published = 1 '.$andClause.' order by ordering';
		try{
			$database->setQuery($sql);
			$res_cats = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "sb_tmpl_default", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}

		// check for sub-categories
		$sql = 'SELECT count(*) as count FROM #__sv_apptpro3_categories WHERE published = 1 AND (parent_category IS NOT NULL AND parent_category != "") ';
		try{
			$database->setQuery($sql);
			$sub_cat_count = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "sb_tmpl_default", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}

	}

	// get resources
	if(count($res_cats) == 0 || $single_resource_mode){
		// resource categories not in use
		if($user->guest){
			//$andClause = " AND access != 'registered_only' ";
			// access must contain '|1|'
			$andClause = " AND access LIKE '%|1|%' ";
		} else {
			$andClause = " AND access != 'public_only' ";
		}
		if($single_resource_mode){
			$andClause .= " AND id_resources = ". (int)$single_resource_id;
		}
		if($single_category_mode){
			$safe_search_string = '%|' . $database->escape( $cat, true ) . '|%' ;
			$andClause .= " AND category_scope LIKE ".$database->quote( $safe_search_string, false );
		}

		if($single_resource_mode){
			$sql = 'SELECT id_resources,name,description,ordering,disable_dates_before,cost,access,gap,ddslick_image_path,ddslick_image_text FROM #__sv_apptpro3_resources WHERE published=1 '.$andClause.' ORDER BY ordering';
		} else {
			$sql = '(SELECT 0 as id_resources, \''.JText::_('RS1_GAD_SCRN_RESOURCE_DROPDOWN').'\' as name, \''.JText::_('RS1_GAD_SCRN_RESOURCE_DROPDOWN').'\' as description, 0 as ordering, "" as cost, "" as access, 0 as gap, "" as ddslick_image_path, "" as ddslick_image_text) UNION (SELECT id_resources,name,description,ordering,cost,access,gap,ddslick_image_path,ddslick_image_text FROM #__sv_apptpro3_resources WHERE published=1 '.$andClause.') ORDER BY ordering';
		}
		try{
			$database->setQuery($sql);
			$res_rows_raw = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "sb_tmpl_default", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}
		$res_rows_count = 0;
		for($i=0; $i < count( $res_rows_raw ); $i++) {
			if(display_this_resource($res_rows_raw[$i], $user)){
				$res_rows[$res_rows_count] = $res_rows_raw[$i];
				$res_rows_count ++;
			}
		}
	}

	// get config stuff
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}

	// purge stale paypal bookings
	if($apptpro_config->purge_stale_paypal == "Yes"){
		purgeStalePayPalBookings($apptpro_config->minutes_to_stale);
	}

	$sms_dial_code = $apptpro_config->clickatell_dialing_code;

	// get udfs
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_udfs WHERE published=1 AND udf_show_on_screen="Yes" AND scope = "" AND staff_only != "Yes" ORDER BY ordering';
	try{
		$database->setQuery($sql);
		$udf_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}

	// get seat types
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_seat_types WHERE published=1 ORDER BY ordering';
	try{
		$database->setQuery($sql);
		$seat_type_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}

	// get users
	$sql = 'SELECT id,name FROM #__users order by name';
	try{
		$database->setQuery($sql);
		$user_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}

	// get user credit
	$sql = 'SELECT balance FROM #__sv_apptpro3_user_credit WHERE user_id = '.$user->id;
	try{
		$database->setQuery($sql);
		$user_credit = NULL;
		$user_credit = $database -> loadResult();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}

	// check to see if any extras are published, if so show extras line in PayPal totals
	$sql = 'SELECT count(*) as count FROM #__sv_apptpro3_extras WHERE published = 1 AND staff_only != "Yes"';
	try{
		$database->setQuery($sql);
		$extras_row_count = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}

	// get resource rates
	$database = JFactory::getDBO();
	$sql = 'SELECT id_resources,rate,rate_unit,deposit_amount,deposit_unit FROM #__sv_apptpro3_resources';
	try{
		$database->setQuery($sql);
		$res_rates = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}
	$rateArrayString = "<script type='text/javascript'>".
	"var aryRates = {";
	$base_rate = "0.00";
	for($i=0; $i<count($res_rates); $i++){
		if($apptpro_config->enable_overrides == "Yes"){
			$base_rate = getOverrideRate("resource", $res_rates[$i]->id_resources, $res_rates[$i]->rate, $user->id, "rate");
		} else {
			$base_rate = $res_rates[$i]->rate;
		}
		$rateArrayString = $rateArrayString.$res_rates[$i]->id_resources.":".$base_rate."";
		if($i<count($res_rates)-1){
			$rateArrayString = $rateArrayString.",";
		}
	}
	$rateArrayString = $rateArrayString."}</script>";

	$rate_unitArrayString = "<script type='text/javascript'>".
	"var aryRateUnits = {";
	for($i=0; $i<count($res_rates); $i++){
		$rate_unitArrayString = $rate_unitArrayString.$res_rates[$i]->id_resources.":'".$res_rates[$i]->rate_unit."'";
		if($i<count($res_rates)-1){
			$rate_unitArrayString = $rate_unitArrayString.",";
		}
	}
	$rate_unitArrayString = $rate_unitArrayString."}</script>";

	$depositArrayString = "<script type='text/javascript'>".
	"var aryDeposit = {";
	for($i=0; $i<count($res_rates); $i++){
		$depositArrayString = $depositArrayString.$res_rates[$i]->id_resources.":'".$res_rates[$i]->deposit_amount."'";
		if($i<count($res_rates)-1){
			$depositArrayString = $depositArrayString.",";
		}
	}
	$depositArrayString = $depositArrayString."}</script>";

	$deposit_unitArrayString = "<script type='text/javascript'>".
	"var aryDepositUnits = {";
	for($i=0; $i<count($res_rates); $i++){
		$deposit_unitArrayString = $deposit_unitArrayString.$res_rates[$i]->id_resources.":'".$res_rates[$i]->deposit_unit."'";
		if($i<count($res_rates)-1){
			$deposit_unitArrayString = $deposit_unitArrayString.",";
		}
	}
	$deposit_unitArrayString = $deposit_unitArrayString."}</script>";

	if($apptpro_config->clickatell_show_code == "Yes"){
		// get dialing codes
		$database = JFactory::getDBO();
		try{
			$database->setQuery("SELECT * FROM #__sv_apptpro3_dialing_codes ORDER BY country" );
			$dial_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "sb_tmpl_default", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}
	}

	// this overrides the disable-dates-before setting
	if($jinput->getString('mystartdate','')!=""){
   		$startdate = $jinput->getString('mystartdate',''); // usage http://....&mystartdate=2009-09-14
	} else {
		$startdate = JText::_('RS1_INPUT_SCRN_DATE_PROMPT');
	}

	$display_picker_date = $startdate;

	$user = JFactory::getUser();
	$name = "";
	$email = "";
	if(!$user->guest){
		// check to see id user is an admin
		$sql = "SELECT count(*) as count FROM #__sv_apptpro3_resources WHERE published=1 AND ".
			"resource_admins LIKE '%|".$user->id."|%';";
		try{
			$database->setQuery($sql);
			$check = NULL;
			$check = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "sb_tmpl_default", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}
		if($check->count >0){
			$show_admin = true;
		}
		$name = $user->name;
		$email = $user->email;
		// if you want the user's email to be read-only change the above to:
		//$email = $user->email."\" readonly=readonly";

		$user_id = $user->id;

	} else {
		$show_admin = false;
		$user_id = "";
	}

	$err = "";
	$alreadyPosted = false;
	if($session->get("alreadyPosted") == "Yes" ){
		// used hit refresh on confimration page
		//$err = "Data already saved to database.";
		$alreadyPosted = true;
		$session->set("alreadyPosted", "");
	}

	$pay_proc_enabled = isPayProcEnabled();
	$sql = 'SELECT * FROM #__sv_apptpro3_payment_processors WHERE published = 1;';
	try{
		$database->setQuery($sql);
		$pay_procs = NULL;
		$pay_procs = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "gad_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}

	$udf_help_icon = "<img alt=\"\" src='".getImageSrc("help_udf.png")."' class='sv_help_icon' ";

?>
<?php $document = JFactory::getDocument();
$document->addStyleSheet( JURI::base( true )."/components/com_rsappt_pro3/sv_apptpro.css");
?>
<script src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/date.js"></script>
<script src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/script.js"></script>
<script src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/jquery.validate.min.js"></script>
<script src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/ddslick.js"></script>
<script src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/iframeResizer.contentWindow.min.js"></script>

<?php
$document = JFactory::getDocument();
$document->addStyleSheet( "//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css");
?>
<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<?php if($apptpro_config->use_jquery_tooltips == "Yes"){ ?>
<link href="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/sv_tooltip.css" rel="stylesheet">
<script src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/sv_tooltip.js"></script>
<?php } ?>

<?php if($apptpro_config->cart_enable == "Yes" || $apptpro_config->cart_enable == "Public"){ ?>
    <script>
        var iframe = null;
        var cart_dialog = null;
        var cart_title = "<?php echo JText::_('RS1_VIEW_CART_SCRN_TITLE')?>"
        var cart_close = "<?php echo JText::_('SV_CART_CLOSE')?>"
    </script>
<?php } ?>
<script src="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/datepicker_locale/datepicker-<?php echo PICKER_LANG?>.js"></script>
<script>
	var non_booking_days = null;
	var bookoff_dates = null;
	var myMinDate = null;
	var myMaxDate = null;
	jQuery.noConflict();
	if(isNaN(myMinDate)){
		myMinDate = 1;
	}
	if(isNaN(myMaxDate) || myMaxDate == ""){
		myMaxDate = null;
	}
	jQuery(function() {
  		jQuery( "#display_startdate" ).datepicker({
			beforeShowDay: checkday,
			minDate: myMinDate,
			maxDate: myMaxDate,
			showOn: "button",
	 		dateFormat: "<?php echo $apptpro_config->date_picker_format;?>",
			firstDay: <?php echo $apptpro_config->popup_week_start_day ?>,
			buttonImage: "<?php echo JURI::base( true );?>/components/com_rsappt_pro3/icon_cal_gr.png",
			buttonImageOnly: true,
			buttonText: "<?php echo JText::_('RS1_INPUT_SCRN_DATE_PROMPT');?>",
			altField: "#startdate",
			altFormat: "yy-mm-dd" //DO NOT CHANGE
    	});
	});

	function doSubmit(pp){

		document.getElementById("errors").innerHTML = document.getElementById("wait_text").value;

		// ajax validate form
		result = validateForm();
		//alert("|"+result+"|");

		if(result.indexOf('<?php echo JText::_('RS1_INPUT_SCRN_VALIDATION_OK');?>')>-1){
			document.getElementById("ppsubmit").value = pp;
		    //document.body.style.cursor = "wait";
			document.frmRequest.task.value = "process_booking_request";
			document.frmRequest.submit();
			return true;
		} else {
			disable_enableSubmitButtons("enable");
			return false;
		}
		return false;
	}

	function checkSMS(){
		if(document.getElementById("use_sms").checked == true){
			document.getElementById("sms_reminders").value="Yes";
		} else {
			document.getElementById("sms_reminders").value="No";
		}
	}

</script>
<script>
		window.onload = function() {
		   jQuery('#resources_slick').ddslick({
			   onSelected: function(data){
				   jQuery('#resources').val(data.selectedData.value);
				   if(need_changeResource == 1){
					   changeResource();
				   } else {
					   need_changeResource = 1;
				   }
			}
		   });
		   jQuery('#category_id_slick').ddslick({
			   onSelected: function(data){jQuery('#category_id').val(data.selectedData.value);changeCategory();}
		   });
			if(document.getElementById("resources")!=null){
				if(document.getElementById("resources").options.length==2){
					if(document.getElementById("resources_slick") != null){
						jQuery('#resources_slick').ddslick('select', {index: 1 });
						changeResource();
					} else {
						document.getElementById("resources").options[1].selected=true;
						changeResource();
					}
				}
				if(document.getElementById("resources").options.length==1){
					changeResource();
				}

			}
		<?php if($single_category_mode){ ?>
				if(document.getElementById("category_id_slick") != null){
					jQuery('#category_id_slick').ddslick('select', {index: 1 });
				} else {
					document.getElementById("category_id").options[1].selected=true;
					changeCategory();
				}
		<?php } ?>
				if (window!=window.top){
				// if run in a popup, add a close button
				if(document.getElementById("closer")!=null){
					document.getElementById("closer").style.display = "";
					document.getElementById("closer").style.visibility = "visible";
				}
			}

		<?php if($default_resource_specified){ ?>
			if(document.getElementById("resources_slick") != null){
				// get index from value of he normal dropdown..
				//the value for which we are searching
				var searchBy = '<?php echo $default_resource_id;?>';

				//#resources_slick is the id of ddSlick selectbox
				jQuery('#resources_slick li').each(function( index ) {
					  //traverse all the options and get the value of current item
					  var curValue = jQuery( this ).find('.dd-option-value').val();
					  //check if the value is matching with the searching value
					  if(curValue == searchBy){
						  //if found then use the current index number to make selected
						  jQuery('#resources_slick').ddslick('select', {index: jQuery(this).index()});
					  }
				});
				changeResource();
			} else {
				jQuery('#resources').val('<?php echo $default_resource_id;?>');
				changeResource();
			}
		<?php } ?>

		<?php if($default_category_specified){ ?>
			if(document.getElementById("category_id_slick") != null){
				// get index from value of he normal dropdown..
				//the value for which we are searching
				var searchBy = '<?php echo $default_category_id;?>';

				//#resources_slick is the id of ddSlick selectbox
				jQuery('#category_id_slick li').each(function( index ) {
					  //traverse all the options and get the value of current item
					  var curValue = jQuery( this ).find('.dd-option-value').val();
					  //check if the value is matching with the searching value
					  if(curValue == searchBy){
						  //if found then use the current index number to make selected
						  jQuery('#category_id_slick').ddslick('select', {index: jQuery(this).index()});
					  }
				});
				changeCategory();
			} else {
				jQuery('#category_id').val('<?php echo $default_category_id;?>');
				changeCategory();
			}
		<?php } ?>

			submit_section_show_hide("hide")
		}
</script>

<script>
jQuery(document).ready(function(){
});
</script>


	<?php echo $rateArrayString; ?>
    <?php echo $rate_unitArrayString; ?>
    <?php echo $depositArrayString; ?>
    <?php echo $deposit_unitArrayString; ?>

<form name="frmRequest" id="frmRequest" action="<?php echo JRoute::_($this->request_url) ?>" method="post">
<div id="sv_apptpro_request">
  <table >
	<?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "<tr><td colspan='8'><BR /><span class='sv_apptpro_errors'>".JText::_('RS1_INPUT_SCRN_LOGIN_REQUIRED')."</span></td></tr>";} ?>
    <tr>
      <td colspan="4" > <h3><?php echo JText::_('RS1_INPUT_SCRN_TITLE');?></h3></td>
    </tr>
    <tr>
      <td colspan="4" style="vertical-align:top; margin:auto"><div id="sv_header"><?php echo JText::_($apptpro_config->headerText); ?> </div></td>
    </tr>
<?php
	//If you wish to give staff the ability to add bookings for other users,
	//enter the $groupname that should see the 'Select a User' dropdown list.
	//It can be s standard group like 'Author', 'Publisher', etc - or a group you have created.
	//Example:
	//$groupname = "Author";
	//or
	//$groupname = "MyGroupHere";
	$groupname = "";
	$thecount = 0;
	if($groupname != ""){
		$sql = "SELECT count(*) FROM  #__user_usergroup_map WHERE ".
			" user_id=".$user->id." AND group_id=(SELECT id FROM #__usergroups WHERE title='".$groupname."')";
		try{
			$database->setQuery($sql);
			$thecount = $database -> loadResult();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "sb_tmpl_default", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}
	}
	if($thecount>0){ ?>
    <tr>
      <td class="sv_apptpro_request_select_user_label"><?php echo JText::_('RS1_INPUT_SCRN_SELECT_USER');?></td>
  	  <td colspan="3" style="vertical-align:top"><select name="users" id="users" class="sv_apptpro_request_dropdown" onchange="changeUser();">
            <?php
			$k = 0;
			for($i=0; $i < count( $user_rows ); $i++) {
			$user_row = $user_rows[$i];
			?>
                <option value="<?php echo $user_row->id; ?>" <?php if($user_row->id == $user->id ){echo " selected='selected' ";} ?>><?php echo $user_row->name; ?></option>
                <?php $k = 1 - $k;
			} ?>
              </select> &nbsp;&nbsp;<label id="user_fetch" class="sv_apptpro_errors">&nbsp;</label>    </td>
    </tr>
<?php } ?>
    <tr>
      <td class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_NAME');?></td>
      <td style="vertical-align:top; width:80%" colspan="3"><input name="name" type="text" id="name" class="sv_apptpro_request_text"
           placeholder= '<?php echo JText::_('RS1_INPUT_SCRN_NAME_PLACEHOLDER');?>'
      		size="40" maxlength="50" title="<?php echo JText::_('RS1_INPUT_SCRN_NAME_TOOLTIP');?>" value="<?php echo $name; ?>"
            <?php if($name != "" && $apptpro_config->name_read_only == "Yes"){echo " readonly='readonly'";}?> />
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
            <?php echo $required_symbol;?>     </td>
    </tr>
	<?php
		// if cb_mapping value specified, fetch the cb data
		if($user->guest == false and $apptpro_config->phone_cb_mapping != "" and $jinput->getString('phone', '') == ""){
			$phone = getCBdata($apptpro_config->phone_cb_mapping, $user->id);
		} else if($user->guest == false and $apptpro_config->phone_profile_mapping != "" and $jinput->getString('phone', '') == ""){
			$phone = getProfiledata($apptpro_config->phone_profile_mapping, $user->id);
		} else if($user->guest == false and $apptpro_config->phone_js_mapping != "" and $jinput->getString('phone', '') == ""){
			$phone = getJSdata($apptpro_config->phone_js_mapping, $user->id);
		} else {
			$phone = $jinput->getString('phone');
		}
	?>
    <?php if($apptpro_config->requirePhone == "Hide"){?>
	    <input name="phone" type="hidden" id="phone" value="" />
    <?php } else { ?>
     <tr>
      <td class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_PHONE');?></td>
      <td colspan="3" style="vertical-align:top"><input name="phone" type="text" id="phone" value="<?php echo $phone; ?>"
           placeholder= '<?php echo JText::_('RS1_INPUT_SCRN_PHONE_PLACEHOLDER');?>'
      		size="15" maxlength="20" title="<?php echo JText::_('RS1_INPUT_SCRN_PHONE_TOOLTIP');?>"
             class="sv_apptpro_request_text"/> <?php echo ($apptpro_config->requirePhone == "Yes"?$required_symbol:"")?></td>
    </tr>
	<?php } ?>
    <?php if(($apptpro_config->sms_to_resource_only == 'No')
		&& ($apptpro_config->enable_clickatell == "Yes" || $apptpro_config->enable_eztexting == "Yes" || $apptpro_config->enable_twilio == "Yes")){?>
    <tr>
      <td class="sv_apptpro_request_label" style="vertical-align:top"><?php echo JText::_('RS1_INPUT_SCRN_SMS_LABEL');?></td>
      <td colspan="3" style="vertical-align:top"><input type="checkbox" name="use_sms" id="use_sms" onchange="checkSMS();" class="sv_apptpro_request_text"/>&nbsp;
	  		<?php echo JText::_('RS1_INPUT_SCRN_SMS_CHK_LABEL');?>&nbsp;<br />
	      	<?php echo JText::_('RS1_INPUT_SCRN_SMS_PHONE');?>&nbsp;<input name="sms_phone" type="text" id="sms_phone" value="<?php echo $jinput->getString('sms_phone'); ?>"
      		size="15" maxlength="20" title="<?php echo JText::_('RS1_INPUT_SCRN_SMS_PHONE_TOOLTIP');?>"
             class="sv_apptpro_request_text"/>
             <?php if($apptpro_config->clickatell_show_code == "Yes"){ ?>
	            <select name="sms_dial_code" id="sms_dial_code" class="sv_apptpro_request_dropdown" title="<?php echo (blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_('RS1_INPUT_SCRN_SMS_CODE_TOOLTIP'));?>">
              <?php
				$k = 0;
				for($i=0; $i < count( $dial_rows ); $i++) {
				$dial_row = $dial_rows[$i];
				?>
          <option value="<?php echo $dial_row->dial_code; ?>"  <?php if($apptpro_config->clickatell_dialing_code == $dial_row->dial_code){echo " selected='selected' ";} ?>><?php echo $dial_row->country." - ".$dial_row->dial_code ?></option>
              <?php $k = 1 - $k;
				} ?>
      		</select>&nbsp;
   			 <?php } else { ?>
             <input type="hidden" name="sms_dial_code" id="sms_dial_code" value="<?php echo $apptpro_config->clickatell_dialing_code?>" /></td>
             <?php } ?>
             <input type="hidden" name="sms_reminders" id="sms_reminders" value="No" /></td>
    </tr>
    <?php }?>

    <?php if($apptpro_config->requireEmail == "Hide"){?>
	    <input name="email" type="hidden" id="email" value="" />
    <?php } else { ?>
    <tr>
      <td class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_EMAIL');?></td>
      <td colspan="3" style="vertical-align:top"><input name="email" type="text" id="email" value="<?php echo $email ?>"
      		 title="<?php echo JText::_('RS1_INPUT_SCRN_EMAIL_TOOLTIP');?>" size="40" maxlength="50"
	           placeholder= '<?php echo JText::_('RS1_INPUT_SCRN_EMAIL_PLACEHOLDER');?>'
              class="sv_apptpro_request_text"> <?php echo ($apptpro_config->requireEmail == "Yes"?$required_symbol:"")?></td>
    </tr>
	<?php } ?>
    <?php if(count($udf_rows > 0)){
        $k = 0;
        for($i=0; $i < count( $udf_rows ); $i++) {
        	$udf_row = $udf_rows[$i];
			// if cb_mapping value specified, fetch the cb data
			if($user->guest == false and $udf_row->cb_mapping != "" and $jinput->getString('user_field'.$i.'_value', '') == ""){
				$udf_value = getCBdata($udf_row->cb_mapping, $user->id);
			} else if($user->guest == false and $udf_row->profile_mapping != "" and $jinput->getString('user_field'.$i.'_value', '') == ""){
				$udf_value = getProfiledata($udf_row->profile_mapping, $user->id);
			} else if($user->guest == false and $udf_row->js_mapping != "" and $jinput->getString('user_field'.$i.'_value', '') == ""){
				$udf_value = getJSdata($udf_row->js_mapping, $user->id);
			} else {
				$udf_value = $jinput->getString('user_field'.$i.'_value', '');
			}
        	?>
            <tr>
              <td class="sv_apptpro_request_label" style="vertical-align:top"><label id="<?php echo 'user_field'.$i.'_label'; ?>" class="sv_apptpro_request_text"><?php echo JText::_(stripslashes($udf_row->udf_label)) ?></label></td>
              <td colspan="3" style="vertical-align:top">
                <?php
				if($udf_row->read_only == "Yes" && $udf_row->cb_mapping != "" && $user->guest == false){$readonly = " readonly='readonly' ";}
				else if($udf_row->js_read_only == "Yes" && $udf_row->js_mapping != "" && $user->guest == false){$readonly = " readonly='readonly' ";}
				else if($udf_row->profile_read_only == "Yes" && $udf_row->profile_mapping != "" && $user->guest == false){$readonly = " readonly='readonly' ";}
				else {$readonly ="";}
				?>
				<?php if($udf_row->udf_type == 'Textbox'){ ?>
                    <input name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" type="text" value="<?php echo $udf_value; ?>"
                    size="<?php echo $udf_row->udf_size ?>" maxlength="255"
                     <?php echo ($udf_row->udf_placeholder_text != ""?" placeholder='".$udf_row->udf_placeholder_text."'":"")?>
                    <?php echo $readonly?>
                     class="sv_apptpro_request_text" title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>"/>
                     <?php echo ($udf_row->udf_required == "Yes"?$required_symbol:"")?>
                     <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" />
                <?php } else if($udf_row->udf_type == 'Textarea'){ ?>
                    <textarea name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value"
                     <?php echo ($udf_row->udf_placeholder_text != ""?" placeholder='".$udf_row->udf_placeholder_text."'":"")?>
                    <?php echo $readonly?>
                    rows="<?php echo $udf_row->udf_rows ?>" cols="<?php echo $udf_row->udf_cols ?>"
                     class="sv_apptpro_request_text" title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>"/><?php echo $udf_value; ?></textarea>
                     <?php echo ($udf_row->udf_required == "Yes"?$required_symbol:"")?>
                     <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" />
                <?php } else if($udf_row->udf_type == 'Radio'){
						$col_count = 0;
						$aryButtons = explode(",", JText::sprintf("%s",stripslashes($udf_row->udf_radio_options)));
						echo "<table class='sv_udf_radio_table'><tr><td>";
						foreach ($aryButtons as $button){
	                        $col_count++; ?>
							<input name="user_field<?php echo $i?>_value" type="radio" id="user_field<?php echo $i?>_value"
                            <?php
								if(strpos($button, "(d)")>-1){
									echo " checked=\"checked\" ";
									$button = str_replace("(d)","", $button);
								} ?>
							value="<?php echo JText::_(stripslashes(trim($button))) ?>" title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>"/>
                            <span class='sv_udf_radio_text'><?php echo JText::_(stripslashes(trim($button)))?></span>
                            <?php if($col_count >= $udf_row->udf_cols){$col_count = 0; echo "</td></tr><tr><td>";}else{echo "</td><td>";}?>
                            <?php // if($col_count >= $udf_row->udf_cols){$col_count = 0; echo "<br />";}else{echo "&emsp;";}?>
						<?php }
						echo ($udf_row->udf_required == "Yes"?"<td>".$required_symbol."</td>":"");
                        echo "</tr></table>"; ?>
                     <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" />
                <?php } else if($udf_row->udf_type == 'List'){
						$aryOptions = explode(",", JText::sprintf("%s",stripslashes($udf_row->udf_radio_options))); ?>
						<select name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" class="sv_apptpro_request_dropdown"
                        title="<?php echo (blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_(stripslashes($udf_row->udf_tooltip))) ?>">
                        <?php
						foreach ($aryOptions as $listitem){ ?>
				            <option value="<?php echo JText::_(str_replace("(d)","", $listitem)); ?>"
                            <?php
								if(strpos($listitem, "(d)")>-1){
									echo " selected=true ";
									$listitem = str_replace("(d)","", $listitem);
								} ?>
                                ><?php echo JText::_(stripslashes($listitem)); ?></option>
						<?php } ?>
                        </select>
                <?php } else if($udf_row->udf_type == 'Date'){ ?>
                	<script>
						jQuery(function() {
							jQuery( "#user_field<?php echo $i?>_value" ).datepicker({
								showOn: "button",
								firstDay: <?php echo $apptpro_config->popup_week_start_day ?>,
								changeMonth: true,
								changeYear: true,
								yearRange: "1920:2020",
								dateFormat: "<?php echo $apptpro_config->date_picker_format;?>",
								buttonImage: "<?php echo JURI::base( true );?>/components/com_rsappt_pro3/icon_cal_gr.png",
								buttonImageOnly: true,
								buttonText: "<?php echo JText::_('RS1_INPUT_SCRN_DATE_PROMPT');?>"
							});
						});
					</script>
                    <input type="text" readonly="readonly" id="user_field<?php echo $i?>_value" name="user_field<?php echo $i?>_value"
                    	class="sv_date_box" size="10" maxlength="10" value="<?php echo $display_picker_date ?>">
                    <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" />
                <?php } else if($udf_row->udf_type == 'Content'){ ?>
                    <label> <?php echo JText::_($udf_row->udf_content) ?></label>
                    <input type="hidden" name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" value="<?php echo JText::_(htmlentities($udf_row->udf_content, ENT_QUOTES, "UTF-8")) ?>">
                    <input type="hidden" name="user_field<?php echo $i?>_type" id="user_field<?php echo $i?>_type" value='Content'>
                <?php } else { ?>
                    <input name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" type="checkbox" value="<?php echo JText::_('RS1_INPUT_SCRN_CHECKED');?>" title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>"/>
                     <?php echo ($udf_row->udf_required == "Yes"?$required_symbol:"")?>
                    <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" />
                <?php } ?>
                     <input type="hidden" name="user_field<?php echo $i?>_udf_id" id="user_field<?php echo $i?>_udf_id" value="<?php echo $udf_row->id_udfs ?>" />
                <?php if($udf_row->udf_help != "" && $udf_row->udf_help_as_icon == "Yes" ){
					//echo $udf_help_icon." title='".JText::_(stripslashes($udf_row->udf_help))."'>";
			    	include JPATH_COMPONENT.DS."sv_udf_help.php";
				}?>
                </td>
            </tr>
            <?php if($udf_row->udf_help_as_icon == "No" && $udf_row->udf_help != ""){ ?>
            <tr>
      		<td ></td>
	      		<td colspan="3" style="vertical-align:top" class="sv_apptpro_request_helptext"><?php echo JText::_(stripslashes($udf_row->udf_help)) ?></td>
            </tr>
            <?php } ?>
          <?php $k = 1 - $k;
		} ?>
    <?php }?>

	<?php if(count($res_cats) > 0 ){ ?>
    <tr>
      <td class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE_CATEGORIES');?></td>
      <td colspan="3" style="vertical-align:top"><select name="category_id" id="category_id" class="sv_apptpro_request_dropdown" onchange="changeCategory();"
        <?php echo ($apptpro_config->enable_ddslick == "Yes"?" style=\"visibility:hidden; display:none\"":"");?>
      title="<?php echo (blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_('RS1_INPUT_SCRN_RESOURCE_CATEGORIES_TOOLTIP'));?>">
          <option value="0"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE_CATEGORIES_PROMPT');?></option>
          <?php
					$k = 0;
					for($i=0; $i < count( $res_cats ); $i++) {
					$res_cat = $res_cats[$i];
					?>
          <option value="<?php echo $res_cat->id_categories; ?>" ><?php echo JText::_(stripslashes($res_cat->name)); ?></option>
          <?php $k = 1 - $k;
					} ?>
        </select>
      <?php if($apptpro_config->enable_ddslick == "Yes"){?>
            <select id="category_id_slick" >
	          <option value="0"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE_CATEGORIES_PROMPT');?></option>
          <?php
                    $k = 0;
                    for($i=0; $i < count( $res_cats ); $i++) {
                    $res_cat = $res_cats[$i];
                    ?>
	            <option value="<?php echo $res_cat->id_categories; ?>"
                data-imagesrc="<?php echo ($res_cat->ddslick_image_path!=""?getResourceImageURL($res_cat->ddslick_image_path):"")?>"
                    data-description="<?php echo $res_cat->ddslick_image_text?>">
                <?php echo JText::_(stripslashes($res_cat->name)); ?></option>
          <?php $k = 1 - $k;
                    } ?>
            </select>
      <?php } ?>
      </td>
    </tr>
    <?php if($sub_cat_count->count > 0 ){ // there are sub cats ?>
    <tr id="subcats_row" style="visibility:hidden; display:none"><td></td><td colspan="3"><div id="subcats_div"></div></td></tr>
	<?php } ?>
    <tr>
      <td class="sv_apptpro_request_label"><label id="resources_label" style="visibility:hidden;"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE');?></label></td>
      <td colspan="3" width="80%" style="vertical-align:top"><div id="resources_div" style="visibility:hidden;">&nbsp;</div></td>
    </tr>
    <?php } else { ?>
    <tr>
      <td class="sv_apptpro_request_label"><label id="resources_label" style="visibility:hidden;"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE');?></label></td>
      <td colspan="3" style="vertical-align:top"><select name="resources" id="resources" class="sv_apptpro_request_dropdown" onchange="changeResource()"
        <?php echo ($apptpro_config->enable_ddslick == "Yes"?" style=\"visibility:hidden; display:none\"":"");?>
      title="<?php echo (blockIETooltips($apptpro_config->use_jquery_tooltips)?"":JText::_('RS1_INPUT_SCRN_RESOURCE_TOOLTIP'));?>">
          <?php
					$k = 0;
					for($i=0; $i < count( $res_rows ); $i++) {
					$res_row = $res_rows[$i];
					?>
          <option value="<?php echo $res_row->id_resources; ?>" ><?php echo JText::_(stripslashes($res_row->name)); echo ($res_row->cost==""?"":" - "); echo JText::_(stripslashes($res_row->cost)); ?></option>
          <?php $k = 1 - $k;
					} ?>
        </select>
      <?php if($apptpro_config->enable_ddslick == "Yes"){?>
            <select id="resources_slick" >
          <?php
                    $k = 0;
                    for($i=0; $i < count( $res_rows ); $i++) {
                    $res_row = $res_rows[$i];
                    ?>
	            <option value="<?php echo $res_row->id_resources; ?>"
                data-imagesrc="<?php echo ($res_row->ddslick_image_path!=""?getResourceImageURL($res_row->ddslick_image_path):"")?>"
                    data-description="<?php echo $res_row->ddslick_image_text?>">
                <?php echo JText::_(stripslashes($res_row->name)); echo ($res_row->cost==""?"":" - "); echo JText::_(stripslashes($res_row->cost)); ?></option>
          <?php $k = 1 - $k;
                    } ?>
            </select>
      <?php } ?>
      </td>
    </tr>

    <?php } ?>

    <tr id="services" style="visibility:hidden; display:none">
      <td class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_SERVICES');?></td>
    <td colspan="3"><div id="services_div">&nbsp;</div></td>
    </tr>
    <tr id="resource_udfs" style="visibility:hidden; display:none"><td></td><td colspan="3"><div id="resource_udfs_div"></div></td></tr>
    <tr id="resource_seat_types" style="visibility:hidden; display:none"><td colspan="4"><div id="resource_seat_types_div"></div></td></tr>
    <tr id="resource_extras" style="visibility:hidden; display:none"><td colspan="4"><div id="resource_extras_div"></div></td></tr>

	<tr id="datetime" style="display: none">
      <td style="vertical-align:top"> <?php echo JText::_('RS1_INPUT_SCRN_DATETIME');?></td>
      <td colspan="3" style="vertical-align:top">

       	<input name="startdate" id="startdate" type="hidden"
                  class="sv_date_box" value="<?php echo $display_picker_date ?>"/>

		<input type="text" readonly="readonly" id="display_startdate" name="display_startdate" class="sv_date_box" size="10" maxlength="10" value="<?php echo $display_picker_date ?>"
	      onchange="getSlots();">

        <input type="hidden" name="selected_resource_id" id="selected_resource_id" value="-1" />
        <input type="hidden" name="enddate" id="enddate" value="<?php echo $enddate ?>" />
        <input type="hidden" name="starttime" id="starttime" value="<?php echo $starttime ?>"/>
        <input type="hidden" name="endtime" id="endtime" value="<?php echo $endtime ?>"/>
        <input type="hidden" name="endtime_original" id="endtime_original" value=""/>
        <div id="slots" style="visibility:hidden;">&nbsp;</div></td>
	</tr>


    <!-- if you do not want the booking selection to disply on screen, comment out the following table row -->
    <tr id="booking_detail" style="visibility:hidden; display:none">
	  <td></td>
      <td colspan="3">
      <table>
      <tr>
      <td><label style="padding-right:20px;" ><?php echo JText::_('RS1_GAD_SCRN_DETAIL');?></label></td>
	  <td><label class="sv_apptpro_selected_resource" id="selected_starttime"> </label></td>
	  <td><label class="sv_apptpro_selected_resource" ><?php echo JText::_('RS1_TO');?></label></td>
      <td><label class="sv_apptpro_selected_resource" id="selected_endtime"> </label></td>
      </tr>
      </table>
    </td>
    </tr>
	 <?php if($pay_proc_enabled || $apptpro_config->non_pay_booking_button == "DAB" || $apptpro_config->non_pay_booking_button == "DO" ){ ?>
    <tr class="submit_section">
      <td class="sv_apptpro_request_label" style="vertical-align:top">&nbsp;</td>
      <td colspan="3" style="vertical-align:top; height:auto">
      <div id="calcResults" style="visibility:hidden; display:none; height:auto">
        <table style="border:1px solid black; width:300px; margin:auto" class="calcResults_outside">
          <tr class="calcResults_header">
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:center"><?php echo JText::_('RS1_INPUT_SCRN_RES_RATE');?></td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:center"><label id="res_hours_label"><?php echo JText::_('RS1_INPUT_SCRN_RES_RATE_UNITS');?></label></td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:center"><?php echo JText::_('RS1_INPUT_SCRN_RES_RATE_TOTAL');?></td>
          </tr>
          <tr style="text-align:right">
            <td style="border-bottom:solid 1px; border-right:solid 1px; height:auto;"><div style="float:right">
            <div style="display: table-cell; padding-left:0px;"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?></div>
            <div style="display: table-cell; padding-left:5px;"><label id="res_rate"></label></div></div>
            </td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:center"><label id="res_hours"></label></td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><div style="float:right">
            <div style="display: table-cell; padding-left:0px;"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?></div>
            <div style="display: table-cell; padding-left:5px;"><label id="res_total"></label></div></div></td>
          </tr>
      <?php if ($extras_row_count->count > 0 ){?>
          <tr>
            <td style="border-bottom:solid 1px;">&nbsp;            </td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><?php echo JText::_('RS1_INPUT_SCRN_EXTRAS_FEE');?>:&nbsp;</td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><div style="display: table-cell; padding-left:5px; float:right"><label id="extras_fee"></label></div></td>
          </tr>
      <?php } ?>
      <?php if ($apptpro_config->additional_fee != 0.00 ){?>
          <tr>
            <td style="border-bottom:solid 1px;">&nbsp;            </td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><?php echo JText::_('RS1_INPUT_SCRN_RES_ADDITIONAL_FEE');?>:&nbsp;</td>
            <td style="border-bottom:solid 1px; border-right:solid 1px;"><div style="display: table-cell; padding-left:5px; float:right"><label id="res_fee"></label></div></td>
          </tr>
      <?php } ?>
      <?php if($apptpro_config->enable_coupons == "Yes" || $apptpro_config->enable_eb_discount == "Yes"){ ?>
          <tr>
            <td style="border-bottom:solid 1px;">&nbsp;            </td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><?php echo JText::_('RS1_INPUT_SCRN_DISCOUNT');?>:&nbsp;</td>
            <td style="border-bottom:solid 1px; border-right:solid 1px;"><div style="display: table-cell; padding-left:5px; float:right"><label id="discount"></label></div></td>
          </tr>
	  <?php } ?>
      <?php if($apptpro_config->enable_gift_cert == "Yes"){ ?>
          <tr style="text-align:right" id="gc_row">
            <td style="border-bottom:solid 1px;">&nbsp;            </td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><?php echo JText::_('RS1_INPUT_SCRN_GC_CREDIT');?>&nbsp;</td>
            <td style="border-bottom:solid 1px; border-right:solid 1px;"><div style="display: table-cell; padding-left:5px; float:right"><label id="gc_credit"></label> </div></td>
          </tr>
	  <?php } ?>
      <?php if($user_credit != NULL){ ?>
          <tr style="text-align:right">
            <td style="border-bottom:solid 1px;">&nbsp;            </td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><?php echo JText::_('RS1_INPUT_SCRN_USER_CREDIT');?>:&nbsp;</td>
            <td style="border-bottom:solid 1px; border-right:solid 1px;"><div style="display: table-cell; padding-left:5px; float:right"><label id="uc_credit"></label></div></td>
          </tr>
	  <?php } ?>
          <tr style="text-align:right">
            <td style="border-bottom:solid 1px;">&nbsp;
                <input type="hidden" id="additionalfee" value="<?php echo $apptpro_config->additional_fee ?>" />
            	<input type="hidden" id="feerate" value="<?php echo $apptpro_config->fee_rate ?>" />
            	<input type="hidden" id="rateunit" value="<?php echo $apptpro_config->fee_rate ?>" />
                <input type="hidden" id="grand_total" name="grand_total" value="<?php echo $grand_total ?>" />
             </td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><?php echo JText::_('RS1_INPUT_SCRN_RES_RATE_TOTAL');?>:&nbsp;</td>
            <td style="border-bottom:solid 1px; border-right:solid 1px;"><div style="display: table-cell; padding-left:5px; float:right">
			<div style="display: table-cell; padding-left:5px;"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?></div>
            <div style="display: table-cell; padding-left:5px;"><label id="res_grand_total"></label></div></div>
            </td>
          </tr>
          <tr style="text-align:right" id="deposit_only">
            <td style="border-bottom:solid 1px;">&nbsp;            </td>
            <td style="border-bottom:solid 1px; border-right:solid 1px; text-align:right"><?php echo JText::_('RS1_INPUT_SCRN_DEPOSIT');?>:&nbsp;</td>
            <td style="border-bottom:solid 1px; border-right:solid 1px;"><div style="float:right"><div style="display: table-cell; padding-left:0px;"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?></div>
            <div style="display: table-cell; padding-left:5px;"><label id="display_deposit_amount"></label></div></div>
            <input type="hidden" id="deposit_amount" name="deposit_amount" value="0.00" />
			</td>
<?php if($apptpro_config->enable_coupons == "Yes"){ ?>
     <tr>
        <td colspan="3"><input name="coupon_code" type="text" id="coupon_code" value="" size="20" maxlength="80"
	           placeholder= '<?php echo JText::_('RS1_INPUT_SCRN_COUPON_PLACEHOLDER');?>'
              title="<?php echo JText::_('RS1_INPUT_SCRN_COUPON_TOOLTIP');?>" />
              <input type="button" class="button" value="<?php echo JText::_('RS1_INPUT_SCRN_COUPON_BUTTON');?>" onclick="getCoupon()" />
              <div id="coupon_info"></div>
              <input type="hidden" id="coupon_value" />
              <input type="hidden" id="coupon_units" />
        </td>
    </tr>
 <?php } ?>
 <?php if($apptpro_config->enable_gift_cert == "Yes"){ ?>
     <tr class="submit_section" style="border-top:solid 1px">
        <td colspan="3"><?php echo JText::_('RS1_INPUT_SCRN_GIFT_CERT');?>
        <input name="gift_cert" type="text" id="gift_cert" value="" size="20"
	           placeholder= '<?php echo JText::_('RS1_INPUT_SCRN_GIFT_CERT_PLACEHOLDER');?>'
              title="<?php echo JText::_('RS1_INPUT_SCRN_GIFT_CERT_TOOLTIP');?>" />
              <input type="button" class="button" value="<?php echo JText::_('RS1_INPUT_SCRN_GIFT_CERT_BUTTON');?>" onclick="getGiftCert()" />
              <div id="gift_cert_info"></div>
              <input type="hidden" id="gift_cert_bal" />
        </td>
    </tr>
 <?php } ?>
        </table>
      </div>
      </td>
    </tr>
<?php } ?>
    <tr class="submit_section">
      <td></td>
      <td colspan="3"><div id="errors" class="sv_apptpro_errors"><?php echo $err ?></div></td>
	</tr>
    <tr  style="margin:auto; text-align:center" class="submit_section">
      <td colspan="4" style="vertical-align:top; margin:auto"><input  name="cbCopyMe" type="hidden" value="yes"  />
<?php if($apptpro_config->cart_enable == "Yes" || $apptpro_config->cart_enable == "Public"){ ?>
        <input type="button" class="button" value="<?php echo JText::_('RS1_INPUT_SCRN_ADD_TO_CART');?>" id="btnAddToCart" onclick="addToCart(); return false;"
        <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
        <input type="button" class="button" value="<?php echo JText::_('RS1_INPUT_SCRN_VIEW_CART');?>" onclick="viewCart(); return false;"
        <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
<?php } else { ?>
	<?php if( ($apptpro_config->non_pay_booking_button == "Yes" || $pay_proc_enabled == false)
            && $apptpro_config->non_pay_booking_button != "DAB" ){  ?>
              <input type="submit" class="button"  name="submit0" id="submit0" onclick="return doSubmit(0);"
                value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT');?>"
                  <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
    <?php } ?>
    <?php if($apptpro_config->non_pay_booking_button == "DAB"){  ?>
              <input type="submit" class="button"  name="submit3" id="submit4" onclick="return doSubmit(1);"
                value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT');?>"
                  <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
                  <input type="hidden" id="PayPal_mode" value="DAB" />
    <?php } ?>
<?php // put a hidden button on screen in case amount due is $0 and we hide the payment processor button(s)
	if( $apptpro_config->non_pay_booking_button == "No" && $pay_proc_enabled == true) {  ?>
    	<div id="hidden_submit" style="display:none; visibility:hidden">
          <input type="submit" class="button"  name="submit0" id="submit0" onclick="return doSubmit(0);"
            value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT');?>"
              <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
        </div>
<?php } ?>

<?php // Step through all the enabled payment processors and drop in book now buttons.
	foreach($pay_procs as $pay_proc){
		// get settings
		$prefix = $pay_proc->prefix;
		$sql = "SELECT * FROM #__sv_apptpro3_".$pay_proc->config_table;
		try{
			$database->setQuery($sql);
			$pay_proc_settings = NULL;
			$pay_proc_settings = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_pay_procs_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}
		$enable = $prefix."_enable";
		if($pay_proc_settings->$enable == "Yes"){
			$submit_function = "doSubmit";
	    	include JPATH_COMPONENT.DS."payment_processors".DS.$pay_proc->prefix.DS.$pay_proc->prefix."_button.php";
		}
	}?>
<?php } ?>
      </td>
    </tr>
  <?php if($apptpro_config->allow_cancellation == 'Yes'){ ?>
	<tr><td colspan="4">
		<table style="margin:auto" class="sv_apptpro_request_cancel_row" >
        <tr >
          <td><?php echo JText::_('RS1_INPUT_SCRN_CANCEL_TEXT');?></td>
          <td colspan="3" style="vertical-align:top">
          <input name="cancellation_id" type="text" id="cancellation_id" value="" size="50" maxlength="80"
          title="<?php echo JText::_('RS1_INPUT_SCRN_CANCEL_TOOLTIP');?>" style="font-size:10px" />
          <input type="button" class="button"  name="btnCancel" onclick="doCancel(); return false;"
          value="<?php echo JText::_('RS1_INPUT_SCRN_CANCEL_BUTTON');?>"
          <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?>></td>
        </tr>
        <tr>
          <td >&nbsp;</td>
          <td colspan="3" style="vertical-align:top"><div id="cancel_results">      </div></td>
        </tr>
        </table>
	</td></tr>
  <?php } ?>
    <tr>
      <td colspan="4" style="vertical-align:top; margin:auto"><div id="sv_footer"><?php echo JText::_($apptpro_config->footerText) ?></div></td>
    </tr>
  </table>
  </div>
  <?php if($apptpro_config->hide_logo == 'No'){ ?>
	  <span style="font-size:9px; color:#999999">powered by <a href="http://www.AppointmentBookingPro.com" target="_blank">AppointmentBookingPro.com</a> v 3.0.6</span>
	  <?php } ?>
  <input type="hidden" id="wait_text" value="<?php echo JText::_('RS1_INPUT_SCRN_PLEASE_WAIT');?>" />
  <input type="hidden" id="select_date_text" value="<?php echo JText::_('RS1_INPUT_SCRN_DATE_PROMPT');?>" />
  <input type="hidden" id="udf_count" name="udf_count" value="<?php echo count($udf_rows);?>" />
  <input type="hidden" id="enable_paypal" value="<?php echo $apptpro_config->enable_paypal ?>" />
  <input type="hidden" id="authnet_enable" value="<?php echo $apptpro_config->authnet_enable ?>" />
  <input type="hidden" id="_2co_enable" value="<?php echo $apptpro_config->_2co_enable ?>" />
  <input type="hidden" id="non_pay_booking_button" value="<?php echo $apptpro_config->non_pay_booking_button ?>" />
  <input type="hidden" id="flat_rate_text" name="flat_rate_text" value="<?php echo JText::_('RS1_INPUT_SCRN_RES_FLAT_RATE'); ?>" />
  <input type="hidden" id="non_flat_rate_text" name="non_flat_rate_text" value="<?php echo JText::_('RS1_INPUT_SCRN_RES_RATE_UNITS'); ?>" />
  <input type="hidden" id="ppsubmit" name="ppsubmit" value="" />
  <input type="hidden" id="screen_type" name="screen_type" value="non-gad" />
  <input type="hidden" id="reg" name="reg" value="<?php echo ($user->guest?'No':'Yes')?>" />
  <input type="hidden" name="sub_cat_count" id="sub_cat_count" value="<?php echo $sub_cat_count->count ?>"/>
  <input type="hidden" id="uc" value="<?php echo $user_credit ?>" />
  <input type="hidden" id="end_of_day" value="23:59:59" />
  <input type="hidden" id="timeFormat" value="<?php echo $apptpro_config->timeFormat ?>" />

  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
  	<input type="hidden" id="controller" name="controller" value="booking_screen_simple" />
	<input type="hidden" name="id" value="<?php echo $user->id; ?>" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" id="frompage" name="frompage" value="booking_screen_simple" />
  	<input type="hidden" name="frompage_item" id="frompage_item" value="<?php echo $itemId ?>" />

<input type='hidden' name="x_login" value="<?php echo $api_login_id?>" />
<input type='hidden' name="x_fp_hash" value="<?php echo $fingerprint?>" />
<input type='hidden' name="x_amount" value="<?php echo $amount?>" />
<input type='hidden' name="x_fp_timestamp" value="<?php echo $fp_timestamp?>" />
<input type='hidden' name="x_fp_sequence" value="<?php echo $fp_sequence?>" />
<input type='hidden' name="x_version" value="3.1">
<input type='hidden' name="x_show_form" value="payment_form">
<input type='hidden' name="x_test_request" value="false" />
<input type='hidden' name="x_method" value="cc">
	<input type="hidden" name="preset_service" id="preset_service" value="<?php echo $single_service_id; ?>" />
	<input type="hidden" name="validate_text" id="validate_text" value="<?php echo JText::_('RS1_INPUT_SCRN_VALIDATION_OK');?>" />
	<input type="hidden" name="preset_date" id="preset_date" value="<?php echo $startdate; ?>" />
	<input type="hidden" name="date_picker_format" id="date_picker_format" value="<?php echo  $apptpro_config->date_picker_format;?>" />
  <input type="hidden" id="enable_payproc" value="<?php echo ($pay_proc_enabled?"Yes":"No")?>" />
	<input type="hidden" name="gap" id="gap" value="<?php echo $apptpro_config->gap; ?>" />
	<input type="hidden" name="res_spec_gap" id="res_spec_gap" value="0" />
  <?php echo ($apptpro_config->enable_eb_discount=="Yes"?getResourceEBDiscounts():"") ?>
  <?php echo getCategoryDurations(); ?>
	<input type="hidden" name="jit_submit" id="jit_submit" value="<?php echo $apptpro_config->jit_submit; ?>" />
	<input type="hidden" name="uc_used" id="uc_used" value="0" />
	<input type="hidden" name="gc_used" id="gc_used" value="0" />
	<input type="hidden" name="applied_credit" id="applied_credit" value="0" />

</form>
