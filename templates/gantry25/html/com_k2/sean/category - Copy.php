<?php
/**
 * @version		$Id: category.php 1812 2013-01-14 18:45:06Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

?>

<!-- Start K2 Category Layout -->
<div id="k2Container" class="itemListView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')): ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<?php if($this->params->get('catFeedIcon')): ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

	<?php if(isset($this->category) || ( $this->params->get('subCategories') && isset($this->subCategories) && count($this->subCategories) )): ?>
	<?php //echo($this->category->parent); ?>
	<!-- Blocks for current category and subcategories -->
	<div class="itemListCategoriesBlock <?php echo strtolower(preg_replace('/\s+/', '', K2HelperUtilities::cleanHtml($this->category->name))) ; ?>">

		<?php if(isset($this->category) && ( $this->params->get('catImage') || $this->params->get('catTitle') || $this->params->get('catDescription') || $this->category->event->K2CategoryDisplay )): ?>
			<?php if($this->category->parent==2 || $this->category->parent==3) : ?>
			<?php $categoryHeaderWidth = 95; ?>
			<!-- Category block -->
			<div class="itemListCategory childOfCat<?php echo $this->category->parent; ?> <?php echo strtolower(preg_replace('/\s+/', '', K2HelperUtilities::cleanHtml($this->category->name))) ; ?>" style="width:<?php echo $categoryHeaderWidth; ?>%; float:left;">
				<div class="itemListCategoryInner">
					<?php if(isset($this->addLink)): ?>
					<!-- Item add link -->
					<span class="catItemAddLink">
						<a class="modal" rel="{handler:'iframe',size:{x:990,y:650}}" href="<?php echo $this->addLink; ?>">
							<?php echo JText::_('K2_ADD_A_NEW_ITEM_IN_THIS_CATEGORY'); ?>
						</a>
					</span>
					<?php endif; ?>
		
					<?php if($this->params->get('catImage') && $this->category->image): ?>
					<!-- Category image -->
					<img alt="<?php echo K2HelperUtilities::cleanHtml($this->category->name); ?>" src="<?php echo $this->category->image; ?>" style="width:<?php echo $this->params->get('catImageWidth'); ?>px; height:auto;" />
					<?php endif; ?>
		
					<?php if($this->params->get('catTitle')): ?>
					<!-- Category title -->
					<h2><?php echo $this->category->name; ?><?php if($this->params->get('catTitleItemCounter')) echo ' ('.$this->pagination->total.')'; ?></h2>
					<?php endif; ?>
		
					<?php if($this->params->get('catDescription')): ?>
					<!-- Category description -->
					<?php echo $this->category->description; ?>
					<?php endif; ?>
		
					<!-- K2 Plugins: K2CategoryDisplay -->
					<?php echo $this->category->event->K2CategoryDisplay; ?>
		
					<div class="clr"></div>
				</div>
			</div>
			<?php if ($this->category->parent == '2') {
				$linkBack = "/sean-homes/shop.html";
			} else {
				$linkBack = "/sean-homes/learn.html";
			} ?>
			<div class="linkBack onCat<?php echo $this->category->parent; ?>" style="width:<?php echo (100 - $categoryHeaderWidth); ?>%" onclick="window.location.href='<?php echo $linkBack; ?>'">
			<?php if ($this->category->parent == '2'): ?>
				<h3><a href="<?php echo $linkBack; ?>">Shop</a></h3>
			<?php else: ?>
				<h3><a href="<?php echo $linkBack; ?>">Learn</a></h3>
			<?php endif; ?>
			</div>

			
			<?php endif; ?>
		<?php endif; ?>

		<?php if($this->params->get('subCategories') && isset($this->subCategories) && count($this->subCategories)): ?>
		<!-- Subcategories -->
		<div class="itemListSubCategories">
			<?php if ($this->category->id == '2' || $this->category->id == "3") {
				$categoriesBlockWidth = 95;
			} else {
				$categoriesBlockWidth = 100;	
			}?>
			<?php foreach($this->subCategories as $key=>$subCategory): ?>

			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('subCatColumns'))==0))
				$lastContainer= ' subCategoryContainerLast';
			else
				$lastContainer='';
			?>
			<?php if($subCategory->id == '9') {
				$subCategoryLink = '/Blog';//'index.php?option=com_easyblog&view=latest&Itemid=133';	
			} else {
				$subCategoryLink =	$subCategory->link;
			}?>
			<div onclick="window.location.href='<?php echo $subCategoryLink; ?>';" class="subCategoryContainer<?php echo $lastContainer; ?> <?php echo strtolower(preg_replace('/\s+/', '', K2HelperUtilities::cleanHtml($subCategory->name))) ; ?>"<?php echo (count($this->subCategories)==1) ? '' : ' style="width:'.number_format($categoriesBlockWidth/$this->params->get('subCatColumns'), 1).'%;"'; ?>>
				<div class="subCategory">
					<?php if($this->params->get('subCatImage') && $subCategory->image): ?>
					<!-- Subcategory image -->
					<a class="subCategoryImage" href="<?php echo $subCategoryLink; ?>">
						<img alt="<?php echo K2HelperUtilities::cleanHtml($subCategory->name); ?>" src="<?php echo $subCategory->image; ?>" />
					</a>
					<?php endif; ?>

					<?php if($this->params->get('subCatTitle')): ?>
					<!-- Subcategory title -->
					<h2><a href="<?php echo $subCategoryLink; ?>"><?php echo K2HelperUtilities::cleanHtml($subCategory->name); ?><?php if($this->params->get('subCatTitleItemCounter')) echo ' ('.$subCategory->numOfItems.')'; ?></a></h2>
					<?php endif; ?>

					<?php if($this->params->get('subCatDescription')): ?>
					<!-- Subcategory description -->
					<?php echo $subCategory->description; ?>
					<?php endif; ?>


					<div class="clr"></div>
				</div>
			</div>
			<?php endforeach; ?>
			<?php if ($this->category->id == '2' || $this->category->id == "3"): ?> 
				<?php if ($this->category->id == '2') {
					$linkBack = "/sean-homes/shop.html";
				} else {
					$linkBack = "/sean-homes/learn.html";
				} ?>
				<div class="linkBack onCat<?php echo $this->category->id; ?>" style="width:<?php echo (100 - $categoriesBlockWidth); ?>%" onclick="window.location.href='<?php echo $linkBack; ?>'">
				<?php if ($this->category->id == '2'): ?>
					<h3><a href="<?php echo $linkBack; ?>">Shop</a></h3>
				<?php else: ?>
					<h3><a href="<?php echo $linkBack; ?>">Learn</a></h3>
				<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

	</div>
	<?php endif; ?>



	<?php if((isset($this->leading) || isset($this->primary) || isset($this->secondary) || isset($this->links)) && (count($this->leading) || count($this->primary) || count($this->secondary) || count($this->links))): ?>
	<!-- Item list -->
	<div class="itemList <?php echo strtolower(preg_replace('/\s+/', '', K2HelperUtilities::cleanHtml($this->category->name))) ; ?>">

		<?php if(isset($this->leading) && count($this->leading)): ?>
		<!-- Leading items -->
		<div id="itemListLeading">
			<?php foreach($this->leading as $key=>$item): ?>

			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('num_leading_columns'))==0) || count($this->leading)<$this->params->get('num_leading_columns') )
				$lastContainer= ' itemContainerLast';
			else
				$lastContainer='';
			?>
			
			<div class="itemContainer<?php echo $lastContainer; ?>"<?php echo (count($this->leading)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('num_leading_columns'), 1).'%;"'; ?>>
				<?php
					// Load category_item.php by default
					$this->item=$item;
					echo $this->loadTemplate('item');
				?>
			</div>
			<?php if(($key+1)%($this->params->get('num_leading_columns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($this->primary) && count($this->primary)): ?>
		<!-- Primary items -->
		<div id="itemListPrimary">
			<?php foreach($this->primary as $key=>$item): ?>
			
			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('num_primary_columns'))==0) || count($this->primary)<$this->params->get('num_primary_columns') )
				$lastContainer= ' itemContainerLast';
			else
				$lastContainer='';
			?>
			
			<div class="itemContainer<?php echo $lastContainer; ?>"<?php echo (count($this->primary)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('num_primary_columns'), 1).'%;"'; ?>>
				<?php
					// Load category_item.php by default
					$this->item=$item;
					echo $this->loadTemplate('item');
				?>
			</div>
			<?php if(($key+1)%($this->params->get('num_primary_columns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($this->secondary) && count($this->secondary)): ?>
		<!-- Secondary items -->
		<div id="itemListSecondary">
			<?php foreach($this->secondary as $key=>$item): ?>
			
			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('num_secondary_columns'))==0) || count($this->secondary)<$this->params->get('num_secondary_columns') )
				$lastContainer= ' itemContainerLast';
			else
				$lastContainer='';
			?>
			
			<div class="itemContainer<?php echo $lastContainer; ?>"<?php echo (count($this->secondary)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('num_secondary_columns'), 1).'%;"'; ?>>
				<?php
					// Load category_item.php by default
					$this->item=$item;
					echo $this->loadTemplate('item');
				?>
			</div>
			<?php if(($key+1)%($this->params->get('num_secondary_columns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($this->links) && count($this->links)): ?>
		<!-- Link items -->
		<div id="itemListLinks">
			<h4><?php echo JText::_('K2_MORE'); ?></h4>
			<?php foreach($this->links as $key=>$item): ?>

			<?php
			// Define a CSS class for the last container on each row
			if( (($key+1)%($this->params->get('num_links_columns'))==0) || count($this->links)<$this->params->get('num_links_columns') )
				$lastContainer= ' itemContainerLast';
			else
				$lastContainer='';
			?>

			<div class="itemContainer<?php echo $lastContainer; ?>"<?php echo (count($this->links)==1) ? '' : ' style="width:'.number_format(100/$this->params->get('num_links_columns'), 1).'%;"'; ?>>
				<?php
					// Load category_item_links.php by default
					$this->item=$item;
					echo $this->loadTemplate('item_links');
				?>
			</div>
			<?php if(($key+1)%($this->params->get('num_links_columns'))==0): ?>
			<div class="clr"></div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="clr"></div>
		</div>
		<?php endif; ?>

	</div>

	<!-- Pagination -->
	<?php if(count($this->pagination->getPagesLinks())): ?>
	<div class="k2Pagination">
		<?php if($this->params->get('catPagination')) echo $this->pagination->getPagesLinks(); ?>
		<div class="clr"></div>
		<?php if($this->params->get('catPaginationResults')) echo $this->pagination->getPagesCounter(); ?>
	</div>
	<?php endif; ?>

	<?php endif; ?>
</div>
<!-- End K2 Category Layout -->
