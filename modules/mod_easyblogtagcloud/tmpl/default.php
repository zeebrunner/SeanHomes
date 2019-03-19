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
$layout     = $params->get( 'layout', 'tagcloud');

?>
<div class="ezb-mod mod_easyblogtagcloud<?php echo $params->get( 'moduleclass_sfx' ) ?>">
	<?php
		if( !empty( $tagcloud ) )
		{
			require( JModuleHelper::getLayoutPath('mod_easyblogtagcloud', 'default_' . $layout ) );
		}
		else
		{
			echo JText::_('MOD_EASYBLOGTAGCLOUD_NO_TAG');
		}
	?>
</div>
