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

	$database = JFactory::getDBO(); 
	// get config stuff
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "cancel_booking_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}		

?>

<form name="frmRequest" action="<?php echo "index.php?option=com_rsappt_pro3&view=cancel_link" ?>" method="post">
<div id="sv_apptpro_request_gad">
<?php $document = JFactory::getDocument();
$document->addStyleSheet( JURI::base( true )."/components/com_rsappt_pro3/sv_apptpro.css");
?>
<script language="JavaScript" src="<?php echo $this->baseurl;?>/components/com_rsappt_pro3/script.js"></script>
<script language="JavaScript">

function doCancelBooking(){
	var cc = document.getElementById("cancellation_id").value;
	if(cc == ""){
		alert("<?php echo JText::_('RS1_CANCEL_CODE_PROMPT');?>");
		return false;
	}
	document.frmRequest.action = "<?php echo JURI::base()?>index.php?option=com_rsappt_pro3&view=cancel_link&cc=" + cc;
	document.frmRequest.submit();		
	
}
</script>

  
	<br/>	
    <p>
    <?php echo JText::_('RS1_CANCEL_PAGE_TEXT');?>
    </p>
    <table border="0" cellpadding="4" >
    <tr> <td>
    <?php echo JText::_('RS1_CANCEL_CODE_PROMPT');?> <input type="text" size="50" name="cancellation_id" id="cancellation_id" />
    </td>
    </tr>
	<tr><td>&nbsp;</td></tr>
    <tr >
      <td colspan="2" valign="top"> 
      <input type="button" class="button" onclick="doCancelBooking()"  value="<?php echo JText::_('RS1_CANCEL_CODE_NEXT');?>"/></td>
    </tr>
    </table>
    <div class="sv_apptpro_errors" style="padding-top:20px; font-size:14px">
    <label id="cancel_results" />
	</div>
    <input type="hidden" id="wait_text" value="<?php echo JText::_('RS1_INPUT_SCRN_PLEASE_WAIT');?>" />
   
  <br />
      <?php if($apptpro_config->hide_logo == 'No'){ ?>
        <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
      <?php } ?>
      <?php echo JHTML::_( 'form.token' ); ?>
</div>
</form>
