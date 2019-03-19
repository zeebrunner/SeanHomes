<?php 
		$display_picker_dategoog = $this->filter_goog_startdate;	
		if($display_picker_dategoog != ""){
			switch ($apptpro_config->date_picker_format) {
				case "yy-mm-dd":
					$display_picker_dategoog = date("Y-m-d", strtotime($this->filter_goog_startdate));
					break;
				case "dd-mm-yy":
					$display_picker_dategoog = date("d-m-Y", strtotime($this->filter_goog_startdate));
					break;
				case "mm-dd-yy":
					$display_picker_dategoog = date("m-d-Y", strtotime($this->filter_goog_startdate));
					break;
				default:	
					$display_picker_dategoog = date("Y-m-d", strtotime($this->filter_goog_startdate));
					break;
			}
		}
	
		$display_picker_date2goog = $this->filter_goog_enddate;
		if($display_picker_date2goog != ""){
			switch ($apptpro_config->date_picker_format) {
				case "yy-mm-dd":
					$display_picker_date2goog = date("Y-m-d", strtotime($this->filter_goog_enddate));
					break;
				case "dd-mm-yy":
					$display_picker_date2goog = date("d-m-Y", strtotime($this->filter_goog_enddate));
					break;
				case "mm-dd-yy":
					$display_picker_date2goog = date("m-d-Y", strtotime($this->filter_goog_enddate));
					break;
				default:	
					$display_picker_date2goog = date("Y-m-d", strtotime($this->filter_goog_enddate));
					break;
			}
		}
?>
<script>
	jQuery(function() {
  		jQuery( "#display_picker_dategoog" ).datepicker({
			showOn: "button",
	 		dateFormat: "<?php echo $apptpro_config->date_picker_format;?>",
			firstDay: <?php echo $apptpro_config->popup_week_start_day ?>, 
			buttonImage: "<?php echo JURI::base( true );?>/components/com_rsappt_pro3/icon_cal_gr.png",
			buttonImageOnly: true,
			buttonText: "<?php echo JText::_('RS1_INPUT_SCRN_DATE_PROMPT');?>",
			altField: "#googstartdateFilter",
			altFormat: "yy-mm-dd" //DO NOT CHANGE 			
    	});
  		jQuery( "#display_picker_date2goog" ).datepicker({
			showOn: "button",
	 		dateFormat: "<?php echo $apptpro_config->date_picker_format;?>",
			firstDay: <?php echo $apptpro_config->popup_week_start_day ?>, 
			buttonImage: "<?php echo JURI::base( true );?>/components/com_rsappt_pro3/icon_cal_gr.png",
			buttonImageOnly: true,
			buttonText: "<?php echo JText::_('RS1_INPUT_SCRN_DATE_PROMPT');?>",
			altField: "#googenddateFilter",
			altFormat: "yy-mm-dd" //DO NOT CHANGE 			
    	});
	});
