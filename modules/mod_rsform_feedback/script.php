<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class mod_rsform_feedbackInstallerScript
{
	public function preflight($type, $parent) {
		if ($type != 'uninstall') {
			$app = JFactory::getApplication();
			
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) {
				$app->enqueueMessage('Please install the RSForm! Pro component before continuing.', 'error');
				return false;
			}
			
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php')) {
				$app->enqueueMessage('Please upgrade RSForm! Pro to at least R45 before continuing!', 'error');
				return false;
			}
			
			$jversion = new JVersion();
			if (!$jversion->isCompatible('2.5.5')) {
				$app->enqueueMessage('Please upgrade to at least Joomla! 2.5.5 before continuing!', 'error');
				return false;
			}
			
			if (!function_exists('imagerotate')) {
				$app->enqueueMessage('This module requires the imagerotate() function to be enabled. Please contact your hosting provider before continuing!', 'error');
				return false;
			}
		}
		
		return true;
	}
	
	public function install($parent) {
		$installer = $parent->getParent();
		$src = $installer->getPath('source').'/site';
		$dest = JPATH_SITE.'/components/com_rsform';
		
		JFolder::copy($src, $dest, '', true);
	}
	
	public function update($parent) {
		$installer = $parent->getParent();
		$src = $installer->getPath('source').'/site';
		$dest = JPATH_SITE.'/components/com_rsform';
		
		JFolder::copy($src, $dest, '', true);
	}
}