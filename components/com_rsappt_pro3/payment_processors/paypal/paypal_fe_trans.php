<?php 
		$display_picker_datepp = $this->filter_pp_startdate;	
		if($display_picker_datepp != ""){
			switch ($apptpro_config->date_picker_format) {
				case "yy-mm-dd":
					$display_picker_datepp = date("Y-m-d", strtotime($this->filter_pp_startdate));
					break;
				case "dd-mm-yy":
					$display_picker_datepp = date("d-m-Y", strtotime($this->filter_pp_startdate));
					break;
				case "mm-dd-yy":
					$display_picker_datepp = date("m-d-Y", strtotime($this->filter_pp_startdate));
					break;
				default:	
					$display_picker_datepp = date("Y-m-d", strtotime($this->filter_pp_startdate));
					break;
			}
		}
	
		$display_picker_date2pp = $this->filter_pp_enddate;
		if($display_picker_date2pp != ""){
			switch ($apptpro_config->date_picker_format) {
				case "yy-mm-dd":
					$display_picker_date2pp = date("Y-m-d", strtotime($this->filter_pp_enddate));
					break;
				case "dd-mm-yy":
					$display_picker_date2pp = date("d-m-Y", strtotime($this->filter_pp_enddate));
					break;
				case "mm-dd-yy":
					$display_picker_date2pp = date("m-d-Y", strtotime($this->filter_pp_enddate));
					break;
				default:	
					$display_picker_date2pp = date("Y-m-d", strtotime($this->filter_pp_enddate));
					break;
			}
		}
?>
<script>
	jQuery(function() {
  		jQuery( "#display_picker_datepp" ).datepicker({
			showOn: "button",
	 		dateFormat: "<?php echo $apptpro_config->date_picker_format;?>",
			firstDay: <?php echo $apptpro_config->popup_week_start_day ?>, 
			buttonImage: "<?php echo JURI::base( true );?>/components/com_rsappt_pro3/icon_cal_gr.png",
			buttonImageOnly: true,
			buttonText: "<?php echo JText::_('RS1_INPUT_SCRN_DATE_PROMPT');?>",
			altField: "#ppstartdateFilter",
			altFormat: "yy-mm-dd" //DO NOT CHANGE 			
    	});
  		jQuery( "#display_picker_date2pp" ).datepicker({
			showOn: "button",
	 		dateFormat: "<?php echo $apptpro_config->date_picker_format;?>",
			firstDay: <?php echo $apptpro_config->popup_week_start_day ?>, 
			buttonImage: "<?php echo JURI::base( true );?>/components/com_rsappt_pro3/icon_cal_gr.png",
			buttonImageOnly: true,
			buttonText: "<?php echo JText::_('RS1_INPUT_SCRN_DATE_PROMPT');?>",
			altField: "#ppenddateFilter",
			altFormat: "yy-mm-dd" //DO NOT CHANGE 			
    	});
	});
