<?php
 /**
  * @version   $Id: javascript.php 18707 2014-02-12 03:13:07Z djamil $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

echo "var rokgallery_slideshow;window.addEvent('domready', function(){
	rokgallery_slideshow = new RokGallery.Slideshow('rg-".$passed_params->moduleid."', {
		animation: '".$passed_params->animation_type."',
		duration: ".$passed_params->animation_duration.",
		autoplay: {
			enabled: ".$passed_params->autoplay_enabled.",
			delay: ".$passed_params->autoplay_delay."
		}
	});
});

if (Browser.chrome || Browser.safari){
	window.addEvent('load', function(){
        if (rokgallery_slideshow.scrollerContainer) rokgallery_slideshow.refreshThumbs(rokgallery_slideshow.current);
		(function(){
            if (rokgallery_slideshow.scrollerContainer) rokgallery_slideshow.refreshThumbs(rokgallery_slideshow.current);
        }).delay(100);
	});
}
";
?>
