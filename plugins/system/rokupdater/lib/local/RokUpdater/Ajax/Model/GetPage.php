<?php
/**
 * @version   $Id: GetPage.php 10081 2013-05-06 21:55:41Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokUpdater_Ajax_Model_GetPage implements RokUpdater_Ajax_IModel
{
	public function run()
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rokupdater', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$lang->load('plg_system_rokupdater', JPATH_ADMINISTRATOR, null, false, false);
		$lang->load('plg_system_rokupdater', JPATH_PLUGINS . '/system/rokupdater', $lang->getDefault(), false, false);
		$lang->load('plg_system_rokupdater', JPATH_PLUGINS . '/system/rokupdater', null, false, false);

		$base_path = JPATH_PLUGINS . '/system/rokupdater/assets/pages/';
		$input     = JFactory::getApplication()->input;
		$page      = $input->get('page', null);
		if (null == $page ) {
			return JText::sprintf('ROKUPDATER_UNABLE_TO_FINE_PAGE',$page);
		}
		$full_page_path = $base_path . $page . '.php';
		if (!file_exists($full_page_path))
		{
			return JText::sprintf('ROKUPDATER_UNABLE_TO_FINE_PAGE',$page);
		}
		ob_start();
		include($full_page_path);
		return ob_get_clean();
	}

}
