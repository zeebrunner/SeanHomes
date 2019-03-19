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

	$jinput = JFactory::getApplication()->input;
	$req_id = $jinput->getString( 'req_id', '' );
	$itemid = $jinput->getString( 'Itemid', '' );
	$option = $jinput->getString( 'option', '' );
	$cc = $jinput->getString( 'cc', '' );
	$which_message = $jinput->getString( 'which_message', 'confirmation' );

	include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );

	if($cc == "cart"){
		// used cart confirmation stored in session
		$session = JFactory::getSession();
		$message = $session->get('confirmation_message');
	} else {
		$message = buildMessage($req_id, $which_message, "No", $cc, "Yes");
	}

	// get config stuff
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_confitmation_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}
//	if(!class_exists('Mobile_Detect')) {
//		require_once JPATH_SITE.DS."components".DS."com_rsappt_pro3".DS."Mobile_Detect.php";
//	}
//	$detect = new Mobile_Detect();
	$appWeb      = new JApplicationWeb;

	echo $message;

?>

  <input type="hidden" name="option" value="<?php echo $option; ?>" />
  <input type="hidden" name="controller" value="booking_screen_simple" />
  <input type="hidden" name="task" id="task" value="" />
  <input type="hidden" name="frompage_item" value="<?php echo $itemid ?>" />
  <br />
      <?php if($apptpro_config->hide_logo == 'No'){ ?>
        <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
      <?php } ?>
</form>

<style>
.imgBlock{
	margin-top: calc(26vh + 5%) !important;
}
body.menu-vip-event #rt-transition #rt-mainbody-surround #rt-main .rt-container .sidebar-b + .rt-grid-10.rt-push-6 .component-content .thnxMsg .addressVIP{
	margin-top: calc(5vh + 10%) !important;
}
@media(max-width:1600px) and (min-width:1330px){
	.imgBlock{
		margin-top: calc(32vh + 5%) !important;
	}
}
@media(max-width:1330px) and (min-width:850px){
	.imgBlock{
		margin-top: calc(35vh + 5%) !important;
	}
}
@media(max-width:850px) and (min-width:400px){
	.imgBlock{
		margin-top: calc(26vh + 5%) !important;
	}
}
@media(max-width:400px){
	.sa3-sb3-mb10{
		background: #df1b21 url(/images/vip/redCarpetVIP.png) no-repeat scroll 0% 0% !important;
	}
	body.menu-vip-event #rt-transition #rt-mainbody-surround #rt-main .rt-container .sidebar-b + .rt-grid-10.rt-push-6 .component-content .thnxMsg .imgBlock .gantry-width-50 span {
    color: white !important;
	}
	.rt-container{
		background: rgba(0, 0, 0, 0.38);
	}
}
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
</style>
<script>
	jQuery(window).on('load', function(){
		jQuery.ajax({
		  url: "Mailchimp/API/src/API.php",
			data:{'MERGE0': jQuery('#f_email').val(),'name': jQuery('#f_name').val(),'MERGE3':jQuery('#cancellation_id').val(),'MERGE4':jQuery('#booking_id').val()}, type:'POST'
		})
	  .done(function( data ) {
			console.log(data);
	  });
	});
	jQuery(document).ready(function(){
		jQuery('.rt-social-buttons').prepend('<a class="item homeRed" href="/"><span class="icon-home" style="font-size: 15px;"></span></a>');
	});
</script>
