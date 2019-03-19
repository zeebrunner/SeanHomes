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


/**
 * rsappt_pro3  Controller
 */
 
class paypal_transactionsController extends JControllerForm
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		// Register Extra tasks
		$this->registerTask( 'export_paypal', 'export_data' );

	}
	
	function cancel($key=null)
	{
		$this->setRedirect( 'index.php?option=com_rsappt_pro3&controller=payment_transactions' );
	}	

	/**
	 * Method display
	 * 
	 * 1) create a classVIEWclass(VIEW) and a classMODELclass(Model)
	 * 2) pass MODEL into VIEW
	 * 3)	load template and render it  	  	 	 
	 */

	function display($cachable=false, $urlparams=false) {
		parent::display();
		
		require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'rsappt_pro3.php';
		rsappt_pro3Helper::addSubmenu('paypal_transactions');
		
	}

	function export_data(){
		$jinput = JFactory::getApplication()->input;
		$uid = $jinput->get( 'cid', array(0), 'post', 'array' );
		$sql = "SELECT ".
			  " #__sv_apptpro3_paypal_transactions.id_paypal_transactions, #__sv_apptpro3_paypal_transactions.firstname, #__sv_apptpro3_paypal_transactions.lastname,".
			  " #__sv_apptpro3_paypal_transactions.buyer_email, #__sv_apptpro3_paypal_transactions.street, #__sv_apptpro3_paypal_transactions.city,".
			  " #__sv_apptpro3_paypal_transactions.state, #__sv_apptpro3_paypal_transactions.zipcode, #__sv_apptpro3_paypal_transactions.itemname,".
			  " #__sv_apptpro3_paypal_transactions.itemnumber, #__sv_apptpro3_paypal_transactions.quantity, #__sv_apptpro3_paypal_transactions.paymentdate,".
			  " #__sv_apptpro3_paypal_transactions.paymenttype, #__sv_apptpro3_paypal_transactions.txnid, #__sv_apptpro3_paypal_transactions.mc_gross,".
			  " #__sv_apptpro3_paypal_transactions.mc_fee, #__sv_apptpro3_paypal_transactions.paymentstatus, #__sv_apptpro3_paypal_transactions.pendingreason,".
			  " #__sv_apptpro3_paypal_transactions.txntype, #__sv_apptpro3_paypal_transactions.tax, #__sv_apptpro3_paypal_transactions.mc_currency,".
			  " #__sv_apptpro3_paypal_transactions.reasoncode, #__sv_apptpro3_paypal_transactions.custom, #__sv_apptpro3_paypal_transactions.country,".
			  " #__sv_apptpro3_paypal_transactions.datecreation, #__sv_apptpro3_paypal_transactions.stamp, #__sv_apptpro3_requests.id_requests AS RequestID,".
			  " #__sv_apptpro3_requests.name AS Requester, #__sv_apptpro3_requests.startdate, #__sv_apptpro3_requests.starttime,".
			  " #__sv_apptpro3_requests.enddate, #__sv_apptpro3_requests.endtime, #__sv_apptpro3_resources.name AS Resource ".
			  " FROM ".
			  " #__sv_apptpro3_paypal_transactions INNER JOIN ".
			  " #__sv_apptpro3_requests ON #__sv_apptpro3_paypal_transactions.custom = ".
			  " #__sv_apptpro3_requests.id_requests INNER JOIN ".
			  " #__sv_apptpro3_resources ON #__sv_apptpro3_requests.resource = ".
			  " #__sv_apptpro3_resources.id_resources ".
				" WHERE #__sv_apptpro3_paypal_transactions.id_paypal_transactions IN (".implode(",", $uid).")";
		//echo $sql;
		//exit;
		
		ob_end_clean();
			
		$file_name = 'export_sv_paypal_transactions.csv';
			
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Accept-Ranges: bytes');
		header('Content-Disposition: attachment; filename='.basename($file_name).';');
		header('Content-Type: text/plain; '.'_ISO');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Pragma: no-cache');
			
		$database = JFactory::getDBO();
		try{
			$database->setQuery($sql);
			$rows = $database -> loadObjectList();
		} catch (RuntimeException $e) {
			logIt($e->getMessage(), "be_ctrl_paypal_trans", "", "");
			echo JText::_('RS1_SQL_ERROR').$e->getMessage();
			exit;
		}		
		
		$csv_save = '';
		if (!empty($rows)) {
				$comma = ',';
				$CR = "\r";
				// Make csv rows for field name
				$i=0;
				$fields = $rows[0];
				$cnt_fields = count($fields);
				$csv_fields = '';
				foreach($fields as $name=>$val) {
						$i++;
						//if ($cnt_fields<=$i) $comma = '';
						$csv_fields .= $name.$comma;
				}
				// Make csv rows for data
				$csv_values = '';
				foreach($rows as $row) {
						$i=0;
						$comma = ',';
						foreach($row as $name=>$val) {
								$i++;
								//if ($cnt_fields<=$i) $comma = '';
								$csv_values .= '"'.$val.'"'.$comma;
						}
						$csv_values .= $CR;
				}
				$csv_save = $csv_fields.$CR.$csv_values;
		}
		echo $csv_save;
		die();  // no need to send anything else

	}


}	
?>