</script>
		<?php echo JText::_('RS1_ADMIN_SCRN_TAB_GOOGLE_WALLET');?>
	  <table class="table table-striped" width="100%" >
        <tr class="fe_admin_header">
          <td>
          <div class="control-label"><?php echo JText::_('RS1_ADMIN_SCRN_STAMP_DATEFILTER');?></div>
          <div class="controls">
           <input readonly="readonly" name="googstartdateFilter" id="googstartdateFilter" type="hidden" 
              class="sv_date_box" size="10" maxlength="10" value="<?php echo $this->filter_goog_startdate; ?>" />
    
            <input type="text" readonly="readonly" id="display_picker_dategoog" name="display_picker_dategoog" class="sv_date_box" size="10" maxlength="10" 
                value="<?php echo $display_picker_dategoog ?>" onchange="selectGOOGStartDate(); return false;">          
            <br/>           
           <input readonly="readonly" name="googenddateFilter" id="googenddateFilter" type="hidden" 
              class="sv_date_box" size="10" maxlength="10" value="<?php echo $this->filter_goog_enddate; ?>" />
    
            <input type="text" readonly="readonly" id="display_picker_date2goog" name="display_picker_date2goog" class="sv_date_box" size="10" maxlength="10" 
                value="<?php echo $display_picker_date2goog?>" onchange="selectGOOGEndDate(); return false;">
        </div>
        </td>
        </tr>
        <tr>
        <td>
        	<table width="100%">
            <tr class="fe_admin_header">
              <!--<th  class="svtitle" width="5%" align="center"><input type="checkbox" name="toggle6" value="" onclick="checkAll2(<?php echo count($this->items_pp); ?>, 'pp_cb',6);" /></th>-->
              <th class="svtitle" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_GOOGLE_WALLET_ORDID_COL_HEAD'), 'gw_order_id', $this->lists['order_Dir_goog'], $this->lists['order_goog'], "goog_" ); ?></th>
              <!--<th class="svtitle" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_GOOGLE_WALLET_REQ_ID_COL_HEAD'), 'request_id', $this->lists['order_Dir_goog'], $this->lists['order_goog'], "goog_" ); ?></th>-->
              <!--<th class="svtitle" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_GOOGLE_WALLET_ITEM_COL_HEAD'), 'gw_item', $this->lists['order_Dir_goog'], $this->lists['order_goog'], "goog_" ); ?></th>-->
              <!--<th class="svtitle" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_GOOGLE_WALLET_ITEM_DESC_COL_HEAD'), 'gw_item_desc', $this->lists['order_Dir_goog'], $this->lists['order_goog'], "goog_" ); ?></th>-->
              <th class="svtitle" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_GOOGLE_WALLET_PRICE_COL_HEAD'), 'gw_price', $this->lists['order_Dir_goog'], $this->lists['order_goog'], "goog_" ); ?></th>
              <th class="svtitle" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_SCRN_TIMESTAMP_COL_HEAD'), 'stamp', $this->lists['order_Dir_goog'], $this->lists['order_goog'], "goog_" ); ?></th>
            </tr>
			<?php
            $k = 0;
            for($i=0; $i < count( $this->items_goog ); $i++) {
            $goog_row = $this->items_goog[$i];
            $link = JRoute::_( 'index.php?option=com_rsappt_pro3&controller=admin_detail&task=google_wallet_transactions_detail&cid='. $goog_row->id_google_wallet_transactions.'&frompage=advadmin&tab='.$tab);
           ?>
            <tr class="<?php echo "row$k"; ?>">
              <!--<td width="5%" align="center"><input type="checkbox" id="an_cb<?php echo $i;?>" name="cid_an[]" value="<?php echo $goog_row->id_google_wallet_transactions; ?>" onclick="Joomla.isChecked(this.checked);" /></td>-->
              <td align="center"><a href="<?php echo $link; ?>"><u>...<?php echo stripslashes(substr($goog_row->gw_order_id,strlen($goog_row->gw_order_id-10),10)); ?></u></a></td>
              <?php if(strpos($goog_row->request_id, "cart|") === false ){?>
                <!--<td align="center"><?php echo $goog_row->request_id; ?>&nbsp;</td>-->
              <?php } else {?>
                <!--<td align="center"><?php echo "cart"; ?></td>-->
              <?php } ?>  
              <!--<td align="left"><?php echo stripslashes($goog_row->gw_item); ?>&nbsp;</td>-->
              <!--<td align="left"><?php echo stripslashes($goog_row->gw_description); ?>&nbsp;</td>-->
              <td align="left"><?php echo $goog_row->gw_price; ?>&nbsp;</td>
              <td align="center"><?php echo $goog_row->stamp; ?>&nbsp;</td>
              <?php $k = 1 - $k; ?>
            </tr>
            <?php } 
    ?>	
    	</table>
	  </td>
      </tr>
      </table>

	  <input type="hidden" name="goog_filter_order" value="<?php echo $this->lists['order_goog'];?>" />
  	  <input type="hidden" name="goog_filter_order_Dir" value ="<?php echo $this->lists['order_Dir_goog'] ?>" />
  	  <input type="hidden" name="google_wallet_tab" id="google_wallet_tab"  value ="<?php echo $tab ?>" />
       
		
		