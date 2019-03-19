<?php
/**
 * @version   $Id: rokgallery.php 10868 2013-05-30 04:05:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 *
 */
class plgButtonRokGallery extends JPlugin
{
    /**
     * @param $subject
     * @param $config
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $lang = JFactory::getLanguage();
        $option = 'com_rokgallery';
	    $lang->load($option, JPATH_BASE, $lang->getDefault(), false, false);
	    $lang->load($option, JPATH_BASE, null, false, false);
	    $lang->load($option, JPATH_BASE . "/components/$option", $lang->getDefault(), false, false);
	    $lang->load($option, JPATH_BASE . "/components/$option", null, false, false);
    }

    /**
     * @param $name
     * @return JObject
     */
    function onDisplay($name)
    {
        JHtml::_('behavior.modal');
        $doc = JFactory::getDocument();
        $app = JFactory::getApplication();
        $content = $this->_subject->getContent($name);

        $link = 'index.php?option=com_rokgallery&view=gallerypicker&tmpl=component&textarea=' . $name;

        $version = new JVersion();

        if (version_compare($version->getShortVersion(), '3.0', '>=')) {
            $style = " .btn .icon-linkrokgallery { background: url(" . JURI::root(true) . "/administrator/components/com_rokgallery/assets/images/rokgallery_14x14.png) 100% 0 no-repeat; } ";
        } else {
            $style = " .button2-left .linkrokgallery { background: url(" . JURI::root(true) . "/administrator/components/com_rokgallery/assets/images/rokgallery-button.png) 100% 0 no-repeat; } ";
        }
        //$script ="
        //function jSelectArticle(id, title, object) {
        //    var articlehref = 'index.php?option=com_content&view=article&id='+id;
		//	var articlelink = ' <a href=\"'+articlehref+'\">'+title+'</a> ';
		//	jInsertEditorText( articlelink, 'text' );
		//	SqueezeBox.close();
		//}
	    //";
        //
        //$doc->addScriptDeclaration($script);
        $doc->addStyleDeclaration($style);

        $button = new JObject();
        $button->set('modal', true);
        $button->set('link', $link);
        $button->set('text', JText::_('RokGallery'));
        $button->set('name', 'linkrokgallery');
        $button->set('options', "{handler: 'iframe', size: {x: 695, y: 400}}");

        return $button;
    }

    /**
     * @return mixed
     */
    function onAfterRender()
    {
        $app = JFactory::getApplication();

        if ($app->isAdmin()) return;
    }
}
