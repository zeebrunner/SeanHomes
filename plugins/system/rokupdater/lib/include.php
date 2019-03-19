<?php
/**
 * @version   $Id: include.php 8934 2013-03-29 19:17:23Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
if (!defined('ROKUPDATER_LIB')) {
	if (!function_exists('rockettheme_cleanPath')) {
		function rockettheme_cleanPath($path)
		{
			if (!preg_match('#^/$#', $path)) {
				$path = preg_replace('#[/\\\\]+#', '/', $path);
				$path = preg_replace('#/$#', '', $path);
			}
			return $path;
		}
	}

	if (!defined('ROKUPDATER_LIB_PATH')) define('ROKUPDATER_LIB_PATH', rockettheme_cleanPath(dirname(__FILE__)));
	if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

	// Check to see if there is a requiments file and run it.
	// Catch any exceptions and log them as errors.
	$requirements_file = rockettheme_cleanPath(ROKUPDATER_LIB_PATH . '/requirements.php');
	if (file_exists($requirements_file)) {
		try {
			require_once($requirements_file);
		} catch (Exception $e) {
			return;
		}
	}

	define('ROKUPDATER_LIB', '1.0.8');
	define('ROKUPDATER_LIB_DEBUG', false);

	$loader = require rockettheme_cleanPath(ROKUPDATER_LIB_PATH.'/vendor/autoload.php');
	//$loader->add('rokoauth',dirname(__FILE__));

	// load up the supporting functions
	$functions_path = rockettheme_cleanPath(ROKUPDATER_LIB_PATH . '/functions.php');
	if (file_exists($functions_path)) {
		require_once($functions_path);
	}
}
return "ROKUPDATER_LIB_INCLUDED";