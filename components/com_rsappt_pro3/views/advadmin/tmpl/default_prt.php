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
 
	jimport( 'joomla.application.helper' );
	jimport('joomla.filter.output');
	$jinput = JFactory::getApplication()->input;

	$itemid = $jinput->getString( 'Itemid', '' );
	$option = $jinput->getString( 'option', '' );

	$filter="";
	$session = &JFactory::getSession();
	
	$filter = $this->filter_request_status;
	$resourceFilter = $this->filter_request_resource;
	$startdateFilter = $this->filter_startdate;

	if($session->get("current_tab") != "" ){
		$current_tab = $session->get("current_tab");
		$session->set("current_tab", "");
	} else {
		$current_tab = $jinput->getString( 'current_tab', '0' );
	}

	include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );
	
	$user = JFactory::getUser();
	
	$ordering = ($this->lists['order'] == 'ordering');
	 
	if($user->guest){
		echo "<font color='red'>".JText::_('RS1_ADMIN_SCRN_NO_LOGIN')."</font>";
		$showform = false;
	} else{
		$showform = true;

		$database = JFactory::getDBO();
		
		// check to see id user is an admin		
		$sql = "SELECT count(*) as count FROM #__sv_apptpro3_resources WHERE ".
			"resource_admins LIKE '%|".$user->id."|%';";
		try{	
			$database->setQuery($sql);
			$check = NULL;
			$check = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "advadmin_tmpl_default_prt", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		

		if($check->count == 0){
			echo "<font color='red'>".JText::_('RS1_ADMIN_SCRN_NOT_ADMIN')."</font>";
		}	
		
		// get resources
		$sql = "SELECT * FROM #__sv_apptpro3_resources ".
		"WHERE resource_admins LIKE '%|".$user->id."|%' and published=1 ".
		"ORDER BY ordering;";
		try{
			$database->setQuery($sql);
			$res_rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "advadmin_tmpl_default_prt", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
		
		// get statuses
		$sql = "SELECT * FROM #__sv_apptpro3_status ORDER BY ordering ";
		try{
			$database->setQuery($sql);
			$statuses = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "advadmin_tmpl_default_prt", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
	
		$database = JFactory::getDBO(); 
		$sql = 'SELECT * FROM #__sv_apptpro3_config';
		try{
			$database->setQuery($sql);
			$apptpro_config = NULL;
			$apptpro_config = $database -> loadObject();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "advadmin_tmpl_default_prt", "", "");
			echo JText::_('RS1_SQL_ERROR');
			return false;
		}		
	
		$div_cal = "";
		if($apptpro_config->use_div_calendar == "Yes"){
			$div_cal = "'testdiv1'";
		}
		
		$tab = 0;
	}	
?>
<link href="<?php echo $this->baseurl;?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
<div id="sv_apptpro_print">
<?php if($showform){?>
   	<table width="100%" >
		<tr>
		  <td align="left" colspan="2"> <h3><?php echo JText::_('RS1_ADMIN_SCRN_TAB_BOOKING');?></h3></td>
		</tr>
	</table>
      <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminheading">
        <tr bgcolor="#F4F4F4">
          <th class="sv_title" width="3%">&nbsp;</th>
          <th class="sv_title" align="center"><?php echo JText::_('RS1_ADMIN_SCRN_REQ_ID_COL_HEAD'); ?></th>
          <th class="sv_title" align="left"><?php echo JText::_('RS1_ADMIN_SCRN_NAME_COL_HEAD'); ?></th>
          <th class="sv_title" align="left"><?php echo JText::_('RS1_ADMIN_SCRN_EMAIL_COL_HEAD'); ?></th>
          <th class="sv_title" align="left"><?php echo JText::_('RS1_ADMIN_SCRN_RESID_COL_HEAD'); ?></th>
          <th class="sv_title" align="left"><?php echo JText::_('RS1_ADMIN_SCRN_FROM_COL_HEAD'); ?></th>
          <th class="sv_title" align="left"><?php echo JText::_('RS1_ADMIN_SCRN_SERVICE_COL_HEAD'); ?></th>
          <th class="sv_title" align="center"><?php echo JText::_('RS1_ADMIN_SCRN_STATUS_COL_HEAD'); ?></th>
          <th class="sv_title" align="center"><?php echo JText::_('RS1_ADMIN_SCRN_PAYMENT_COL_HEAD'); ?></th>
        </tr>
        <?php
        $k = 0;
        for($i=0; $i < count( $this->items ); $i++) {
        $row = $this->items[$i];

       ?>
        <tr class="<?php echo "row$k"; ?>">
          <td align="center"><input type="checkbox" id="appt_cb<?php echo $i;?>" name="cid_req[]" value="<?php echo $row->id_requests; ?>" onclick="isChecked(this.checked);" /></td>
          <td align="center"><?php echo $row->id_requests; ?></td>
          <td><?php echo stripslashes($row->name); ?></td>    
          <td align="left"><?php echo $row->email; ?></td>
          <td align="left"><?php echo JText::_(stripslashes($row->ResourceName)); ?>&nbsp;</td>
          <td align="left"><?php echo $row->display_startdate; ?>&nbsp;<?php echo $row->display_starttime; ?> </td>
          <td align="left"><?php echo JText::_(stripslashes($row->ServiceName)); ?> </td>
          <td align="center"><?php echo translated_status($row->request_status); ?></td>
	      <td align="center"><?php echo translated_status($row->payment_status).($row->invoice_number != ""?"<br/>(".$row->invoice_number.")":"") ?></td>
          <?php $k = 1 - $k; ?>
        </tr>
        <?php } 
    
    ?>
      </table>
      </div>
      <?php if($apptpro_config->hide_logo == 'No'){ ?>
        <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
      <?php } ?>
<?php } ?>
</form>
<hr />
<p align="center">
  <input type="button"  onClick="window.print()"  value="<?php echo JText::_('RS1_ADMIN_SCRN_BTN_PRINT'); ?>"/>&nbsp;
  <input type="button"  onClick="window.close()"  value="<?php echo JText::_('RS1_ADMIN_SCRN_BTN_CLOSE'); ?>"/>
  </p>
