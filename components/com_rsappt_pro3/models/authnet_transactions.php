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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class adminModelauthnet_transactions extends JModelLegacy
{

	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $_table_prefix = null;
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		global $context;
	  	$mainframe = JFactory::getApplication();
	  
		//initialize class property
	    $this->_table_prefix = '#__sv_apptpro3_';	
	  
		//DEVNOTE: Get the pagination request variables
		$limit			= $mainframe->getUserStateFromRequest( $context.'an_limit', 'an_limit', 0, 0);
		$limitstart = $mainframe->getUserStateFromRequest( $context.'an_limitstart', 'an_limitstart', 0 );

		$limit			= $mainframe->getUserStateFromRequest( $context.'an_limit', 'an_limit', 0, 0);
		$limitstart = $mainframe->getUserStateFromRequest( $context.'an_limitstart', 'an_limitstart', 0 );

		$this->setState('an_limit', $limit);
		$this->setState('an_limitstart', $limitstart);

	}
	
	
	/**
	 * Method to get a authnet_transactions data
	 *
	 * this method is called from the owner VIEW by VIEW->get('Data');
	 * - get list of all authnet_transactions for the current data page.
	 * - pagination is spec. by variables limitstart,limit.
	 * - ordering of list is build in _buildContentOrderBy  	 	 	  	 
	 * @since 1.5
	 */
	function getData()
	{
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('an_limitstart'), $this->getState('an_limit'));
		}

		return $this->_data;
	}


	/**
	 * Method to get the total number of authnet_transactions items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object for the authnet_transactions
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('an_limitstart'), $this->getState('an_limit') );
		}

		return $this->_pagination;
	}
  	
	function _buildQuery()
	{
		$orderby	= $this->_buildContentOrderBy();
		$query = ' SELECT * FROM '.$this->_table_prefix.'authnet_transactions ';
		$filter = "WHERE ";	

		global $context;
	  	$mainframe = JFactory::getApplication();

		$filter_an_startdate = $mainframe->getUserStateFromRequest( $context.'filter_an_startdate', 'filter_an_startdate', date("Y-m-d", strtotime('first day of last month')) );
		$filter_an_enddate = $mainframe->getUserStateFromRequest( $context.'filter_an_enddate',  'filter_an_enddate', '' );		

		if($filter_an_startdate != ""){
			$filter .= " stamp>='".$filter_an_startdate."' ";
		}
		if($filter_an_enddate != ""){
			if($filter != "WHERE "){
				$filter .= " AND ";
			}
			$filter .= " stamp<='".$filter_an_enddate."' ";
		}
		if($filter != "WHERE "){
			$query .= $filter;
		}
		$query .= $orderby;
		
		//echo $query;
		//exit;

		return $query;
	}
	

	function _buildContentOrderBy()
	{
		global $context;
	  	$mainframe = JFactory::getApplication();

		$filter_order     = $mainframe->getUserStateFromRequest( $context.'an_filter_order',      'an_filter_order', 	  'stamp' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'an_filter_order_Dir',  'an_filter_order_Dir', '' );		

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , ordering ';			

		return $orderby;
	}
	
}
?>
