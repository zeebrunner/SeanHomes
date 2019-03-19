<?php
/**
 * @version   $Id: debuglink.php 8935 2013-03-29 19:17:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('JPATH_PLATFORM') or die;


class JFormFieldDebugLink extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'DebugLink';

	/**
	 * Method to get the field input markup for a spacer.
	 * The spacer does not have accept input.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html          = array();
		$log_file_path = JPATH_ROOT . '/logs/' . $this->element['logfile'];
		if (is_file($log_file_path)) {
			//$html[] = '<a href="' . JUri::root(true) . '/plugins/system/rokoverrides/fields/debuglink/download.php?logfile=' . $this->element['logfile'] . '">' . $this->element['logfile'] . '</a>';
			$html[] = $log_file_path;
		} else {
			$html[] = JText::_('Unavailable');
		}
		return implode('', $html);
	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   11.1
	 */
	protected function getTitle()
	{
		return $this->getLabel();
	}
}
