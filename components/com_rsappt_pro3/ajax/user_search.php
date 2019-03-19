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



defined( '_JEXEC' ) or die( 'Restricted access' );
include_once( JPATH_SITE."/administrator/components/com_rsappt_pro3/functions_pro2.php" );

	$jinput = JFactory::getApplication()->input;

	header('Content-Type: text/HTML'); 
	header("Cache-Control: no-cache, must-revalidate");
	//A date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$database = JFactory::getDBO();
		
	$search_string = $jinput->getString("src_for", "-1");	
	$search_string = str_replace("~", "'",$search_string);
	$safe_search_string = '%' . $database->escape( $search_string, true ) . '%' ;
	$sql = 'SELECT id,name,username FROM #__users WHERE block = 0 AND (name LIKE '.$database->quote( $safe_search_string, false ).' OR username LIKE '.$database->quote( $safe_search_string, false ).')  order by name';
	try{
		$database->setQuery($sql);
		$result_list = NULL;
		$result_list = $database -> loadObjectList();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "user_search", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}		

	if(count($result_list) == 0){
		echo JText::_('RS1_ADMIN_USER_SEARCH_NONE_FOUND').$search_string;
	} else {
		echo "<select id='thename' name='thename' size=".count($result_list)." style='height:auto' ".(count($result_list)==1?"onclick=":"onchange=")."'goBack()'>";
		for($i=0; $i < count( $result_list ); $i++) {
			$user_row = $result_list[$i];
			echo "<option value='".$user_row->id."|".$user_row->name."'>".$user_row->name." (".$user_row->username.")</opiton>";			
		}	
		echo "</select>";
		echo "<br/>".JText::_('RS1_ADMIN_USER_SEARCH_PICK_ONE');
	}

	exit;
	

?>