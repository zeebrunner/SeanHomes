<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * @version 1.2
 * @package MortgagePaymentCalculatorCalculateInstallment 
 * @copyright 2012 OrdaSoft
 * @author 2012 Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru)
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @description Mortgage Payment Calculator - Calculate Installment
 * Homepage: http://www.ordasoft.com
*/

// Include the syndicate functions only once
require_once dirname(__FILE__). '/helper.php';

$link = modPaymentCalculHelper::getLink($params);

require JModuleHelper::getLayoutPath('mod_mortgage_payment_calculator', $params->get('layout', 'default'));