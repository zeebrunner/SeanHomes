<?php
	if(!isset($cart)){$cart = "no";};

	require_once JPATH_SITE."/components/com_rsappt_pro3/anet_php_sdk/AuthorizeNet.php"; 

	$sql = 'SELECT * FROM #__sv_apptpro3_authnet_aim_settings;';
	try{
		$database->setQuery($sql);
		$authnet_settings_aim = NULL;
		$authnet_settings_aim = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "authnet_aim_goto", "", "");
		echo JText::_('RS1_SQL_ERROR');
		exit;
	}

	define("AUTHORIZENET_API_LOGIN_ID", $authnet_settings_aim->authnet_aim_api_login_id);
	define("AUTHORIZENET_TRANSACTION_KEY", $authnet_settings_aim->authnet_aim_transaction_key);

    $transaction = new AuthorizeNetAIM();
	if($authnet_settings_aim->authnet_aim_server == "Test"){
		define("AUTHORIZENET_SANDBOX", true);
	    $transaction->setSandbox(true);
	} else {
		define("AUTHORIZENET_SANDBOX", false);
	    $transaction->setSandbox(false);
	}
	if($cart != "Yes"){
		$transaction->setFields(
			array(
			'amount' => $payment_required, 
			'card_num' => $_POST['x_card_num'], 
			'exp_date' => $_POST['x_exp_date'],
			'first_name' => $_POST['x_first_name'],
			'last_name' => $_POST['x_last_name'],
			'address' => $_POST['x_address'],
			'city' => $_POST['x_city'],
			'state' => $_POST['x_state'],
			'country' => $_POST['x_country'],
			'zip' => $_POST['x_zip'],
			'card_code' => $_POST['x_card_code']
			)
		);
	} else {
		$jinput = JFactory::getApplication()->input;
		$xfo =  base64_decode ($jinput->getString('xfo',''));
		$xfo_array = explode("|", $xfo);
		//print_r($xfo_array);
		//echo $payment_required;
		//exit;		
			$transaction->setFields(
				array(
				'amount' => $payment_required, 
				'card_num' => $xfo_array[0], 
				'exp_date' => $xfo_array[1], 
				'first_name' => $xfo_array[3], 
				'last_name' => $xfo_array[4], 
				'address' => $xfo_array[5], 
				'city' => $xfo_array[6], 
				'state' => $xfo_array[7], 
				'country' => $xfo_array[8], 
				'zip' => $xfo_array[9], 
				'card_code' => $xfo_array[2], 
			)
		);		
	}
    $response = $transaction->authorizeAndCapture();
    if ($response->approved) {

	   	include JPATH_COMPONENT.DS."payment_processors".DS."authnet_aim".DS."authnet_aim_process_payment.php";

    } else {
		if($cart == "Yes"){
			logIt("Transaction refused: ".$response->response_reason_text." - Request ID: cart", "authnet_aim_goto", "", "");
		} else {
			// transaction was refused by Authet
			// log it
			logIt("Transaction refused: ".$response->response_reason_text." - Request ID: ".$request_id, "authnet_aim_goto", "", "");
			
			//set'pending' booking to 'deleted'
			$sql = "UPDATE #__sv_apptpro3_requests set payment_processor_used='AuthnetAIM', request_status='deleted', ".
			" admin_comment='Authnet AIM transaction refused: ".$response->response_reason_text."'".
			" WHERE id_requests=".$request_id;
			try{				
				$database->setQuery($sql);
				$database->execute();
			} catch (RuntimeException $e) {
				logIt($e->getMessage(), "authnet_aim_goto", "", "");
			}			
		}
		echo "<p>".JText::_('RS1_ADMIN_AUTHNET_AIM_REFUSED').$response->response_reason_text;
		echo "</p><p>&nbsp;</p>";
    }

	
?>