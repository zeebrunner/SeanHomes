<?php
	// get 2co settings
	$sql = 'SELECT * FROM #__sv_apptpro3__2co_settings;';
	try{
		$database->setQuery($sql);
		$_2co_settings = NULL;
		$_2co_settings = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "pay_procs_button", "", "");
		echo JText::_('RS1_SQL_ERROR');
		exit;
	}

	  	if($_2co_settings->_2co_button_url != ""){ 
			$_2co_button_url = $_2co_settings->_2co_button_url;?>
	      		<input type="image" id="btn2Co" align="top" src="<?php echo $_2co_button_url ?>" border="0" name="submit" alt="submit this form" onclick="<?php echo $submit_function ?>('_2co'); return false;"
                <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo "disabled";} ?> style="border:none" >
      	<?php } else { ?>
      	<input type="submit" class="button" id="btn2Co" onclick="<?php echo $submit_function ?>('_2co'); return false;" name="submit4" value="<?php echo JText::_('RS1_INPUT_SCRN_SUBMIT_2CO');?>"
                <?php if($apptpro_config->requireLogin == "Yes" && $user->guest){ echo " disabled ";} ?> />
      	<?php } ?>

