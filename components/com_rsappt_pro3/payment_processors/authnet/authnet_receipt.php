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

?>

<HTML>
  <HEAD>
    <title>Payment Receipt</title>

    <link href="<?php echo JURI::base()."components/com_rsappt_pro3/payment_processors/authnet/authnet_receipt.css"?>" rel="stylesheet">
    <link href="<?php echo JURI::base()."components/com_rsappt_pro3/sv_apptpro.css"?>" rel="stylesheet">

  </HEAD>
  <BODY>
 
<table class="sv_receipt_table" width="500"  align="center" >
  <tr>
    <td colspan="2" align="center"><DIV id="divClickAway"><A HREF="<?php echo JURI::base().'index.php?option=com_rsappt_pro3&view='.$frompage.'&task=authnet_return&req_id='.$x_invoice_num?>">Return to  site</A></DIV></td>	
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><h3>Thank you for your order</h3></td>
  </tr>
  <tr>
    <td colspan="2"><hr>You may print this receipt page for your records.
	</td>
  </tr>
    <td colspan="2"><hr><h4>Booking Information</h4></td>
  <tr>
    <td>Name:</td>
    <td><?php echo $x_first_name." ".$x_last_name ?></td>
  </tr>
  <tr>
    <td>Appointment:</td>
    <td><?php echo $x_description ?></td>
  </tr>
  <tr>
    <td>Booking ID:</td>
    <td><?php echo $x_invoice_num ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  </tr>
    <td colspan="2"><hr><h4>Payment Information</h4></td>
  <tr>
  <tr>
    <td>Name:</td>
    <td><?php echo $x_first_name  ?>&nbsp;<?php echo $x_last_name  ?></td>
  </tr>
  <tr>
    <td>Method:</td>
    <td><?php echo $x_card_type?>&nbsp;<?php echo $x_account_number?></td>
  </tr>
  <tr>
  <tr>
    <td>Authorization Code:</td>
    <td><?php echo $x_auth_code ?></td>
  </tr>
  <tr>
    <td>Amount:</td>
    <td><?php echo JText::_('RS1_INPUT_SCRN_CURRENCY_SYMBOL');?><?php echo $x_amount ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
 
  </BODY>
</HTML>
<?php exit; ?>

