<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.0.473
 * @date				2015-12-07
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

if(version_compare(JVERSION, '3', 'ge')) {

  Class ShlMvcController_Base extends JControllerLegacy {
  }

} else {

  jimport( 'joomla.application.component.controller' );
  Class ShlMvcController_Base extends JController {
  }

}