</script>
        <table style="border-bottom:1px solid #666666;" width="100%">
            <tr>
              <th align="left" ><?php echo JText::_('RS1_ADMIN_SCRN_TAB_PAYPAL_FULL');?><br /></th>
                <th align="right"></th> 
          </tr>
        </table>
        <table cellpadding="4" cellspacing="0" border="0" class="adminlist" width="100%">
		<thead>
        <tr class="fe_admin_header">
          <td style="font-size:11px; text-align:right" colspan="7">
            <?php echo JText::_('RS1_ADMIN_SCRN_STAMP_DATEFILTER');?>&nbsp;
            
           <input readonly="readonly" name="ppstartdateFilter" id="ppstartdateFilter" type="hidden" 
              class="sv_date_box" size="10" maxlength="10" value="<?php echo $this->filter_pp_startdate; ?>" />
    
            <input type="text" readonly="readonly" id="display_picker_datepp" name="display_picker_datepp" class="sv_date_box" size="10" maxlength="10" 
                value="<?php echo $display_picker_datepp ?>" onchange="selectPPStartDate(); return false;">          
            &nbsp;           
           <input readonly="readonly" name="ppenddateFilter" id="ppenddateFilter" type="hidden" 
              class="sv_date_box" size="10" maxlength="10" value="<?php echo $this->filter_pp_enddate; ?>" />
    
            <input type="text" readonly="readonly" id="display_picker_date2pp" name="display_picker_date2pp" class="sv_date_box" size="10" maxlength="10" 
                value="<?php echo $display_picker_date2pp?>" onchange="selectPPEndDate(); return false;">
                        
            <!--<a href="#" onclick="ppcleardate();"><?php echo JText::_('RS1_ADMIN_SCRN_DATEFILTER_CLEAR');?></a>&nbsp;&nbsp;-->
          </td>
        </tr>
        <tr class="fe_admin_header">
          <!--<th  class="svtitle" width="5%" align="center"><input type="checkbox" name="toggle6" value="" onclick="checkAll2(<?php echo count($this->items_pp); ?>, 'pp_cb',6);" /></th>-->
          <th class="svtitle" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_SCRN_PP_TXN_COL_HEAD'), 'txnid', $this->lists['order_Dir_pp'], $this->lists['order_pp'], "pp_" ); ?></th>
          <th class="svtitle" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_SCRN_REQ_ID_COL_HEAD'), 'custom', $this->lists['order_Dir_pp'], $this->lists['order_pp'], "pp_" ); ?></th>
          <th class="svtitle" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_SCRN_BUYER_COL_HEAD'), 'lastname', $this->lists['order_Dir_pp'], $this->lists['order_pp'], "pp_" ); ?></th>
          <th class="svtitle" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_SCRN_PAY_DATE_COL_HEAD'), 'paymentdate', $this->lists['order_Dir_pp'], $this->lists['order_pp'], "pp_" ); ?></th>
          <th class="svtitle" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_SCRN_PAY_STATUS_COL_HEAD'), 'paymentstatus', $this->lists['order_Dir_pp'], $this->lists['order_pp'], "pp_" ); ?></th>
          <th class="svtitle" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_SCRN_TIMESTAMP_COL_HEAD'), 'stamp', $this->lists['order_Dir_pp'], $this->lists['order_pp'], "pp_" ); ?></th>
        </tr>
      </thead>
        <?php
        $k = 0;
        for($i=0; $i < count( $this->items_pp ); $i++) {
        $pp_row = $this->items_pp[$i];
		$link = JRoute::_( 'index.php?option=com_rsappt_pro3&controller=admin_detail&task=paypal_transactions_detail&cid='. $pp_row->id_paypal_transactions.'&frompage=advadmin&tab='.$tab);
       ?>
        <tr class="<?php echo "row$k"; ?>">
          <!--<td width="5%" align="center"><input type="checkbox" id="pp_cb<?php echo $i;?>" name="cid_pp[]" value="<?php echo $pp_row->id_paypal_transactions; ?>" onclick="Joomla.isChecked(this.checked);" /></td>-->
          <td width="5%" align="center"><a href="<?php echo $link; ?>"><u><?php echo stripslashes($pp_row->txnid); ?></u></a></td>
          <td width="20%" align="center"><?php echo $pp_row->custom; ?>&nbsp;</td>
          <td width="20%" align="left"><?php echo stripslashes($pp_row->lastname.", ".$pp_row->firstname); ?>&nbsp;</td>
          <td width="20%" align="left"><?php echo $pp_row->paymentdate; ?>&nbsp;</td>
          <td width="20%" align="center"><?php echo $pp_row->paymentstatus; ?>&nbsp;</td> <!-- from paypal, not ABPro status, cannot use translated_status -->
          <td width="20%" align="center"><?php echo $pp_row->stamp; ?>&nbsp;</td>
          <?php $k = 1 - $k; ?>
        </tr>
        <?php } 
    
    ?>	
      </table>

	  <input type="hidden" name="pp_filter_order" value="<?php echo $this->lists['order_pp'];?>" />
  	  <input type="hidden" name="pp_filter_order_Dir" value ="<?php echo $this->lists['order_Dir_pp'] ?>" />
  	  <input type="hidden" name="paypal_tab" id="paypal_tab"  value ="<?php echo $tab ?>" />
    
   
		
		