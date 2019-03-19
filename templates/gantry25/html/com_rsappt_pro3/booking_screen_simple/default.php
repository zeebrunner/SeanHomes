<?php
/*
 ****************************************************************
 Copyright (C) 2008-2013 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2013 Soft Ventures, Inc. All rights reserved.
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

//error_reporting(E_ALL);
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

	JHTML::_('behavior.tooltip');
	JHTML::_('behavior.modal');
	jimport( 'joomla.application.helper' );

	$mainframe = JFactory::getApplication();
	$session = JSession::getInstance($handler=null, $options=null);

	$option = JRequest::getString( 'option', '' );
	$user = JFactory::getUser();
	$itemId = JRequest::getVar('Itemid');

	include_once( JPATH_SITE."/administrator/components/com_rsappt_pro3/sendmail_pro3.php" );
	include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );



/***** perform cancellation ****/
if(isset($_GET['cancelid'])) {
	$cancelUrl = JURI::base() . "index.php?";
    $cancelData = "option=com_rsappt_pro3&controller=ajax&task=cancel_booking&format=raw&cancellation_id=".$_GET['cancelid']."&userDateTime=2015-3-28 15:50:00&browser=Chrome";

    $result = file_get_contents($cancelUrl . $cancelData);
    //print $result;
}
/*****   **/

function getCurrentSeats($inDate, $inTotalSeats)
{

    $resource = 1; /// resource ID

    $startdate = $inDate;
    $totalSeats = $inTotalSeats;

    $database = JFactory::getDBO();
    $sql = "SELECT Sum(booked_seats) FROM #__sv_apptpro3_requests " .
           " WHERE " .
           //   " id_requests != ".$exclude_request." AND ".
           " startdate = '" . $startdate . "' AND " .
           //   " starttime = '".$starttime."' AND ".
           //    " endtime = '".$endtime."' AND ".
           " resource = " . $resource . " AND " .
           "(request_status = 'accepted' or request_status = 'pending') AND " .
           " booked_seats > 0;";

       $database->setQuery($sql);
       $currentcount = $database->loadResult();

       $currentSeatTaken = 100 / $totalSeats * $currentcount; // this is a percentage

    return $currentSeatTaken;
}


	// -----------------------------------------------------------------------
	// see if we need to switch into single-resource or single-category mode.
	$single_resource_mode = false;
	$single_resource_id = "";
	$single_category_mode = false;
	$single_category_id = "";
	$single_service_mode = false;
	$single_service_id = "";
	$single_service_resource = "";

	$params = $mainframe->getPageParameters('com_rsappt_pro3');

	if($params->get('res_or_cat') == 1 && $params->get('passed_id') != ""){
		// single resource mode on, set by menu parameter
		$single_resource_mode = true;
		$single_resource_id = $params->get('passed_id');
		//echo "single resource mode (menu), id=".$single_resource_id;
	}

	if(JRequest::getInt('res','')!=""){
		// single resource mode on, set by menu parameter
		$single_resource_mode = true;
		$single_resource_id = JRequest::getInt('res','');
		//echo "single resource mode (querystring), id=".$single_resource_id;
	}

	if($params->get('res_or_cat') == 2 && $params->get('passed_id') != ""){
		// single category mode on, set by menu parameter
		$single_category_mode = true;
		$single_category_id = $params->get('passed_id');
		//echo "single category mode (menu), id=".$single_category_id;
	}

	if(JRequest::getInt('cat','')!=""){
		// single category mode on, set by menu parameter
		$single_category_mode = true;
		$single_category_id = JRequest::getInt('cat','');
		//echo "single category mode (querystring), id=".$single_category_id;
	}

	if($params->get('res_or_cat') == 3 && $params->get('passed_id') != ""){
		// single service mode on, set by menu parameter
		$single_service_mode = true;
		$single_service_id = $params->get('passed_id');
		//echo "single resource mode (menu), id=".$single_resource_id;
	}

	if(JRequest::getInt('srv','')!=""){
		// single service mode on, set by querystring arg
		// single service overrides all else, it will force single resource
		$single_service_mode = true;
		$single_service_id = JRequest::getInt('srv','');
		//echo "single service mode (querystring), id=".$single_service_id;
	}

	$auto_cancelation = "";
	$user_email = "";
	$user_fullname = "";
	$user_phone ="";

	if(isset($_COOKIE['UBAM'])){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
		->select($db->quoteName(array('name','email')))
		->from($db->quoteName('#__bamuserdata'))
		->where($db->quoteName('cookie_value')." = ".$db->quote($_COOKIE['UBAM']));
		$db->setQuery($query);
		$record_exists = $db->loadAssoc();


		if($record_exists) {
			//$firstname=$record_exists['name'];
			//$lastname=$record_exists['last_name'];
			//$user_fullname = $firstname;
			$user_email=$record_exists['email'];
		}
	}
	if(!$user_email) {
		if(JRequest::getVar("email")) $user_email = JRequest::getVar("email");
	}
	if(!$user_fullname) {
		if(JRequest::getVar("fname")) $user_fname = JRequest::getVar("fname");
		if(JRequest::getVar("lname")) $user_lname = JRequest::getVar("lname");
		$user_fullname = $user_fname.' '.$user_lname;
	}

	if(JRequest::getVar("cancelation_code")) $auto_cancelation = JRequest::getVar("cancelation_code");
	if(JRequest::getVar("phone")) $user_phone = JRequest::getVar("phone");
	if(JRequest::getVar("cell")) $user_cell = JRequest::getVar("cell");

	$user_email = trim($user_email);

	// -----------------------------------------------------------------------

	$name = "";
	$email = "";
	$enddate = "";
	$starttime = "";
	$endtime = "";
	$grand_total = "";
	$comment = "";
	$err = "";

	$api_login_id = "";
	$fingerprint = "";
	$amount = "";
	$fp_timestamp = "";
	$fp_sequence = "";

	$showform= true;

	$required_symbol = " <span class='required'>(required)</span>";
	$required_symbol = "*";


	// get data for dropdownlist
	$database = JFactory::getDBO();

	$andClause = "";

	// if single service mode, find resource for the service and set single resource mode as well..
	if($single_service_mode){
		// get resource for the service
		$sql = 'SELECT resource_id FROM #__sv_apptpro3_services WHERE id_services = '.$single_service_id.' AND published = 1;';
		$database->setQuery($sql);
		$single_service_resource = null;
		$single_service_resource = $database -> loadResult();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			logIt($database->getErrorMsg());
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
			$andClause .= " AND id_categories = ". $single_category_id;
		} else {
			$andClause .= " AND (parent_category IS NULL OR parent_category = '') ";
		}
		$database = JFactory::getDBO();
		$sql = 'SELECT * FROM #__sv_apptpro3_categories WHERE published = 1 '.$andClause.' order by ordering';
		$database->setQuery($sql);
		$res_cats = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			return false;
		}

		// check for sub-categories
		$sql = 'SELECT count(*) as count FROM #__sv_apptpro3_categories WHERE published = 1 AND (parent_category IS NOT NULL AND parent_category != "") ';
		$database->setQuery($sql);
		$sub_cat_count = $database -> loadObject();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			logIt($database->getErrorMsg());
			return false;
		}
		//echo $sub_cat_count->count;

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
			$andClause .= " AND id_resources = ". $single_resource_id;
		}
		if($single_category_mode){
			$andClause .= " AND category_scope LIKE '%|".$cat."|%'";
		}

		if($single_resource_mode){
			$sql = 'SELECT id_resources,name,description,ordering,disable_dates_before,cost,access FROM #__sv_apptpro3_resources WHERE published=1 '.$andClause.' ORDER BY ordering';
		} else {
			$sql = '(SELECT 0 as id_resources, \''.JText::_('RS1_GAD_SCRN_RESOURCE_DROPDOWN').'\' as name, \''.JText::_('RS1_GAD_SCRN_RESOURCE_DROPDOWN').'\' as description, 0 as ordering, "" as cost, "" as access) UNION (SELECT id_resources,name,description,ordering,cost,access FROM #__sv_apptpro3_resources WHERE published=1 '.$andClause.') ORDER BY ordering';
		}
		$database->setQuery($sql);
		$res_rows_raw = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
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
	$database->setQuery($sql);
	$apptpro_config = NULL;
	$apptpro_config = $database -> loadObject();
	if ($database -> getErrorNum()) {
		echo "DB Err: ". $database -> stderr();
		return false;
	}

	// purge stale paypal bookings
	if($apptpro_config->purge_stale_paypal == "Yes"){
		purgeStalePayPalBookings($apptpro_config->minutes_to_stale);
	}

	$sms_dial_code = $apptpro_config->clickatell_dialing_code;

	// get udfs
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_udfs WHERE published=1 AND udf_show_on_screen="Yes" AND scope = ""  ORDER BY ordering';
	$database->setQuery($sql);
	$udf_rows = $database -> loadObjectList();
	if ($database -> getErrorNum()) {
		echo "DB Err: ". $database -> stderr();
		return false;
	}

	// get seat types
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_seat_types WHERE published=1 ORDER BY ordering';
	$database->setQuery($sql);
	$seat_type_rows = $database -> loadObjectList();
	if ($database -> getErrorNum()) {
		echo "DB Err: ". $database -> stderr();
		return false;
	}


	$div_cal = "";
	if($apptpro_config->use_div_calendar == "Yes"){
		$div_cal = "'testdiv1'";
	}

	// get users
	$sql = 'SELECT id,name FROM #__users order by name';
	$database->setQuery($sql);
	$user_rows = $database -> loadObjectList();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
		return false;
	}

	// get user credit
	$sql = 'SELECT balance FROM #__sv_apptpro3_user_credit WHERE user_id = '.$user->id;
	$database->setQuery($sql);
	$user_credit = NULL;
	$user_credit = $database -> loadResult();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
		return false;
	}

	// check to see if any extras are published, if so show extras line in PayPal totals
	$sql = 'SELECT count(*) as count FROM #__sv_apptpro3_extras WHERE published = 1';
	$database->setQuery($sql);
	$extras_row_count = $database -> loadObject();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
		logIt($database->getErrorMsg());
		return false;
	}
	//echo $extras_row_count->count;

	// get resource rates
	$database = JFactory::getDBO();
	$sql = 'SELECT id_resources,rate,rate_unit,deposit_amount,deposit_unit FROM #__sv_apptpro3_resources';
	$database->setQuery($sql);
	$res_rates = $database -> loadObjectList();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
		return false;
	}
	$rateArrayString = "<script type='text/javascript'>".
	"var aryRates = {";
	for($i=0; $i<count($res_rates); $i++){
		$rateArrayString = $rateArrayString.$res_rates[$i]->id_resources.":".$res_rates[$i]->rate."";
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
		$database->setQuery("SELECT * FROM #__sv_apptpro3_dialing_codes ORDER BY country" );
		$dial_rows = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			return false;
		}
	}


	$startdate = JText::_('RS1_INPUT_SCRN_DATE_PROMPT');


	$user = JFactory::getUser();
	if(!$user->guest){
		// check to see id user is an admin
		$sql = "SELECT count(*) as count FROM #__sv_apptpro3_resources WHERE published=1 AND ".
			"resource_admins LIKE '%|".$user->id."|%';";
		$database->setQuery($sql);
		$check = NULL;
		$check = $database -> loadObject();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
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

		$fname = (isset($_SESSION['fname']) && $_SESSION['fname'] != '')?$_SESSION['fname']:$_GET['fname'];
		$lname = (isset($_SESSION['lname']) && $_SESSION['lname'] != '')?$_SESSION['lname']:$_GET['lname'];
		$name = $fname." ".$lname;
		$email = (isset($_SESSION['email']) && $_SESSION['email'] != '')?$_SESSION['email']:$_GET['email'];


		if(($_SESSION['fname'] || $_SESSION['lname']) && $_SESSION['email'])
        {
            $name =  $_SESSION['fname']." ". $_SESSION['lname'];
            $email = $_SESSION['email'];
        }
        else if (($_GET['fname'] || $_GET['lname']) && $_GET['email']){
            $name =  $_GET['fname']." ". $_GET['lname'];
            $email = $_GET['email'];
        }

	}

	$err = "";
	$alreadyPosted = false;
	if($session->get("alreadyPosted") == "Yes" ){
		// used hit refresh on confimration page
		//$err = "Data already saved to database.";
		$alreadyPosted = true;
		$session->set("alreadyPosted", "");
	}


