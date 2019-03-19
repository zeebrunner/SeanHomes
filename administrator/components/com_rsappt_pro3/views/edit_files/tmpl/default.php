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

<form action="<?php echo JRoute::_($this->request_url); ?>" method="post" name="adminForm" id="adminForm">
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">

    <ul class="nav nav-tabs">
        <li class="active"><a href="#panel1" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_SCRN_EDIT_FILES_CSS_TAB');?></a></li>
        <li><a href="#panel2" data-toggle="tab"><?php echo JText::_('RS1_ADMIN_SCRN_EDIT_FILES_LANG_TAB');?></a></li>
    </ul>

<div class="tab-content">
	<div id="panel1" class="tab-pane active">
		<table width="100%" border="1" cellpadding="2" cellspacing="0">
    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_EDIT_FILES_CSS');?></td>
    </tr>
    <tr>
      <td ><textarea style="width:80% !important" wrap="off" rows="20" cols="200" name="cssfile" id="cssfile"><?php $fn = JPATH_SITE."/components/com_rsappt_pro3/sv_apptpro.css";
            print htmlspecialchars(implode("",file($fn)));?> 
		</textarea></td>
    </tr>
<!--    <tr>
      <td><p>
        <input name="Reset" type="button" value="<?php echo JText::_('RS1_ADMIN_SCRN_EDIT_FILES_CSS_RESET');?>" onclick="resetCSS()" />
      </p>
      <p><?php echo JText::_('RS1_ADMIN_SCRN_EDIT_FILES_CSS_NOTE');?></p></td>
    </tr>-->
  </table>    
    </div>
	<div id="panel2" class="tab-pane">   
	    <table width="100%" border="0" cellpadding="2" cellspacing="0">
<!--    <tr>
      <td><?php echo JText::_('RS1_ADMIN_SCRN_EDIT_FILES_LANG_REMEMBER_TO_SAVE');?></td>
    </tr>
    <tr>
      <td><select id="langfiles" name="langfiles">
--><?php
	$lang = JFactory::getLanguage();
	$directory = JPATH_SITE.DS."language".DS;
	$file = scandir($directory);
?>      
      </select>
<!--      </td>
    </tr>
--><?php   
	$lang_file_count = 0;
	foreach( $file as $this_file ) {
		if( $this_file != "." && $this_file != ".." ) {
			if( is_dir("$directory/$this_file") ) {
				$file2 = scandir("$directory".DS."$this_file");
				//print_r($file2);
				foreach( $file2 as $this_file2 ) {
					if( $this_file2 != "." && $this_file2 != ".." ) {
						if( !is_dir("$directory/$this_file/$this_file2") ) {
							if(strpos($this_file2, "com_rsappt_pro" ) >0 ){
								$filename = "langfile".$lang_file_count ?>
                            	<tr><td><b><?php echo "$directory".$this_file.DS.$this_file2 ?></b></td></tr>
								<tr>
								  <td><textarea style="width:95% !important" rows="20" cols="200" wrap="off" name="<?php echo $filename?>" id="<?php echo $filename?>"><?php $fn = "$directory/$this_file/$this_file2";
										print implode("",file($fn));?> 
									</textarea><br/><br/>&nbsp;
                                    <input type="hidden" name="save_<?php echo $filename?>" value="<?php echo "$directory".$this_file.DS.$this_file2 ?>"/>
                                  </td>
								</tr>
                             <?php   
							 $lang_file_count++;
							}
						}
					}
				}
			}
		}
	}
 ?>   
<!--    <tr>
      <td><textarea rows="20" cols="80" wrap="off" name="langfile" id="langfile"><?php $fn = JPATH_SITE."/language/en-GB/en-GB.com_rsappt_pro3.ini";
            print htmlspecialchars(implode("",file($fn)));?> 
		</textarea></td>
    </tr>-->
  </table> 
    </div>
  
  <input type="hidden" name="lang_file_count" id="lang_file_count" value="<?php echo $lang_file_count;?>" />
</div>
  <p>&nbsp;</p>
  <p>
  <input type="hidden" name="controller" value="edit_files" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="hidemainmenu" value="0" />  
  <input type="hidden" name="task" value="" />
  <br />
  <span style="font-size:10px"> Appointment Booking Pro Ver. 3.0.6 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
</form>
