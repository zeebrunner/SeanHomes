<?php
/*
 * @version $Id: crawler_status.php,v 1.3 2013/09/25 07:33:17 Jot Exp $
 * @package JotCachePlugins
 * @category Joomla 2.5
 * @copyright (C) 2011-2014 Vladimir Kanich
 * @license GPL2
 */
defined('_JEXEC') or die('Restricted access');
$lang = JFactory::getLanguage();
$lang->load('plg_jotcacheplugins_crawler', JPATH_ADMINISTRATOR, null, false, false);
$database = JFactory::getDBO();
$sql = $database->getQuery(true);
$sql->select('COUNT(*)')
->from('#__jotcache')
->where($database->quoteName('agent') . ' = ' . $database->quote(1));
$database->setQuery($sql);
$total = $database->loadResult();
echo sprintf(JText::_('PLG_JCPLUGINS_CRAWLER_STATUS'), $total);
?>