?>


<script language="JavaScript">

	function doSubmit(pp){

		selectDaySlot('<span class="time button active" onclick="selectDaySlot(this);" data="2017-04-29|11:00,17:00">11:00 AM</span>');

		//document.getElementById("errors").innerHTML = document.getElementById("wait_text").value;
		document.getElementById("errors").innerHTML = document.getElementById("wait_text").value;
		//document.getElementById("errors1").innerHTML = document.getElementById("wait_text").value;
			if(jQuery('#user_field0_value').val()==null){
				document.getElementById("errors").innerHTML = 'Please fill in the fields. ';
				return false;
			}

        if(!document.getElementById("name").value || document.getElementById("name").value == '' ){
            document.getElementById("errors").innerHTML = 'Validation Failed:<br>Please enter your name';
            return false;
        }
        if(!document.getElementById("email").value || document.getElementById("email").value == ''  ){
            document.getElementById("errors").innerHTML = 'Validation Failed:<br>Please enter your email';
            return false;
        }
       if( !validateEmailFormat(document.getElementById("email").value) ){
            document.getElementById("errors").innerHTML = 'Validation Failed:<br>Please enter a valid email address';
            return false;
        }
/*        if(document.getElementById("use_sms").checked && (!document.getElementById("sms_phone").value || document.getElementById("sms_phone").value == '') ){
            document.getElementById("errors").innerHTML = 'Validation Failed:<br>Please enter your cell phone number';
            return false;
        }
*/
        if(document.getElementById("use_sms").checked && !validatePhoneNumber(document.getElementById("sms_phone").value)){
            document.getElementById("errors").innerHTML = 'Validation Failed:<br>Please enter a valid cell phone number (e.g. xxx-xxx-xxxx)';
            return false;
        }


        // ajax validate form
        result = validateForm();
        console.log("|"+result+"|");

		if(result.indexOf('<?php echo JText::_('RS1_INPUT_SCRN_VALIDATION_OK');?>')>-1){
			document.getElementById("ppsubmit").value = pp;
		    //document.body.style.cursor = "wait";
			document.frmRequest.task.value = "process_booking_request";
			return true;
		} else {
            document.getElementById("errors").innerHTML = result;
			//document.getElementById("errors1").innerHTML = result;
			return false;
		}
		return false;
	}

    function validateEmailFormat(email) {
        var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        return re.test(email);
    }
    function validatePhoneNumber(phone)
    {

        var length = phone.length;
        var ph = phone.replace(/\s/g, '');
        ph = ph.replace(/-/g, "");

        chk1="1234567890()-+ ";
        for(i=0;i<length;i++) {
            ch1=ph.charAt(i);
            rtn1=chk1.indexOf(ch1);
            if(rtn1==-1)
                return false;
        }
        if(ph.length != 10) return false;
        return true;
    }

    function checkSMS(){
		if(document.getElementById("use_sms").checked == true){
			document.getElementById("sms_reminders").value="Yes";
			jQuery('#cell_required').show();
			jQuery('#sms_phone_block').show();
		} else {
			document.getElementById("sms_reminders").value="No";
			jQuery('#cell_required').hide();
			jQuery('#sms_phone_block').hide();
		}
	}




	//var week1 = ['2015-04-11', '2015-04-12', '2015-04-13', '2015-04-14', '2015-04-15', '2015-04-16'];
	//var week2 = [ '2015-04-18', '2015-04-19', '2015-04-20', '2015-04-21', '2015-04-22', '2015-04-23'];
    var week1 = ['2015-12-05'];
    //var week2 = [ '2015-04-18', '2015-04-19'];

	//var week3 = [ '2015-04-25', '2015-04-26'];


	var datesLimit = 0;
    var weekDayCounter = 0;
	var slotsInner = [];
    var weekDates = [];
	var daySlots = [];
	var timelotsHTML = '';

    var globalDate = false;
    var counter = 0;
    var nextWeekTriggered = false;




    function formatTime(h_24) {
        var h = h_24 % 12;
        if (h === 0) h = 12;
        return (h < 10 ? '0' : '') + h + (h_24 < 12 ? 'AM' : 'PM');
    }

    function getDaysSlots(){

        document.getElementById("startdate").value = week1[weekDayCounter];

        if(globalDate != '') document.getElementById("startdate").value = globalDate;

        setTimeout(getSlots, 300);

        return true;

    }
	function moreDaysSlots(slots) {

        slotsInner.push(slots);

        var temp = "<option value";
        var count = slots.split(temp).length -2;  // how many slots are returned
        //console.log(count);

        if(globalDate)
       {
            globalDate = false;
            weekDayCounter--;

       } else {
            nextWeekTrigger = true;
            weekDates.push(week1[weekDayCounter]);

       }
         weekDayCounter++;



        if(weekDayCounter < week1.length) {


            if (count <= 0 && !nextWeekTriggered ) { // if only two slots left available in week one, show slots for same day in week2 // changes to 0 to turn it off

                nextWeekTriggered = true; /// load next week date only once
                counter++;

                globalDate = week2[weekDayCounter-1];  /// load next week date
                weekDates.push(week2[weekDayCounter-1]);
                getDaysSlots();

            }else getDaysSlots();


        }
            else checkSlots();


			//document.getElementById("startdate").value = '<?php $startdate ?>';  // this was setting startdate to ""


		return true;
	}
    function checkSlots(){
        showDaysSlots();
    }

	function showDaysSlots() {

        weekDates.sort(); // rearranging dates in case "same day next week" day opened due to lack of timeslots (<2)

       // console.log(slotsInner);
       // console.log(weekDates);


        for (var i=0; i < slotsInner.length; i++) {
			var optionsArray = slotsInner[i].split('<option');

			var slotsObjects = [];
			for(var j=2; j < optionsArray.length; j++) {
				var valStart = optionsArray[j].indexOf('value=');
				var valEnd = optionsArray[j].indexOf('>');
				var value = optionsArray[j].substring(valStart+6,valEnd);
				var labelStart = valEnd+1;
				var labelEnd = optionsArray[j].indexOf('</option>');
				var label = optionsArray[j].substring(labelStart,labelEnd);

				var option = {
					date:weekDates[i],
					value:value,
					label:label
				};

				slotsObjects.push(option);

			}
			daySlots.push(slotsObjects);

		}
       // alert(weekDates.length);

        for(var i=0; i < weekDates.length; i++) {

			var slotsObjects = daySlots[i];
			var slotsObjectsLength = slotsObjects.length;

			if(slotsObjectsLength > 0) {
				timelotsHTML += '<div class="apptDay">';
				timelotsHTML += '<div class="apptdate" id="day'+weekDates[i]+'"><img src="images/apptbooking/dates/'+weekDates[i]+'.png" alt="'+weekDates[i]+'" ><\/div>';

				timelotsHTML += '<div class="timelots">';



				for(var j=0; j < slotsObjectsLength; j++) {

					//if(j==0 && weekDates[i]=="2015-06-06") continue; //VIP

					var tempp = slotsObjects[j].label.split(' - ')[0]; // value=8:00,9:00>

					/*tempp = tempp.replace("value=", ""); // 8:00 - 9:00>
					tempp = tempp.replace(",", " - "); // 8:00 - 9:00>
					tempp = tempp.replace(">", ""); // 8:00 - 9:00
					tempp = tempp.replace("\"", ""); // remove 1st occurence of "
					tempp = tempp.replace("\"", ""); //remove 2nd occurence of "
					console.log(tempp);*/
					slotsObjects[j].value = slotsObjects[j].value.replace("\"", "");
					slotsObjects[j].value = slotsObjects[j].value.replace("\"", "");
					/*var temppArr = tempp.split(' - ');

					var startArr = temppArr[0].split(':');

					var endArr = temppArr[1].split(':');

					var startTime = formatTime(startArr[0]);
					startTime = startTime.slice(0, 2)+":"+startArr[1] +startTime.slice(2);

					var endTime = formatTime(endArr[0]);
					endTime = endTime.slice(0, 2)+":"+endArr[1]+endTime.slice(2);

					tempp = startTime +" - "+endTime;
					tempp = startTime;*/

					var currentSeatsSat = <?php echo getCurrentSeats("2015-10-17", "600"); ?>;
					//var currentSeatsSun = <?php echo getCurrentSeats("2015-10-18", "700"); ?>;

                    //console.log(currentSeatsSat);

                    /****  hack: only showing the below timeslots for the date specified **/
                    /*if(slotsObjects[j].date == '2015-05-23') {
                        if (slotsObjects[j].value != '13:00,13:45' && slotsObjects[j].value != '13:45,14:30' && slotsObjects[j].value != '14:30,15:15' && slotsObjects[j].value != '15:15,16:00' && slotsObjects[j].value != '16:00,16:45')
                            continue;
                    }*/

                    /*if(slotsObjects[j].date == '2015-06-06') {
                        if ((slotsObjects[j].value == '14:45,15:30' || slotsObjects[j].value == '15:30,16:15' || slotsObjects[j].value == '16:15,17:00' || slotsObjects[j].value == '17:00,17:45') && currentSeatsSat < 70) continue;
                    }*/
                    /*if(slotsObjects[j].date == '2015-04-12') {
                        if ((slotsObjects[j].value == '15:15,16:00' || slotsObjects[j].value == '16:00,16:45' || slotsObjects[j].value == '16:45,17:30') && currentSeatsSun < 70) continue;
                    }*/

                    timelotsHTML += '<span class="time button" onclick="selectDaySlot(this);" data="'+slotsObjects[j].date+'|'+slotsObjects[j].value+'">'+tempp+'<\/span>';

				}
				timelotsHTML += '<\/div><div class="clear"><\/div><\/div>';
			}
        }




		//document.getElementById("slots").innerHTML = timelotsHTML;

		//autopopulate();
	}
	function selectDaySlot(e) {


		console.log(e);
		jQuery('.time.button').removeClass('active');
		jQuery(e).toggleClass('active');
		var dataAttr = jQuery(e).attr('data').split('|');

		document.getElementById("startdate").value = dataAttr[0];

		document.getElementById("enddate").value = document.getElementById("startdate").value;

		var temp = dataAttr[1].split(',');
		document.getElementById("starttime").value = temp[0];
		console.log(temp[0]);
		document.getElementById("endtime").value = temp[1];
		console.log(temp[1]);

        console.log("in selectDaySlot"+document.getElementById("starttime").value +" " + document.getElementById("endtime").value);
	}

	function autopopulate() {
		var cancelation_code = "<?php echo $auto_cancelation; ?>";
		if(cancelation_code) {
			document.getElementById("cancellation_id").value = cancelation_code;
			doCancel();
		}

		var user_email = "<?php echo $user_email; ?>";
		if(user_email) {
			document.getElementById("email").value = user_email;
		}

		var user_fullname = "<?php echo $user_fullname; ?>";
		if(user_fullname) {
			document.getElementById("name").value = user_fullname;
		}

		var user_phone = "<?php echo $user_phone; ?>";
		if(user_phone) {
			document.getElementById("phone").value = user_phone;
		}

		var user_cell = "<?php echo $user_cell; ?>";
		if(user_cell) {
			document.getElementById("sms_phone").value = user_cell;
		}

		var prev_page = "<?php echo $_SERVER['HTTP_REFERER']; ?>";
		if(prev_page.indexOf('soulcondos.com') > -1) {
			console.log(prev_page);
			ga('send', 'event', 'Signed Up', 'Submit', 'First Step Sign Up');
		}
	}

