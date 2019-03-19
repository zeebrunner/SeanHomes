<?php
/**
 * @version   $Id: rockettheme_login.php 11288 2013-06-06 15:36:57Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_PLATFORM') or die;
$plugin_root_url =  rokupdater_get_plugin_url();
$site_root_url = rokupdater_get_root_url();
?>
<!doctype html>
<html>
	<head>
		<title>
			Rockettheme Login page
		</title>
		<link rel="stylesheet" href="<?php echo $plugin_root_url.'/assets/css/rokupdater-j25.css';?>" type="text/css">
		<script src="<?php echo $site_root_url.'/media/system/js/mootools-core.js';?>" type="text/javascript"></script>
		<script src="<?php echo $plugin_root_url.'/fields/assets/ajax/js/moofx.js';?>" type="text/javascript"></script>
		<script src="<?php echo $plugin_root_url.'/fields/assets/ajax/js/Auth.js';?>" type="text/javascript"></script>
	</head>
	<body class="nopadding">
		<div class="rokupdater-login">
			<span class="rok-logo"></span>
			<h1><?php echo JText::_('RocketTheme Login'); ?></h1>
			<form name="login" action="<?php echo $plugin_root_url.'/ajax.php?ajax_model=authenticate';?>" autocomplete="off">
				<div class="inputfield username">
				<input type="text" name="userid" placeholder="Username" />
				</div>
				<div class="inputfield password">
					<input type="password" name="pswrd" placeholder="Password" />
				</div>
				<div class="textlinks">
					<a href="http://www.rockettheme.com/component/user/remind" class="forgot-user" target="_blank"><?php echo JText::_('Forgot Username?'); ?></a>
					<a href="http://www.rockettheme.com/component/user/reset" class="forgot-password" target="_blank"><?php echo JText::_('Forgot Password?'); ?></a>
				</div>
				<a class="btn btn-login" data-auth><?php echo JText::_('Authenticate'); ?></a>
			</form>
			<div class="rok-errors" data-error-msg></div>
			<div class="spinner" data-spinner></div>
		</div>
	</body>
</html>
