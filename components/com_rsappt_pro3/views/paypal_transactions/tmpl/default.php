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


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'ordering');

// get config stuff
$database = JFactory::getDBO();
$sql = 'SELECT * FROM #__sv_apptpro3_config';
try{
	$database->setQuery($sql);
	$apptpro_config = NULL;
	$apptpro_config = $database -> loadObject();
} catch (RuntimeException $e) {
	logIt($e->getMessage(), "pp_trans_tmpl_default", "", "");
	echo JText::_('RS1_SQL_ERROR');
	return false;
}		


?>

<script language="javascript" type="text/javascript">
	function setReqID(id){
		document.getElementById("xid").value = id;
		Joomla.submitform('get_booking');
		return false;
	}

function Joomla.submitform(pressbutton){
var form = document.adminForm;
   if (pressbutton)
    {form.task.value=pressbutton;}
    
	if ((pressbutton=='add')||(pressbutton=='edit')||(pressbutton=='publish')||(pressbutton=='unpublish')
	 ||(pressbutton=='orderdown')||(pressbutton=='orderup')||(pressbutton=='saveorder')||(pressbutton=='remove') )
	 {
	  form.controller.value="paypal_transactions_detail";
	 } 
	try {
		form.onsubmit();
		}
	catch(e){}
	
	form.submit();
}

</script>
<style type="text/css">
<!--
.icon-48-paypal { background-image: url(./components/com_rsappt_pro3/images/pay.png); }
-->
}
</style>
<table class="adminheading">
  <tr>
    <th><?php echo JText::_('RS1_ADMIN_PAYPAL_LIST');?></th>
  </tr>
</table>
<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" >
  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<thead>
    <tr>
      <th width="3%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" /></th>
      <th class="sv_title" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_PP_TXN_COL_HEAD'), 'txnid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="center"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_REQ_ID_COL_HEAD'), 'custom', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_BUYER_COL_HEAD'), 'lastname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_EMAIL_COL_HEAD'), 'buyer_email', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_PAY_DATE_COL_HEAD'), 'paymentdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="left"><?php echo JHTML::_( 'grid.sort',  JText::_('RS1_ADMIN_PAYPAL_PAY_STATUS_COL_HEAD'), 'paymentstatus', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_GROSS_COL_HEAD'), 'mc_gross', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_FEE_COL_HEAD'), 'mc_fee', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_TAX_COL_HEAD'), 'tax', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
      <th class="sv_title" align="left"><?php echo JHTML::_( 'grid.sort', JText::_('RS1_ADMIN_PAYPAL_TIMESTAMP_COL_HEAD'), 'stamp', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
    </tr>
    </thead>
    <?php
	$k = 0;
	for($i=0; $i < count( $this->items ); $i++) {
	$row = $this->items[$i];
 	$published 	= JHTML::_('grid.published', $row, $i );
	$link 	= JRoute::_( 'index.php?option=com_rsappt_pro3&controller=paypal_transactions_detail&task=edit&cid[]='. $row->id_paypal_transactions );
	$link_to_request = JRoute::_( 'index.php?option=com_rsappt_pro3&controller=requests_detail&task=edit&cid[]='. $row->custom."&frompage=PayPal" );
	$checked 	= JHTML::_('grid.checkedout', $row, $i, 'id_paypal_transactions');
  ?>
    <tr class="<?php echo "row$k"; ?>">
      <td align="center"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id_paypal_transactions; ?>" onclick="isChecked(this.checked);" /></td>
      <td align="center"><a href=<?php echo $link; ?>><?php echo $row->txnid; ?></a></td>
	  <?php if($row->custom != "cart"){?>
	      <td align="center"><a href=<?php echo $link_to_request; ?>><?php echo $row->custom; ?></a>&nbsp;</td>
      <?php } else { ?>
	      <td align="center"><?php echo $row->custom; ?></td>
      <?php } ?>    
      <td align="left"><?php echo $row->lastname; ?>, <?php echo $row->firstname; ?>&nbsp;</td>
      <td align="left"><?php echo $row->buyer_email; ?>&nbsp;</td>
      <td align="center"><?php echo $row->paymentdate; ?>&nbsp;</td>
      <td align="center"><?php echo $row->paymentstatus; ?>&nbsp;</td>
      <td align="right"><?php echo $row->mc_gross; ?>&nbsp;</td>
      <td align="right"><?php echo $row->mc_fee; ?>&nbsp;</td>
      <td align="right"><?php echo $row->tax; ?>&nbsp;</td>
      <td><?php echo $row->stamp; ?></td>
      <?php $k = 1 - $k; ?>
    </tr>
    <?php } 

	?>
	<tfoot>
   	<td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
    </tfoot>
  </table>
  <input type="hidden" name="controller" value="paypal_transactions" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
  <input type="hidden" name="hidemainmenu" value="0" />  
  <input type="hidden" name="xid" id="xid" value="" />
  <br />
   <?php if($apptpro_config->hide_logo == 'No'){ ?>
	  <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
   <?php } ?>

</form>
