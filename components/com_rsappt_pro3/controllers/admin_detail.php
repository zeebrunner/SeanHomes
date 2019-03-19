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

//DEVNOTE: import CONTROLLER object class
jimport( 'joomla.application.component.controller' ); 


class admin_detailController extends JControllerForm
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		// Register Extra tasks
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'cancel', 'cancel' );
		$this->registerTask( 'req_close', 'req_close' );
		
		$this->registerTask( 'res_detail', 'go_res_detail' );
		$this->registerTask( 'save_res_detail', 'save_res_detail' );

		$this->registerTask( 'services_detail', 'go_services_detail' );
		$this->registerTask( 'save_services_detail', 'save_services_detail' );

		$this->registerTask( 'timeslots_detail', 'go_timeslots_detail' );
		$this->registerTask( 'save_timeslots_detail', 'save_timeslots_detail' );

		$this->registerTask( 'bookoffs_detail', 'go_bookoffs_detail' );
		$this->registerTask( 'save_bookoffs_detail', 'save_bookoffs_detail' );
		$this->registerTask( 'create_bookoff_series', 'create_bookoff_series' );

		$this->registerTask( 'paypal_transactions_detail', 'go_paypal_transactions_detail' );

		$this->registerTask( 'authnet_transactions_detail', 'go_authnet_transactions_detail' );
		$this->registerTask( 'authnet_aim_transactions_detail', 'go_authnet_aim_transactions_detail' );
		$this->registerTask( 'google_wallet_transactions_detail', 'go_google_wallet_transactions_detail' );

		$this->registerTask( 'coupons_detail', 'go_coupons_detail' );
		$this->registerTask( 'save_coupon_detail', 'save_coupon_detail' );

		$this->registerTask( 'extras_detail', 'go_extras_detail' );
		$this->registerTask( 'save_extras_detail', 'save_extras_detail' );

		$this->registerTask( 'front_desk_add', 'front_desk_add' );

		$this->registerTask( '_2co_transactions_detail', 'go_2co_transactions_detail' );

		$this->registerTask( 'readonly', 'readonly');

		$this->registerTask( 'user_search', 'user_search' );

		$this->registerTask( 'res_cancel', 'res_cancel' );
		$this->registerTask( 'res_close', 'res_close' );
		$this->registerTask( 'srv_cancel', 'srv_cancel' );
		$this->registerTask( 'srv_close', 'srv_close' );
		$this->registerTask( 'ts_cancel', 'ts_cancel' );
		$this->registerTask( 'ts_close', 'ts_close' );
		$this->registerTask( 'bo_cancel', 'bo_cancel' );
		$this->registerTask( 'bo_close', 'bo_close' );
		$this->registerTask( 'coup_cancel', 'coup_cancel' );
		$this->registerTask( 'coup_close', 'coup_close' );
		$this->registerTask( 'extra_cancel', 'extra_cancel' );
		$this->registerTask( 'extra_close', 'extra_close' );

		$this->registerTask( 'pp_close', 'pp_close' );

		$this->registerTask( 'rate_adjustments_detail', 'go_rate_adjustments_detail' );
		$this->registerTask( 'save_rate_adjustments_detail', 'save_rate_adjustments_detail' );

		$this->registerTask( 'seat_adjustments_detail', 'go_seat_adjustments_detail' );
		$this->registerTask( 'save_seat_adjustments_detail', 'save_seat_adjustments_detail' );

	}

	function edit($key=null, $urlVar=null)
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$jinput->set( 'view', 'requests_detail' );
		$jinput->set( 'layout', 'form' );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $jinput->getString( 'id', '' ));

		parent::display();

	}

	function go_res_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'resources_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);

		parent::display();

	}


	function save($key=null, $urlVar=null)
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
		$cid	= $jinput->get('cid');
		$jinput->set('id', $cid);
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'requests_detail.php');
		$model = new admin_detailModelrequests_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
	
		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item);
		} else {
			$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item;
		}
		$this->setRedirect($url, $msg);
	}

	function save_res_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
