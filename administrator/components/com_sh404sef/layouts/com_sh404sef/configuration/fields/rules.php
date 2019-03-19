<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2015
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.2.3180
 * @date		2015-12-23
 */

defined('JPATH_BASE') or die;

?>

<div class="control-group">
<div class="shrules-label">
<div class="controls">
<?php
echo $displayData->input;
?>
<?php
$element = $displayData->element;
if (!empty($element['additionaltext']))
{
	echo '<span class = "sh404sef-additionaltext">' . (string) $element['additionaltext'] . '</span>';
}
?>
</div>
</div>
</div>
