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

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );


/**
 [controller]View[controller]
 */
class admin_detailViewseat_adjustments_detail extends JViewLegacy
{
	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
	
		global $context;
	  	$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;

		$uri = JFactory::getURI()->toString();
		$user = JFactory::getUser();
		
		require_once(JPATH_COMPONENT.DS.'models'.DS.'seat_adjustments_detail.php');
		$model = new admin_detailModelseat_adjustments_detail;

		$frompage = $jinput->getString('frompage', '');
		$frompage_item = $jinput->getString('Item_id', '');
		$fromtab = $jinput->getString('tab');


    	//DEVNOTE: let's be the template 'form.php' instead of 'default.php' 
		$this->setLayout('form');

    	//DEVNOTE: prepare array 
		$lists = array();


		//get the data
		$detail	= $this->get('data');
		//print_r($detail);
		
    	//DEVNOTE: the new record ?  Edit or Create?
		$isNew		= ($detail->id_seat_adjustments < 1);

		// fail if checked out not by 'me'
		$lock_msg = "";
		if ($model->isCheckedOut( $user->get('id') )) {
			// get name of res-admin who has it locked
			$lock_msg = JText::_('RS1_LOCKED')." (".$model->checkedOutBy().") ";
		} else {
			// Edit or Create?
			if (!$isNew){
				$model->checkout( $user->get('id') );
			} else{
				// initialise new record
				$detail->published = 1;
				$detail->ordering 	= 0;
			}
		}
		
		// build the html select list
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $detail->published );


		jimport('joomla.filter.filteroutput');	
		JFilterOutput::objectHTMLSafe( $detail, ENT_QUOTES );			

		$this->assignRef('lists',		$lists);
		$this->assignRef('detail',		$detail);
		$this->assignRef('request_url',	$uri );
		$this->assignRef('user_id',		$user->id);		
		$this->assignRef('frompage',	$frompage);
		$this->assignRef('frompage_item',	$frompage_item);
		$this->assignRef('fromtab',	$fromtab);
		$this->assignRef('lock_msg',	$lock_msg);

		$appWeb      = new JApplicationWeb;
		$layout = ($appWeb->client->mobile ? 'mobile' : null);
		$agent = $appWeb->client->userAgent;
		$this->assignRef('agent',	$agent);
		// dev only hard code mobile view
		//$layout = 'mobile';
		
    	parent::display($layout);
	}
}	
?>
