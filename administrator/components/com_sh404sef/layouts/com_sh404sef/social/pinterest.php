<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2015
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.2.3180
 * @date        2015-12-23
 */

/**
 * Input:
 *
 * $displayData['url']
 * $displayData['imageSrc']
 * $displayData['imageDesc']
 * $displayData['pinItCountLayout']
 * $displayData['pinItButtonText']
 *
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
<!-- Pinterest button -->
<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($displayData['url']); ?>&media=<?php echo urlencode($displayData['imageSrc']);
echo empty($displayData['imageDesc']) ? '' : '&description=' . urlencode($displayData['imageDesc']); ?>"
   class="pin-it-button"
   count-layout="<?php echo $displayData['pinItCountLayout']; ?>">
	<?php echo $displayData['pinItButtonText']; ?></a>
<!-- End of Pinterest button -->
