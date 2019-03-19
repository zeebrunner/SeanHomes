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



var xhr = false;
var xhr2 = false; // need 2 so we can do 2 simultanious ajax calls to get different info
var xhr3 = false; // need 3 so we can do 3 simultanious ajax calls to get different info
var xhr4 = false; // need 4 so we can do 4 simultanious ajax calls to get different info
var xhr5 = false; // need 5 so we can do 5 simultanious ajax calls to get different info
var xhr27 = false; // for mouseovers
var hours = 0.0;
var total = 0.0;
var rate = 0.0;
var startdate = "";
var starttime = "";
var enddate = "";
var endtime = "";
var additionalfee = 0.00;
var feerate = "";
var fee = 0.0;
var res_id = 0;
var res_rate = 0.00;
var submit_status;
//var old_ts = "";
var extras_total_cost = 0.0;
var multiSlotSelect = new Array();

var old_ts = {
	"id": "",
    "ts_width": "0",
	"ts_left": "0",
    "ts_index": "0"
}






function changeCategory(fd){ // Not Used
	var fd_booking = "No";
		if(fd != undefined) { //If fd was passed in, use it
			fd_booking = "Yes";
		}

	if(document.getElementById("resource_udfs") != null){
		document.getElementById("resource_udfs").style.display = "none";
		document.getElementById("resource_udfs").style.visibility = "hidden";
	}
	if(document.getElementById("category_id").selectedIndex  === 0){
		if(document.getElementById("datetime")!=null){ document.getElementById("datetime").style.display = "none";}
		document.getElementById("services_div").style.display = "none";
		document.getElementById("resources").style.display = "none";
		document.getElementById("gad_container").style.display = "none";
		document.getElementById("subcats_row").style.visibility = "hidden";
		document.getElementById("subcats_row").style.display = "none";
		document.getElementById("subcats_div").innerHTML = "";
		if(document.getElementById("service_summary") != null){
			document.getElementById("service_summary").style.display = "none";
			document.getElementById("service_summary").style.visibility = "hidden";
		}

		return false;
	}

	// if there are sub-categories we need to fetch them rather than any resources
	if(document.getElementById("sub_cat_count").value != "0"){
		if(document.getElementById("sub_category_id")!=null){
			// if we are already showing a sub-category, we need to clear that out 
			document.getElementById("subcats_row").style.visibility = "hidden";
			document.getElementById("subcats_row").style.display = "none";
			document.getElementById("subcats_div").innerHTML = "";
		}
		getSubCategories(document.getElementById("category_id").value, fd_booking);
		return false;
	}
	

	if(document.getElementById("mode") != null){
		if(document.getElementById("category_id").value === "0"){
			document.getElementById("table_here").innerHTML = "";
			document.getElementById("table_here").visible = false;
			document.getElementById("table_here").display = "none";	
			return false;
		}
		document.getElementById("table_here").innerHTML = document.getElementById("wait_text").value;

		document.getElementById("gad_container").style.display = "";
		buildTable();
	} else {
		document.getElementById("slots").style.visibility = "hidden";
		document.getElementById("startdate").value = "";
	}

	//getResources(fd_booking);
	
}

function changeResource(){ // Not Used
	
	if(document.getElementById("errors") != null){
		document.getElementById("errors").innerHTML = "";
	}
	if(document.getElementById("resources") === null){
		return false;
	}
	multiSlotSelect.length = 0;
	
	if(document.getElementById("resources") != null){
//		if(document.getElementById("resources").value === "0"){ // removed Jan 21/12 while woking on service duration issue on resource change
			document.getElementById("services").style.display = "none";
			document.getElementById("services").style.visibility = "hidden";
			document.getElementById("services_div").style.display = "none";
			document.getElementById("services_div").style.visibility = "hidden";
			if(document.getElementById("service_name") != null){
				for (var loop=0; loop < document.getElementById("service_name").options.length; loop++) {
					document.getElementById("service_name").options[loop] = null; // remove the option
				}
				document.getElementById("service_name").options.length = 0;
			}
			if(document.getElementById("datetime") != null){
				document.getElementById("datetime").style.display = "none";
			}
			if(document.getElementById("service_summary") != null){
				document.getElementById("service_summary").style.display = "none";
				document.getElementById("service_summary").style.visibility = "hidden";
			}

//		}
	}

	if(document.getElementById("mode") != null){
		if(document.getElementById("resources").value === "0"){
			document.getElementById("mode").value = "single_day";
		} else {
			document.getElementById("mode").value = "single_resource";
		}
		document.getElementById("table_here").innerHTML = document.getElementById("wait_text").value;
		document.getElementById("gad_container").style.display = "";
		buildTable();
	} else {
		document.getElementById("slots").style.visibility = "hidden";
		document.getElementById("startdate").value = document.getElementById("wait_text").value;
		getCalDays();
	}
	
	if(document.getElementById("coupon_value") != null){
		document.getElementById("coupon_info").innerHTML = "";
		document.getElementById("coupon_value").value = "0";
		document.getElementById("coupon_units").value = "";
	}
	
	//getServices();
	//getResourceUFDs();
	//getResourceSeatTypes();
	//getExtras();
	//hideTotal();
	
}



function getSlots(){

	//document.getElementById("slots").innerHTML = document.getElementById("wait_text").value;
	//document.getElementById("slots").innerHTML = ""; // not displaying any slots for this project
	document.getElementById("slots").style.visibility = "visible";

	if(document.getElementById("errors") != null){
		document.getElementById("errors").innerHTML = "";
	}

	if(document.getElementById("resources") === null){
		document.getElementById("slots").style.visibility = "hidden";
		return false;
	}

	if(document.getElementById("resources").value === "0"){
		document.getElementById("slots").style.visibility = "hidden";
		return false;
	}


    document.getElementById("enddate").value = document.getElementById("startdate").value;



	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {
		xhr.onreadystatechange = showSlots;
		var data = "startdate=" + document.getElementById("startdate").value;
		data = data + "&res=" + document.getElementById("resources").value;
		data = data + "&reg=" + document.getElementById("reg").value;
		data = data + "&browser=" + BrowserDetect.browser;
		if(document.getElementById("mobile")!=null){
			data = data + "&mobile=" + document.getElementById("mobile").value;	
		}

		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data+"&u="+(new Date()).getTime(), true);

		xhr.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
	
	
function showSlots() {
		
	if (xhr.readyState === 4) {
		document.getElementById("slots").style.visibility = "visible";
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}
		
		//document.getElementById("slots").innerHTML = outMsg;


		moreDaysSlots(outMsg);

		set_starttime();
		
	}
	return true;
}


function getCalDays(){ // Not Used



    if(document.getElementById("resources") === null){
		return false;
	}
	if(document.getElementById("resources").value === "0"){
		return false;
	}
	document.getElementById("datetime").style.display = "";

	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();

	}
	else {

		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {

		xhr.onreadystatechange = showCalDays;
		//var data = "res=" + document.getElementById("resources").value;
		var data = "res=1";

		data = data + "&browser=" + BrowserDetect.browser;
		data = data + "&caldays=yes";
		//alert(data);
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr.send('');
	}
	else {

		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
	
	
function showCalDays() { // Not Used	
		
	if (xhr.readyState === 4) {
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}

		// for dev
		//document.getElementById("cancel_results").innerHTML = outMsg;
		
		//eval(outMsg);
		//document.getElementById("datetime").style.display = "";
		//document.getElementById("startdate").value = document.getElementById("select_date_text").value;
		//document.getElementById("anchor1").style.display = "";

	}
	return true;
}

function set_starttime(){
	
	if(document.getElementById("errors") != null){
		document.getElementById("errors").innerHTML = "";
	}
	
	var start = document.getElementById("timeslots").value;
	var temp = new Array();
	temp = start.split(',');
		
	//document.getElementById("starttime").value = temp[0];
	//document.getElementById("endtime").value = temp[1];
	
	/*
	if(document.getElementById("enable_paypal").value === 'Yes' 
		|| document.getElementById("authnet_enable").value === "Prod" || document.getElementById("authnet_enable").value === "Test"
		|| document.getElementById("_2co_enable").value === 'Yes' 
		|| document.getElementById("non_pay_booking_button").value === "DO" || document.getElementById("non_pay_booking_button").value === "DAB" ){
		res_id = document.getElementById("resources").value;
		calcTotal();
	}
	*/
	
	return true;
}

function getResources(fd){ // Not Used
	var cat_id ="";
	if(document.getElementById("category_id").value === "0"){
		return false;
	}
	cat_id = document.getElementById("category_id").value;
	
	if(document.getElementById("sub_category_id") != null){
		if(document.getElementById("sub_category_id").value === "0"){
			return false;
		}
		cat_id = document.getElementById("sub_category_id").value;
	}
	
	document.getElementById("resources_div").style.display = "";
	document.getElementById("resources_div").innerHTML = document.getElementById("wait_text").value;
	document.getElementById("resources_div").style.visibility = "visible";

	document.getElementById("services").style.display = "none";
	document.getElementById("services").style.visibility = "hidden";
	document.getElementById("services_div").style.display = "none";
	document.getElementById("services_div").style.visibility = "hidden";
	if(document.getElementById("service_summary") != null){
		document.getElementById("service_summary").style.display = "none";
		document.getElementById("service_summary").style.visibility = "hidden";
	}
	
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {
		xhr.onreadystatechange = showResources;
		var data = "cat=" + cat_id;
		if(document.getElementById("gridwidth")!=null){
			// gad
			data = data + "&gad=Yes";
		} else {
			data = data + "&gad=No";
		}
		data = data + "&browser=" + BrowserDetect.browser;
		data = data + "&reg=" + document.getElementById("reg").value;	
		data = data + "&fd="+fd;
		data = data + "&res=yes";
		//alert(data);
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
function showResources() {	// Not Used
		
	if (xhr.readyState === 4) {
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}

		document.getElementById("resources_div").innerHTML = outMsg;
		
		if(document.getElementById("resources").options.length===2){
			document.getElementById("resources").options[1].selected=true;
			changeResource();
		}

	}
	changeResource();
	return true;
}

var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index === -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari"
		},
		{
			prop: window.opera,
			identity: "Opera"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};
BrowserDetect.init();

function doCancel(){
	if(document.getElementById("cancellation_id")!=null && document.getElementById("cancellation_id").value === ""){
		alert(document.getElementById("cancellation_id").title);
		return false;
	}
	if(document.getElementById("wait_text")!=null){
	document.getElementById("cancel_results").innerHTML = document.getElementById("wait_text").value;
	document.getElementById("cancel_results").style.visibility = "visible";
	}
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {
		xhr.onreadystatechange = showCancel_Results;
		var data = "cancellation_id=" + encodeURIComponent(document.getElementById("cancellation_id").value);
		// need local date/time as yyyy-mm-dd-hh-mm
		var currentTime = new Date();
		data = data + "&userDateTime=" + currentTime.getFullYear() + "-" + (currentTime.getMonth() + 1) + "-" + currentTime.getDate();
		data = data + " " + currentTime.getHours() + ":" + currentTime.getMinutes() + ":00";
		data = data + "&browser=" + BrowserDetect.browser;

		// asynchronous
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=cancel_booking&format=raw&" + data, true);
		xhr.send('');

		// synchronous
		//xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=cancel_booking&format=raw&" + data, false);
		//xhr.send(null);

		//var outMsg = "";
		//outMsg = xhr.responseText;

		//document.getElementById("cancel_results").innerHTML = outMsg;
		
		// if being done from my bookings
		//if(document.getElementById("view").value === "mybookings"){
			//alert(removeHTMLTags(outMsg));
			//return true;
		//}
		
		// refresh grid to remove booking
		//changeDate();

		//return(outMsg);
		// synchronous
		
		return true;

	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
		// synchronous
		return false;
	}
	return true;
}
 
function doDelete(){
	if(document.getElementById("cancellation_id").value === ""){
		alert(document.getElementById("cancellation_id").title);
		return false;
	}

	document.getElementById("cancel_results").innerHTML = document.getElementById("wait_text").value;
	document.getElementById("cancel_results").style.visibility = "visible";

	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {
		xhr.onreadystatechange = showCancel_Results;
		var data = "cancellation_id=" + encodeURIComponent(document.getElementById("cancellation_id").value);
		// need local date/time as yyyy-mm-dd-hh-mm
		var currentTime = new Date();
		data = data + "&userDateTime=" + currentTime.getFullYear() + "-" + (currentTime.getMonth() + 1) + "-" + currentTime.getDate();
		data = data + " " + currentTime.getHours() + ":" + currentTime.getMinutes() + ":00";
		data = data + "&browser=" + BrowserDetect.browser;
		//alert(data);

		// asynchronous
		//xhr.open("GET", presetIndex()+"?option=com_rsappt_pro14&page=ajax_cancel&format=raw&" + data, true);
		//xhr.send('');

		// synchronous
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=delete_booking&format=raw&" + data, false);
		xhr.send(null);

		var outMsg = "";
		outMsg = xhr.responseText;
		document.getElementById("cancel_results").innerHTML = outMsg;

		// if being done from my bookings
		if(document.getElementById("view").value === "mybookings"){
			alert(removeHTMLTags(outMsg));
			window.location.reload();
		}

		return(outMsg);
		
		// synchronous

	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
		// synchronous
		return false;
	}
	return true;
}


function showCancel_Results(){
	if (xhr.readyState === 4) {
		document.getElementById("cancel_results").style.visibility = "visible";
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
			
			var messageStart = outMsg.indexOf("Your preview appointment for");
			var messageEnd = outMsg.indexOf("has been cancelled.");
		
		
		if(messageStart != -1 && messageEnd != -1) {
			outMsg = outMsg.substring(messageStart, messageEnd + 19);
		}

		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}
		
		document.getElementById("cancel_results").innerHTML = outMsg;
		
	}
	return true;
}

