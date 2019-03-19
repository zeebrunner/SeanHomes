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

?>

<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">
<fieldset class="adminform">
  <table border="0" cellpadding="2" cellspacing="0">
    <tr class="admin_detail_row0">
      <td colspan="2"><u><?php echo JText::_('RS1_ADMIN_SCRN_GOOGLE_WALLET_TXN_DETAIL_INTRO');?></u></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_GOOGLE_WALLET_TXN_ORDER_ID');?></td>
      <td><?php echo $this->detail->gw_order_id; ?>&nbsp;</td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_GOOGLE_WALLET_TXN_DETAIL_REQID');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="10" maxsize="10" name="custom" value="<?php echo $this->detail->request_id; ?>" /></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_GOOGLE_WALLET_TXN_DETAIL_ITEM');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="100" name="firstname" value="<?php echo stripslashes($this->detail->gw_item); ?>" /></td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_GOOGLE_WALLET_TXN_DETAIL_DESC');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="100" name="lastname" value="<?php echo stripslashes($this->detail->gw_description); ?>" /></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_GOOGLE_WALLET_TXN_DETAIL_PRICE');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="50" maxsize="100" name="email" value="<?php echo $this->detail->gw_price; ?>" /></td>
    </tr>
</table>
</fieldset>
  <input type="hidden" name="id_google_wallet_transactions" value="<?php echo $this->detail->id_google_wallet_transactions; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="google_wallet_transactions_detail" />
  <input type="hidden" name="frompage" value="<?php echo $this->frompage; ?>" />
  <br />
  <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
</form>
