<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2015
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.2.3180
 * @date        2015-12-23
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

if (!empty($this->analytics->filters)) :
	?>
	<div class="filter-select hidden-phone">
		<?php
		foreach ($this->analytics->filters as $filter) :
			echo $filter;
		endforeach;
		?>
	</div>
	<?php
endif;
?>