//		$cid	= $jinput->get( 'cid', array(0), 'post', 'array' );
//		$post['id'] = $jinput->getString('res_id');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'resources_detail.php');
		$model = new admin_detailModelresources_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab);
		} else {
			$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab;
		}
		$this->setRedirect($url, $msg);
	}


	function go_services_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'services_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function save_services_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
//		$cid	= $jinput->get( 'cid', array(0), 'post', 'array' );
//		$post['id'] = $jinput->getString('srv_id');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'services_detail.php');
		$model = new admin_detailModelservices_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab);
		} else {
			$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab;
		}
		$this->setRedirect($url, $msg);
	}


	function remove()
	{
		$jinput = JFactory::getApplication()->input;
		global $mainframe;

		$cid = $jinput->get( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('admin_detail');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		} else {
			// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
			$config = JFactory::getConfig();
			$seo = $config->get( 'sef' );
			if($seo == "1"){		
				$this->setRedirect( JRoute::_('index.php?option=com_rsappt_pro3&controller=admin') );
			} else {
				$this->setRedirect( 'index.php?option=com_rsappt_pro3&controller=admin');
			}
		}
	}
	
	
	
	/** function cancel
	*
	* Check in the selected detail 
	* and set Redirection to the list of items	
	* 		
	* @return set Redirection
	*/
	function cancel($key=null)
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');
		
		// Checkin the detail
		require_once(JPATH_COMPONENT.DS.'models'.DS.'requests_detail.php');
		$model = new admin_detailModelrequests_detail;
		$model->checkin();
		
		$this->close_cancel_redirect();		
	}	

	function req_close()
	{
		$this->close_cancel_redirect();
	}	

	function go_timeslots_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'timeslots_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function save_timeslots_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
		$post['id'] = $jinput->getString('ts_id');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'timeslots_detail.php');
		$model = new admin_detailModeltimeslots_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$this->setRedirect( JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab), $msg );
		} else {
			$this->setRedirect( 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab, $msg );
		}
	}


	function go_bookoffs_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'bookoffs_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function save_bookoffs_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
		$post['id'] = $jinput->getString('bo_id');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'bookoffs_detail.php');
		$model = new admin_detailModelbookoffs_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$this->setRedirect(JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab), $msg );
		} else {
			$this->setRedirect('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab, $msg );
		}
	}

	function create_bookoff_series(){
		$jinput = JFactory::getApplication()->input;

		$post	= $jinput->post->getArray();
		$post['id'] = $jinput->getString('bo_id');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		$off_date = $jinput->getString('off_date');
		$off_date2 = $jinput->getString('off_date2');
		$resource = $jinput->getString('resource_id');
		$full_day = $jinput->getString('full_day');
		$bookoff_starttime = $jinput->getString('bookoff_starttime');
		$bookoff_endtime = $jinput->getString('bookoff_endtime');
		$resource_desc = $jinput->getString('description');
		$published = $jinput->getString('published');
		$bo_Sun = $jinput->getString('chkSunday');
		$bo_Mon = $jinput->getString('chkMonday');
		$bo_Tue = $jinput->getString('chkTuesday');
		$bo_Wed = $jinput->getString('chkWednesday');
		$bo_Thu = $jinput->getString('chkThursday');
		$bo_Fri = $jinput->getString('chkFriday');
		$bo_Sat = $jinput->getString('chkSaturday');
		$bo_days = " ";
		if($bo_Sun == 'on'){$bo_days .= "|0|";}
		if($bo_Mon == 'on'){$bo_days .= "|1|";}
		if($bo_Tue == 'on'){$bo_days .= "|2|";}
		if($bo_Wed == 'on'){$bo_days .= "|3|";}
		if($bo_Thu == 'on'){$bo_days .= "|4|";}
		if($bo_Fri == 'on'){$bo_days .= "|5|";}
		if($bo_Sat == 'on'){$bo_days .= "|6|";}
		
		$d1 = strtotime($off_date);
		$d2 = strtotime($off_date2);
		$database = JFactory::getDBO();
		while($d1 <= $d2){
			$process_date = getdate($d1);
			$process_wday = "|".$process_date[wday]."|";
			if(strpos($bo_days, $process_wday) >0 ) {
				$sql = "INSERT INTO #__sv_apptpro3_bookoffs (resource_id,description,off_date,full_day,bookoff_starttime,bookoff_endtime,published)".
				" VALUES(".$resource.",'".$database->escape($resource_desc)."','".date("Y-m-d", $d1)."','".$full_day."','".$bookoff_starttime."','".$bookoff_endtime."',".$published.")";
				try{
					$database->setQuery( $sql );
					$database->execute();
				} catch (RuntimeException $e) {
					logIt($e->getMessage(), "ctrl_admin_detail", "", "");
					echo JText::_('RS1_SQL_ERROR');
					exit;
				}
			}
			$d1 = $d1+86400; 
		}
		$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		
		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$this->setRedirect( JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab), $msg );
		} else {
			$this->setRedirect( 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab, $msg );
		}
	}



	function go_paypal_transactions_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('tab');
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'paypal_transactions_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'fromtab', $fromtab);		
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function go_authnet_transactions_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'authnet_transactions_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'fromtab', $fromtab);		
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function go_authnet_aim_transactions_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'authnet_aim_transactions_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'fromtab', $fromtab);		
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function go_google_wallet_transactions_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'google_wallet_transactions_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'fromtab', $fromtab);		
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}


	function go_2co_transactions_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('tab');
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', '_2co_transactions_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'fromtab', $fromtab);		
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function go_coupons_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'coupons_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function save_coupon_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
		$post['id'] = $jinput->getString('coup_id');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'coupons_detail.php');
		$model = new admin_detailModelcoupons_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab);
		} else {
			$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab;
		}
		$this->setRedirect($url, $msg);
	}

	function go_extras_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'extras_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function save_extras_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
		$post['id'] = $jinput->getString('ext_id');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'extras_detail.php');
		$model = new admin_detailModelextras_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab);
		} else {
			$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab;
		}
		$this->setRedirect($url, $msg);
	}

	function go_rate_adjustments_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'rate_adjustments_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function save_rate_adjustments_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
		$post['id'] = $jinput->getString('id_rate_adjustments');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'rate_adjustments_detail.php');
		$model = new admin_detailModelrate_adjustments_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab);
		} else {
			$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab;
		}
		$this->setRedirect($url, $msg);
	}

	function go_seat_adjustments_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'seat_adjustments_detail' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function save_seat_adjustments_detail()
	{
		$jinput = JFactory::getApplication()->input;
		$post	= $jinput->post->getArray();
		$post['id'] = $jinput->getString('id_seat_adjustments');
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');

		require_once(JPATH_COMPONENT.DS.'models'.DS.'seat_adjustments_detail.php');
		$model = new admin_detailModelseat_adjustments_detail;
 		if($model == null){
			echo "model = null";
			exit;
		}
	
		if ($model->store($post)) {
			$msg = JText::_( 'COM_RSAPPT_SAVE_OK' );
		} else {
			$msg = JText::_( 'COM_RSAPPT_ERROR_SAVING' ).": ".$model->getError();
			logit($model->getError(), $model->getName()); 
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($seo == "1"){		
			$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab);
		} else {
			$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab;
		}
		$this->setRedirect($url, $msg);
	}


	function front_desk_add()
	{
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString( 'frompage', '' );
		$id = $jinput->getString( 'cid', '' );
		$jinput->set( 'view', 'front_desk_add' );
		$jinput->set( 'layout', 'default'  );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'listpage', $frompage);
		$jinput->set( 'Itemid', $jinput->getString( 'Itemid'));
		$jinput->set( 'id', $id[0]);
	
		parent::display();

	}

	function readonly(){
		$jinput = JFactory::getApplication()->input;

		$jinput->set( 'view', 'requests_detail' );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'layout', 'default_readonly');
		//$jinput->set( 'tmpl', 'component');
		$jinput->set( 'frompage', $jinput->getString('frompage'));

		parent::display();

	}
	
	function user_search(){

		$jinput = JFactory::getApplication()->input;
		$jinput->set( 'view', 'user_search' );
		$jinput->set( 'hidemainmenu', 1);
		$jinput->set( 'layout', 'default');
		$jinput->set( 'tmpl', 'component');
		$jinput->set( 'frompage', $jinput->getString('frompage'));

		parent::display();

	}

	function res_cancel()
	{
		// Checkin the detail
		require_once(JPATH_COMPONENT.DS.'models'.DS.'resources_detail.php');
		$model = new admin_detailModelresources_detail;
		$model->checkin();

		$this->close_cancel_redirect();
	}

	function res_close()
	{
		$this->close_cancel_redirect();
	}

	function srv_cancel()
	{	
		// Checkin the detail
		require_once(JPATH_COMPONENT.DS.'models'.DS.'services_detail.php');
		$model = new admin_detailModelservices_detail;
		$model->checkin();

		$this->close_cancel_redirect();
	}

	function srv_close()
	{
		$this->close_cancel_redirect();
	}

	function ts_cancel()
	{	
		// Checkin the detail
		require_once(JPATH_COMPONENT.DS.'models'.DS.'timeslots_detail.php');
		$model = new admin_detailModeltimeslots_detail;
		$model->checkin();

		$this->close_cancel_redirect();
	}

	function ts_close()
	{
		$this->close_cancel_redirect();
	}

	function bo_cancel()
	{	
		// Checkin the detail
		require_once(JPATH_COMPONENT.DS.'models'.DS.'bookoffs_detail.php');
		$model = new admin_detailModelbookoffs_detail;
		$model->checkin();

		$this->close_cancel_redirect();
	}

	function bo_close()
	{
		$this->close_cancel_redirect();
	}
	
	function coup_cancel()
	{	
		// Checkin the detail
		require_once(JPATH_COMPONENT.DS.'models'.DS.'coupons_detail.php');
		$model = new admin_detailModelcoupons_detail;
		$model->checkin();

		$this->close_cancel_redirect();
	}

	function coup_close()
	{
		$this->close_cancel_redirect();
	}

	function extra_cancel()
	{	
		// Checkin the detail
		require_once(JPATH_COMPONENT.DS.'models'.DS.'extras_detail.php');
		$model = new admin_detailModelextras_detail;
		$model->checkin();

		$this->close_cancel_redirect();
	}

	function extra_close()
	{
		$this->close_cancel_redirect();
	}

	function pp_close()
	{
		$this->close_cancel_redirect();
	}
	
	
	function close_cancel_redirect(){
		$jinput = JFactory::getApplication()->input;
		$frompage = $jinput->getString('frompage');
		$frompage_item = $jinput->getString('frompage_item');
		$fromtab = $jinput->getString('fromtab');
 		// With J1.7, JRoute screws up the url for use with setRedirect, if not using SEO
		$config = JFactory::getConfig();
		$seo = $config->get( 'sef' );
		if($frompage == "front_desk" || $frompage == "admin"){
			if($seo == "1"){		
				$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item);
			} else {
				$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item;
			}
		} else {
			if($seo == "1"){		
				$url = JRoute::_('index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab);
			} else {
				$url = 'index.php?option=com_rsappt_pro3&view='.$frompage.'&Itemid='.$frompage_item.'&current_tab='.$fromtab;
			}
		}
		$this->setRedirect($url);
	}
	

}

