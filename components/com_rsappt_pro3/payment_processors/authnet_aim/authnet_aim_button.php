<?php 
	if(!isset($isMobile)){$isMobile = "no";};
	if(!isset($isCart)){$isCart = "no";};
	// get authnet_aim settings
	$sql = 'SELECT * FROM #__sv_apptpro3_authnet_aim_settings;';
	try{
		$database->setQuery($sql);
		$authnet_aim_settings = NULL;
		$authnet_aim_settings = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "pay_procs_button", "", "");
		echo JText::_('RS1_SQL_ERROR');
		exit;
	}
	$prefix = "authnet_aim";
	  	if($authnet_aim_settings->authnet_aim_button_url != ""){ 
			$authnet_aim_button_url = $authnet_aim_settings->authnet_aim_button_url;?>
	      		<input type="image" id="btnAuthNet" align="top" src="<?php echo $authnet_aim_button_url ?>" border="0" name="submit_aim" alt="submit this form" onclick="checkout_aim(); return false;"
                <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> style="border:none" >
      	<?php } else { ?>
      	<input type="submit" class="button" id="btnAuthNet" onclick="checkout_aim(); return false;" name="submit_aim" value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT_AUTHNET');?>"
                <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo " disabled ";} ?> />
      	<?php } ?>

<link href="<?php echo JURI::base( true );?>/components/com_rsappt_pro3/payment_processors/authnet_aim/authnet_aim_checkout.css" rel="stylesheet">
<div id="authnet_aim_form" class="svAuthnet_aim_Container<?php echo ($isMobile=='yes'?"_mobile":"")?>">
<?php
   	include JPATH_COMPONENT.DS."payment_processors".DS."authnet_aim".DS."authnet_aim_checkout_form.php";
?>
</div>

<script>
function checkout_aim(){
	<?php if($isCart != "yes"){?>
	result = validateForm();
	if(result.indexOf('<?php echo JText::_('RS1_INPUT_SCRN_VALIDATION_OK');?>')>-1){
		jQuery('#errors').html("");
	<?php } ?>	
		if(jQuery('#grand_total').val() != ""){
			 if(parseFloat(jQuery('#grand_total').val()) > 0){			 
				jQuery('#authnet_aim_form').show();
			 }
		}
	<?php if($isCart != "yes"){?>	
	} else {
		disable_enableSubmitButtons("enable");	
	}
	return false;	
	<?php } ?>	
	
}

</script>
