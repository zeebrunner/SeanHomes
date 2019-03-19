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

?>

<form action="index2.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<table style="border: solid 1px #ECECEC; background-color:#FFF" >	
	<tr>
    <td valign="top" align="center">
        <table width="98%" border="0" cellspacing="0" cellpadding="5" align="center" >
          <tr>
            <td valign="bottom" width="15%"  align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=config_detail"><img src="<?php echo "./components/com_rsappt_pro3/images/configure.jpg"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=config_detail"><?php echo JText::_('RS1_ADMIN_MENU_CONFIGURE');?></a></td>
            <td valign="bottom" width="16%" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=requests"><img src="<?php echo "./components/com_rsappt_pro3/images/bookings.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=requests"><?php echo JText::_('RS1_ADMIN_MENU_APPOINTMENTS');?></a></td>
            <td valign="bottom" width="16%" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=categories"><img src="<?php echo "./components/com_rsappt_pro3/images/pad.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=categories"><?php echo JText::_('RS1_ADMIN_MENU_CATEGORIES');?></a></td>
            <td valign="bottom" width="16%" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=resources"><img src="<?php echo "./components/com_rsappt_pro3/images/resources.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=resources"><?php echo JText::_('RS1_ADMIN_MENU_RESOURCES');?></a></td>
            <td valign="bottom" width="16%" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=services"><img src="<?php echo "./components/com_rsappt_pro3/images/pad.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=services"><?php echo JText::_('RS1_ADMIN_MENU_SERVICES');?></a></td>
            <td valign="bottom" width="16%" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=timeslots"><img src="<?php echo "./components/com_rsappt_pro3/images/timeslots.jpg"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=timeslots"><?php echo JText::_('RS1_ADMIN_MENU_TIMESLOTS');?></a></td>
          </tr>
          <tr>
			<td>&nbsp;</td>
            <td valign="bottom" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=bookoffs"><img src="<?php echo "./components/com_rsappt_pro3/images/bookoffs.jpg"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=bookoffs"><?php echo JText::_('RS1_ADMIN_MENU_BOOKOFFS');?></a></td>
            <td valign="bottom" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=udfs"><img src="<?php echo "./components/com_rsappt_pro3/images/udf.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=udfs"><?php echo JText::_('RS1_ADMIN_MENU_UDFS');?></a></td> 
            <td valign="bottom" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=coupons"><img src="<?php echo "./components/com_rsappt_pro3/images/coupon.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=coupons"><?php echo JText::_('RS1_ADMIN_MENU_COUPONS');?></a></td>
            <td valign="bottom" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=user_credit&gc=gc"><img src="<?php echo "./components/com_rsappt_pro3/images/gc2.png"?>" /></a><br />
            <a href="index.php?option=com_rsappt_pro3&amp;controller=user_credit&gc=gc"><?php echo JText::_('RS1_ADMIN_MENU_GIFT_CERT');?></a></td>
            <td valign="bottom" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=user_credit"><img src="<?php echo "./components/com_rsappt_pro3/images/vault.png"?>" /></a><br />
            <a href="index.php?option=com_rsappt_pro3&amp;controller=user_credit"><?php echo JText::_('RS1_ADMIN_MENU_CREDIT');?></a></td>
          </tr>
          <tr>
			<td>&nbsp;</td>
            <td valign="middle" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=seat_types"><img src="<?php echo "./components/com_rsappt_pro3/images/seats.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=seat_types"><?php echo JText::_('RS1_ADMIN_MENU_SEATS');?></a></td>
            <td valign="middle" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=extras"><img src="<?php echo "./components/com_rsappt_pro3/images/extras.png"?>" /></a><br />
            <a href="index.php?option=com_rsappt_pro3&amp;controller=extras"><?php echo JText::_('RS1_ADMIN_MENU_EXTRAS');?></a></td>
            <td valign="middle" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=payment_processors"><img src="<?php echo "./components/com_rsappt_pro3/images/pay_proc.png"?>" /></a><br />
            <a href="index.php?option=com_rsappt_pro3&amp;controller=payment_processors"><?php echo JText::_('RS1_ADMIN_MENU_PAYPROC');?></a></td>
            <td valign="middle" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=payment_transactions"><img src="<?php echo "./components/com_rsappt_pro3/images/pay_proc.png"?>" /></a><br />
            <a href="index.php?option=com_rsappt_pro3&amp;controller=payment_transactions"><?php echo JText::_('RS1_ADMIN_PAYMENT_TRANSACTIONS');?></a></td>
            <td valign="middle" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=sms_processors"><img src="<?php echo "./components/com_rsappt_pro3/images/sms_proc.png"?>" /></a><br />
			<a href="index.php?option=com_rsappt_pro3&amp;controller=sms_processors"><?php echo JText::_('RS1_ADMIN_MENU_SMSPROC');?></a></td>          
          </tr>
          <tr>
			<td>&nbsp;</td>
            <td valign="top" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=edit_files"><img src="<?php echo "./components/com_rsappt_pro3/images/log.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=edit_files"><?php echo JText::_('RS1_ADMIN_MENU_EDITFILES');?></a></td>
            <td valign="top" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=backup_restore"><img src="<?php echo "./components/com_rsappt_pro3/images/backup.png"?>" /></a><br />
              <a href="index.php?option=com_rsappt_pro3&amp;controller=backup_restore"><?php echo JText::_('RS1_ADMIN_MENU_BACKUP');?></a></td>
            <td valign="top" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=mail"><img src="<?php echo "./components/com_rsappt_pro3/images/email.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=mail"><?php echo JText::_('RS1_ADMIN_MENU_MAIL');?></a></td>
            <td valign="top" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=errorlog"><img src="<?php echo "./components/com_rsappt_pro3/images/log.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=errorlog"><?php echo JText::_('RS1_ADMIN_MENU_ERRLOG');?></a></td>
            <td valign="top" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=reminderlog"><img src="<?php echo "./components/com_rsappt_pro3/images/log.png"?>" /></a><br /><a href="index.php?option=com_rsappt_pro3&amp;controller=reminderlog"><?php echo JText::_('RS1_ADMIN_MENU_REMLOG');?></a></td>
