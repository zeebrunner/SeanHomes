<?php
/**
* @version   $Id: social.php 11529 2013-06-17 14:15:23Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureSocial extends GantryFeature {
	var $_feature_name = 'social';

	function init(){
		global $gantry;
	}

	function render($position="") {
		ob_start();
		global $gantry;
		?>
		<div class="rt-social-buttons">
			<span class="rt-social-header"><?php echo JText::_('EVERYTHING_SEAN'); ?></span>

			<?php if (($gantry->get('social-button-1-icon') != "") and ($gantry->get('social-button-1-link') != "")) : ?>
			<a class="social-button rt-social-button-1" href="<?php echo $gantry->get('social-button-1-link'); ?>" target="new" onclick="_gaq.push(['_trackEvent', 'Menu SM Button', 'Button Pressed', 'Menu-Facebook']);">
				<span class="<?php echo $gantry->get('social-button-1-icon'); ?>"></span>
			</a>
			<?php endif; ?>

			<?php if (($gantry->get('social-button-2-icon') != "") and ($gantry->get('social-button-2-link') != "")) : ?>
			<a class="social-button rt-social-button-2" href="<?php echo $gantry->get('social-button-2-link'); ?>" target="new" onclick="_gaq.push(['_trackEvent', 'Menu SM Button', 'Button Pressed', 'Menu-Twitter']);">
				<span class="<?php echo $gantry->get('social-button-2-icon'); ?>"></span>
			</a>
			<?php endif; ?>

			<?php if (($gantry->get('social-button-3-icon') != "") and ($gantry->get('social-button-3-link') != "")) : ?>
			<a class="social-button rt-social-button-3" href="<?php echo $gantry->get('social-button-3-link'); ?>" target="new" onclick="_gaq.push(['_trackEvent', 'Menu SM Button', 'Button Pressed', 'Menu-Youtube']);">
				<span class="<?php echo $gantry->get('social-button-3-icon'); ?>"></span>
			</a>
			<?php endif; ?>

			<?php if (($gantry->get('social-button-4-icon') != "") and ($gantry->get('social-button-4-link') != "")) : ?>
			<a class="social-button rt-social-button-4" href="<?php echo $gantry->get('social-button-4-link'); ?>" target="new" onclick="_gaq.push(['_trackEvent', 'Menu SM Button', 'Button Pressed', 'Menu-Instagram']);">
				<span class="<?php echo $gantry->get('social-button-4-icon'); ?>"></span>
			</a>
			<?php endif; ?>

			<?php if (($gantry->get('social-button-5-icon') != "") and ($gantry->get('social-button-5-link') != "")) : ?>
			<a class="social-button rt-social-button-5" href="<?php echo $gantry->get('social-button-5-link'); ?>">
				<span class="<?php echo $gantry->get('social-button-5-icon'); ?>"></span>
			</a>
			<?php endif; ?>

			<?php if (($gantry->get('social-button-6-icon') != "") and ($gantry->get('social-button-6-link') != "")) : ?>
			<a class="social-button rt-social-button-6" href="<?php echo $gantry->get('social-button-6-link'); ?>" target="new">
				<span class="<?php echo $gantry->get('social-button-6-icon'); ?>"></span>
			</a>
			<?php endif; ?>															

			<div class="clear"></div>	
		</div>
		<?php
		return ob_get_clean();
	}
}
