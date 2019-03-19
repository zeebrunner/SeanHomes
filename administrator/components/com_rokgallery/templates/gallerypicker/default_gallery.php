<?php
/**
  * @version   $Id: default_gallery.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

$gallery = $that->gallery;
$count = 'total-slices-' . count($gallery->Slices);
?>

<li data-id="gallery-<?php echo $gallery->id; ?>" class="gallery <?php echo $count;?>">
    <div class="wrapper">
        <?php echo RokCommon_Composite::get('rokgallery.gallerypicker')->load('default_gallery_preview.php', array('that'=>$that)); ?>
        <div class="clr"></div>
    </div>
    <div class="gallery-title">
        <span><?php echo $gallery->name; ?></span>
    </div>
    <div class="clr"></div>
</li>
