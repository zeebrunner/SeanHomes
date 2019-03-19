<?php
/**
  * @version   $Id: default.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
?>

<ul>
	<?php foreach($slices as $slice): ?>
	<li data-id="<?php echo $slice->id;?>"><img src="<?php echo $slice->miniadminthumburl;?>" width="<?php echo (RokGallery_Config::DEFAULT_MINI_ADMIN_THUMB_XSIZE); ?>" height="<?php echo (RokGallery_Config::DEFAULT_MINI_ADMIN_THUMB_YSIZE); ?>" alt="" title="" /></li>
	<?php endforeach; ?>
</ul>

<div class="clr"></div>