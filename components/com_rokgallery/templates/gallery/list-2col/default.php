<?php
 /**
  * @version   $Id: default.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
?>

<?php if ($that->show_page_heading): ?>
<h1><?php echo $that->page_heading; ?></h1>
<?php endif; ?>

<div class="rg-list-view-container<?php echo $that->pageclass_sfx; ?>">
    <?php echo RokCommon_Composite::get($that->context)->load('header.php', array('that' => $that));?>
    <div class="rg-list-view rg-col2">
        <?php
        foreach ($that->images as $that->image):
            $that->slice = $that->slices[$that->image->id];
            echo RokCommon_Composite::get($that->context)->load('default_row.php', array('that' => $that));
            $that->item_number++;
        endforeach;
        ?>
    </div>
</div>
<?php echo RokCommon_Composite::get($that->context)->load('pagination.php', array('that' => $that));?>