</script>
<script language="javascript">
		window.onload = function() {
			autopopulate();


			if(document.getElementById("resources")!=null){
				if(document.getElementById("resources").options.length==1){
					document.getElementById("resources").options[0].selected=true;
				}
				if(document.getElementById("resources").options.length==2){
					document.getElementById("resources").options[1].selected=true;
				}
                document.getElementById("resources").value = "1";
				changeResource();
			}
		<?php if($single_category_mode){ ?>
				document.getElementById("category_id").options[1].selected=true;
				changeCategory();
		<?php } ?>
			getDaysSlots();


		}



</script>
<?php echo $rateArrayString; ?>
<?php echo $rate_unitArrayString; ?>
<?php echo $depositArrayString; ?>
<?php echo $deposit_unitArrayString; ?>

<div id="apptWrapper">
<h2 id="apptbookingHeader" class="title large">
RSVP FOR THE EVENT
</h2>


<?php
if(isset($_GET['cancelid'])) {
?>
<script>
jQuery(document).ready(function(){
	jQuery('#apptbookingHeader').text('Your RSVP appointment has been cancelled.');
	var count = 0;
	var cancelEmail = setInterval(function(){
		count+=1;
		if(jQuery('#email').val()!=' ' && jQuery('#email').val()!='' ){
			console.log('go');
			clearInterval(cancelEmail);
			jQuery.ajax({
				url: "Mailchimp/API/src/UpdateMember.php",
				data:{'MERGE0':jQuery('#email').val()}, type:'POST'
			})
			.done(function( data ) {
				console.log(data);
			});
		}else{
			console.log('bad');
		}
		if(count == 100){
			clearInterval(cancelEmail);
		}
	},200);

});
</script>
<?php
}
?>

