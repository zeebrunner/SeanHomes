<?php
/**
 * @version $Id: rokcandy_install.script.php 26966 2015-02-24 10:02:39Z matias $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class Com_RokCandyInstallerScript
{
    /**
     * @var JInstallerComponent
     */
    protected $parent;

    public function install($parent)
    {
        $this->parent = $parent;

        $this->save_category(array(
             'extension' => 'com_rokcandy',
             'title' => 'Basic',
             'alias' => 'basic',
             'published' => 1,
             'id' => 0));

        $this->save_category(array(
              'extension' => 'com_rokcandy',
              'title' => 'Typography',
              'alias' => 'typography',
              'published' => 1,
              'id' => 0));

        $this->save_category(array(
              'extension' => 'com_rokcandy',
              'title' => 'Uncategorised',
              'alias' => 'uncategorised',
              'published' => 1,
              'id' => 0));

        return true;

    }

    protected function save_category($data)
    {
        // Initialise variables;
        $dispatcher = JDispatcher::getInstance();
        $table = JTable::getInstance('category');
        $pk = (!empty($data['id'])) ? $data['id'] : 0;
        $isNew = true;

        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');

        // Load the row if saving an existing category.
        if ($pk > 0)
        {
            $table->load($pk);
            $isNew = false;
        }

        $data['parent_id'] = "";

        // This is a new category
        if ($isNew) {
            $table->setLocation($data['parent_id'], 'last-child');
        }
        //not new but doesn't match
        elseif (!$isNew && $table->parent_id != $data['parent_id']) {
             $table->setLocation($data['parent_id'], 'last-child');
         }

        // Alter the title for save as copy
        if (!$isNew && $data['id'] == 0 && $table->parent_id == $data['parent_id'])
        {
            $m = null;
            $data['alias'] = '';
            if (preg_match('#\((\d+)\)$#', $table->title, $m))
            {
                $data['title'] = preg_replace('#\(\d+\)$#', '(' . ($m[1] + 1) . ')', $table->title);
            }
            else
            {
                $data['title'] .= ' (2)';
            }
        }

        // Bind the data.
        if (!$table->bind($data))
        {
            $this->parent->setError($table->getError());
            return false;
        }

        // Bind the rules.
        if (isset($data['rules']))
        {
            $rules = new JAccessRules($data['rules']);
            $table->setRules($rules);
        }

        // Check the data.
        if (!$table->check())
        {
            $this->parent->setError($table->getError());
            return false;
        }

        // Trigger the onContentBeforeSave event.
        $result = $dispatcher->trigger('onContentBeforeSave', array('com_category.category', &$table, $isNew));
        if (in_array(false, $result, true))
        {
            $this->parent->setError($table->getError());
            return false;
        }

        // Store the data.
        if (!$table->store())
        {
            $this->parent->setError($table->getError());
            return false;
        }

        // Trigger the onContentAfterSave event.
        $dispatcher->trigger('onContentAfterSave', array('com_category.category', &$table, $isNew));

        // Rebuild the tree path.
        if (!$table->rebuildPath($table->id))
        {
            $this->parent->setError($table->getError());
            return false;
        }

        return true;

    }

    function preflight($type, $parent) {

        if ($type == 'install') {

            //do a little cleanup before install
            $db = JFactory::getDBO();

            $db->setQuery("DELETE FROM #__menu WHERE path LIKE 'rokcandy%'");
            $db->execute();

            $db->setQuery("DELETE FROM #__assets WHERE id IN (SELECT asset_id as id from #__categories where extension = 'com_rokcandy')");
            $db->execute();

            $db->setQuery("DELETE FROM #__categories WHERE extension = 'com_rokcandy'");
            $db->execute();
        }
    }

    public function uninstall($parent) {

        $db1 = JFactory::getDBO();
        $db1->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "rokcandy" AND `folder` = "system"');
        $id1 = $db1->loadResult();
        if ($id1) {
            $installer = new JInstaller;
            $installer->uninstall('plugin',$id1,1);
        }

        // Uninstalls RokCandy button editor plugin
        $db2 = JFactory::getDBO();
        $db2->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "rokcandy" AND `folder` = "editors-xtd"');
        $id2 = $db2->loadResult();
        if ($id2) {
            $installer = new JInstaller;
            $installer->uninstall('plugin',$id2,1);
        }
    }
}
