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
 * $displayData['fbColorscheme']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
<!-- HTML5 Facebook send button -->
<div class="fb-send" data-href="<?php echo $displayData['url']; ?>"
     data-colorscheme="<?php echo $displayData['fbColorscheme']; ?>"></div>
<!-- End of HTML5 Facebook send button -->