<form name="frmRequest" action="<?php echo JRoute::_($this->request_url) ?>" method="post">
<?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "<span class='sv_apptpro_errors'>".JText::_('RS1_INPUT_SCRN_LOGIN_REQUIRED')."</span>";} ?>

<div id="sv_apptpro_request">
	<div id="userInfo">

		<div class="sv_apptpro_request_block name_block">
			<?php if(!$user_fullname) : ?>
			 <div id="name_block" >
				<label for="name" class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_NAME');?><?php echo $required_symbol;?></label>
				<input name="name" type="text" id="name" class="sv_apptpro_request_text" size="40" maxlength="50" placeholder="Name:" title="<?php echo JText::_('RS1_INPUT_SCRN_NAME_TOOLTIP');?>" value="<?php echo $name;?>" <?php if($name != "" && $apptpro_config->name_read_only == "Yes"){echo " readonly='readonly'";}?> />
			</div>
			<?php else : ?>
				<p class="username"><?php //echo $user_fullname; ?></p>
				<input name="name" type="text" id="name" placeholder="Name:" value="<?php echo $user_fullname; ?>" />
			<?php endif; ?>
			<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
		</div>
		<?php if($apptpro_config->requireEmail == "Hide"){?>
			<input name="email" type="hidden" id="email" value="" />
		<?php } else { ?>
		<div class="sv_apptpro_request_block email_block" style="margin-bottom: 7px;display:flex;">
			<?php if(!$user_email) : ?>
				<div id="email_block" >
				<label for="email" class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_EMAIL');?><?php echo ($apptpro_config->requireEmail == "Yes"?$required_symbol:"")?></label>
				<input name="email" type="email" id="email" value="<?php echo $email ?>" title="<?php echo JText::_('RS1_INPUT_SCRN_EMAIL_TOOLTIP');?>" size="40" maxlength="50" class="sv_apptpro_request_text">
				</div>
			<?php else : ?>
				<p class="useremail" style="display:none;"><?php echo $user_email; ?></p>
				<input name="email" type="text" id="email" value="<?php echo $email ?>">
			<?php endif; ?>
		</div>
		<?php } ?>
		<?php if($apptpro_config->requirePhone == "Hide"){?>
			<input name="phone" type="hidden" id="phone" value="" />
		<?php } else { ?>
		<div class="sv_apptpro_request_block">
			<label for="phone" class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_PHONE');?><?php echo ($apptpro_config->requirePhone == "Yes"?$required_symbol:"")?></label>
			<input name="phone" type="text" id="phone" value="<?php echo $phone ?>" title="<?php echo JText::_('RS1_INPUT_SCRN_PHONE_TOOLTIP');?>" size="15" maxlength="20" class="sv_apptpro_request_text">
		</div>
		<?php } ?>


		<div class="sv_apptpro_request_block name_block">
		  <input name="sms_phone" style="margin-bottom: 22px;" type="text" placeholder="Mobile:" id="sms_phone" value="<?php echo $user_cell; ?>"  size="15" maxlength="20" title="<?php echo JText::_('RS1_INPUT_SCRN_SMS_PHONE_TOOLTIP');?>" class="sv_apptpro_request_text"/>
		</div>

		<?php if(count($udf_rows > 0)){
			$k = 0;
			for($i=0; $i < count( $udf_rows ); $i++) {
				$udf_row = $udf_rows[$i];
				// if cb_mapping value specified, fetch the cb data
				if($user->guest == false and $udf_row->cb_mapping != "" and JRequest::getVar('user_field'.$i.'_value', '') == ""){
					$udf_value = getCBdata($udf_row->cb_mapping, $user->id);
				} else if($user->guest == false and $udf_row->profile_mapping != "" and JRequest::getVar('user_field'.$i.'_value', '') == ""){
					$udf_value = getProfiledata($udf_row->profile_mapping, $user->id);
				} else if($user->guest == false and $udf_row->js_mapping != "" and JRequest::getVar('user_field'.$i.'_value', '') == ""){
					$udf_value = getJSdata($udf_row->js_mapping, $user->id);
				} else {
					$udf_value = JRequest::getVar('user_field'.$i.'_value', '');
				}
				?>
				<div class="sv_apptpro_request_block udf">
				  <label id="<?php echo 'user_field'.$i.'_label'; ?>" class="sv_apptpro_request_label udf"><?php echo JText::_(stripslashes($udf_row->udf_label)) ?>:</label>
					<?php
					if($udf_row->read_only == "Yes" && $udf_row->cb_mapping != "" && $user->guest == false){$readonly = " readonly='readonly' ";}
					else if($udf_row->js_read_only == "Yes" && $udf_row->js_mapping != "" && $user->guest == false){$readonly = " readonly='readonly' ";}
					else if($udf_row->profile_read_only == "Yes" && $udf_row->profile_mapping != "" && $user->guest == false){$readonly = " readonly='readonly' ";}
					else {$readonly ="";}
					?>
					<?php if($udf_row->udf_type == 'Textbox'){ ?>
						<input name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" type="text" value="<?php echo $udf_value; ?>"
						size="<?php echo $udf_row->udf_size ?>" maxlength="255"
						<?php echo $readonly?>
						 class="sv_apptpro_request_text" title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>"/>
						 <?php echo ($udf_row->udf_required == "Yes"?$required_symbol:"")?>
						 <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" /></td>
					<?php } else if($udf_row->udf_type == 'Textarea'){ ?>
						<textarea name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value"
						<?php echo $readonly?>
						rows="<?php echo $udf_row->udf_rows ?>" cols="<?php echo $udf_row->udf_cols ?>"
						 class="sv_apptpro_request_text" title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>"/><?php echo $udf_value; ?></textarea>
						 <?php echo ($udf_row->udf_required == "Yes"?$required_symbol:"")?>
						 <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" /></td>
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
								value="<?php echo stripslashes(trim($button)) ?>" title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>"/>
								<?php echo JText::_(stripslashes(trim($button)))?>
								<?php if($col_count >= $udf_row->udf_cols){$col_count = 0; echo "</td></tr><tr><td>";}else{echo "</td><td>";}?>
								<?php // if($col_count >= $udf_row->udf_cols){$col_count = 0; echo "<br />";}else{echo "&emsp;";}?>
							<?php }
							echo "</tr></table>"; ?>
						 <?php echo ($udf_row->udf_required == "Yes"?$required_symbol:"")?>
						 <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" /></td>
					<?php } else if($udf_row->udf_type == 'List'){
							$aryOptions = explode(",",  JText::_(stripslashes($udf_row->udf_radio_options))); ?>
							<select name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" class="sv_apptpro_request_dropdown"
							title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>">
							<?php
							foreach ($aryOptions as $listitem){ ?>
								<option value="<?php echo str_replace("(d)","", $listitem); ?>"
								<?php
									if(strpos($listitem, "(d)")>-1){
										echo " selected=true ";
										$listitem = str_replace("(d)","", $listitem);
									} ?>
									><?php echo JText::_(stripslashes($listitem)); ?></option>
							<?php } ?>
							</select>
					<?php } else if($udf_row->udf_type == 'Date'){ ?>
						<input readonly="readonly" name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" type="text"
							  class="sv_ts_request_text" size="10" maxlength="10" value=""/>
							  &nbsp;<a href="#" id="anchor10<?php echo $i?>" onclick="cal.select(document.forms['frmRequest'].<?php echo "user_field".$i."_value"?>,'anchor10<?php echo $i?>','yyyy-MM-dd'); return false;"
							 name="anchor10<?php echo $i?>"><img height="15" hspace="2" src="<?php echo JURI::base();?>components/com_rsappt_pro3/icon_cal.gif" width="16" border="0"></a>
						 <input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" /></td>
					<?php } else if($udf_row->udf_type == 'Content'){ ?>
						<label> <?php echo JText::_($udf_row->udf_content) ?></label>
						<input type="hidden" name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" value="<?php echo JText::_(htmlentities($udf_row->udf_content, ENT_QUOTES, "UTF-8")) ?>">
						<input type="hidden" name="user_field<?php echo $i?>_type" id="user_field<?php echo $i?>_type" value='Content'>
					<?php } else { ?>
						<input name="user_field<?php echo $i?>_value" id="user_field<?php echo $i?>_value" type="checkbox" value="<?php echo JText::_('RS1_INPUT_SCRN_CHECKED');?>" title="<?php echo JText::_(stripslashes($udf_row->udf_tooltip)) ?>"/>
						 <?php echo ($udf_row->udf_required == "Yes"?$required_symbol:"")?>
						<input type="hidden" name="user_field<?php echo $i?>_is_required" id="user_field<?php echo $i?>_is_required" value="<?php echo $udf_row->udf_required ?>" /></td>
					<?php } ?>
						 <input type="hidden" name="user_field<?php echo $i?>_udf_id" id="user_field<?php echo $i?>_udf_id" value="<?php echo $udf_row->id_udfs ?>" />

					<?php echo JText::_(stripslashes($udf_row->udf_help)) ?>
				</div>
			  <?php $k = 1 - $k;
			} ?>
		<?php }?>
		<?php

        if(($apptpro_config->sms_to_resource_only == 'No') && ($apptpro_config->enable_clickatell == "Yes" || $apptpro_config->enable_eztexting == "Yes" || $apptpro_config->enable_twilio == "Yes")){?>

		<div class="sv_apptpro_request_block " >
        	<input type="checkbox" name="use_sms" id="use_sms" onchange="checkSMS();" class="sv_apptpro_request_text"/>
            <?php echo JText::_('RS1_INPUT_SCRN_SMS_LABEL');?>
            <div id="sms_phone_block" >

                <label for="sms_phone" style="display:none" class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_SMS_PHONE');?>
									<span id="cell_required" style="display:none;"><?php echo $required_symbol;?></span>
								</label>

				<?php if($apptpro_config->clickatell_show_code == "Yes"){ ?>
                    <select name="sms_dial_code" id="sms_dial_code" class="sv_apptpro_request_dropdown" title="<?php echo JText::_('RS1_INPUT_SCRN_SMS_CODE_TOOLTIP');?>">
                        <?php
                        $k = 0;
                        for($i=0; $i < count( $dial_rows ); $i++) {
                            $dial_row = $dial_rows[$i];
                            ?>
                            <option value="<?php echo $dial_row->dial_code; ?>"  <?php if($apptpro_config->clickatell_dialing_code == $dial_row->dial_code){echo " selected='selected' ";} ?>><?php echo $dial_row->country." - ".$dial_row->dial_code ?></option>
                            <?php $k = 1 - $k;
                        } ?>
                    </select>
                <?php } else { ?>
				 <input type="hidden" name="sms_dial_code" id="sms_dial_code" value="<?php echo $apptpro_config->clickatell_dialing_code?>" /></td>
				 <?php } ?>
            </div>

			<input type="hidden" name="sms_reminders" id="sms_reminders" value="No" />
		</div>
		<?php }?>
	</div>
    <div id="errors" class="sv_apptpro_errors" ><?php echo $err ?></div>

    <div class="sv_apptpro_request_block" id="submitBlock">
        <input  name="cbCopyMe" type="hidden" value="yes"  />
        <?php if($apptpro_config->cart_enable == "Yes"){ ?>
                <input type="button" class="button" value="<?php echo JText::_('RS1_INPUT_SCRN_ADD_TO_CART');?>" onclick="addToCart(); return false;"
                <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
                <input type="button" class="button" value="<?php echo JText::_('RS1_INPUT_SCRN_VIEW_CART');?>" onclick="viewCart(); return false;"
                <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
        <?php } else { ?>
            <?php if( ($apptpro_config->non_pay_booking_button == "Yes" || ($apptpro_config->authnet_enable == "No" && $apptpro_config->enable_paypal == "No" && $apptpro_config->_2co_enable == "No"))
                    && $apptpro_config->non_pay_booking_button != "DAB" ){  ?>
                      <input type="submit" class="button"  name="submit" id="submit" onclick="return doSubmit(0);"
                        value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT');?>"
                          <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
            <?php } ?>
            <?php if($apptpro_config->non_pay_booking_button == "DAB"){  ?>
                      <input type="submit" class="button"  name="submit3" id="submit4" onclick="return doSubmit(1);"
                        value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT');?>"
                          <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> />
                          <input type="hidden" id="PayPal_mode" value="DAB" />
            <?php } ?>
            <?php if($apptpro_config->enable_paypal == "Yes"){
                    if($apptpro_config->paypal_button_url != ""){
                        $lang = JFactory::getLanguage();
                        $paypal_button_url = str_replace("en_US", str_replace("-", "_", $lang->getTag()), $apptpro_config->paypal_button_url);?>
                            <input type="image" id="btnPayPal"  align="top" src="<?php echo $paypal_button_url ?>" border="0" name="submit" alt="submit this form" onclick="return doSubmit(1);"
                            <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> >
                    <?php } else { ?>
                    <input type="submit" class="button" onclick="return doSubmit(1);" name="submit2" id="submit2" value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT_PAYPAL');?>"
                            <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo " disabled ";} ?> />
                    <?php } ?>

            <?php } ?>
            <?php if($apptpro_config->authnet_enable == "Prod" || $apptpro_config->authnet_enable == "Test"){
                    if($apptpro_config->authnet_button_url != ""){
                        $authnet_button_url = $apptpro_config->authnet_button_url;?>
                            <input type="image" id="btnAuthNet"  align="top" src="<?php echo $authnet_button_url ?>" border="0" name="submit" alt="submit this form" onclick="return doSubmit(2);"
                            <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> >
                    <?php } else { ?>
                    <input type="submit" class="button" onclick="return doSubmit(2);" name="submit3" id="submit3" value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT_AUTHNET');?>"
                            <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo " disabled ";} ?> />
                    <?php } ?>

            <?php } ?>
            <?php if($apptpro_config->_2co_enable == "Yes"){
                    if($apptpro_config->_2co_button_url != ""){
                        $_2co_button_url = $apptpro_config->_2co_button_url;?>
                            <input type="image" id="btn2Co"  align="top" src="<?php echo $_2co_button_url ?>" border="0" name="submit" alt="submit this form" onclick="return doSubmit(3);"
                            <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> >
                    <?php } else { ?>
                    <input type="submit" class="button" onclick="return doSubmit(3);" name="submit4" id="submit4" value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT_2CO');?>"
                            <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo " disabled ";} ?> />
                    <?php } ?>

            <?php } ?>
        <?php } ?>
    </div>
		<p class="disclaimer" style="
    font-size: 14px;
    margin-top: 12px;">Person who RSVPs for event must be purchaser listed on APS.</p>
    <p class="disclaimer" style="display:none;">Person who RSVPs for event must be purchaser listed on APS.<?php //echo JText::_($apptpro_config->footerText); ?></p>