function validateForm(){




    if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if(document.getElementById("submit") != null){
		document.getElementById("submit").disabled = true;
	}




	if(document.getElementById("btnPayPal") != null){
		document.getElementById("btnPayPal").disabled = true;
	}

   // if(document.getElementById("sms_reminders").value =='Yes' && document.getElementById("sms_phone").value == '')
   // return "VALIDATION FAILED: <br> CELL NUMBER IS REQUIRED";

	if (xhr) {
		// synchronous
		//xhr.onreadystatechange = showValidation_Results;
		var data = "name=" + encodeURIComponent(document.getElementById("name").value);
		data = data + "&phone=" + encodeURIComponent(document.getElementById("phone").value);
		data = data + "&sms_reminders=" + encodeURIComponent(document.getElementById("use_sms").value);
		data = data + "&sms_phone=" + encodeURIComponent(document.getElementById("sms_phone").value);
		data = data + "&email=" + encodeURIComponent(document.getElementById("email").value);
		data = data + "&scrn=" + document.getElementById("screen_type").value;
		// To add service validation in fe_val you will need to un-comment the lines below so service is sent
//		if(document.getElementById("service_name") != null){
//			if(document.getElementById("service_name").options.length > 0){
//				var selected_id = document.getElementById("service_name").options[document.getElementById("service_name").selectedIndex].value;
//				data = data + "&srv=" + selected_id;
//			}
//		}
		var udf_count = parseInt(document.getElementById("udf_count").value);
		if(document.getElementById("res_udf_count")!=null){
			// add resource specific
			udf_count += parseInt(document.getElementById("res_udf_count").value);
		}
		data = data + "&udf_count=" + udf_count;
		for(i=0; i<udf_count; i++){
			// To only send required fields to validation 
//			temp_name = "user_field" + i + "_is_required";
//			if(document.getElementById(temp_name)!=null){
//				// if UDF has _is_required, check to see if it is NO, if so do not send.
//				if(document.getElementById(temp_name).value == "No"){
//					// not a required UDF, do not send
//					continue;
//				}
//			} else {
//				// if UDF has no is required, do not send it
//				continue;
//			}
			udf_name = "user_field" + i + "_label";
			if(document.getElementById(udf_name)!=null){ 
				data = data + "&" + udf_name + "=" + encodeURIComponent(document.getElementById(udf_name).innerHTML);		
				udf_name = "user_field" + i + "_value";
				if(document.getElementById(udf_name).type === "checkbox"){
					if(document.getElementById(udf_name).checked){
						if(document.getElementById(udf_name)!=null){ data = data + "&" + udf_name + "=" + 'Checked';}
					} else {
						if(document.getElementById(udf_name)!=null){ data = data + "&" + udf_name + "=" + '';}
					}
				} else if(document.getElementById(udf_name).type === "radio"){
					var checked_value = getCheckedValue(udf_name);
					data = data + "&" + udf_name + "=" + checked_value;
				} else {
					if(document.getElementById(udf_name)!=null){ data = data + "&" + udf_name + "=" + encodeURIComponent(document.getElementById(udf_name).value);}		
				}
				udf_name = "user_field" + i + "_is_required";
				if(document.getElementById(udf_name)!=null){ data = data + "&" + udf_name + "=" + encodeURIComponent(document.getElementById(udf_name).value);}
			}
		}
		if(document.getElementById("category_id")!=null){
			data = data + "&category_id=" + document.getElementById("category_id").value;
		} else{
			data = data + "&category_id=-1";
		}
		if(document.getElementById("resources")!=null){
			if(document.getElementById("mode")===null){
				// non gad
				data = data + "&resource=" + document.getElementById("resources").value;
			} else {
				if(document.getElementById("gad_mobile_simple")!=null){
					// gad is in use but mobile device is switched to simple
					data = data + "&resource=" + document.getElementById("resources").value;
				} else {
					data = data + "&resource=" + document.getElementById("selected_resource_id").value;
				}
			}
		} else{
			data = data + "&resource=-1";
		}
		data = data + "&user_id=" + document.getElementById("user_id").value;
		data = data + "&startdate=" + document.getElementById("startdate").value;
		data = data + "&starttime=" + document.getElementById("starttime").value;
		data = data + "&enddate=" + document.getElementById("enddate").value;
		data = data + "&endtime=" + document.getElementById("endtime").value;

		if(document.getElementById("seat_type_count")!=null && document.getElementById("seat_type_count").value != 0){
			var seat_count = 0; 
			for(i=0; i<parseInt(document.getElementById("seat_type_count").value); i++){
				seat_name = "seat_"+i;
				if(document.getElementById(seat_name)!=null){ // null if no resource yet selected
					seat_count = seat_count + parseInt(document.getElementById(seat_name).value);
				}
			}
			data = data + "&booked_seats=" + seat_count;	
		}
		if(document.getElementById("PayPal_mode")!=null){
			data = data + "&PayPal_mode=" + document.getElementById("PayPal_mode").value;
			if(document.getElementById("grand_total")!=null){
				data = data + "&PayPal_due=" + document.getElementById("grand_total").value;
			}
		}
		if(document.getElementById("recaptcha_challenge_field")!=null){
			data = data + "&recap_chal=" + document.getElementById("recaptcha_challenge_field").value;
			data = data + "&recap_resp=" + document.getElementById("recaptcha_response_field").value;
		}
		
		data = data + "&browser=" + BrowserDetect.browser;
		data = data.replace(/'/g, "&rsquo;");
		//alert(data);

		// asynchronous
		//xhr.open("GET", presetIndex()+"?option=com_rsappt_pro14&page=ajax_validate&format=raw&" + data, true);
		//xhr.send('');

		if(document.getElementById("mobile")===null){
	 		document.body.style.cursor = "wait";    
		}

		// synchronous
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_validate&format=raw&" + data, false);
//		xhr.open("POST", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_validate&format=raw&" + data, false);
//		xhr.open("POST", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_validate&format=raw", false);
		xhr.send(null);

		var outMsg = "";
		outMsg = xhr.responseText;

        document.getElementById("errors").innerHTML = outMsg;

		if(document.getElementById("mobile")===null){
	 		document.body.style.cursor = "default";    
		}

		if(document.getElementById("submit") != null){
			document.getElementById("submit").disabled = false;
		}
		if(document.getElementById("btnPayPal") != null){
			document.getElementById("btnPayPal").disabled = false;
		}

		return(outMsg);
		// synchronous
		
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
		// synchronous
		return false;
	}
	//return true;
}

function validateFormEdit(){
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if(document.getElementById("saveLink") != null){
		document.getElementById("saveLink").disabled = true;
	}
	if(document.getElementById("closeLink") != null){
		document.getElementById("closeLink").disabled = true;
	}

	if (xhr) {
		// synchronous
		//xhr.onreadystatechange = showValidation_Results;
		var data = "name=" + encodeURIComponent(document.getElementById("name").value);
		data = data + "&phone=" + encodeURIComponent(document.getElementById("phone").value);
		data = data + "&email=" + encodeURIComponent(document.getElementById("email").value);
		data = data + "&request=" + document.getElementById("id_requests").value;
		data = data + "&request_status=" + document.getElementById("request_status").value;
		data = data + "&resource=" + document.getElementById("resource").value;
		data = data + "&user_id=" + document.getElementById("user_id").value;
		data = data + "&startdate=" + document.getElementById("startdate").value;
		data = data + "&starttime=" + document.getElementById("starttime").value;
		data = data + "&enddate=" + document.getElementById("enddate").value;
		data = data + "&endtime=" + document.getElementById("endtime").value;	
		if(document.getElementById("seat_type_count")!=null && document.getElementById("seat_type_count").value != 0){
			var seat_count = 0; 
			for(i=0; i<parseInt(document.getElementById("seat_type_count").value); i++){
				seat_name = "seat_"+i;
				if(document.getElementById(seat_name)!=null){ // null if no resource yet selected
					seat_count = seat_count + parseInt(document.getElementById(seat_name).value);
				}
			}
			data = data + "&booked_seats=" + seat_count;	
		}
		data = data.replace(/'/g, "&rsquo;");
		//alert(data);

		// synchronous
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_validate_edit&format=raw&" + data, false);
		xhr.send(null);

		var outMsg = "";
		outMsg = xhr.responseText;
		document.getElementById("errors").innerHTML = outMsg;

		if(document.getElementById("saveLink") != null){
			document.getElementById("saveLink").disabled = false;
		}
		if(document.getElementById("closeLink") != null){
			document.getElementById("closeLink").disabled = false;
		}

		return(outMsg);
		// synchronous
		
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
		// synchronous
		return false;
	}
	//return true;
}

/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var svBase64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = svBase64._utf8_encode(input);
 
		while (i < input.length) {
 
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
 
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
 
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
		}
 
		return output;
	},
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
		while (i < input.length) {
 
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
 
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
 
			output = output + String.fromCharCode(chr1);
 
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
 
		}
 
		output = svBase64._utf8_decode(output);
 
		return output;
 
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}


