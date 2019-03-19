<?php
/**
 * @version   $Id: Ajax.php 10214 2013-05-13 04:37:51Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokUpdater_Ajax
{
	/**
	 *
	 */
	const DEFAULT_MODEL_CONTEXT_PREFIX = 'ajax_model_';

	/**
	 * @param $modelname
	 *
	 * @internal param string $encoding
	 *
	 * @return string
	 */
	public function run($modelname)
	{
		// Set up an independent AJAX error handler
		set_error_handler(array($this, 'error_handler'));
		set_exception_handler(array($this, 'exception_handler'));

		while (@ob_end_clean()) ; // clean any pending output buffers
		ob_start(); // start a fresh one
		try {
			$container            = RokUpdater_ServiceProvider::getInstance();
			$model_container_name = self::DEFAULT_MODEL_CONTEXT_PREFIX . strtolower($modelname);
			/** @var RokUpdater_Ajax_IModel $model */
			$model = $container->$model_container_name;
			// set the result to the run
			$result = $model->run();
		} catch (Exception $ae) {
			$result          = new stdClass();
			$result->status  = "error";
			$result->message = $ae->getMessage();
			$result          = json_encode($result);
		}
		// restore normal error handling;
		restore_error_handler();
		restore_exception_handler();
		return $result;
	}

	/**
	 * @static
	 *
	 * @param Exception $exception
	 */
	public static function exception_handler(Exception $exception)
	{
		echo "Uncaught Exception: " . $exception->getMessage() . "\n";
		echo '[' . $exception->getCode() . '] File: ' . $exception->getFile() . ' Line: ' . $exception->getLine();
	}

	/**
	 * @static
	 *
	 * @param $errno
	 * @param $errstr
	 * @param $errfile
	 * @param $errline
	 *
	 * @return bool
	 * @throws RokCommon_Ajax_Exception
	 */
	public function error_handler($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno)) {
			// This error code is not included in error_reporting
			return;
		}

		switch ($errno) {
			case E_USER_ERROR:
				echo "ERROR [$errno] $errstr\n";
				echo "  Fatal error on line $errline in file $errfile";
				echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")\n";
				echo "Aborting...\n";
				exit(1);
				break;
			case E_USER_WARNING:
				echo "WARNING [$errno] $errstr\n";
				break;
			case E_USER_NOTICE:
				echo "NOTICE [$errno] $errstr\n";
				break;
			case E_STRICT:
				return false;
				break;
			default:
				throw new RokUpdater_Exception("UNHANDLED ERROR [$errno] $errstr $errfile:$errline");
				break;
		}

		/* Don't execute PHP internal error handler */
		return true;
	}

	/**
	 * @param $str
	 *
	 * @return string
	 */
	public function smartStripSlashes($str)
	{
		$cd1 = substr_count($str, "\"");
		$cd2 = substr_count($str, "\\\"");
		$cs1 = substr_count($str, "'");
		$cs2 = substr_count($str, "\\'");
		$tmp = strtr($str, array(
		                        "\\\"" => "",
		                        "\\'"  => ""
		                   ));
		$cb1 = substr_count($tmp, "\\");
		$cb2 = substr_count($tmp, "\\\\");
		if ($cd1 == $cd2 && $cs1 == $cs2 && $cb1 == 2 * $cb2) {
			return strtr($str, array(
			                        "\\\"" => "\"",
			                        "\\'"  => "'",
			                        "\\\\" => "\\"
			                   ));
		}
		return $str;
	}
}
