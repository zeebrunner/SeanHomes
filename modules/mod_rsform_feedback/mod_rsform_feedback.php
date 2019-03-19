<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if ($params->get('open-in', 'same') == 'modal')
	JHTML::_('behavior.modal');

if (!defined('RSFORM_FEEDBACK_MODULE'))
	define('RSFORM_FEEDBACK_MODULE', $module->id);

$sfx 		= $params->get('moduleclass_sfx');
$position 	= $params->get('position', 'left');
if ($position == 'top' || $position == 'bottom') {
	$size = $params->get('font-size', 14) * strlen($params->get('string'));
	$size = (int) ($size / 1.7);
}

$bg_color 		= $params->get('bg-color', '#FFFFFF');
$border_color 	= $params->get('border-color', '#000000');
$form_url 	= 'index.php?option=com_rsform&formId='.$params->get('formId').($params->get('open-in', 'same') == 'modal' ? '&tmpl=component' : '').($params->get('itemid') ? '&Itemid='.(int)$params->get('itemid') : '');
$image_url 	= 'index.php?option=com_rsform&controller=feedback&task=image&module_id='.$module->id.'&rand='.md5(uniqid('rsform'));

$attr = '';
if ($params->get('open-in', 'same') == 'new') {
	$attr = ' target="_blank"';
} elseif ($params->get('open-in', 'same') == 'modal') {
	$attr = ' class="modal" rel="{handler: \'iframe\', size: {x: '.(int) $params->get('modal_x', 660).', y: '.(int) $params->get('modal_y', 475).'}}"';
}

// record params to be used by the component
$session = JFactory::getSession();
$session->set('mod_rsform_feedback.params.'.$module->id, $params);
	
require JModuleHelper::getLayoutPath('mod_rsform_feedback');