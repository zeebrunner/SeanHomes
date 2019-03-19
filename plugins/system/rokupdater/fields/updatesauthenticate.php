<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldUpdatesAuthenticate extends JFormField
{
	public static $assets_loaded = false;
	/** @var \RokUpdater_ServiceProvider */
	protected $container;
	/**
	 * @var string
	 */
	protected $type = 'UpdatesAuthenticate';

	public function __construct($form = null)
	{
		if (!defined('ROKUPDATER_ROOT_PATH')) {
			define('ROKUPDATER_ROOT_PATH', JPATH_PLUGINS . '/system/rokupdater/');
		}
		require_once(ROKUPDATER_ROOT_PATH . '/lib/include.php');
		$this->container = RokUpdater_ServiceProvider::getInstance();
		parent::__construct($form);
	}

	/**
	 * @return string
	 */
	protected function getInput()
	{
		require_once(dirname(__FILE__) . '/../lib/include.php');

		$document = JFactory::getDocument();
		if (!self::$assets_loaded) {
			$jversion = new JVersion();
			if (version_compare($jversion->getShortVersion(), '3.0.0', '>')) {
				JHtml::_('behavior.modal', 'a.modal');
				$document->addStylesheet(JURI::root(true) . '/plugins/system/rokupdater/assets/css/rokupdater-j30.css');
			} else {
				JHtml::_('behavior.modal', 'a.modal');
				$document->addStylesheet(JURI::root(true) . '/plugins/system/rokupdater/assets/css/rokupdater-j25.css');
			}
			$document->addStylesheet(JURI::root(true) . '/plugins/system/rokupdater/assets/css/hint.min.css');
			$document->addScript(JURI::root(true) . '/plugins/system/rokupdater/fields/assets/ajax/js/Logout.js');
			JHtml::_('behavior.framework');
			self::$assets_loaded = true;
		}

		$body = JResponse::getBody();

		$jversion = new JVersion();
		$is_j30   = version_compare($jversion->getShortVersion(), '3.0.0', '>');


		$subscriber_info = $this->container->storageservice->getSubscriberInfo();


		$clubs = '';
		if ($subscriber_info != null) {
			foreach ($subscriber_info->getSubscriptions() as $club) {
				$clubs .= sprintf('<span class="%1s rok-badge">%2s</span> ', ($club->getActive()) ? 'rok-active' : 'rok-inactive', $club->getClub());
			}
		}

		if ($subscriber_info != null && $subscriber_info->expires > time()) {
			$status_msg_txt    = JText::sprintf('ROKUPDATER_LABEL_LOGOUT_MESSAGE', $subscriber_info->getUsername());
			$logout_button_txt = sprintf('<a class="btn btn-info" type="button" href="' . JURI::root(true) . '/plugins/system/rokupdater/ajax.php?ajax_model=logout' . '" data-rokupdater-logout><i class="rok-lock"></i> %1s</a>', JText::_('ROKUPDATER_LABEL_LOGOUT_BUTTON'));
			if ($is_j30) {
				$output = '<div class="rokupdater info j30"><i class="rok-rocketlogo"></i>' . $status_msg_txt . $clubs . ' ' . $logout_button_txt . '</div>';
			} else {
				$output = '<div class="rokupdater info j25"><i class="rok-rocketlogo"></i>' . $status_msg_txt . $clubs . ' ' . $logout_button_txt . '</div>';
			}
		} else {
			$auth_required_txt = JText::_('ROKUPDATER_LABEL_AUTH_NEEDED_MESSAGE');
			$auth_button_txt   = sprintf('<a class="btn btn-warning modal" href="%1s" type="button" rel="{handler: \'iframe\', size: {x: 400, y: 500}, onClose: function() {}}"><i class="rok-lock"></i> %2s</a>', JURI::root(true) . '/plugins/system/rokupdater/ajax.php?ajax_model=getpage&page=rockettheme_login', JText::_('ROKUPDATER_LABEL_LOGIN_BUTTON'));
			if ($is_j30) {
				$output = '<div class="rokupdater auth j30"><i class="rok-rocketlogo"></i>' . $auth_required_txt . ' ' . $auth_button_txt . '</div>';
			} else {
				$output = '<div class="rokupdater auth j25"><i class="rok-rocketlogo"></i>' . $auth_required_txt . ' ' . $auth_button_txt . '</div>';
			}
		}

		return $output;
	}

}
