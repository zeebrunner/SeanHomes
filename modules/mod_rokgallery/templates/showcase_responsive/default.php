<?php
 /**
  * @version   $Id: default.php 18763 2014-02-14 18:22:13Z djamil $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

  $slice_0 = $passed_params->slices[0];
  $slices_size = array('width' => $slice_0->xsize, 'height' => $slice_0->ysize);
  $container_size = array(
	'width' => $slices_size['width'] + ($passed_params->showcase_responsive_imgpadding * 2) . 'px',
	'height' => $slices_size['height'] + ($passed_params->showcase_responsive_imgpadding * 2) . 'px'
  );

?>

<div id="rg-<?php echo $passed_params->moduleid; ?>" class="rg-sc layout-<?php echo $passed_params->showcase_responsive_image_position; ?><?php if ($passed_params->showcase_responsive_arrows=='onhover'):?> onhover<?php endif; ?>">
	<div class="rg-scr-main">
		<div class="rg-scr-slide">
			<?php if ($passed_params->showcase_responsive_image_position == "left") : ?>
				<div class="rg-scr-slice-container">
					<div class="rg-scr-img-padding" style="padding: <?php echo $passed_params->showcase_responsive_imgpadding; ?>px;">
						<div class="rg-scr-img-list" style="max-width: <?php echo $slices_size['width']; ?>px;">
							<?php foreach ($passed_params->slices as $slice): ?>
							<div class="rg-scr-slice">
							<?php if ($passed_params->link!='none'):?>
			                	<a <?php echo $slice->rel;?> href="<?php echo $slice->link;?>">
			                <?php endif;?>
			            		<img title="" alt="<?php echo $slice->title;?>" src="<?php echo $slice->imageurl;?>" style="max-width: 100%;height: auto;" />
			                <?php if ($passed_params->link!='none'):?>
			            		</a>
			            	<?php endif;?>
			            	</div>
		    		    	<?php endforeach; ?>
						</div>
					</div>
				</div>
				<?php if (($passed_params->title)||($passed_params->caption)): ?>
				<div class="rg-scr-content">
					<?php foreach ($passed_params->slices as $slice): ?>
					<?php
						$slice_title = ($slice->title)?$slice->title:'';
		            	$slice_caption = ($slice->caption)?$slice->caption:'';
					?>
					<div class="rg-scr-info">
						<?php if ($passed_params->title):?>
			            <h1 class="rg-scr-title"><span class="rg-scr-title-span"><?php echo $slice_title;?></span></h1>
			            <?php endif;?>


			            <?php if ($passed_params->caption):?>
			            <div class="rg-scr-desc-surround">
			            	<span class="rg-scr-caption"><?php echo $slice_caption;?></span>
			            </div>
			            <?php endif;?>
			         </div>
					<?php endforeach; ?>
				</div>
				<?php endif;?>
			<?php elseif ($passed_params->showcase_responsive_image_position == "right") : ?>
				<?php if (($passed_params->title)||($passed_params->caption)): ?>
				<div class="rg-scr-content">
					<?php foreach ($passed_params->slices as $slice): ?>
					<?php
						$slice_title = ($slice->title)?$slice->title:'';
		            	$slice_caption = ($slice->caption)?$slice->caption:'';
					?>
					<div class="rg-scr-info">
						<?php if ($passed_params->title):?>
			            <h1 class="rg-scr-title"><span class="rg-scr-title-span"><?php echo $slice_title;?></span></h1>
			            <?php endif;?>


			            <?php if ($passed_params->caption):?>
			            <div class="rg-scr-desc-surround">
			            	<span class="rg-scr-caption"><?php echo $slice_caption;?></span>
			            </div>
			            <?php endif;?>
			         </div>
					<?php endforeach; ?>
				</div>
				<?php endif;?>
				<div class="rg-scr-slice-container">
					<div class="rg-scr-img-padding" style="padding: <?php echo $passed_params->showcase_responsive_imgpadding; ?>px;">
						<div class="rg-scr-img-list" style="max-width: <?php echo $slices_size['width']; ?>px;">
							<?php foreach ($passed_params->slices as $slice): ?>
							<div class="rg-scr-slice">
							<?php if ($passed_params->link!='none'):?>
			                	<a <?php echo $slice->rel;?> href="<?php echo $slice->link;?>">
			                <?php endif;?>
			            		<img title="" alt="<?php echo $slice->title;?>" src="<?php echo $slice->imageurl;?>" style="max-width: 100%;height: auto;" />
			                <?php if ($passed_params->link!='none'):?>
			            		</a>
			            	<?php endif;?>
			            	</div>
		    		    	<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<?php if ($passed_params->autoplay_enabled == 2): ?>
		<div class="rg-scr-loader">
			<div class="rg-scr-progress"></div>
		</div>
		<?php endif; ?>

	</div>

	<?php if ($passed_params->showcase_responsive_arrows!='no'):?>
	<div class="rg-scr-controls">
		<span class="prev"></span>
		<span class="next"></span>
	</div>
	<?php endif;?>

</div>
