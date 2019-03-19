<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
define('_JEXEC', 1);
//defined('_JEXEC') or die('Restricted access');

/*
if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

// Execute the application.
$app->execute();*/

ini_set('display_errors', '1');
error_reporting(E_ALL);
print "hi";


$db = JFactory::getDBO();

$query = "SELECT * FROM delme";
$db->setQuery($query);

$result = $db->loadObjectList();

var_dump($result);
foreach($result as $res){
	print $res->email."<br>";

}