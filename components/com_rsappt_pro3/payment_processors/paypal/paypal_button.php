<?php 
	// get paypal settings
	$sql = 'SELECT * FROM #__sv_apptpro3_paypal_settings;';
	try{
		$database->setQuery($sql);
		$paypal_settings = NULL;
		$paypal_settings = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "pay_procs_button", "", "");
		echo JText::_('RS1_SQL_ERROR');
		exit;
	}
	
  	if($paypal_settings->paypal_button_url != ""){ 
        	$lang = JFactory::getLanguage();
			$paypal_button_url = str_replace("en_US", str_replace("-", "_", $lang->getTag()), $paypal_settings->paypal_button_url);?>
	      		<input type="image" id="btnPayPal" src="<?php echo $paypal_button_url ?>" name="submit" alt="submit this form" onclick="<?php echo $submit_function ?>('paypal'); return false;"
                <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> >
      	<?php } else { ?>
      	<input type="submit" class="button" id="btnPayPal" onclick="<?php echo $submit_function ?>('paypal'); return false;" name="submit2" value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT_PAYPAL');?>"
                <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo " disabled ";} ?> />
      	<?php } ?>
