<?php
/*
 * @version $Id: crawlerext_form.php,v 1.1 2013/10/10 04:51:41 Jot Exp $
 * @package JotCachePlugins
 * @category Joomla 2.5
 * @copyright (C) 2011-2014 Vladimir Kanich
 * @license GPL2
 */
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
$app = JFactory::getApplication();
$depth = $app->getUserStateFromRequest('jotcache.crawlerext.depth', 'depth', JRequest::getInt('depth'), 'int');
$maxDepth = 5;
$lang = JFactory::getLanguage();
$lang->load('plg_jotcacheplugins_crawlerext', JPATH_ADMINISTRATOR, null, false, false);
$depthOptions = array();
for ($i = 1; $i < $maxDepth + 1; $i++) {
$depthOptions[$i] = $i;
}?>
<form action="<?php echo JRoute::_('index.php?option=com_jotcache'); ?>" method="post" name="adminForm_crawlerext" id="adminForm_Crawlerext">
  <h3><?php echo JText::_('PLG_JCPLUGINS_CRAWLEREXT_TITLE'); ?></h3>
  <table class="adminlist" style="width:30%;">
    <tr>
      <td class="hasTip" title="<?php echo JText::_('PLG_JCPLUGINS_CRAWLEREXT_DEPTH_DESC'); ?>"><?php echo JText::_('PLG_JCPLUGINS_CRAWLEREXT_DEPTH'); ?> </td>
      <td style="width:40%;"><?php echo JHTML::_('select.genericlist', $depthOptions, 'jcstates[depth]', 'style="margin-bottom:0px;" size="1"', 'value', 'text', $depth); ?></td>
    </tr>
  </table>
  <input type="hidden" name="view" value="recache" />
  <input type="hidden" name="task" value="display" />
  <input type="hidden" name="scope" value="direct" />
  <input type="hidden" name="jotcacheplugin" value="crawlerext" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="hidemainmenu" value="0" />
  <?php echo JHtml::_('form.token'); ?>
</form>