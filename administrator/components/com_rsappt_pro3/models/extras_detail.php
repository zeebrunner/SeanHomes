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


class extras_detailModelextras_detail extends JModelLegacy
{
		var $_id_extras = null;
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
	 * Method to set the extras identifier
	 *
	 * @access	public
	 * @param	int extras identifier
	 */
	function setId($id_extras)
	{
		// Set extras id and wipe data
		$this->_id_extras		= $id_extras;
		$this->_data	= null;
	}

	/**
	 * Method to get a extras
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the extras data
		if ($this->_loadData())
		{
		//load the data nothing else	  
		}
		else  $this->_initData();
		//print_r($this->_data);	
		
   	return $this->_data;
	}
	
	/**
	 * Method to checkout/lock the extras
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_id_extras)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	= JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$extras = $this->getTable();
			
			
			if(!$extras->checkout($uid, $this->_id_extras)) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}
	/**
	 * Method to checkin/unlock the extras
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_id_extras)
		{
			$extras = $this->getTable();
			if(! $extras->checkin($this->_id_extras)) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}	
	/**
	 * Tests if extras is checked out
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
	 * Method to load content extras data
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
			$query = 'SELECT * FROM '.$this->_table_prefix.'extras WHERE id_extras = '. $this->_id_extras;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			//print_r($this->_data);
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the extras data
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
			$detail->id_extras	= null;
			$detail->extras_label = null;
			$detail->extras_tooltip = null;
			$detail->extras_cost = '0.00';
			$detail->cost_unit = "Hour";
			$detail->default_quantity = 0;
			$detail->min_quantity = 0;
			$detail->max_quantity = 1;
			$detail->extras_help = null;
			$detail->resource_scope = null;
			$detail->service_scope = null;
			$detail->extras_duration = 0;
			$detail->extras_duration_unit = "Minute";
			$detail->extras_duration_effect = "PerUnit";
			$detail->staff_only = "No";
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
	 * Method to store the extras text
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		//DEVNOTE: Load table class from com_rsappt_pro3/tables/extras_detail.php	
		$row = $this->getTable();

		// Bind the form fields to the extras table
		if (!$row->bind($data)) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// if new item, order last in appropriate group
		if (!$row->id_extras) {			
			$row->ordering = $row->getNextOrder();
		}

		//DEVNOTE: Make sure the extras table is valid
		//JTable return always true but there is space to put
		//our custom check method
/*		if (!$row->check()) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}*/

		// Store the extras table to the database
		if (!$row->store()) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	
		/**
	 * Method to (un)publish a extras
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

			$query = 'UPDATE '.$this->_table_prefix.'extras'
				. ' SET published = ' . intval( $publish )
				. ' WHERE id_extras IN ( '.$cids.' )'
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
	 * Method to move a extras_detail
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function saveorder($cid = array(), $order)
	{
		//DEVNOTE: Load table class from com_sv_ser/tables/extras_detail.php		
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
	 * Method to move a extras 
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function move($direction)
	{
	//DEVNOTE: Load table class from com_sv_ser/tables/extras_detail.php	
		$row = $this->getTable();
	//DEVNOTE: we need to pass here id of extras detail 		
		if (!$row->load($this->_id_extras)) {
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
			$query = 'DELETE FROM '.$this->_table_prefix.'extras WHERE id_extras IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->execute()) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			// remove from rate overrides
			$this->_db->setQuery("DELETE FROM #__sv_apptpro3_rate_overrides WHERE entity_type = 'extra' AND entity_id IN (".$cids.")");
			if (!$this->_db->execute()) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
		}

		return true;
	}
	

}

?>
