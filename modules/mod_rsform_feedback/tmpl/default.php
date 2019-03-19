<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<style type="text/css">
#rsform_feedback_<?php echo $module->id; ?> {
	z-index: 999;
<?php if ($position == 'left') { ?>
	position: fixed;
	top: 45%;
	left: 0;
<?php } elseif ($position == 'right') { ?>
	position: fixed;
	top: 45%;
	right: 0;
<?php } elseif ($position == 'top' || $position == 'bottom') { ?>
	width: <?php echo $size; ?>px;
	margin: 0 auto;
<?php } ?>
}

#rsform_feedback_<?php echo $module->id; ?> img {
	background-color: <?php echo $bg_color; ?>;
	border: solid 2px <?php echo $border_color; ?>;
	padding: 5px;
}

#rsform_feedback_container_<?php echo $module->id; ?> {
<?php if ($position == 'top') { ?>
	width: 100%;
	position: fixed;
	z-index: 1000;
	top: 0;
<?php } elseif ($position == 'bottom') { ?>
	width: 100%;
	position: fixed;
	z-index: 1000;
	bottom: 0;
<?php } ?>
}
</style>
	
<div id="rsform_feedback_container_<?php echo $module->id; ?><?php echo $sfx; ?>">
	<div id="rsform_feedback_<?php echo $module->id; ?><?php echo $sfx; ?>">
	<a href="<?php echo JRoute::_($form_url); ?>" <?php echo $attr; ?>><img src="<?php echo JRoute::_($image_url); ?>" alt="<?php echo htmlentities($params->get('string'), ENT_COMPAT, 'utf-8'); ?>" /></a>
	</div>
</div>