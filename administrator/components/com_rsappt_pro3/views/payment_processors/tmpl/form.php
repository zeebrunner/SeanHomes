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
				 
	// get config stuff
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_pay_procs_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		

	// get payment processors
	$sql = 'SELECT * FROM #__sv_apptpro3_payment_processors WHERE published = 1 ORDER BY ordering;';
	try{
		$database->setQuery($sql);
		$pay_procs = NULL;
		$pay_procs = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_pay_procs_tmpl_form", "", "");
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
		// get cb profile columns
		try{
			$database->setQuery("SELECT * FROM #__comprofiler_fields WHERE #__comprofiler_fields.table = '#__comprofiler' and (type='text' or type='predefined') ORDER BY name" );
			$cb_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_pay_procs_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
	}

	if(in_array($database->replacePrefix('#__community_fields'), $tables)){
		// get js profile columns
		try{
			$database->setQuery("SELECT * FROM #__community_fields WHERE type!='group' ORDER BY name" );
			$js_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_pay_procs_tmpl_form", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
	}
	
	?>
<div id="testdiv1" style="VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: white; layer-background-color: white; z-index:99999"> </div>
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/calStyles.css" rel="stylesheet">
<link href="<?php echo JURI::root( true );?>/administrator/components/com_rsappt_pro3/abpro_admin.css" rel="stylesheet">
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">

<script language="JavaScript" src="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/CalendarPopup.js"></script>
<script language="JavaScript">
	var now = new Date();
	var cal = new CalendarPopup( <?php echo $div_cal ?>);
	cal.setCssPrefix("TEST");
	cal.setWeekStartDay(<?php echo $apptpro_config->popup_week_start_day ?>);
</script>
<?php echo JText::_('RS1_ADMIN_PAYMENT_PROCESSORS');?>
<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">

  <?php 
  
 	// get data for dropdowns
	$database = JFactory::getDBO();
	try{
		$database->setQuery("SELECT * FROM #__sv_apptpro3_pp_currency ORDER BY description" );
		$currency_rows = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "be_pay_procs_tmpl_form", "", "");
		echo JText::_('RS1_SQL_ERROR').$e->getMessage();
		exit;
	}		
	?>
    
    <ul class="nav nav-tabs">
        <li class="active"><a href="#panel1" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_PAYMENT_PROCESSORS_GENERAL_TAB');?></a></li>
        
	<?php
		$i = 2; 
		foreach($pay_procs as $pay_proc){ ?>
	        <li><a href="#panel<?php echo $i?>" data-toggle="tab"><?php echo JText::_($pay_proc->display_name);?></a></li>              
    <?php 
    		$i++;
		} ?>

<!--        <li><a href="#panel2" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_PAYMENT_PROCESSORS_PAYPAL_TAB');?></a></li>
        <li><a href="#panel3" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_AUTHNET_TAB');?></a></li>
        <li><a href="#panel4" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_2CO_TAB');?></a></li>
-->    </ul>

	<div class="tab-content">
		<div id="panel1" class="tab-pane active">
        <table class="table table-striped" >
        <tr >
          <td width="15%"><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_FEE');?>: </td>
          <td><input type="text" style="width:80px; text-align: center" id="additional_fee" name="additional_fee" size="4" maxsize="5" value="<?php echo $this->detail->additional_fee ?>" /> 
            &nbsp;&nbsp;
            &nbsp;&nbsp;
            <select name="fee_rate" style="width:auto; min-width:100px">
              <option value="Fixed" <?php if($this->detail->fee_rate == "Fixed"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_FEE_FIXED');?></option>
              <option value="Percent" <?php if($this->detail->fee_rate == "Percent"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_FEE_PERCENT');?></option>
            </select>      </td> 
         <td width="50%"><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_FEE_HELP');?></td>         
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_COUPON_ENABLE');?>: </td>
          <td><select name="enable_coupons">
              <option value="Yes" <?php if($this->detail->enable_coupons == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->enable_coupons == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_COUPON_ENABLE_HELP');?></td>
        </tr>
        <tr>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_FORCE_NON_PAY_BUTTON');?>: </td>
          <td><select name="non_pay_booking_button">
              <option value="Yes" <?php if($this->detail->non_pay_booking_button == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->non_pay_booking_button == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
              <option value="DO" <?php if($this->detail->non_pay_booking_button == "DO"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_DISPLAY_ONLY');?></option>
              <option value="DAB" <?php if($this->detail->non_pay_booking_button == "DAB"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_DISPLAY_AND_BLOCK');?></option>
            </select></td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_FORCE_NON_PAY_BUTTON_HELP');?></td>
        </tr>
        </tr>
         <tr>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_PURGE_STALE');?>: </td>
          <td><div style="display: table-cell; padding-left:10px;"><select name="purge_stale_paypal" style="width:100px;">
              <option value="Yes" <?php if($this->detail->purge_stale_paypal == "Yes"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_YES');?></option>
              <option value="No" <?php if($this->detail->purge_stale_paypal == "No"){echo " selected='selected' ";} ?>><?php echo JText::_('RS1_ADMIN_SCRN_NO');?></option>
            </select></div>
            <div style="display: table-cell; padding-left:10px;"><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_PURGE_AFTER');?></div>
            <div style="display: table-cell; padding-left:10px;"><input type="text" style="width:30px; text-align: center" size="3" name="minutes_to_stale" value="<?php echo $this->detail->minutes_to_stale?>" /></div>
            <div style="display: table-cell; padding-left:10px;"><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_PURGE_MINUTES');?></div>
          </td>
          <td><?php echo JText::_('RS1_ADMIN_CONFIG_PAYPAL_PURGE_STALE_HELP');?></td>        
        </tr>
      </table>
        </div>
	<?php
		$i2 = 2; 
		foreach($pay_procs as $pay_proc){ ?>
	        <div id="panel<?php echo $i2?>" class="tab-pane">
	    	    <?php include JPATH_COMPONENT.DS."payment_processors".DS.$pay_proc->prefix.DS.$pay_proc->config_table.".php";?>
            </div>    
    <?php 
    		$i2++;
		} ?>
        
	</div>

  <input type="hidden" name="id_config" value="<?php echo $this->detail->id_config; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="payment_processors" />
  <br />
  <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
</form>
