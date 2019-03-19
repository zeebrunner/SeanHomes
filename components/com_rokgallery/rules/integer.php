<?php
 /**
  * @version   $Id: integer.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */


jimport('joomla.form.formrule');

class JFormRuleInteger extends JFormRule
{
	public function test(& $element, $value, $group = null, & $input = null, & $form = null)
	{
        $min = null;
        $max = null;
        // If the field is empty and not required, the field is valid.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');
		if (!$required && empty($value)) {
			return true;
		}

        $value = intval($value);

        if ($element['min'])
        {
            if (intval($element['min'])){
                $min = (int) $element['min'];
            }
        }

        if (null != $min)
        {
            if ($value < $min)
            {
                return false;
            }
        }

        if ($element['max'])
        {
            if (intval($element['max'])){
                $max = (int) $element['max'];
            }
        }

        if (null != $max)
        {
            if ($value > $max)
            {
                return false;
            }
        }
		return true;
	}
}
