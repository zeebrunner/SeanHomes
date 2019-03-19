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
 * $displayData['plusOneAnnotation']
 * $displayData['plusOneSize']
 * $displayData['url']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
<!-- Google+ button -->
<g:plusone callback="_sh404sefSocialTrackGPlusTracking" annotation="<?php echo $displayData['plusOneAnnotation']; ?>"
           size="<?php echo $displayData['plusOneSize']; ?>" href="<?php echo $displayData['url']; ?>"></g:plusone>
<!-- End of  Google+ button -->