</div>


	<!--<h3 class="header"><?php /*echo JText::_('CLICK_TO_PICK_YOUR_TIME'); */?></h3>-->



	<div id="userAppt" >
		<div class="sv_apptpro_request_block_hidden" style="display:none;">
			<?php if(count($res_cats) > 0 ){ ?>
			<div>
			  <label class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE_CATEGORIES');?></label>
			 <select name="category_id" id="category_id" class="sv_apptpro_request_dropdown" onchange="changeCategory();"
			  title="<?php echo JText::_('RS1_INPUT_SCRN_RESOURCE_CATEGORIES_TOOLTIP');?>">
				  <option value="0"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE_CATEGORIES_PROMPT');?></option>
				  <?php
							$k = 0;
							for($i=0; $i < count( $res_cats ); $i++) {
							$res_cat = $res_cats[$i];
							?>
				  <option value="<?php echo $res_cat->id_categories; ?>" <?php if($resource_id == $res_cat->id_categories ){echo " selected='selected' ";} ?>><?php echo JText::_(stripslashes($res_cat->name)); ?></option>
				  <?php $k = 1 - $k;
							} ?>
				</select>
				<div align="right"></div>
			</div>
			<?php if($sub_cat_count->count > 0 ){ // there are sub cats ?>
			<div id="subcats_row" style="visibility:hidden; display:none"><div id="subcats_div"></div></div>
			<?php } ?>
			<div>
			  <label class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE');?></label>
			  <div id="resources_div" style="visibility:hidden;">&nbsp;</div>
			</div>
			<?php } else { ?>
			<div>
			  <label class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_RESOURCE');?></label>
			  <select name="resources" id="resources" class="sv_apptpro_request_dropdown" onchange="changeResource()"
			  title="<?php echo JText::_('RS1_INPUT_SCRN_RESOURCE_TOOLTIP');?>">
				  <?php
							$k = 0;
							for($i=0; $i < count( $res_rows ); $i++) {
							$res_row = $res_rows[$i];
							?>
				  <option value="<?php echo $res_row->id_resources; ?>" ><?php echo JText::_(stripslashes($res_row->name)); echo ($res_row->cost==""?"":" - "); echo JText::_(stripslashes($res_row->cost)); ?></option>
				  <?php $k = 1 - $k;
							} ?>
				</select>
			</div>
			<?php } ?>
			<div id="services" style="visibility:hidden; display:none"><label class="sv_apptpro_request_label"><?php echo JText::_('RS1_INPUT_SCRN_SERVICES');?></label><div id="services_div">&nbsp;</div></div>
			<div id="resource_seat_types" style="visibility:hidden; display:none"><div id="resource_seat_types_div"></div></div>
			<div id="resource_extras" style="visibility:hidden; display:none"><div id="resource_extras_div"></div></div>
			<div id="resource_udfs" style="visibility:hidden; display:none"><div id="resource_udfs_div">&nbsp;</div></div>
		</div>
		<div class="sv_apptpro_request_block" id="datetime" style="display: none">
			<div class="sv_apptpro_request_block">


				<input type="hidden" name="startdate" id="startdate" value="<?php echo $startdate ?>"/>
				<input type="hidden" name="timeslots" id="timeslots" value="<?php echo $starttime ?>,<?php echo $endtime ?>"/>
				<input type="hidden" name="enddate" id="enddate" value="<?php echo $enddate ?>" />
				<input type="hidden" name="starttime" id="starttime" value="<?php echo $starttime ?>"/>
				<input type="hidden" name="endtime" id="endtime" value="<?php echo $endtime ?>"/>
			</div>
			<div class="sv_apptpro_request_block">
				<div id="slots" style="visibility:hidden;">&nbsp;</div>
				<div class="clear"></div>
			</div>

		</div>
		<div class="sv_apptpro_request_block_hidden" style="display:none;">
		<input type="hidden" id="enable_paypal" value="<?php echo $apptpro_config->enable_paypal ?>" />
		<?php if($apptpro_config->enable_paypal == "Yes"
			  || $apptpro_config->authnet_enable == "Prod" || $apptpro_config->authnet_enable == "Test"
			  || $apptpro_config->_2co_enable == "Yes"
			  || $apptpro_config->non_pay_booking_button == "DAB" ||  $apptpro_config->non_pay_booking_button == "DO" ){ ?>
		  <div id="calcResults" style="visibility:hidden; display:none; height:auto">
			<table border="1" align="left" width="300" cellpadding="4" cellspacing="0"  class="calcResults_outside">
			  <tr align="center" class="calcResults_header">
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_RES_RATE');?></td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><label id="res_hours_label"><?php echo JText::_('RS1_INPUT_SCRN_RES_RATE_UNITS');?></label></td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_RES_RATE_TOTAL');?></td>
			  </tr>
			  <tr align="right" >
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>&nbsp;<label id="res_rate"></label></td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><label id="res_hours"></label>&nbsp;</td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>&nbsp;<label id="res_total"></label></td>
			  </tr>
		  <?php if ($extras_row_count->count > 0 ){?>
			  <tr align="right">
				<td style="border-bottom:solid 1px;">&nbsp;            </td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_EXTRAS_FEE');?>:&nbsp;</td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;">&nbsp;<label id="extras_fee"></label></td>
			  </tr>
		  <?php } ?>
		  <?php if ($apptpro_config->additional_fee != 0.00 ){?>
			  <tr align="right">
				<td style="border-bottom:solid 1px;">&nbsp;            </td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_RES_ADDITIONAL_FEE');?>:&nbsp;</td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;">&nbsp;<label id="res_fee"></label></td>
			  </tr>
		  <?php } ?>
		  <?php if($apptpro_config->enable_coupons == "Yes"){ ?>
			  <tr align="right">
				<td style="border-bottom:solid 1px;">&nbsp;            </td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_DISCOUNT');?>:&nbsp;</td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;">&nbsp;<label id="discount"></label></td>
			  </tr>
		  <?php } ?>
		  <?php if($user_credit != NULL){ ?>
			  <tr align="right">
				<td style="border-bottom:solid 1px;">&nbsp;            </td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_USER_CREDIT');?>:&nbsp;</td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;">&nbsp;<label id="credit"></label> <input type="hidden" name="applied_credit" id="applied_credit" /></td>
			  </tr>
		  <?php } ?>
			  <tr align="right">
				<td style="border-bottom:solid 1px;">&nbsp;
					<input type="hidden" id="additionalfee" value="<?php echo $apptpro_config->additional_fee ?>" />
					<input type="hidden" id="feerate" value="<?php echo $apptpro_config->fee_rate ?>" />
					<input type="hidden" id="rateunit" value="<?php echo $apptpro_config->fee_rate ?>" />
					<input type="hidden" id="grand_total" name="grand_total" value="<?php echo $grand_total ?>" />
				 </td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_RES_RATE_TOTAL');?>:&nbsp;</td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>&nbsp;<label id="res_grand_total"></label></td>
			  </tr>
			  <tr align="right" id="deposit_only">
				<td style="border-bottom:solid 1px;">&nbsp;            </td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_DEPOSIT');?>:&nbsp;</td>
				<td style="border-bottom:solid 1px; border-right:solid 1px;"><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?>
				&nbsp;<label id="display_deposit_amount"></label>
				<input type="hidden" id="deposit_amount" name="deposit_amount" value="0.00" />
				</td>
			  </tr>
			<?php if($apptpro_config->enable_coupons == "Yes"){ ?>
				 <tr>
					<td colspan="3"><input name="coupon_code" type="text" id="coupon_code" value="" size="20" maxlength="80"
						  title="<?php echo JText::_('RS1_INPUT_SCRN_COUPON_TOOLTIP');?>" />
						  <input type="button" class="button" value="<?php echo JText::_('RS1_INPUT_SCRN_COUPON_BUTTON');?>" onclick="getCoupon()" />
						  <div id="coupon_info"></div>
						  <input type="hidden" id="coupon_value" />
						  <input type="hidden" id="coupon_units" />
					</td>
				</tr>
			 <?php } ?>
			</table>
		  </div>
		<?php } ?>
		</div>
	</div>
