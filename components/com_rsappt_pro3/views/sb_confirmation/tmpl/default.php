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

	$jinput = JFactory::getApplication()->input;
	$req_id = $jinput->getString( 'req_id', '' );
	$itemid = $jinput->getString( 'Itemid', '' );
	$option = $jinput->getString( 'option', '' );
	$cc = $jinput->getString( 'cc', '' );
	$which_message = $jinput->getString( 'which_message', 'confirmation' );

	include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );

	if($cc == "cart"){
		// used cart confirmation stored in session
		$session = JFactory::getSession();
		$message = $session->get('confirmation_message');
	} else {
		$message = buildMessage($req_id, $which_message, "No", $cc, "Yes");
	}
	
	// get config stuff
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "sb_confitmation_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}		
//	if(!class_exists('Mobile_Detect')) {
//		require_once JPATH_SITE.DS."components".DS."com_rsappt_pro3".DS."Mobile_Detect.php";
//	}
//	$detect = new Mobile_Detect();
	$appWeb      = new JApplicationWeb;
	
	echo $message;

?>
	<?php if(!$appWeb->client->mobile){ ?>
	<p>
    <input type="button" value="<?php echo JText::_('RS1_PRINT_THIS_PAGE');?>" onclick="window.print();">
    </p>
	<?php } ?>

  <input type="hidden" name="option" value="<?php echo $option; ?>" />
  <input type="hidden" name="controller" value="booking_screen_simple" />
  <input type="hidden" name="task" id="task" value="" />
  <input type="hidden" name="frompage_item" value="<?php echo $itemid ?>" />
  <br />
      <?php if($apptpro_config->hide_logo == 'No'){ ?>
        <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
      <?php } ?>
</form>
