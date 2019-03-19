<?php
/**
  * @version   $Id: default_row.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
?>
<?php if ($that->row_entry_number % $that->items_per_row == 0): ?>
    <div class="gallery-row clearfix">
<?php endif; ?>
<?php echo RokCommon_Composite::get('rokgallery.default')->load('default_file.php', array('that'=>$that)); ?>
<?php if ($that->row_entry_number % $that->items_per_row == (($that->items_per_row-1)%$that->items_per_row) || ($that->item_number == $that->items_to_be_rendered)): ?>
    <div class="clr"></div>
		</div>
<?php endif;?>
