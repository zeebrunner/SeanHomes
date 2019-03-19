<?php
/*
 ****************************************************************
 Copyright (C) 2008-2015 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2015 Soft Ventures, Inc. All rights reserved.
 * @license	GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 * ABPro is distributed WITHOUT ANY WARRANTY, or implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 *
 ************************************************************
 The latest version of ABPro is available to subscribers at:
 http://www.appointmentbookingpro.com/
 ************************************************************
 */


// No direct access to this file
defined('_JEXEC') or die;
/**
 * rsappt_pro3 component helper.
 */
class rsappt_pro3Helper
{
        /**
         * Configure the Linkbar.
         */
        public static function addSubmenu($submenu)
        {
			$doc = JFactory::getDocument();
//			if(strpos($doc->getTitle(),"cpanel")===false){
                JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.CONTROL_PANEL'),
                        'index.php?option=com_rsappt_pro3&controller=cpanel',
                        $submenu=='cpanel');
                JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.APPOINTMENTS'),
                        'index.php?option=com_rsappt_pro3&controller=requests',
                        $submenu=='requests');
                JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.BOOK-OFFS'),
                        'index.php?option=com_rsappt_pro3&controller=bookoffs',
                        $submenu=='bookoffs');
				JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.CATEGORIES'),
                        'index.php?option=com_rsappt_pro3&controller=categories',
                        $submenu=='categories');
                JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.CONFIGURE'),
                        'index.php?option=com_rsappt_pro3&controller=config_detail',
                        $submenu=='config');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_COUPONS'),
                        'index.php?option=com_rsappt_pro3&controller=coupons',
                        $submenu=='coupons');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_EMAIL_MARKETING'),
                        'index.php?option=com_rsappt_pro3&controller=email_marketing',
                        $submenu=='email_marketing');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_EXTRAS'),
                        'index.php?option=com_rsappt_pro3&controller=extras',
                        $submenu=='extras');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_GIFT_CERT'),
                        'index.php?option=com_rsappt_pro3&controller=user_credit&gc=gc',
                        $submenu=='user_credit');
				JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_MAIL'),
                        'index.php?option=com_rsappt_pro3&controller=mail',
                        $submenu=='mail');							
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_PAYPROC'),
                        'index.php?option=com_rsappt_pro3&controller=payment_processors',
                        $submenu=='payment_processors');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_RATE_ADJUSTMENTS'),
                        'index.php?option=com_rsappt_pro3&controller=rate_adjustments',
                        $submenu=='rate_adjustments');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_RATE_OVERRIDES'),
                        'index.php?option=com_rsappt_pro3&controller=rate_overrides',
                        $submenu=='rate_overrides');
                JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.RESOURCES'),
                        'index.php?option=com_rsappt_pro3&controller=resources',
                        $submenu=='resources');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_SEAT_ADJUSTMENTS'),
                        'index.php?option=com_rsappt_pro3&controller=seat_adjustments',
                        $submenu=='seat_adjustments');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_SEATS'),
                        'index.php?option=com_rsappt_pro3&controller=seat_types',
                        $submenu=='seat_types');
                JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.SERVICES'),
                        'index.php?option=com_rsappt_pro3&controller=services',
                        $submenu=='services');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_SMSPROC'),
                        'index.php?option=com_rsappt_pro3&controller=sms_processors',
                        $submenu=='sms_processors');
                JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.TIME_SLOTS'),
                        'index.php?option=com_rsappt_pro3&controller=timeslots',
                        $submenu=='timeslots');
                JSubMenuHelper::addEntry(
                        JText::_('COM_RSAPPT_PRO_SUBMENU.UDFS'),
                        'index.php?option=com_rsappt_pro3&controller=udfs',
                        $submenu=='udfs');
                JSubMenuHelper::addEntry(
                        JText::_('RS1_ADMIN_MENU_CREDIT'),
                        'index.php?option=com_rsappt_pro3&controller=user_credit',
                        $submenu=='user_credit');
//			}
        }
}
?>