<!--            <td valign="bottom" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=activitylog"><img src="<?php echo "./components/com_rsappt_pro3/images/log.png"?>" /></a><br />
            <a href="index.php?option=com_rsappt_pro3&amp;controller=activitylog"><?php echo JText::_('RS1_ADMIN_MENU_ACTIVITYLOG');?></a></td>-->
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="center" style="border: solid 1px #ECECEC"><a href="index.php?option=com_rsappt_pro3&amp;controller=rate_overrides"><?php echo JText::_('RS1_ADMIN_MENU_RATE_OVERRIDES');?></a></td>
			<td align="center" style="border: solid 1px #ECECEC"><a href="index.php?option=com_rsappt_pro3&amp;controller=rate_adjustments"><?php echo JText::_('RS1_ADMIN_MENU_RATE_ADJUSTMENTS');?></a></td>
			<td align="center" style="border: solid 1px #ECECEC"><a href="index.php?option=com_rsappt_pro3&amp;controller=seat_adjustments"><?php echo JText::_('RS1_ADMIN_MENU_SEAT_ADJUSTMENTS');?></a></td>
			<td align="center" style="border: solid 1px #ECECEC"><a href="index.php?option=com_rsappt_pro3&amp;controller=email_marketing"><?php echo JText::_('RS1_ADMIN_MENU_EMAIL_MARKETING');?></a></td>
            <td valign="bottom" style="border: solid 1px #ECECEC" align="center"><a href="index.php?option=com_rsappt_pro3&amp;controller=about"><img src="<?php echo "./components/com_rsappt_pro3/images/about.png"?>" /></a><br />
            <a href="index.php?option=com_rsappt_pro3&amp;controller=about"><?php echo JText::_('RS1_ADMIN_MENU_ABOUT');?></a></td>
          </tr>
          <tr>
  			<td>&nbsp;</td>
            <td valign="middle" style="border: solid 1px #ECECEC" align="center" colspan="5"><?php echo JText::_('RS1_ADMIN_CONTROL_FOOTER');?></td>
          </tr>
        </table>	  
      <br /></td>
    </tr>
</table>
</form>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank"><input name="cmd" type="hidden" value="_s-xclick" />
<p style="text-align: center;"><input name="hosted_button_id" type="hidden" value="ANLMNFXKW3AXC" /> <input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" type="image" /> <img style="display: block; margin-left: auto; margin-right: auto;" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /></p>
</form>
<hr>
  <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