/* end Base64 */

function trim(inStr){
	return inStr.replace(/^\s+|\s+$/g, '') ;
}

function removeHTMLTags(strInputCode){
 	strInputCode = strInputCode.replace(/&(lt|gt);/g, function (strMatch, p1){
	 	return (p1 === "lt")? "<" : ">";
	});
	var strTagStrippedText = strInputCode.replace(/<\/?[^>]+(>|$)/g, "");
 	return strTagStrippedText;	
}

function presetIndex(){
	// this function modified the ajax call syntax so it works with SEO ON or OFF
	var loc = ""+document.location;
	if(loc.indexOf("?")> -1 && loc.indexOf("index.php")> -1){
		// not SEO
		return("./index.php");
	} else if(loc.indexOf("?")> -1){
	    // seo with qs param, strip the qs param
	    var first_bit = loc.substring(0, loc.indexOf("?"));
		return(first_bit+"/index.php");
	} else if(loc.indexOf("index.php")>-1){
	    return loc.substring(0, loc.indexOf("index.php")+9);
	} else {
		return(window.location.href+"/index.php");
	}
}


function getServices(){
	if(document.getElementById("service_durations") != null){
		document.getElementById("service_durations").value = "";
	}
	if(document.getElementById("resources") === null){
		return false;
	}
	if(document.getElementById("resources").value === "0"){
		return false;
	}
	
	if (window.XMLHttpRequest) {
		xhr2 = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr2 = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr2) {
		xhr2.onreadystatechange = showServices;
		var data = "res=" + document.getElementById("resources").value;
		data = data + "&browser=" + BrowserDetect.browser;
		data = data + "&serv=yes";
		if(document.getElementById("screen_type").value === "fd_gad"){
			data = data + "&fd=Yes";
		}
		if(document.getElementById("users")!=null){
			data = data + "&uid="+document.getElementById("users").value;		
		}
		if(document.getElementById("preset_service")!=null){
			data = data + "&preset_service=" + document.getElementById("preset_service").value;	
		}
		
		//alert(data);
		
		xhr2.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr2.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
	
function showServices() {	
		
	if (xhr2.readyState === 4) {
	
		if (xhr2.status === 200) {		
			var outMsg = xhr2.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr2.status;
		}

		// for dev
		//document.getElementById("cancel_results").innerHTML = outMsg;

		if(outMsg.indexOf("<select name=")>-1){
			document.getElementById("services").style.display = "";
			document.getElementById("services").style.visibility = "visible";
			document.getElementById("services_div").style.display = "";
			document.getElementById("services_div").style.visibility = "visible";
			document.getElementById("services_div").innerHTML = outMsg;
			if(document.getElementById("service_summary") != null){
				document.getElementById("service_summary").style.display = "";
				document.getElementById("service_summary").style.visibility = "visible";
			}
		} else {
			document.getElementById("services").style.display = "none";
			document.getElementById("services").style.visibility = "hidden";
			document.getElementById("services_div").style.display = "none";
			document.getElementById("services_div").style.visibility = "hidden";
			if(document.getElementById("service_summary") != null){
				document.getElementById("service_summary").style.display = "none";
				document.getElementById("service_summary").style.visibility = "hidden";
			}
		}

	}
	setDuration();
	calcTotal();

	return true;
}

function changeResourceFE(){

	if(document.getElementById("resource").value === "0"){
		return false;
	}
	
	if(document.getElementById("require_validation")!= null){
		document.getElementById("require_validation").value = "Yes";
	}
	
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {
		xhr.onreadystatechange = showServicesFE;
		var data = "res=" + document.getElementById("resource").value;
		data = data + "&adminserv=yes";
		//alert(data);
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
		
function showServicesFE() {	
		
	if (xhr.readyState === 4) {
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}

		document.getElementById("service").options.length=0;
		if(outMsg.length>2){
			eval(outMsg);
		}
		// dev only
		//document.getElementById("admin_comment").innerHTML = outMsg;
		
	}
	return true;
}

function buildTable(){
	if(document.getElementById("sv_apptpro_request_gad_mobile")!=null){
		var griddiv = document.getElementById("sv_apptpro_request_gad_mobile");
		//alert(griddiv.clientWidth);
	} else {
		var griddiv = document.getElementById("sv_apptpro_request_gad");
		//alert(griddiv.clientWidth);
	}
   	curr_width = parseInt(griddiv.clientWidth * .90);
	
	if(multiSlotSelect.length==0){
	document.getElementById("booking_detail").style.display = "none";
	document.getElementById("booking_detail").style.visibility = "hidden";
	}
	document.getElementById("selected_resource_id").value="-1";
	document.getElementById("startdate").value="";
	document.getElementById("enddate").value="";
	document.getElementById("starttime").value="";
	document.getElementById("endtime").value="";
	document.getElementById("errors").innerHTML = "";

	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {
		xhr.onreadystatechange = showTable;
		var data = "gridstarttime=" + document.getElementById("gridstarttime").value;
		data = data + "&gridendtime=" + document.getElementById("gridendtime").value;
		if(document.getElementById("category_id")!=null){
			if(document.getElementById("sub_category_id")!=null){
				data = data + "&category=" + document.getElementById("sub_category_id").value;
			} else {
				data = data + "&category=" + document.getElementById("category_id").value;
			}
		} else{
			data = data + "&category=0";
		}
		if(document.getElementById("resources")!=null){
			data = data + "&mode=" + document.getElementById("mode").value;	
			data = data + "&resource=" + document.getElementById("resources").value;	
		} else {
			data = data + "&mode=single_day";	
			data = data + "&resource=0";	
		}
		data = data + "&grid_date=" + document.getElementById("grid_date").value;	
		data = data + "&grid_days=" + document.getElementById("grid_days").value;	
		if(document.getElementById("gridwidth")!=null){
			data = data + "&gridwidth=" + document.getElementById("gridwidth").value;	
			data = data + "&namewidth=" + document.getElementById("namewidth").value;	
		} else {
			data = data + "&gridwidth=" + curr_width;	
			data = data + "&namewidth=" + (parseInt(curr_width) * .10);	
		}
		data = data + "&reg=" + document.getElementById("reg").value;	
		if(document.getElementById("mobile")!=null){
			data = data + "&mobile=" + document.getElementById("mobile").value;	
		}
		if(document.getElementById("fd")!=null){
			data = data + "&fd=Yes";	
		}
		data = data + "&browser=" + BrowserDetect.browser;
		//alert(data);
		if(document.getElementById("mobile")===null){
	 		document.body.style.cursor = "wait";    
		}
		if(document.getElementById("gad2").value === "Yes"){
			xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_gad2&format=raw&" + data, true);
		} else {
			xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_gad&format=raw&" + data, true);
		}
		xhr.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
function showTable() {	
		
	if (xhr.readyState === 4) {
		document.getElementById("table_here").style.visibility = "visible";
		document.getElementById("table_here").style.display = "";
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}

		if(document.getElementById("mobile")===null){
	 		document.body.style.cursor = "default";    
		}
		document.getElementById("table_here").innerHTML = outMsg;

	}

	return true;
}

function changeGrid(){

	if(document.getElementById("category_id")!=null){
		if(document.getElementById("category_id").value === "0"){
		document.getElementById("table_here").innerHTML = "";
		document.getElementById("table_here").visible = false;
		document.getElementById("table_here").display = "none";	
			return false;
		}
	}

	document.getElementById("table_here").innerHTML = document.getElementById("wait_text").value;
	
	document.getElementById("gad_container").style.display = "";
	buildTable();
	
}

function changeDate(){

	if(document.getElementById("errors") != null){
		document.getElementById("errors").innerHTML = "";
	}

	if(document.getElementById("category_id")!=null){
		if(document.getElementById("category_id").value === "0"){
		document.getElementById("table_here").innerHTML = "";
		document.getElementById("table_here").visible = false;
		document.getElementById("table_here").display = "none";	
			return false;
		}
	}

	//document.getElementById("table_here").innerHTML = document.getElementById("wait_text").value;
	
	document.getElementById("gad_container").style.display = "";
	buildTable();
	
}

function gridPrevious(){
	document.getElementById("grid_date").value = document.getElementById("grid_previous").value;		
	changeDate();
}

function gridNext(){
	document.getElementById("grid_date").value = document.getElementById("grid_next").value;	
	changeDate();
}


function selectTimeslot(selected, e){
	//alert(selected);

	// may be used in the future to detect a shift-click
	//shiftPressed=e.shiftKey;
	//alert(shiftPressed);
	
	document.getElementById("errors").innerHTML = "";

	document.getElementById("booking_detail").style.display = "";
	document.getElementById("booking_detail").style.visibility = "visible";
	
	ary_selected = selected.split("|");
	document.getElementById("selected_resource_id").value=ary_selected[0];
	res_id = document.getElementById("selected_resource_id").value;

//  the replace messes up Chinese resource names	
//	document.getElementById("selected_resource").innerHTML = svBase64.decode(ary_selected[1].replace(/\+/g,  " "));
	document.getElementById("selected_resource").innerHTML = svBase64.decode(ary_selected[1]);
	
	document.getElementById("startdate").value=ary_selected[2];
	document.getElementById("enddate").value=ary_selected[2];
	document.getElementById("selected_date").innerHTML = svBase64.decode(ary_selected[3].replace(/\+/g,  " "));
//	document.getElementById("selected_date").innerHTML = svBase64.decode(ary_selected[3]);

	document.getElementById("starttime").value=ary_selected[4];
	document.getElementById("selected_starttime").innerHTML = svBase64.decode(ary_selected[5].replace(/\+/g,  " "));

	document.getElementById("endtime").value=ary_selected[6];
	document.getElementById("selected_endtime").innerHTML = svBase64.decode(ary_selected[7].replace(/\+/g,  " "));

	if(old_ts['id'] != "" && old_ts['ts_width'] != 0){
		// there is an old ts (previously selected) so set it back
		if(document.getElementById(old_ts['id']) != null){
		    document.getElementById(old_ts['id']).className = "sv_gad_timeslot_available";
			if(document.getElementById("pxm") != null){
				document.getElementById(old_ts['id']).style.width = old_ts['ts_width']+"px";
				document.getElementById(old_ts['id']).style.left = old_ts['ts_left'];
				document.getElementById(old_ts['id']).style.zIndex = parseInt(old_ts['ts_index']);
				old_ts['ts_width'] = 0;
				old_ts['ts_left'] = 0;
				old_ts['ts_index'] = 0;
			}
		}
	}
	document.getElementById(ary_selected[8]).className = "sv_gad_timeslot_selected";
	old_ts['id'] = ary_selected[8];
	old_ts['ts_width'] = ""+document.getElementById(old_ts['id']).clientWidth;
	old_ts['ts_index'] = "0";
	old_ts['ts_left'] = document.getElementById(old_ts['id']).style.left;

// if in day view, we need to selec the chosen resoure in order to show its services
	// But only load services if we have changed resources since last click
	var LoadServices = false;
	if(document.getElementById("resources").value!=document.getElementById("selected_resource_id").value){
		LoadServices = true;
	}
	document.getElementById("resources").value=document.getElementById("selected_resource_id").value;
	//changeResource();
	if(document.getElementById("mode").value === "single_day" && LoadServices === true){
		if(document.getElementById("service_name") != null){
			for (var loop=0; loop < document.getElementById("service_name").options.length; loop++) {
				document.getElementById("service_name").options[loop] = null; // remove the option
			}
			document.getElementById("service_name").options.length = 0;
		}		
		getServices();
		getResourceUFDs();
		getResourceSeatTypes();
		getExtras();
	}
	
	checkForBookingOverlap(document.getElementById("startdate").value, document.getElementById("starttime").value,
			document.getElementById("enddate").value,document.getElementById("endtime").value, res_id);
	
	// moved to after async call returns
	//setDuration();
	//calcTotal();
}

function parse_service_durations(inStr, i, which){
	aryDurations = inStr.split(",");
	for(x=0; x<aryDurations.length; x++)
	{
		aryTemp = aryDurations[x].split(":");
		if(aryTemp[0]===i){
			if(which==="value"){
				return aryTemp[1];
			} else {
				return aryTemp[2];
			}
		}
	}
}		


function changeMode(id){
	document.getElementById("resources").value=id;
	changeResource();
}		   
	
function changeMode2(newdate){
	document.getElementById("resources").selectedIndex=0;
	changeResource();
	document.getElementById("grid_date").value = newdate;
	changeDate();
}		
// advadm

function changeUser(){

	document.getElementById("user_fetch").innerHTML = document.getElementById("wait_text").value;

	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {
		//xhr.onreadystatechange = showCancel_Results;
		var data = "id=" + document.getElementById("users").value;
		if(document.getElementById("screen_type").value === "fd_gad"){
			data = data + "&fd_gad=1";
		}
		data = data + "&browser=" + BrowserDetect.browser;
		//alert(data);

		// asynchronous
		//xhr.open("GET", presetIndex()+"?option=com_rsappt_pro14&page=ajax_cancel&format=raw&" + data, true);
		//xhr.send('');

		// synchronous
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_fetch&format=raw&" + data, false);
		xhr.send(null);

		var outMsg = "";
		outMsg = xhr.responseText;
		
		// dev only
		//document.getElementById("cancel_results").innerHTML = outMsg;
		
		// results will be name|email|credit,udfx|cb_value,udfy|cb_value
		//alert(outMsg);
		aryResults = outMsg.split("~");
		
		aryNameEmail = aryResults[0].split("|");
		document.getElementById("name").value = trim(aryNameEmail[0]);
		document.getElementById("email").value = aryNameEmail[1];
		document.getElementById("uc").value = aryNameEmail[2];
		document.getElementById("user_id").value =  document.getElementById("users").value;

        if(aryResults.length > 1){
            for(i=1; i<aryResults.length; i++){
                aryUdfs = aryResults[i].split("|")
			  	if(document.getElementById(aryUdfs[0])!=null){
		        	document.getElementById(aryUdfs[0]).value = aryUdfs[1];
				}
            }
        }		
		
		document.getElementById("user_fetch").innerHTML = "";
		// synchronous
		calcTotal(); // changing user mean new user credit

	}
}

function checkAll2( n, fldName, tab ) {
    if (!fldName) {
       fldName = 'cb';
    }
      var f = document.adminForm;
	  switch (tab)
	  {
		case 2: { var c = f.toggle2.checked; break }

		case 3: { var c = f.toggle3.checked; break }
	 
		case 4: { var c = f.toggle4.checked; break }

		case 5: { var c = f.toggle5.checked; break }

		case 6: { var c = f.toggle6.checked; break }

		case 7: { var c = f.toggle7.checked; break }

		case 8: { var c = f.toggle8.checked; break }

		case 9: { var c = f.toggle9.checked; break }

		default: { var c = f.toggle.checked; break }
	  }
      var n2 = 0;
      for (i=0; i < n; i++) {
          cb = eval( 'f.' + fldName + '' + i );
          if (cb) {
              cb.checked = c;
              n2++;
          }
      }
      if (c) {
          document.adminForm.boxchecked.value = n2;
      } else {
          document.adminForm.boxchecked.value = 0;
      }
}


function parse_service_rates(inStr, i, which){
	aryServices = inStr.split(",");
	for(x=0; x<aryServices.length; x++)
	{
		aryTemp = aryServices[x].split(":");
		if(aryTemp[0]===i){
			if(which==="value"){
				return aryTemp[1];
			} else {
				return aryTemp[2];
			}
		}
	}
}	


function calcTotal() {
	if(document.getElementById("multi_select") != null){
		multiCalcTotal();
		return;
	}
	
	if(document.getElementById("enable_paypal").value === 'No' && document.getElementById("authnet_enable").value === 'No'
		&& document.getElementById("_2co_enable").value === 'No' 
		&& document.getElementById("non_pay_booking_button").value != 'DO' && document.getElementById("non_pay_booking_button").value != 'DAB' ){
		// do not show financials
		calcSeatsTotal();
		return false;
	}
	
	if(document.getElementById("screen_type").value === "non-gad"){
		if( document.getElementById("timeslots") === null || document.getElementById("timeslots").selectedIndex === 0){	
			hideTotal();
			calcSeatsTotal();
			return true;
		}
	}

	startdate = document.getElementById("startdate").value;
	starttime = document.getElementById("starttime").value;
	if(document.getElementById("enddate") === null) {
		enddate = startdate;
	} else {
		enddate = document.getElementById("enddate").value;
	}
	endtime = document.getElementById("endtime").value;
	if(document.getElementById("additionalfee") != null){
		additionalfee = document.getElementById("additionalfee").value;
	}
	if(document.getElementById("feerate") != null){
		feerate = document.getElementById("feerate").value;
	}
	// service rates trump resource rates
	if(document.getElementById("service_rates") != null 
							   && document.getElementById("services").style.display != "none"
							   && document.getElementById("service_rates").value != ""){
		// get parse service rates
		var selected_id = document.getElementById("service_name").options[document.getElementById("service_name").selectedIndex].value;
		var service_rate = parse_service_rates(document.getElementById("service_rates").value, selected_id, 'value');
		var service_rate_unit = parse_service_rates(document.getElementById("service_rates").value, selected_id, 'unit');
		//alert(selected_id);
		//alert(service_rate);
		//alert(service_rate_unit);
		if(service_rate==="0.00"){
			rate = parseFloat(aryRates[res_id]);
			rate_unit = aryRateUnits[res_id];
			res_rate=rate.toFixed(2);
		} else {
			rate = parseFloat(service_rate);
			rate_unit = service_rate_unit;	
			res_rate=rate;
		}
		
	} else {
		rate = parseFloat(aryRates[res_id]);
		rate_unit = aryRateUnits[res_id];
		res_rate=rate;
	}

	calcSeatsTotal(); // also sets res_rate
	
//  If you want to make a UDF adjust cost, see http://appointmentbookingpro.com/how-to/172-have-a-udf-selection-adjust-cost.html	
//	my_udf_dropdown_value = document.getElementById("user_field2_value").options[document.getElementById("user_field2_value").selectedIndex].value;
//	// for testing you can uncomment the line below to chcek you have the correct user_field selected
//	//alert(my_udf_dropdown_value);
//	switch(my_udf_dropdown_value){
//		case "Birthday Party":
//		  res_rate = res_rate + .01;
//		  break;
//		case "Team Party":
//		  res_rate = res_rate + .07;
//		  break;
//	}
//	rate = parseFloat(res_rate);
//
//	
	// -------------------------------------------------------------------
	// start date/time = end date/time -> do nothing
	// -------------------------------------------------------------------
	if(startdate === enddate && starttime === endtime){
		hideTotal();
		return true;

	}
	
	// -------------------------------------------------------------------
	// start date = end date -> single day just calc based on times
	// -------------------------------------------------------------------
	if(startdate === enddate){
		//alert("startdate === enddate");
		var startdecimal = 0.0;
		var enddecimal = 0.0;
		var startminutes_as_decimal = 0.0
		var endminutes_as_decimal = 0.0
		
		starttemp = starttime.split(":", 2);
		// minutes as decimal
		startminutes_as_decimal = parseInt(starttemp[1])/60;
		startdecimal = parseFloat(starttemp[0]) + startminutes_as_decimal;


		endtemp = endtime.split(":", 2);
		// minutes as decimal
		if(endtemp[0] == "23" && (endtemp[1] == "59") || endtemp[1] == "55"){
			endtemp[0] = "24";
			endtemp[1] = "00";
		}
		endminutes_as_decimal = parseInt(endtemp[1])/60;
		enddecimal = parseFloat(endtemp[0]) + endminutes_as_decimal;
		
		hours = enddecimal - startdecimal;
		if(hours <0){
			document.getElementById("res_hours").innerHTML = "err";	
			document.getElementById("res_total").innerHTML = "";
			document.getElementById("res_fee").innerHTML = "";
			document.getElementById("res_grand_total").innerHTML = "err";
			return true;
		}

		if(rate_unit === "Hour"){
			total = hours * rate;					
		} else {
			total = rate;					
		}
		
		showTotal();
	
	} else {
	
		begintemp = startdate.split("-",3);
		endtemp = enddate.split("-",3);
		var begingdate = new Date(begintemp[0], (begintemp[1]-1), begintemp[2]);
		var endingdate = new Date(endtemp[0], (endtemp[1]-1), endtemp[2]);
		var one_day=1000*60*60*24;
	
	
		//Calculate difference btw the two dates, and convert to days
		diffdays = Math.ceil((endingdate-begingdate)/(one_day));
	
	
		// -------------------------------------------------------------------
		// start and end on consecutive dates -> calc as start days hours + end days hours
		// -------------------------------------------------------------------
		if(diffdays === 1){
			//alert("diffdays === 1");	
			//alert(getStartDayHours());
			//alert(getEndDayHours());
			hours = getStartDayHours() + getEndDayHours();
			if(hours <0){
				document.getElementById("res_hours").innerHTML = "err";	
				document.getElementById("res_total").innerHTML = "";
				document.getElementById("res_fee").innerHTML = "";
				document.getElementById("res_grand_total").innerHTML = "err";
				return true;
			}
	
			total = hours * rate;	
		}
		
		// -------------------------------------------------------------------
		// start and end date > 1 day apart -> start day + end day + days between
		// -------------------------------------------------------------------
		if(diffdays > 1){
			
			// how many hours in a day
			var hoursperday = parseInt(document.getElementById("endhour").value)+1 - parseInt(document.getElementById("starthour").value);
			
			hours = ((diffdays-1)*hoursperday)+getStartDayHours() + getEndDayHours();
			
			if(hours <0){
				document.getElementById("res_hours").innerHTML = "err";	
				document.getElementById("res_total").innerHTML = "err";
				document.getElementById("res_fee").innerHTML = "err";

				document.getElementById("res_grand_total").innerHTML = "err";
				return true;
			}
	
			total = hours * rate;			
		}
		
		if(diffdays < 1){
			document.getElementById("res_hours").innerHTML = "err";	
			document.getElementById("res_total").innerHTML = "err";
			document.getElementById("res_fee").innerHTML = "err";
			document.getElementById("res_grand_total").innerHTML = "err";
			return true;
		}
		
	
	}
	showTotal();

}

function calcSeatsTotal(){
	// but seat rates trump all ;-)
	if(document.getElementById("seat_type_count") != null && parseInt(document.getElementById("seat_type_count").value) > 0 ){
		var seat_count = 0; 
		rate = 0.00;
		for(i=0; i<parseInt(document.getElementById("seat_type_count").value); i++){
			seat_name_cost = "seat_type_cost_"+i;
			seat_name = "seat_"+i;
			group_seat_name = "seat_group_"+i;
			seat_count += parseInt(document.getElementById(seat_name).value);
			if(document.getElementById(group_seat_name).value === "Yes"){
				if(document.getElementById(seat_name).selectedIndex > 0){
					seat_type_cost_x_qty = parseFloat(document.getElementById(seat_name_cost).value);
				} else {
					seat_type_cost_x_qty = 0;
				}
			} else {
				seat_type_cost_x_qty = parseFloat(document.getElementById(seat_name_cost).value)*parseInt(document.getElementById(seat_name).value);
			}
			rate = rate + seat_type_cost_x_qty;
		}
		document.getElementById("booked_seats_div").innerHTML = seat_count;
		document.getElementById("booked_seats").value = seat_count;
		res_rate=rate;
		// rate units come from the resource (per hour or per booking)

	}
}

function showTotal() {
	if(document.getElementById("startdate").value.indexOf("-") === -1){
		// not a date in startdate, probably says 'Select a Date' but cannot check for that in case non-English
		return;
	}
	if(document.getElementById("calcResults") != null){
		document.getElementById("calcResults").style.visibility = "visible";
	}
	if(typeof(res_rate) === "string"){
    	document.getElementById("res_rate").innerHTML = res_rate;
	} else {
    	document.getElementById("res_rate").innerHTML = res_rate.toFixed(2);
	}
	//document.getElementById("res_rate").innerHTML = aryRates[res_id].toFixed(2);
	if(rate_unit==="Flat"){
		document.getElementById("res_hours_label").innerHTML = document.getElementById("flat_rate_text").value;
		document.getElementById("res_hours").innerHTML = "";
	} else {
		document.getElementById("res_hours_label").innerHTML = document.getElementById("non_flat_rate_text").value;
		document.getElementById("res_hours").innerHTML = hours.toFixed(2);
	}
	
	// add extras
	calcExtrasTotal();
	
	
	document.getElementById("res_total").innerHTML = total.toFixed(2);
	if(feerate === "Fixed"){
		fee = parseFloat(additionalfee);
	} else if(feerate === "Percent") {
		fee = (total * parseFloat(additionalfee)/100);
	}
	if(fee > 0){
		document.getElementById("res_fee").innerHTML = fee.toFixed(2);
	}
	
	var discount = 0;
	if(document.getElementById("coupon_value") != null){
		if(document.getElementById("coupon_value").value != ""){
			if(document.getElementById("coupon_units").value === "percent"){
				discount = (total + fee + extras_total_cost) * parseFloat(document.getElementById("coupon_value").value)/100;				
			} else {
				discount = parseFloat(document.getElementById("coupon_value").value);			
			}
			document.getElementById("discount").innerHTML = "("+discount.toFixed(2)+")";
		}
	}

	// use customer credit is available
	applied_credit = 0.00;
	if(document.getElementById("uc").value != ""){
		user_credit = parseFloat(document.getElementById("uc").value);
		if((total + fee + extras_total_cost - discount) <= user_credit){
			applied_credit = (total + fee + extras_total_cost - discount);
		}
		if((total + fee + extras_total_cost - discount) > user_credit){
			applied_credit = user_credit;
		}
		if(document.getElementById("current_credit") != null){
			document.getElementById("current_credit").innerHTML = "[ "+user_credit.toFixed(2)+ " ] ";
		}
		if(document.getElementById("applied_credit") != null){
			document.getElementById("applied_credit").value = applied_credit.toFixed(2);
			document.getElementById("credit").innerHTML = "("+applied_credit.toFixed(2)+")";
		}
	}
	
	
	manual_payment = 0;
	if(document.getElementById("manual_payment_collected") != null){
		if(!isNaN(parseFloat(document.getElementById("manual_payment_collected").value))){
			manual_payment = parseFloat(document.getElementById("manual_payment_collected").value);						  
		}
	}
	
	document.getElementById("res_grand_total").innerHTML = (total + fee + extras_total_cost - discount - applied_credit - manual_payment).toFixed(2);
	document.getElementById("grand_total").value = (total + fee + extras_total_cost - discount - applied_credit - manual_payment).toFixed(2);
//	if(parseFloat(document.getElementById("grand_total").value) == 0){
//		document.getElementById("PayPalButton").style.visibility = "hidden";
//		document.getElementById("PayPalButton").style.display = "none";		
//		document.getElementById("NonPayPalButton").style.visibility = "visible";
//		document.getElementById("NonPayPalButton").style.display = "";		
//	} else {
//		document.getElementById("PayPalButton").style.visibility = "visible";
//		document.getElementById("PayPalButton").style.display = "";		
//		document.getElementById("NonPayPalButton").style.visibility = "hidden";
//		document.getElementById("NonPayPalButton").style.display = "none";		
//	}
	if(aryDeposit != null){
		if(typeof aryDeposit[res_id] != 'undefined'){
			deposit = parseFloat(aryDeposit[res_id]);
		} else {
			deposit = 0;
		}
		deposit_unit = "Flat";
		if(deposit > 0){
			document.getElementById("deposit_only").style.visibility = "visible";
			document.getElementById("deposit_only").style.display = "";
			deposit_unit = aryDepositUnits[res_id];
			if(deposit_unit == "Flat"){
				document.getElementById("display_deposit_amount").innerHTML = deposit.toFixed(2);
				document.getElementById("deposit_amount").value = deposit.toFixed(2);		
			} else {
				grand_total = parseFloat(document.getElementById("grand_total").value);
				document.getElementById("display_deposit_amount").innerHTML = (deposit * grand_total/100).toFixed(2);
				document.getElementById("deposit_amount").value = (deposit * grand_total/100).toFixed(2);	
			}
		} else {
			document.getElementById("deposit_only").style.visibility = "hidden";
			document.getElementById("deposit_only").style.display = "none";
			document.getElementById("display_deposit_amount").innerHTML = "";		
			document.getElementById("deposit_amount").value = "0.00";		
		}
	} else {
		document.getElementById("deposit_only").style.visibility = "hidden";
		document.getElementById("deposit_only").style.display = "none";
		document.getElementById("display_deposit_amount").innerHTML = "";		
		document.getElementById("deposit_amount").value = "0.00";		
	}

	document.getElementById("calcResults").style.height = "auto";
	document.getElementById("calcResults").style.display = "block";
	
}


function hideTotal(){
  	if(document.getElementById("calcResults")!=null){
    	document.getElementById("calcResults").style.visibility = "hidden";
	    document.getElementById("calcResults").style.height = "1px";
    	document.getElementById("calcResults").style.display = "none";
	}
}

function buildFrontDeskView(day, month, year, week_offset){
   	var Itemid = document.getElementById('frompage_item').value;
   	var view_list_field = document.getElementById('front_desk_view');
    var view_list_selected_index = view_list_field.selectedIndex;
    var view = view_list_field.options[view_list_selected_index].value;

   	var resource_list_field = document.getElementById('resource_filter');
    var resource_list_selected_index = resource_list_field.selectedIndex;
    var resource = resource_list_field.options[resource_list_selected_index].value;

	var category = "";
    if(document.getElementById("category_filter")!=null){
		var category_list_field = document.getElementById('category_filter');
	    var category_list_selected_index = category_list_field.selectedIndex;
	    category = category_list_field.options[category_list_selected_index].value;
	}
    
    if (typeof day === "undefined") {
        if(document.getElementById("cur_day")!=null){
            day = document.getElementById("cur_day").value;
        } else {
		    var d = new Date();
		    day = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
        }
    }
    if (typeof month === "undefined") {
        if(document.getElementById("cur_month")!=null){
            month = document.getElementById("cur_month").value;
        } else {
            month="";
        }
    }
    if (typeof year === "undefined") {
        if(document.getElementById("cur_year")!=null){
            year = document.getElementById("cur_year").value;
        } else {
	        year = "";
		}
    }
    if (typeof week_offset === "undefined") {
        if(document.getElementById("cur_week_offset")!=null){
            week_offset = document.getElementById("cur_week_offset").value;
        } else {
            week_offset="0";
        }
    } 
       

   	var select_list_field = document.getElementById('status_filter');
    var select_list_selected_index = select_list_field.selectedIndex;
    var status = select_list_field.options[select_list_selected_index].value;

    //document.getElementById("calview_here").innerHTML = "Please wait"
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {

		xhr.onreadystatechange = showFrontDeskView;
		var data = "front_desk_view=" + view;
		data = data + "&day=" + day;
		data = data + "&month=" + month;
		data = data + "&year=" + year;
		data = data + "&resource=" + resource;
		data = data + "&category=" + category;
		data = data + "&user=" + document.getElementById("uid").value;
		data = data + "&status=" + status;
		data = data + "&weekoffset=" + week_offset;
		data = data + "&user_search=" + document.getElementById("user_search").value;
		data = data + "&listpage=" + document.getElementById("listpage").value;
		if(document.getElementById("showSeatTotals")!=null){
			data = data + "&showSeatTotals=" + document.getElementById("showSeatTotals").checked;
		}
		if(document.getElementById("printer_view")!=null){
			data = data + "&printer=" + document.getElementById("printer_view").value;
		}
		data = data + "&Itemid=" + Itemid;
		data = data + "&Menuid=" +  document.getElementById("menu_id").value;
		data = data + "&browser=" + BrowserDetect.browser;
		//alert(data);
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_calview&format=raw&" + data, true);
		xhr.send(null);
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
function showFrontDeskView() {	
		
	if (xhr.readyState === 4) {
		document.getElementById("calview_here").style.visibility = "visible";
		document.getElementById("calview_here").style.display = "";
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}

		document.getElementById("calview_here").innerHTML = outMsg;
        
        if(document.getElementById('front_desk_view').selectedIndex < 2){
            document.getElementById("reminder_links").style.visibility = "visible";
		    document.getElementById("reminder_links").style.display = "";
        } else {
            document.getElementById("reminder_links").style.visibility = "hidden";
		    document.getElementById("reminder_links").style.display = "none";
        }
 		document.body.style.cursor = "default";    
	}

	SqueezeBox.initialize({});
	SqueezeBox.assign($$('a.modal'), {
		parse: 'rel'
	});

	return true;
}


function checkForBookingOverlap(startdate, starttime, enddate, endtime, resource){
	document.getElementById("selected_resource_wait").innerHTML = "("+document.getElementById("wait_text").value+")";
	if(document.getElementById("submit") != null){
		submit_status = document.getElementById("submit").disabled;
		document.getElementById("submit").disabled = true;
	}
	
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr) {
		xhr.onreadystatechange = showOverlap_Results;
		var data = "startdate=" + encodeURIComponent(startdate);
		data = data + "&starttime=" + encodeURIComponent(starttime);
		data = data + "&enddate=" + encodeURIComponent(enddate);
		data = data + "&endtime=" + encodeURIComponent(endtime);
		data = data + "&res_id=" + resource;
		if(document.getElementById("mobile")===null){
	 		document.body.style.cursor = "wait";    
		}

		// asynchronous
		xhr.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax_check_overlap&format=raw&" + data, true);
		xhr.send('');

		// synchronous
		//xhr.open("GET", presetIndex()+"?option=com_rsappt_pro14&page=ajax_check_overlap&format=raw&" + data, false);
		//xhr.send(null);
		
		// synchronous
		// must be async because IE locks browser before 'Please Wait' can be displayed
/*		var outMsg = "";
		outMsg = xhr.responseText;
		document.getElementById("adjusted_starttime").value = outMsg;
		if(document.getElementById("mobile")===null){
	 		document.body.style.cursor = "default";    
		}
		document.getElementById("selected_resource_wait").innerHTML = "";
*/
		// synchronous
		
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
		// synchronous
		return false;
	}
	//return true;
}


function showOverlap_Results() {	
		
	if (xhr.readyState === 4) {
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}

		document.getElementById("adjusted_starttime").value = outMsg;
		document.getElementById("selected_resource_wait").innerHTML = "";
		if(document.getElementById("mobile")===null){
	 		document.body.style.cursor = "default";    
		}
		
		setDuration();
		calcTotal();

		if(document.getElementById("submit") != null){
			document.getElementById("submit").disabled = submit_status;
		}

	}
	return true;
}

function setDuration(){

//	if(document.getElementById("multi_select") != null){
//		setMultiDuration();
//		return;
//	}
	
	service_duration = 0;
	extras_duration = 0;
		
// as of 2.0.2 duration can be effected by service OR extras
//	if(document.getElementById("service_durations") === null || document.getElementById("service_durations").value==="") {
//		return;
//	}

	// do services first as service duration SETS the duration, whereas extras only ADD TO the duration.
	
	var service_duration = 0;
	if(document.getElementById("service_name") != null){
		if(document.getElementById("service_name").options.length > 0){
			var which_service = document.getElementById("service_name").selectedItem;
			var selected_id = document.getElementById("service_name").options[document.getElementById("service_name").selectedIndex].value;
			service_duration = parse_service_durations(document.getElementById("service_durations").value, selected_id, 'value');
		}
	}

	// if there are extras durations, add to service duration OR add to timslot then adjust duration as if it were a normal servce duration
	if(document.getElementById("extras_count") != null){
		extras_duration = getExtras_duration();
	}
	
	if(service_duration != 0 ){
	
		var startdate = document.getElementById("startdate").value;
		var starttime;
		if(document.getElementById("adjusted_starttime") === null || trim(document.getElementById("adjusted_starttime").value) === ""){
			starttime = document.getElementById("starttime").value;
		} else {
			// adjusted_starttime holds both the actual and display versions
			aryStarttimes = document.getElementById("adjusted_starttime").value.split("|");

			if(document.getElementById("pxm") != null){
				// change timeslot div to reflact new starttime
				// first get difference in minutes between original and new start times
				
				var d1 = Date.parse(startdate + " " + document.getElementById("starttime").value).getTime();
				var d2 = Date.parse(startdate + " " + aryStarttimes[1]).getTime();
				var newLeft = (d2-d1)/(1000*60) + parseFloat(document.getElementById(old_ts['id']).style.left);
				document.getElementById(old_ts['id']).style.left = newLeft+"px";
			}
			
			starttime = aryStarttimes[1];
			document.getElementById("starttime").value=aryStarttimes[1];
			document.getElementById("selected_starttime").innerHTML = aryStarttimes[0];
		}
		var enddate = document.getElementById("enddate").value;
		var endtime = document.getElementById("endtime").value;
		// calculate new endtime
		var service_duration_unit = parse_service_durations(document.getElementById("service_durations").value, selected_id, 'unit');
		var d1 = Date.parse(startdate + " " + starttime);
		if(d1 != null){
			if(service_duration_unit === "Minute"){
				d1.add({ minute: service_duration });
			} else {
				d1.add({ hour: service_duration });
			}
			
			// add extras is applicable
			if(extras_duration > 0){
				d1.add({ minute: extras_duration });
			}
			
			// if adding yields next day 00:00:00 then set to same day 23:59:59
			if(d1.toString("yyyy-MM-dd") != startdate){
				d1.add({ seconds: -1 })
			}
			var timeformatstring = "";
			if(document.getElementById("timeFormat").value === "12"){
				timeformatstring = "h:mm tt";
			} else {
				timeformatstring = "H:mm";
			}

// Normally the end of the day is the end of the final timeslot, but if using Service based duration 
// bookings are not constrained by timeslot boundaries. In that case the default end time of the grid is end-of-day,
// and ABPro will block bookings going beyond that time.
//
// IF you are using Service based duration AND want DIFFERENT end of day for different week days you can use 
// the code below.
				
			// To hard code different end of day by weekday, uncomment the code below (only affects servce based duration)
			// First we need to determin the day-of-the-week of the selected timeslot
//			var ts_date = Date.parse(startdate + " " + starttime);
//			var day_of_the_week = ts_date.getDay();			
//			switch (day_of_the_week) {
//				case 0: { document.getElementById("end_of_day").value = "16:00"; break } // Sunday
//				case 1: { document.getElementById("end_of_day").value = "17:00"; break } // Monday
//				case 2: { document.getElementById("end_of_day").value = "17:00"; break } // Tuesday
//				case 3: { document.getElementById("end_of_day").value = "17:00"; break } // Wednesday
//				case 4: { document.getElementById("end_of_day").value = "20:00"; break } // Thursday
//				case 5: { document.getElementById("end_of_day").value = "20:00"; break } // Friday
//				case 6: { document.getElementById("end_of_day").value = "17:00"; break } // Saturday
//			}
			
			//To hard code different end of day by resource, uncomment the code below (only affects servce based duration)
//			var my_res = document.getElementById("selected_resource_id").value;
//			switch (my_res) {
//				case "2": { document.getElementById("end_of_day").value = "15:00"; break } // end-of-day is 15:00 for resource 2
//				case "50": { document.getElementById("end_of_day").value = "13:00"; break } // end-of-day is 13:00 for resource 50.
//			}
						
			if(document.getElementById("end_of_day").value === "24:00"){
				//not a valid time to parse
				document.getElementById("end_of_day").value = "23:59:59";
			}
			if(	d1 > Date.parse(startdate + " " + document.getElementById("end_of_day").value, "yyyy-MM-dd H:mm")){
				alert(document.getElementById("beyond_end_of_day").value);
				document.getElementById("booking_detail").style.display = "none";
				document.getElementById("booking_detail").style.visibility = "hidden";		
				document.getElementById("startdate").value="";
				document.getElementById("enddate").value="";
				document.getElementById("starttime").value="";
				document.getElementById("endtime").value="";
				if(old_ts['id'] != 0){
					document.getElementById(old_ts['id']).className = "sv_gad_timeslot_available";
					document.getElementById(old_ts['id']).style.width = old_ts['ts_width']+"px";
					document.getElementById(old_ts['id']).style.left = old_ts['ts_left'];
					document.getElementById(old_ts['id']).style.zIndex = old_ts['ts_index'];
					old_ts['ts_width'] = 0;
					old_ts['ts_left'] = 0;
					old_ts['ts_index'] = 0;
				} 
			} else {
				if(document.getElementById("selected_endtime") != null){
					document.getElementById("selected_endtime").innerHTML = d1.toString(timeformatstring);
					
					if(document.getElementById("pxm") != null){
						// adjust the timeslot disply size to reflect adjusted duration
						// store origincal values (only for time on Y axis = No)						
						var pxminute = parseFloat(document.getElementById("pxm").value);
						var newWidth = 0;
						if(service_duration_unit === "Minute"){
							newWidth = service_duration * pxminute;
						} else {
							newWidth = service_duration * 60 * pxminute;
						}
						if(extras_duration > 0){
							newWidth += extras_duration;
						}						
						document.getElementById(old_ts['id']).style.width = newWidth+"px";
						document.getElementById(old_ts['id']).style.zIndex = 1000;
					}
				}
				document.getElementById("endtime").value = d1.toString("H:mm:ss");
			}
		}
	} // if(service_duration != 0 )
	
	if(service_duration === 0 && extras_duration != 0){
		// There is no servcie duration BUT there is an extras duration  to be added to the timeslot
		
	}
}


function getResourceUFDs(){
	if(document.getElementById("resources") === null){
		return false;
	}
	if(document.getElementById("resources").value === "0"){
		return false;
	}
	
	if (window.XMLHttpRequest) {
		xhr3 = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr3 = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr3) {
		xhr3.onreadystatechange = showResourceUFDs;
		var data = "res=" + document.getElementById("resources").value;
		data = data + "&browser=" + BrowserDetect.browser;
		if(document.getElementById("fd") != null){
			data = data + "&fd="+document.getElementById("fd").value;
		}
		data = data + "&res_udfs=yes";
		//alert(data);
		xhr3.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr3.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
	
function showResourceUFDs() {	
		
	if (xhr3.readyState === 4) {
	
		if (xhr3.status === 200) {		
			var outMsg = xhr3.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr3.status;
		}

		// for dev
		//document.getElementById("cancel_results").innerHTML = outMsg;

		if(outMsg != ""){
			document.getElementById("resource_udfs").style.display = "";
			document.getElementById("resource_udfs").style.visibility = "visible";
			document.getElementById("resource_udfs_div").style.display = "";
			document.getElementById("resource_udfs_div").style.visibility = "visible";
			document.getElementById("resource_udfs_div").innerHTML = outMsg;
		} else {
			document.getElementById("resource_udfs_div").style.visibility = "hidden";
			document.getElementById("resource_udfs_div").style.display = "none";
			document.getElementById("resource_udfs_div").innerHTML = "";
			document.getElementById("resource_udfs").style.visibility = "hidden";
			document.getElementById("resource_udfs").style.display = "none";
			document.getElementById("resource_udfs").style.height = "1px";
		}

	}

	return true;
}


function getResourceSeatTypes(){
	if(document.getElementById("resources") === null){
		return false;
	}
	if(document.getElementById("resources").value === "0"){
		return false;
	}
	
	if (window.XMLHttpRequest) {
		xhr4 = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr4 = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr4) {
		xhr4.onreadystatechange = showResourceSeatTypes;
		var data = "res=" + document.getElementById("resources").value;
		data = data + "&browser=" + BrowserDetect.browser;
		data = data + "&res_seats=yes";
		if(document.getElementById("users")!=null){
			data = data + "&uid="+document.getElementById("users").value;		
		}
		if(document.getElementById("mobile")!=null){
			data = data + "&mobile=" + document.getElementById("mobile").value;	
		}
		//alert(data);
		xhr4.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr4.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
	
function showResourceSeatTypes() {	
		
	if (xhr4.readyState === 4) {
	
		if (xhr4.status === 200) {		
			var outMsg = xhr4.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr4.status;
		}

		// for dev
		// document.getElementById("cancel_results").innerHTML = outMsg;
		//alert(outMsg);
			  
		if(outMsg != ""){
			document.getElementById("resource_seat_types").style.display = "";
			document.getElementById("resource_seat_types").style.visibility = "visible";
			document.getElementById("resource_seat_types_div").style.display = "";
			document.getElementById("resource_seat_types_div").style.visibility = "visible";
			document.getElementById("resource_seat_types_div").innerHTML = outMsg;
		} else {
			document.getElementById("resource_seat_types_div").style.visibility = "hidden";
			document.getElementById("resource_seat_types_div").style.display = "none";
			document.getElementById("resource_seat_types_div").innerHTML = "";
			document.getElementById("resource_seat_types").style.visibility = "hidden";
			document.getElementById("resource_seat_types").style.display = "none";
			document.getElementById("resource_seat_types").style.height = "0px";
		}

	}

	return true;
}


function getExtras(){
	// clear out old stuff
	if(document.getElementById("resource_extras") != null){
		document.getElementById("resource_extras").style.display = "none";
		document.getElementById("resource_extras").style.visibility = "hidden";
	}
	if(document.getElementById("resource_extras_div") != null){
		document.getElementById("resource_extras_div").style.display = "none";
		document.getElementById("resource_extras_div").style.visibility = "hidden";
		document.getElementById("resource_extras_div").innerHTML = "";
	}

	if(document.getElementById("resources") === null){
		return false;
	}
	if(document.getElementById("resources").value === "0"){
		return false;
	}
	
	if (window.XMLHttpRequest) {
		xhr5 = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr5 = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr5) {
		xhr5.onreadystatechange = showExtras;
		var data = "res=" + document.getElementById("resources").value;
		//if(document.getElementById("service_name") != null){
		//	data = data + "&srv=" + document.getElementById("resources").value;		
		//}
		if(document.getElementById("mobile")!=null){
			data = data + "&mobile=" + document.getElementById("mobile").value;	
		}
		data = data + "&browser=" + BrowserDetect.browser;
		data = data + "&extras=yes";
		if(document.getElementById("users")!=null){
			data = data + "&uid="+document.getElementById("users").value;		
		}
		//alert(data);
		xhr5.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr5.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}
	
	
function showExtras() {	
		
	if (xhr5.readyState === 4) {
	
		if (xhr5.status === 200) {		
			var outMsg = xhr5.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr5.status;
		}

		// for dev
		//alert(outMsg);
			  
		if(outMsg != ""){
			document.getElementById("resource_extras").style.display = "";
			document.getElementById("resource_extras").style.visibility = "visible";
			document.getElementById("resource_extras_div").style.display = "";
			document.getElementById("resource_extras_div").style.visibility = "visible";
			document.getElementById("resource_extras_div").innerHTML = outMsg;
		} else {
			document.getElementById("resource_extras_div").style.visibility = "hidden";
			document.getElementById("resource_extras_div").style.display = "none";
			document.getElementById("resource_extras_div").innerHTML = "";
			document.getElementById("resource_extras").style.visibility = "hidden";
			document.getElementById("resource_extras").style.display = "none";
			document.getElementById("resource_extras").style.height = "1px";
		}

	}

	return true;
}


function getCoupon(){
	if(document.getElementById("resources") === null){
		return false;
	}
	
	if(document.getElementById("coupon_code").value === ""){
		document.getElementById("coupon_info").innerHTML = "";
		document.getElementById("coupon_value").value = "0";
		document.getElementById("coupon_units").value = "";
	} else {
		document.getElementById("coupon_info").innerHTML = document.getElementById("wait_text").value;
	}
							   
	if (window.XMLHttpRequest) {
		xhr3 = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr3 = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr3) {
		xhr3.onreadystatechange = showCoupon;
		var data = "getcoup=yes";
		data = data + "&res=" + document.getElementById("resources").value;
		data = data + "&cc=" + document.getElementById("coupon_code").value;
		data = data + "&browser=" + BrowserDetect.browser;
		//alert(data);
		xhr3.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr3.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}

function showCoupon() {	
		
	if (xhr3.readyState === 4) {
	
		if (xhr3.status === 200) {		
			var outMsg = xhr3.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr3.status;
		}

		// for dev
		//document.getElementById("cancel_results").innerHTML = outMsg;

	    if(outMsg != ""){
		    ary = outMsg.split("|");
		    document.getElementById("coupon_info").innerHTML = ary[0];
		    document.getElementById("coupon_value").value = ary[1];
		    document.getElementById("coupon_units").value = ary[2];
			calcTotal();
	    } else {
		    document.getElementById("coupon_info").innerHTML = "";
		    document.getElementById("coupon_value").value = "0";
		    document.getElementById("coupon_units").value = "";
	    }
	}

	return true;
}

function getSubCategories(cat_id, fd){
							   
	document.body.style.cursor = "wait";
	
	if (window.XMLHttpRequest) {
		xhr3 = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr3 = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr3) {
		xhr3.onreadystatechange = showSubCategories;
		var data = "getsubcats=yes";
		data = data + "&cat=" + cat_id;		
		data = data + "&fd=" + fd;
		data = data + "&browser=" + BrowserDetect.browser;
		//alert(data);
		xhr3.open("GET", presetIndex()+"?option=com_rsappt_pro3&controller=ajax&task=ajax&format=raw&" + data, true);
		xhr3.send('');
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
	}
	return true;
}

function showSubCategories(){
	
	document.body.style.cursor = "default";    

	if (xhr3.readyState === 4) {
	
		if (xhr3.status === 200) {		
			var outMsg = xhr3.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr3.status;
		}

		//alert("|"+trim(outMsg)+"|");
		
	    if(trim(outMsg) != ""){
			document.getElementById("subcats_row").style.visibility = "visible";
			document.getElementById("subcats_row").style.display = "";
			document.getElementById("subcats_div").innerHTML = outMsg;
			// hide any resources and grid from previous pick
			if(document.getElementById("datetime")!=null){ document.getElementById("datetime").style.display = "none";}
			document.getElementById("services_div").style.display = "none";
			if(document.getElementById("service_summary") != null){
				document.getElementById("service_summary").style.display = "none";
				document.getElementById("service_summary").style.visibility = "hidden";
			}
			if(document.getElementById("resources")!=null){ document.getElementById("resources").style.display = "none";}
			if(document.getElementById("gad_container")!=null){ document.getElementById("gad_container").style.display = "none";}
			if(document.getElementById("resources")!=null){ document.getElementById("subcats_row_div").style.display = "none";}
			
		} else {
			// no sub categories for that categogy, go ahead and show resources
			document.getElementById("subcats_row").style.visibility = "hidden";
			document.getElementById("subcats_row").style.display = "none";

			if(document.getElementById("mode") != null){
				if(document.getElementById("category_id").value === "0"){
					document.getElementById("table_here").innerHTML = "";
					document.getElementById("table_here").style.visibility = "hidden";
					document.getElementById("table_here").style.display = "none";	
					return false;
				}
				document.getElementById("table_here").innerHTML = document.getElementById("wait_text").value;
		
				document.getElementById("gad_container").style.display = "";
				buildTable();
			} else {
				document.getElementById("slots").style.visibility = "hidden";
				document.getElementById("startdate").value = "";
			}
			$fd = "No";
			if(document.getElementById("fd") != null){
				$fd = document.getElementById("fd").value;
			}
			getResources($fd);
		}
	}

	return true;
}

function changeSubCategory(fd){
	if(document.getElementById("sub_category_id").selectedIndex  === 0){
		if(document.getElementById("datetime")!=null){ document.getElementById("datetime").style.display = "none";}
		document.getElementById("services_div").style.display = "none";
		document.getElementById("resources").style.display = "none";
		document.getElementById("gad_container").style.display = "none";
		if(document.getElementById("service_summary") != null){
			document.getElementById("service_summary").style.display = "none";
			document.getElementById("service_summary").style.visibility = "hidden";
		}
		return false;
	}

	if(document.getElementById("mode") != null){
		if(document.getElementById("category_id").value === "0"){
			document.getElementById("table_here").innerHTML = "";
			document.getElementById("table_here").visible = false;
			document.getElementById("table_here").display = "none";	
			return false;
		}
		document.getElementById("table_here").innerHTML = document.getElementById("wait_text").value;

		document.getElementById("gad_container").style.display = "";
		buildTable();
	} else {
		document.getElementById("slots").style.visibility = "hidden";
		document.getElementById("startdate").value = "";
	}

	getResources(fd);
}

function calcExtrasTotal(){
	extras_total_cost = 0.0;
	if(document.getElementById("extras_count") != null && parseInt(document.getElementById("extras_count").value) > 0 ){
		var extras_count = 0; 
		for(i=0; i<parseInt(document.getElementById("extras_count").value); i++){
			extras_cost = "extras_cost_"+i;
			extras_cost_unit = "extras_cost_unit_"+i;
			extras_name = "extra_"+i;
			//if(document.getElementById(extras_name).selectedIndex > 0){
				extra_qty = parseInt(document.getElementById(extras_name).value);
				if(document.getElementById(extras_name).type === "checkbox"){
					if(document.getElementById(extras_name).checked){
						extra_qty = 1;
					} else {
						extra_qty = 0;
					}
				}
				if(document.getElementById(extras_cost_unit).value === "Hour"){
						extras_total_cost += (extra_qty * parseFloat(document.getElementById(extras_cost).value) * hours);
				} else {
					extras_total_cost += (extra_qty * parseFloat(document.getElementById(extras_cost).value));
				}
			//}
		}
		document.getElementById("extras_fee").innerHTML = extras_total_cost.toFixed(2);
	}
}




function checkWhoBooked(which){
	if(document.getElementById("gad_who_booked")===null){
		return;
	}
	if(document.getElementById("gad_who_booked").value==='No'){
		return;
	}

	// get the onclick so we can parse out the ts date start and end times.
	var ts_onclick = ""+document.getElementById(which).childNodes[0].onclick;
	var i = ts_onclick.indexOf("selectTimeslot(",0);
	ts_onclick = ts_onclick.substring(i+16);
	//document.getElementById(which).setAttribute('title', ts_onclick);
	//return;

	var ary_selected = ts_onclick.split("|");
	var resource = ary_selected[0];
	var startdate = ary_selected[2];
	var enddate = ary_selected[2];
	var starttime = ary_selected[4];
	var endtime = ary_selected[6];
	
	if (window.XMLHttpRequest) {
		xhr27 = new XMLHttpRequest();
	}
	else {
		if (window.ActiveXObject) {
			try {
				xhr27 = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) { }
		}
	}

	if (xhr27) {
		xhr27.onreadystatechange = showWhoBooked;
		var data = "startdate=" + encodeURIComponent(startdate);
		data = data + "&starttime=" + encodeURIComponent(starttime);
		data = data + "&enddate=" + encodeURIComponent(enddate);
		data = data + "&endtime=" + encodeURIComponent(endtime);
		data = data + "&res_id=" + resource;
		data = data + "&ts=" + which;
		//alert(data);

		// asynchronous
		xhr27.open("GET", "./index.php?option=com_rsappt_pro3&controller=ajax&task=ajax_who_booked&format=raw&" + data, true);
		//xhr.open("GET", "./index.php?option=com_rsappt_pro14&page=ajax_check_overlap&format=raw&" + data, true);
		xhr27.send('');

		// synchronous
		//xhr.open("GET", "./index.php?option=com_rsappt_pro14&page=ajax_check_overlap&format=raw&" + data, false);
		//xhr.send(null);
		
		// synchronous
		// must be async because IE locks browser before 'Please Wait' can be displayed
/*		var outMsg = "";
		outMsg = xhr.responseText;
		document.getElementById("adjusted_starttime").value = outMsg;
		if(document.getElementById("mobile")===null){
	 		document.body.style.cursor = "default";    
		}
		document.getElementById("selected_resource_wait").innerHTML = "";
*/
		// synchronous
		
	}
	else {
		alert("Sorry, but I couldn't create an XMLHttpRequest");
		// synchronous
		return false;
	}
	return true;
}

function showWhoBooked() {	
		
	if (xhr27.readyState === 4) {
	
		if (xhr27.status === 200) {		
			var outMsg = xhr27.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}

		//alert(outMsg);
		var ary_booked_info = outMsg.split("|");
		if(ary_booked_info.length === 0){
			return;
		} else {
			//alert(ary_booked_info[1]);
			eval("document.getElementById("+ary_booked_info[0]+").setAttribute('title', '"+ary_booked_info[1]+"');");		
		}
	}
	return;
}

function getCheckedValue(udf_name){
    var radio_length = document.frmRequest[udf_name].length;
    for(udf_i=0; udf_i<radio_length; udf_i++){
        if(document.frmRequest[udf_name][udf_i].checked){
            return document.frmRequest[udf_name][udf_i].value;
        }
    }
    return "";    
}

function getExtras_duration(){
	var extras_count = 0; 
	var total_extras_duration = 0; // in minutes, this release will only support extra duration in minutes
	
	for(i=0; i<parseInt(document.getElementById("extras_count").value); i++){
		extras_duration = "extras_duration_"+i;
		extras_duration_effect = "extras_duration_effect_"+i;
		//extras_cost_unit = "extras_duration_unit_"+i;
		extras_name = "extra_"+i;
		if(document.getElementById(extras_name).type === "checkbox"){
			if(document.getElementById(extras_name).checked){
    			total_extras_duration += parseFloat(document.getElementById(extras_duration).value);
			}			
		} else {
			if(document.getElementById(extras_name).selectedIndex > 0){
				if(document.getElementById(extras_duration_effect).value === "PerUnit"){
					total_extras_duration += (parseInt(document.getElementById(extras_name).value) * parseFloat(document.getElementById(extras_duration).value) );
				} else {
					total_extras_duration += (parseFloat(document.getElementById(extras_duration).value) );
				}
			}
		}
	}
	return total_extras_duration;	
}

function changeExtra(){
	setDuration();
	calcTotal();
}

Joomla.tableOrdering = function tableOrdering( order, dir, prefix ) {
	// I am overriding Joomla's function to make it support ordering different tabs in a form
	var form = document.adminForm;
	//alert(prefix+'filter_order');
	ctl = prefix+'filter_order';
	ctl2 = prefix+'filter_order_Dir';
	document.adminForm.elements[ctl].value = order;
	document.adminForm.elements[ctl2].value	= dir;

	if(document.getElementById("current_tab") != null){
	  switch (prefix)
	  {
		case "req_": { 	document.getElementById("current_tab").value="0"; break }
		case "res_": { 	document.getElementById("current_tab").value=document.getElementById("resources_tab").value; break }
		case "srv_": { 	document.getElementById("current_tab").value=document.getElementById("services_tab").value; break }
		case "ts_": { 	document.getElementById("current_tab").value=document.getElementById("timeslots_tab").value; break }
		case "bo_": { 	document.getElementById("current_tab").value=document.getElementById("bookoffs_tab").value; break }
		case "pp_": { 	document.getElementById("current_tab").value=document.getElementById("paypal_tab").value; break }
		case "an_": { 	document.getElementById("current_tab").value=document.getElementById("authnet_tab").value; break }
		case "2co_": { 	document.getElementById("current_tab").value=document.getElementById("_2co_tab").value; break }
		case "coup_": { document.getElementById("current_tab").value=document.getElementById("coupons_tab").value; break }
		case "ext_": { document.getElementById("current_tab").value=document.getElementById("extras_tab").value; break }
	  }
	}
	submitform();
}


function addToCart(){

	jQuery.noConflict();
	// add to cart will add the booking as 'pending', locking the slot.
	
	// First validate screen..
	result = validateForm();
	//alert("|"+result+"|");

	if(result.indexOf(document.getElementById("validate_text").value)==-1){
		return false;
	}
	// get all input elements
	pagedata = "&"+jQuery(":input").serialize();
	// get udfs
	udf_count = parseInt(document.getElementById("udf_count"));
	res_udf_count = parseInt(document.getElementById("res_udf_count"));
	int_udf_count = udf_count + res_udf_count;
	for(i=0;i<int_udf_count;i++){
		pagedata += "&"+jQuery("user_field"+i+"_value").serialize();
	}
	if(document.getElementById("selected_resource_id") != null){
		// gad and wiz
		pagedata += "&resource="+document.getElementById("selected_resource_id").value;
	} else {
		// simple
		pagedata += "&resource="+document.getElementById("resources").value;
	}
	pagedata += "&ppsubmit=4"; // add to cart
	
	// add timestamp so IE caching will not block the server call in the case of rebooking the same slot	
	// not required for POST
	//pagedata += "&x="+ new Date();
	
	var pagedata = encodeURIComponent(pagedata);
	
	//alert(pagedata); 
    jQuery.ajax({               
		type: "GET",
		dataType: 'json',
		cache: false,
		url: "index.php?option=com_rsappt_pro3&controller=booking_screen_gad&task=process_booking_request",
		data: pagedata,
		success: function(data) {
			if(document.getElementById("controller").value != "booking_screen_simple"){
				buildTable();
			}
			if(document.getElementById("controller").value == "bookingscreengadwiz"){
				gowiz1();
			}		
			document.getElementById("errors").innerHTML = "";
			alert(data.msg);
			//viewCart();
		},
		error: function(data) {
			alert(data.responseText);
		}					
	 });
	
}

function viewCart(){
	var x_size = 750;
	var	y_size = 500;
	if(document.getElementById("mobile")!= null){
		x_size = 350;
		y_size = 250;
	}
	window.parent.SqueezeBox.open('index.php?option=com_rsappt_pro3&view=cart&task=view&tmpl=component', {handler: 'iframe', size: {x: x_size, y: y_size}, onClose: function(){cart_window_close();}});
}

function cart_window_close(){

	if(localStorage["checkout_required"] == "yes"){
		// off to PayPal or Authnet
		localStorage["checkout_required"] = "";
		var dest_url = "index.php?option=com_rsappt_pro3&view=cart&task=checkout&sid="
		+localStorage["checkout_sid"]+"&pp="+localStorage["checkout_dest"]
		+"&cart_total="+localStorage["checkout_cart_total"]+"&frompage="+document.getElementById('frompage').value
		+"&frompage_item="+document.getElementById('frompage_item').value;
		//alert(dest_url);
		//return false;
		document.body.style.cursor = "wait";
		document.location.href = dest_url;
		return true;		
	}
	if(document.getElementById("controller").value != "booking_screen_simple"){
		// Refresh the grid
		buildTable(); // to show pending bookings
		calcTotal(); // to clear out the costs display
	}
	if(localStorage["checkout_complete"] == "yes"){
		localStorage["checkout_complete"] = "";
		if(document.getElementById("controller").value == "bookingscreengadwiz"){
			gowiz1();
		}		
		if(document.getElementById("controller").value == "booking_screen_simple"){
			document.location.reload(true);
		}		
	} else {
		if(document.getElementById("controller").value == "bookingscreengadwiz"){
			gowiz1();
		}		
	}
}
