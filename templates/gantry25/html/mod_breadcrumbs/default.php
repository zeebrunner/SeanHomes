<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_breadcrumbs
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<div class="breadcrumbs <?php echo $moduleclass_sfx; ?>">
<?php if ($params->get('showHere', 1))
	{
		echo '<span class="showHere">' .JText::_('MOD_BREADCRUMBS_HERE').'</span>';
	}

	// Get rid of duplicated entries on trail including home page when using multilanguage
	for ($i = 0; $i < $count; $i ++)
	{
		if ($i == 1 && !empty($list[$i]->link) && !empty($list[$i-1]->link) && $list[$i]->link == $list[$i-1]->link)
		{
			unset($list[$i]);
		}
	}

	// Find last and penultimate items in breadcrumbs list
	end($list);
	$last_item_key = key($list);
	prev($list);
	$penult_item_key = key($list);

	// BAM - Show home
	$show_home = $params->get('showHome', 1);
	// BAM Blog
	$reachedBlogTop = false;
	
	// Generate the trail
	foreach ($list as $key=>$item) :
	// Make a link if not the last item in the breadcrumbs
	$show_last = $params->get('showLast', 1);
	
	if(!$show_home && $key == 0) continue;
	 
	
	
	if(strtolower($item->name)=='blog')$reachedBlogTop=true;
	
	if ($key != $last_item_key && !$reachedBlogTop) 
	{
		
		// Render all but last item - along with separator
		if (!empty($item->link))
		{
			echo '<a href="' . $item->link . '" class="pathway">' . $item->name . '</a>';
		}
		else
		{
			echo '<span class="pathitem">' . $item->name . '</span>';
		}

		if (($key != $penult_item_key) || $show_last)
		{
			echo ' <span class="separator">'.$separator.'</span> ';
		}

	}
	elseif ($show_last)
	{
		// Render last item if reqd.
		echo '<span class="pathitem">' . $item->name . '</span>';
		
		if(strtolower($item->name)=='blog') break;
	}
	
	
	endforeach; ?>
	<a href="#" id="goback" onclick="history.go(-1); return false;"><span><?php echo JText::_("GO_BACK"); ?></span></a>
</div>
