<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
 
defined('_JEXEC') or die('Restricted access');
?>
<!-- Blog post actions -->

<?php if( $params->get( 'showcommentcount' , 0 ) || $params->get( 'showhits' , 0 ) || $params->get( 'showreadmore' , true ) ){ ?>
<div class="mod-post-meta small">
	<?php $url = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $post->id . $menuItemId ); ?>

	<?php if($params->get('showcommentcount', true)) : ?>
	<span>
		<a href="<?php echo $url; ?>" class="post-comments"><?php echo JText::sprintf( 'MOD_EASYBLOGRANDOMPOST_COUNT', $post->commentCount );?></a>
	</span>
	<?php endif; ?>

    <?php if( $params->get( 'showhits' , true ) ): ?>
	<span>
		<a href="<?php echo $url;?>"><?php echo JText::sprintf( 'MOD_EASYBLOGRANDOMPOST_HITS' , $post->hits );?></a>
	</span>
	<?php endif; ?>

	<?php if( $params->get( 'showreadmore' , true ) ): ?>
	<span>
		<a href="<?php echo $url; ?>" class="post-more readon"><?php echo JText::_('MOD_EASYBLOGRANDOMPOST_READMORE'); ?></a>
	</span>
	<?php endif; ?>
</div>
<?php } ?>

<?php if( $params->get( 'showavatar', true ) || $params->get( 'showauthor' ) || $params->get( 'showdate' , true ) ) { ?>
<div class="mod-post-author at-bottom small clearfix">
	<?php if( $params->get( 'showavatar' ) ){ ?>
		<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $post->author->id . $menuItemId ); ?>" class="mod-avatar" alt="<?php echo $post->author->getName(); ?>">
            <img src="<?php echo $post->author->getAvatar();?>" width="30" title="<?php echo $post->author->getName(); ?>" class="avatar" />
        </a>
	<?php } ?>

	<?php $source = $post->source == '' ? '' : '_' . $post->source; ?>
	<?php require( JModuleHelper::getLayoutPath('mod_easyblograndompost', 'source' . $source  ) ); ?>
</div>
<?php } ?>