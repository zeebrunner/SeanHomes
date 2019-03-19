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


class rate_overrides_detailModelrate_overrides_detail extends JModelLegacy
{
		var $_id_rate_overrides = null;
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
	 * Method to set the rate_overrides identifier
	 *
	 * @access	public
	 * @param	int rate_overrides identifier
	 */
	function setId($id_rate_overrides)
	{
		// Set rate_overrides id and wipe data
		$this->_id_rate_overrides		= $id_rate_overrides;
		$this->_data	= null;
	}

	/**
	 * Method to get a rate_overrides
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the rate_overrides data
		if ($this->_loadData())
		{
		//load the data nothing else	  
		}
		else  $this->_initData();
		//print_r($this->_data);	
		
   	return $this->_data;
	}
	
	/**
	 * Method to checkout/lock the rate_overrides
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_id_rate_overrides)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	= JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$rate_overrides = $this->getTable();
			
			
			if(!$rate_overrides->checkout($uid, $this->_id_rate_overrides)) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}
	/**
	 * Method to checkin/unlock the rate_overrides
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_id_rate_overrides)
		{
			$rate_overrides = $this->getTable();
			if(! $rate_overrides->checkin($this->_id_rate_overrides)) {
				//$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}	
	/**
	 * Tests if rate_overrides is checked out
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
	 * Method to load content rate_overrides data
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
			//$query = 'SELECT * FROM '.$this->_table_prefix.'rate_overrides WHERE id_rate_overrides = '. $this->_id_rate_overrides;

			$query = 'SELECT * FROM ((SELECT DISTINCT #__usergroups.id, '.$this->_table_prefix.'rate_overrides.*, #__usergroups.title as user_group, '.
			$this->_table_prefix.'resources.name as res_name, "" as extra_name, "" as srv_name,  "" as seat_name '.
			' FROM '.$this->_table_prefix.'rate_overrides  '.
			' INNER JOIN #__usergroups '. 
			' ON #__usergroups.id = '.$this->_table_prefix.'rate_overrides.group_id  '.
			' INNER JOIN '.$this->_table_prefix.'resources  '.
			' ON '.$this->_table_prefix.'rate_overrides.entity_id = '.$this->_table_prefix.'resources.id_resources  '.
			' WHERE entity_type = \'resource\') '.
			
			'UNION '.
			
			'(SELECT DISTINCT #__usergroups.id, '.$this->_table_prefix.'rate_overrides.*, #__usergroups.title as user_group,  '.
			'"" as res_name, '.
			$this->_table_prefix.'extras.extras_label as extra_name, "" as srv_name,  "" as seat_name '.
			' FROM '.$this->_table_prefix.'rate_overrides  '.
			' INNER JOIN #__usergroups  '.
			' ON #__usergroups.id = '.$this->_table_prefix.'rate_overrides.group_id  '.
			' INNER JOIN '.$this->_table_prefix.'extras  '.
			' ON '.$this->_table_prefix.'rate_overrides.entity_id = '.$this->_table_prefix.'extras.id_extras  '.
			' WHERE entity_type = \'extra\') '.
			
			' UNION '.
			
			'(SELECT DISTINCT #__usergroups.id, '.$this->_table_prefix.'rate_overrides.*, #__usergroups.title as user_group,  '.
			$this->_table_prefix.'resources.name as res_name, "" as extra_name, '.
			$this->_table_prefix.'services.name as srv_name, "" as seat_name '.
			' FROM '.$this->_table_prefix.'rate_overrides  '.
			' INNER JOIN #__usergroups  '.
			' ON #__usergroups.id = '.$this->_table_prefix.'rate_overrides.group_id  '.
			' INNER JOIN '.$this->_table_prefix.'services  '.
			' ON '.$this->_table_prefix.'rate_overrides.entity_id = '.$this->_table_prefix.'services.id_services  '.
			' INNER JOIN '.$this->_table_prefix.'resources  '.
			' ON '.$this->_table_prefix.'services.resource_id = '.$this->_table_prefix.'resources.id_resources  '.
			' WHERE entity_type = \'service\') '.
			
			' UNION '.
			
			'(SELECT DISTINCT #__usergroups.id, '.$this->_table_prefix.'rate_overrides.*, #__usergroups.title as user_group,  '.
			'"" as res_name, "" as extra_name, "" as srv_name, '.
			$this->_table_prefix.'seat_types.seat_type_label as seat_name '.
			' FROM '.$this->_table_prefix.'rate_overrides  '.
			' INNER JOIN #__usergroups  '.
			' ON #__usergroups.id = '.$this->_table_prefix.'rate_overrides.group_id  '.
			' INNER JOIN '.$this->_table_prefix.'seat_types  '.
			' ON '.$this->_table_prefix.'rate_overrides.entity_id = '.$this->_table_prefix.'seat_types.id_seat_types   '.
			' WHERE entity_type = \'seat\')) as temp '.
			' WHERE id_rate_overrides = '. $this->_id_rate_overrides;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			//print_r($this->_data);
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the rate_overrides data
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
			$detail->id_rate_overrides	= 0;
			$detail->entity_type = "";
			$detail->entity_id = 0;
			$detail->group_id = 0;
			$detail->rate_override = null;
			$detail->rate_unit_override = null;
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
	 * Method to store the rate_overrides text
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		//DEVNOTE: Load table class from com_rsappt_pro3/tables/rate_overrides_detail.php	
		$row = $this->getTable();

		// Bind the form fields to the rate_overrides table
		if (!$row->bind($data)) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}

		//DEVNOTE: Make sure the rate_overrides table is valid
		//JTable return always true but there is space to put
		//our custom check method
/*		if (!$row->check()) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}*/

		// Store the rate_overrides table to the database
		if (!$row->store()) {
			//$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	
		/**
	 * Method to (un)publish a rate_overrides
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

			$query = 'UPDATE '.$this->_table_prefix.'rate_overrides'
				. ' SET published = ' . intval( $publish )
				. ' WHERE id_rate_overrides IN ( '.$cids.' )'
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
	 * Method to move a rate_overrides_detail
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function saveorder($cid = array(), $order)
	{
		//DEVNOTE: Load table class from com_sv_ser/tables/rate_overrides_detail.php		
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
	 * Method to move a rate_overrides 
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function move($direction)
	{
	//DEVNOTE: Load table class from com_sv_ser/tables/rate_overrides_detail.php	
		$row = $this->getTable();
	//DEVNOTE: we need to pass here id of rate_overrides detail 		
		if (!$row->load($this->_id_rate_overrides)) {
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
			$query = 'DELETE FROM '.$this->_table_prefix.'rate_overrides WHERE id_rate_overrides IN ( '.$cids.' )';
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
