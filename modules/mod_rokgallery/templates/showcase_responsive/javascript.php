<?php
 /**
  * @version   $Id: javascript.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

echo "window.addEvent('domready', function(){
	new RokGallery.ShowcaseResponsive('rg-".$passed_params->moduleid."', {
		animation: '".$passed_params->showcase_responsive_animation_type."',
		duration: ".$passed_params->showcase_responsive_animation_duration.",
		autoplay: {
			enabled: ".$passed_params->showcase_responsive_autoplay_enabled.",
			delay: ".$passed_params->showcase_responsive_autoplay_delay."
		},
		imgpadding: ".$passed_params->showcase_responsive_imgpadding.",
		captions:{
			animation: '".$passed_params->showcase_responsive_captionsanimation."'
		}
	});
});";
