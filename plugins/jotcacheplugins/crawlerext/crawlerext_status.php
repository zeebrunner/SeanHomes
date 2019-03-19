<?php
/*
 * @version $Id: crawlerext_status.php,v 1.2 2013/10/13 10:53:07 Jot Exp $
 * @package JotCachePlugins
 * @category Joomla 2.5
 * @copyright (C) 2011-2014 Vladimir Kanich
 * @license GPL2
 */
defined('_JEXEC') or die('Restricted access');
$lang = JFactory::getLanguage();
$lang->load('plg_jotcacheplugins_crawlerext', JPATH_ADMINISTRATOR, null, false, false);
$database = JFactory::getDBO();
$app = JFactory::getApplication();
$sql = $database->getQuery(true);
$sql->select('COUNT(*)')
->from('#__jotcache')
->where($database->quoteName('agent') . ' = ' . $database->quote(1));
$database->setQuery($sql);
$total = $database->loadResult();
echo sprintf(JText::_('PLG_JCPLUGINS_CRAWLEREXT_STATUS'), $total);
?>
