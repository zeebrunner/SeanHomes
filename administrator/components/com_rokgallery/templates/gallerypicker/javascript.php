<?php
 /**
  * @version   $Id: javascript.php 27022 2015-02-25 17:35:57Z matias $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
 
$url = JURI::root(true) . '/administrator/index.php?option=com_rokgallery&task=ajax&format=raw';
echo "
	window.addEvent('domready', function(){
		new GalleryPicker('rokgallerypicker', {url: RokGallerySettings.modal_url});
	});
";
