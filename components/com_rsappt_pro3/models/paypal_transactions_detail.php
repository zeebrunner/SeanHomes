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

//DEVNOTE: import MODEL object class
jimport('joomla.application.component.model');


class admin_detailModelpaypal_transactions_detail extends JModelLegacy
{
		var $_id_paypal_transactions = null;
		var $_data = null;
		var $_table_prefix = null;

	function __construct()
	{
		parent::__construct();
		
		//initialize class property
	  	$this->_table_prefix = '#__sv_apptpro3_';			

		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid');

		$this->setId((int)$cid);

	}

	/**
	 * Method to set the paypal_transactions identifier
	 *
	 * @access	public
	 * @param	int paypal_transactions identifier
	 */
	function setId($id_paypal_transactions)
	{
		// Set paypal_transactions id and wipe data
		$this->_id_paypal_transactions		= $id_paypal_transactions;
		$this->_data	= null;
	}

	/**
	 * Method to get a paypal_transactions
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the paypal_transactions data
		if ($this->_loadData())
		{
		//load the data nothing else	  
		}
		else  $this->_initData();
		//print_r($this->_data);	
		
   	return $this->_data;
	}
	
	/**
	 * Method to checkout/lock the paypal_transactions
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_id_paypal_transactions)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	= JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$paypal_transactions = $this->getTable();
			
			
			if(!$paypal_transactions->checkout($uid, $this->_id_paypal_transactions)) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}
	/**
	 * Method to checkin/unlock the paypal_transactions
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_id_paypal_transactions)
		{
			$paypal_transactions = $this->getTable();
			if(! $paypal_transactions->checkin($this->_id_paypal_transactions)) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}	
	/**
	 * Tests if paypal_transactions is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	1.5
	 */
	function isCheckedOut( $uid=0 )
	{
		if ($this->_loadData())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
	}	
		
	/**
	 * Method to load content paypal_transactions data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT * FROM '.$this->_table_prefix.'paypal_transactions WHERE id_paypal_transactions = '. $this->_id_paypal_transactions;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			//print_r($this->_data);
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the paypal_transactions data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$detail = new stdClass();
			$detail->id_paypal_transactions	= 0;
			$detail->txnid = null;
			$detail->custom = null;
			$detail->firstname = null;
			$detail->lastname = null;
			$detail->buyer_email = null;
			$detail->street = null;
			$detail->city = null;
			$detail->state = null;
			$detail->zipcode = null;
			$detail->memo = null;
			$detail->itemname = null;
			$detail->itemnumber = null;
			$detail->os0 = null;
			$detail->on0 = null;
			$detail->os1 = null;
			$detail->on1 = null;
			$detail->quantity = null;
			$detail->paymentdate = null;
			$detail->paymenttype = null;
			$detail->mc_gross = null;
			$detail->mc_fee = null;
			$detail->paymentstatus = null;
			$detail->pendingreason = null;
			$detail->txntype = null;
			$detail->tax = null;
			$detail->mc_currency = null;
			$detail->reasoncode = null;
			$detail->country = null;
			$detail->datecreation = null;
			$detail->checked_out = 0;
			$detail->checked_out_time = 0;
			$detail->ordering = 1;
			$detail->published = 0;
			
			$this->_data	= $detail;
			return (boolean) $this->_data;
		}
		return true;
	}
  	

	/**
	 * Method to store the paypal_transactions text
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		//DEVNOTE: Load table class from com_rsappt_pro3/tables/paypal_transactions_detail.php	
		$row = $this->getTable();

		// Bind the form fields to the paypal_transactions table
		if (!$row->bind($data)) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// if new item, order last in appropriate group
		if (!$row->id_paypal_transactions) {
			$where = 'id_paypal_transactions = ' . $row->id_paypal_transactions ;
			$row->ordering = $row->getNextOrder ( $where );
		}

		//DEVNOTE: Make sure the paypal_transactions table is valid
		//JTable return always true but there is space to put
		//our custom check method
/*		if (!$row->check()) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}*/

		// Store the paypal_transactions table to the database
		if (!$row->store()) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	

	function delete($cid = array())
	{
		$result = false;


		if (count( $cid ))
		{
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM '.$this->_table_prefix.'paypal_transactions WHERE id_paypal_transactions IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->execute()) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	

}

?>
