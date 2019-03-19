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


class rate_adjustments_detailModelrate_adjustments_detail extends JModelLegacy
{
		var $_id_rate_adjustments = null;
		var $_data = null;
		var $_table_prefix = null;

	function __construct()
	{
		parent::__construct();
		
		//initialize class property
	  	$this->_table_prefix = '#__sv_apptpro3_';			

		$jinput = JFactory::getApplication()->input;
		$array = $jinput->get( 'cid', array(0), 'post', 'array' );
		
		$this->setId((int)$array[0]);

	}

	/**
	 * Method to set the rate_adjustments identifier
	 *
	 * @access	public
	 * @param	int rate_adjustments identifier
	 */
	function setId($id_rate_adjustments)
	{
		// Set rate_adjustments id and wipe data
		$this->_id_rate_adjustments		= $id_rate_adjustments;
		$this->_data	= null;
	}

	/**
	 * Method to get a rate_adjustments
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the rate_adjustments data
		if ($this->_loadData())
		{
		//load the data nothing else	  
		}
		else  $this->_initData();
		//print_r($this->_data);	
		
   	return $this->_data;
	}
	
	/**
	 * Method to checkout/lock the rate_adjustments
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_id_rate_adjustments)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	= JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$rate_adjustments = $this->getTable();
			
			
			if(!$rate_adjustments->checkout($uid, $this->_id_rate_adjustments)) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}
	/**
	 * Method to checkin/unlock the rate_adjustments
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_id_rate_adjustments)
		{
			$rate_adjustments = $this->getTable();
			if(! $rate_adjustments->checkin($this->_id_rate_adjustments)) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}	
	/**
	 * Tests if rate_adjustments is checked out
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
	 * Method to load content rate_adjustments data
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
			//$query = 'SELECT * FROM '.$this->_table_prefix.'rate_adjustments WHERE id_rate_adjustments = '. $this->_id_rate_adjustments;

			$query = 'SELECT '.$this->_table_prefix.'rate_adjustments.*, '.$this->_table_prefix.'resources.name as res_name FROM '.$this->_table_prefix.'rate_adjustments  '.
			' INNER JOIN '.$this->_table_prefix.'resources  '.
			' ON '.$this->_table_prefix.'rate_adjustments.entity_id = '.$this->_table_prefix.'resources.id_resources  '.
			' WHERE id_rate_adjustments = '. $this->_id_rate_adjustments;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			//print_r($this->_data);
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the rate_adjustments data
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
			$detail->id_rate_adjustments	= 0;
			$detail->entity_type = "";
			$detail->entity_id = 0;
			$detail->group_id = 0;
			$detail->rate_adjustment = 0.00;
			$detail->rate_adjustment_unit = "Flat";
			$detail->by_day_time = "Day";
			$detail->adjustSunday = "No";
			$detail->adjustMonday = "No";
			$detail->adjustTuesday = "No";
			$detail->adjustWednesday = "No";
			$detail->adjustThursday = "No";
			$detail->adjustFriday = "No";
			$detail->adjustSaturday = "No";
			$detail->timeRangeStart = null;
			$detail->timeRangeEnd = null;
			$detail->checked_out = 0;
			$detail->checked_out_time = 0;
			$detail->ordering = 1;
			$detail->start_publishing = null;
			$detail->end_publishing = null;
			$detail->published = 0;
			$this->_data	= $detail;
			return (boolean) $this->_data;
		}
		return true;
	}
  	

	/**
	 * Method to store the rate_adjustments text
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		//DEVNOTE: Load table class from com_rsappt_pro3/tables/rate_adjustments_detail.php	
		$row = $this->getTable();

		// Bind the form fields to the rate_adjustments table
		if (!$row->bind($data)) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}

		//DEVNOTE: Make sure the rate_adjustments table is valid
		//JTable return always true but there is space to put
		//our custom check method
/*		if (!$row->check()) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}*/

		// Store the rate_adjustments table to the database
		if (!$row->store()) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	
		/**
	 * Method to (un)publish a rate_adjustments
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	= JFactory::getUser();

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE '.$this->_table_prefix.'rate_adjustments'
				. ' SET published = ' . intval( $publish )
				. ' WHERE id_rate_adjustments IN ( '.$cids.' )'
				. ' AND ( checked_out = 0 OR ( checked_out = ' .$user->get('id'). ' ) )'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->execute()) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	
	/**
	 * Method to move a rate_adjustments_detail
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function saveorder($cid = array(), $order)
	{
		//DEVNOTE: Load table class from com_sv_ser/tables/rate_adjustments_detail.php		
		$row = $this->getTable();
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					//$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}
		
		/**
	 * Method to move a rate_adjustments 
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function move($direction)
	{
	//DEVNOTE: Load table class from com_sv_ser/tables/rate_adjustments_detail.php	
		$row = $this->getTable();
	//DEVNOTE: we need to pass here id of rate_adjustments detail 		
		if (!$row->load($this->_id_rate_adjustments)) {
			//$this->setError($this->_db->getErrorMsg());
		
			return false;
		}
  
	//DEVNOTE: call move method of JTABLE. 
  //first parameter: direction [up/down]
  //second parameter: condition
		if (!$row->move( $direction, ' published >= 0 ' )) {
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
			$query = 'DELETE FROM '.$this->_table_prefix.'rate_adjustments WHERE id_rate_adjustments IN ( '.$cids.' )';
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
