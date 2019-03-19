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

<script language="javascript">
	function doBackup(){
		document.body.style.cursor = "wait";
		Joomla.submitform("backup");
	}
	function doRestore(){
		document.body.style.cursor = "wait";
		Joomla.submitform("restore");
	}
	
</script>
<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">
  <table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td colspan="2"><p><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INTRO');?><br/><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INTRO_LANG');?></p>
      <p>&nbsp;</p></td>
    </tr>
  <tr>
    <td align="center" width="30%"><input type="button" name="btnBackup" id="btnBackup" value="<?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_NOW');?>" onclick="doBackup();"/></td>
    <td align="center" width="30%"><input type="button" name="btnRestore" id="btnRestore" value="<?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_RESTORE_NOW');?>" onclick="doRestore();"/></td>
  </tr>
  <tr>
    <td align="center" valign="top" style="border-right:solid 1px #000">
      <br/>
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkBackupErrorLog" id="chkBackupErrorLog" /></div>
	  <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INCL_ERROR');?></label></div>
      <br/>
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkBackupReminderLog" id="chkBackupReminderLog" /></div>
      <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INCL_REM');?></label></div>
      <br/>
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkBackupLangFile" id="chkBackupLangFile" /></div>
      <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INCL_LANG');?></label></div>
    </td>
    <td align="center">
      <br/>
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkRestoreCSS" id="chkRestoreCSS" /></div>
      <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INCL_CSS_REST');?></label></div><br />
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkRestoreErrorLog" id="chkRestoreErrorLog" /></div>
      <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INCL_ERROR_REST');?></label></div><br />
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkRestoreReminderLog" id="chkRestoreReminderLog" /></div>
      <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INCL_REM_REST');?></label></div><br />
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkRestoreLangFile" id="chkRestoreLangFile" /></div>
      <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_INCL_LANG_REST');?></label></div><br />
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkBackfillCats" id="chkBackfillCats" /></div>
      <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_BACKFILL_CATS');?></label></div><br/>
      <div style="display: table-cell; padding-left:5px;"><input type="checkbox" name="chkFromV2" id="chkFromV2" /></div>
      <div style="display: table-cell; padding-left:5px;"><label><?php echo JText::_('RS1_ADMIN_SCRN_RESTORE_FROM_2');?></label></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <hr /><?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_NOTE');?><br/>
      <?php echo JText::_('RS1_ADMIN_SCRN_BACKUP_BACKFILL_CATS_HELP');?></td>
    
    
  </tr>
</table>
  <p>&nbsp;</p>
  <p>
  <input type="hidden" name="controller" value="backup_restore" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="hidemainmenu" value="0" />  
  <input type="hidden" name="task" value="" />
  <br />
  <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
</form>