</div>

<?php if($apptpro_config->hide_logo == 'No'){ ?>
<span style="font-size:9px; color:#999999">powered by <a href="http://www.AppointmentBookingPro.com" target="_blank">AppointmentBookingPro.com</a> v 2.0.7</span>
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
<input type="hidden" name="task" value="" />
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
<input type="hidden" name="alt_cal_pos" id="alt_cal_pos" value="<?php echo $apptpro_config->cal_position_method; ?>" />
<input type="hidden" name="preset_service" id="preset_service" value="<?php echo $single_service_id; ?>" />
<input type="hidden" name="validate_text" id="validate_text" value="<?php echo JText::_('RS1_INPUT_SCRN_VALIDATION_OK');?>" />

</form>

<div class="gantry-width-block gantry-width-33 rt-center">
<span>When</span>
<img style="margin-top: 10px;" src="/images/vip/date-time-VIP-event.png" />

      <div class="clear"></div>
</div>
<div class="gantry-width-block gantry-width-33 rt-center addressVIP" id="addressVIP">
<a style="color:white" target="blank" href="https://www.google.ca/maps/place/Sean+Homes/@44.3487844,-79.7060448,17z/data=!4m13!1m7!3m6!1s0x882abd079cf57775:0x610068227ca1433f!2s401+Essa+Rd,+Barrie,+ON+L4N+9C8!3b1!8m2!3d44.3487844!4d-79.7038561!3m4!1s0x882abd077648fc6f:0x18f29f9783486997!8m2!3d44.348817!4d-79.703426">
	<strong>401 Essa Rd,</strong><br />
Barrie, ON L4N&nbsp;9C8<br />
Just east of Ferndale<br />
Drive / Veterans Drive.
</a>
      <div class="clear"></div>
</div>
<div class="gantry-width-block gantry-width-33 rt-center">
<span>Where</span>
	<a target="blank" href="https://www.google.ca/maps/place/Sean+Homes/@44.3487844,-79.7060448,17z/data=!4m13!1m7!3m6!1s0x882abd079cf57775:0x610068227ca1433f!2s401+Essa+Rd,+Barrie,+ON+L4N+9C8!3b1!8m2!3d44.3487844!4d-79.7038561!3m4!1s0x882abd077648fc6f:0x18f29f9783486997!8m2!3d44.348817!4d-79.703426">
		<img src="/images/vip/mini-map-VIP.png" style="margin-top: 10px;" />
	</a>
      <div class="clear"></div>
</div>
<div class="clear"></div>

<script>

	jQuery(document).ready(function(){
		// auto selecting slot
		selectDaySlot('<span class="time button active" onclick="selectDaySlot(this);" data="2017-04-29|11:00,17:00">11:00 AM</span>');
		jQuery('#email').attr('placeholder', 'Email:');
		jQuery('#email_block > .sv_apptpro_request_label').hide();
		jQuery(jQuery('#user_field0_value>option')[0]).removeAttr("selected");
		jQuery('#user_field0_value').append('<option value="" disabled selected hidden> Number of Attendees:</option>');
		jQuery('#use_sms').after('<span class="SMSBtn" onclick="selectSmsButton()"></span>');
		jQuery('#use_sms').parent().css({'font-size':'calc(1vh + 9px)', 'text-align':'center'});
		jQuery('#user_field0_value').wrap('<div class="attendies_wrap" style="position: relative;"></div>');
		jQuery('.attendies_wrap').append('<img src="https://www.seanhomes.ca/images/vip/select_arrow.png" class="attendies_arrow">');
		jQuery('.rt-social-buttons').prepend('<a class="item homeRed" href="/"><span class="icon-home" style="font-size: 15px;"></span></a>');
	});

	jQuery(window).on('load', function(){
		var count = 0;
		var inputspace = setInterval(function(){
			count+=1;
			if(jQuery('#name').val()==' '){
				clearInterval(inputspace);
				jQuery('#name').val('');
			}
			if(count == 10){
				clearInterval(inputspace);
			}
		},200);
		jQuery('#user_field0_value').on('change', function(){
			handleAttendiesColor();
		});
		handleAttendiesColor();
	});
	function handleAttendiesColor(){
		if(jQuery('#user_field0_value').val()==null){
			jQuery('#user_field0_value').css({'color':'#b5acac'});
		}else{
			jQuery('#user_field0_value').css({'color':'black'});
		}
	}
	function selectSmsButton(){
		jQuery('#use_sms').click();
		jQuery('.SMSBtn').toggleClass('sms_selected');
	}
</script>
<style>
.homeRed{
	width: 100%;
	display: block;
	top: -25%;
	position: absolute;
	text-align: center;
}
@media(max-width:765px){
	.homeRed{
		display: none;
	}
}
input {
    width: 100% !important;
}
#submitBlock > input{
	font-size: 1.3em !important;
}
form[name="frmRequest"] {
    width:50% !important;
}
#user_field0_value {
  width: 100% !important;
  -webkit-appearance: none;
  -moz-appearance: none;
  line-height: 15px;
	margin-top:-15px;
}
#user_field0_value option[disabled] {
  color: #888;
}
#email_block, .name_block{
	display: flex;
	width: 100%;
}
#email{
	background: white !important;
	margin-bottom: 0;
}
#email_block{
	margin-bottom: 0 !important;
}
#user_field0_label{
	display: none !important;
}
#apptbookingHeader{
	font-size: 1.4em;
	padding: 0;
}
.SMSBtn{
	width: 10px;
	height: 10px;
	background: white;
	position: relative;
	display: inline-block;
	border-radius: 11px;
	cursor: pointer;
	border: 2px solid white;
}
#use_sms{
	display: none;
}
.sms_selected{
  background: rgb(0, 0, 0);
}
.sv_apptpro_request_block {
	user-select:none;
}
@media(max-width:1150px) and (min-width:675px){
	form[name="frmRequest"] {
	    width:70% !important;
	}
}
@media(max-width:675px){
	form[name="frmRequest"] {
	    width:100% !important;
	}
}
@media(max-width:365px){
	.component-content{
    width: 100% !important;
	}
}
.attendies_arrow{
	position: absolute;
  right: 7px;
  top: -8%;
  z-index: 1;
  pointer-events: none;
}
@media(min-width:1400px){
	#addressVIP{
		margin-top: 7% !important;
	}
	#addressVIP > a{
		font-size: 20px;
	}
}
@media(max-width:1400px) and (min-width:765px){
	#addressVIP {
	    margin-top: 3% !important;
	}
	#addressVIP > a {
    font-size: 14px;
	}
}
@media(max-width: 765px){
	#addressVIP > a {
	    font-size: 20px;
	}
}
#errors{
	text-align: center;
	background: rgba(0, 0, 0, 0.11);
	margin: 10px 20px;
	padding: 0px;
}
</style>
