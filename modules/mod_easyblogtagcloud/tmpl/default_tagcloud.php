<?php
/*
 * @package		mod_easyblogtagcloud
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

$menuItemId = modEasyBlogTagCloudHelper::_getMenuItemId($params);

?>
<?php foreach($tagcloud as $tag){ ?>
  <a style="font-size: <?php echo floor($tag->fontsize); ?>px;" class="tag-cloud" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id . $menuItemId );?>"><?php echo JText::_( $tag->title ); ?></a>
<?php } ?>