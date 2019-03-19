<?php
/**
 * @version   $Id: ajax.php 11339 2013-06-10 22:14:47Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
define("DEST_LOGFILE", "3");

@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');


function get_absolute_path($path)
{
	$path      = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	$parts     = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
	$absolutes = array();
	foreach ($parts as $part) {
		if ('.' == $part) continue;
		if ('..' == $part) {
			array_pop($absolutes);
		} else {
			$absolutes[] = $part;
		}
	}
	$prefix = '';
	if (DIRECTORY_SEPARATOR == '/') $prefix = '/';
	return $prefix . implode(DIRECTORY_SEPARATOR, $absolutes);
}

function rokupdater_ajax_error_handler($errno, $errstr, $errfile, $errline)
{

	if (!(error_reporting() & $errno)) {
		// This error code is not included in error_reporting
		return;
	}

	switch ($errno) {
		case E_USER_ERROR:
			// Write the error to our log file
			error_log("Error: $errstr \n Fatal error on line $errline in file $errfile \n", DEST_LOGFILE, JPATH_ROOT . '/logs/rokupdater_ajax.log');
			break;

		case E_USER_WARNING:
			// Write the error to our log file
			error_log("Warning: $errstr \n in $errfile on line $errline \n", DEST_LOGFILE, JPATH_ROOT . '/logs/rokupdater_ajax.log');
			break;

		case E_USER_NOTICE:
			// Write the error to our log file
			error_log("Notice: $errstr \n in $errfile on line $errline \n", DEST_LOGFILE, JPATH_ROOT . '/logs/rokupdater_ajax.log');
			break;

		default:
			// Write the error to our log file
			error_log("Unknown error [#$errno]: $errstr \n in $errfile on line $errline \n", DEST_LOGFILE, JPATH_ROOT . '/logs/rokupdater_ajax.log');
			break;
	}

	/* Don't execute PHP internal error handler */
	return true;
}

function rokupdater_ajax_exception_handler(Exception $exception)
{
	error_log('Uncaught Exception: ' . $exception->getMessage() . '[' . $exception->getCode() . '] File: ' . $exception->getFile() . ' Line: ' . $exception->getLine(), DEST_LOGFILE, JPATH_ROOT . '/logs/rokupdater_ajax.log');
}

function rokupdater_get_root_url()
{
	$config = JFactory::getConfig();
	if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI'])) {
		$script_name = $_SERVER['PHP_SELF'];
	} else {
		// Others
		$script_name = $_SERVER['SCRIPT_NAME'];
	}
	$path = rtrim(dirname(dirname(dirname(dirname($script_name)))), '/\\');
	return $path;
}

function rokupdater_get_plugin_url()
{
	$config = JFactory::getConfig();

	if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI'])) {
		$script_name = $_SERVER['PHP_SELF'];
	} else {
		// Others
		$script_name = $_SERVER['SCRIPT_NAME'];
	}
	$path = rtrim(dirname($script_name), '/\\');
	return $path;
}


//we know the path from root so we remove it to get the root
// '/../../..' for plugins; '/../..' for modules
define('_JEXEC', 1);
define('JPATH_BASE', get_absolute_path(dirname($_SERVER['SCRIPT_FILENAME']) . '/../../..'));
define('JPATH_MYWEBAPP', dirname($_SERVER['SCRIPT_FILENAME']));

require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');
require_once(JPATH_LIBRARIES . '/joomla/factory.php');
require_once(JPATH_LIBRARIES . '/import.php');
require_once(JPATH_LIBRARIES . '/cms.php');

set_error_handler('rokupdater_ajax_error_handler');
set_exception_handler('rokupdater_ajax_exception_handler');
// Pre-Load configuration.
ob_start();
require_once JPATH_CONFIGURATION . '/configuration.php';
ob_end_clean();

// Now that you have it, use jimport to get the specific packages your application needs.
jimport('joomla.environment.uri');
jimport('joomla.utilities.date');
jimport('joomla.utilities.utility');
jimport('joomla.event.dispatcher');
jimport('joomla.utilities.arrayhelper');
jimport('joomla.user.user');

//It's an application, so let's get the application helper.
jimport('joomla.application.helper');

$app = JFactory::getApplication('administrator');
$app->initialise();

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/lib/include.php');
$container = RokUpdater_ServiceProvider::getInstance();
/** @var RokUpdater_Ajax $ajax_handler */
$ajax_handler = $container->ajax_handler;
$input        = $app->input;

try {
	$model = $input->get('ajax_model', null, 'word');
	if (null == $model) throw new RokUpdater_Exception('No Ajax Model passed.');
	$results = $ajax_handler->run($model);
	if ($results !== false) {
		echo $results;
	}
} catch (Exception $e) {
	$result = new RokCommon_Ajax_Result();
	$result->setAsError();
	$result->setMessage($e->getMessage());
	echo json_encode($result);
}
