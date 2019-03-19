<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
 * optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die;

include_once dirname(dirname(__FILE__)) . '/jchoptimize/loader.php';

if (version_compare(JVERSION, '3.0', '>'))
{

        class JFormFieldJchmenuitemcompat extends JFormFieldMenuitem
        {

                public function setup(SimpleXMLElement $element, $value, $group = NULL)
                {
                        $sValue = $this->setupJchMenuItem($element, $value, $group);

                        try
                        {
                                $this->checkPcreVersion();
                                $oFileRetriever = JchOptimizeFileRetriever::getInstance();
                        }
                        catch (Exception $ex)
                        {
                                $GLOBALS['bTextArea'] = TRUE;

                                JFactory::getApplication()->enqueueMessage($ex->getMessage(), 'warning');

                                return FALSE;
                        }

                        if (!$oFileRetriever->isHttpAdapterAvailable())
                        {
                                return FALSE;
                        }

                        return parent::setup($element, $sValue, $group);
                }

        }

}
else
{
        JFormHelper::loadFieldClass('MenuItem');

        class JFormFieldJchmenuitemcompat extends JFormFieldMenuitem
        {

                public function setup(&$element, $value, $group = NULL)
                {
                        if (version_compare(PHP_VERSION, '5.3.0', '>='))
                        {
                                $sValue = $this->setupJchMenuItem($element, $value, $group);

                                try
                                {
                                        $this->checkPcreVersion();
                                        $oFileRetriever = JchOptimizeFileRetriever::getInstance();
                                }
                                catch (Exception $ex)
                                {
                                        $GLOBALS['bTextArea'] = TRUE;

                                        JFactory::getApplication()->enqueueMessage($ex->getMessage(), 'error');

                                        return FALSE;
                                }

                                if (!$oFileRetriever->isHttpAdapterAvailable())
                                {
                                        return FALSE;
                                }

                                return parent::setup($element, $sValue, $group);
                        }
                        else
                        {

                                JFactory::getApplication()->enqueueMessage(JText::_('This plugin requires PHP 5.3.0 or greater to work. '
                                                . 'Your installed version is ' . PHP_VERSION), 'error');

                                JFormHelper::loadFieldClass('Textarea');

                                return FALSE;
                        }
                }

        }

}

/**
 * 
 */
class JFormFieldJchmenuitem extends JFormFieldJchmenuitemcompat
{

        public $type = 'jchmenuitem';

        /**
         * 
         * @param type $element
         * @param type $value
         * @param type $group
         * @return type
         */
        public function setupJchMenuItem($element, $value, $group = null)
        {
                $GLOBALS['bTextArea'] = FALSE;

                $this->loadResources();

                if (!$value)
                {
                        $value = $this->getHomePageLink();
                }

                return $value;
        }

        /**
         * 
         * @throws Exception
         */
        protected function checkPcreVersion()
        {
                $pcre_version = preg_replace('#(^\d++\.\d++).++$#', '$1', PCRE_VERSION);

                if (version_compare($pcre_version, '7.2', '<'))
                {
                        throw new Exception('This plugin requires PCRE Version 7.2 or higher to run. Your installed version is ' . PCRE_VERSION);
                }
        }

        /**
         * 
         * @return type
         */
        public static function getHomePageLink()
        {
                $oMenu            = JFactory::getApplication()->getMenu('site');
                $oDefaultMenuItem = $oMenu->getDefault();

                return $oDefaultMenuItem->id;
        }

        /**
         * 
         */
        protected function loadResources()
        {
                $oDocument = JFactory::getDocument();
                $sScript   = '';

                if (!defined('JCH_VERSION'))
                {
                        define('JCH_VERSION', '5.0.3');
                }

                if (version_compare(JVERSION, '3.0', '<'))
                {
                        JHtml::stylesheet('plg_jchoptimize/jquery.chosen.min.css', array(), TRUE);

                        JHtml::script('plg_jchoptimize/jquery.min.js', FALSE, TRUE);
                        JHtml::script('plg_jchoptimize/jquery.noconflict.js', FALSE, TRUE);
                        JHtml::script('plg_jchoptimize/jquery.chosen.min.js', FALSE, TRUE);

                        $sScript .= <<<JCHSCRIPT
                                
jQuery(document).ready( function() {
        jQuery(".chzn-custom-value").chosen({width: "240px"});
});
JCHSCRIPT;
                        $sStyle = <<<JCHCSS
                                
.chosen-container{
        float: left;
        margin: 0 7px 7px 0;
        font-size: 12px;
}
.chosen-container-multi .chosen-choices li.search-field input[type=text]{
        padding: 2px;
}
.chosen-container .chosen-results li{
        line-height: 12px;
}  
.pane-down, .pane-down > .panelform {
        overflow: visible !important;
        height: auto !important;
}   
.panelform {
        margin-bottom: 0 !important;
} 
.adminformlist > li:after,  .adminformlist > li:before {
        display: table;
        content: " ";
        line-height: 0;
}
.adminformlist > li:after {
        clear: both;
}

.container-icons {
        display:table; 
        max-width: 320px;
}

div.icon a {
        height: 75px; 
        width: 81px;
}
                                                
div.icon a span {
        font-size: 1.1em;
}
                                
.jchgroup{
        border: 1px #ccc solid; 
        padding: 0 10px 5px;
        background-color: #f9f9f9;
        margin:10px 0;        
}
.jchgroup fieldset{
        background-color: #f9f9f9 !important;                        
}
.jchgroup h4{
        margin: 10px 0;
}
#jform_params_pro_optimize_images-lbl{
        float:none !important;
        margin-bottom: 10px !important;                        
}                                
        
form#style-form > div.width-60{
        width: 57.5%;
} 
form#style-form > div.width-40{
        width: 42.5%;
}   
.container-icons label{
        margin-top: 0 !important;
        min-width: 80px !important;
}  
#jform_params_pro_staticfiles label{
        min-width: 0 !important;
}  
/* ##<procode>## */
#optimize-images-container{
            position: relative;
    left: -70%;
    width: 170%;
    background-color: white;
    border: solid 1px #ccc;
    padding: 3px;
}   

#files-container{
        width: 50%
}    
/* ##</procode>## */       
JCHCSS;
                        $oDocument->addStyleDeclaration($sStyle);
                        JHtml::stylesheet('plg_jchoptimize/css-lib/admin.css', array(), TRUE);
                        JHtml::script('plg_jchoptimize/js-lib/admin-utility.js', FALSE, TRUE);
                }
                else
                {
                        $oDocument->addStyleSheetVersion(JUri::root(true) . '/media/plg_jchoptimize/css/css-lib/admin.css', JCH_VERSION);
                        $oDocument->addScriptVersion(JUri::root(true) . '/media/plg_jchoptimize/js/js-lib/admin-utility.js', JCH_VERSION);
                }

                $sScript .= <<<JCHSCRIPT
function submitJchSettings(){
        Joomla.submitbutton('plugin.apply');
}                        
jQuery(document).ready(function() {
    jQuery('.collapsible').collapsible();
  });
                        
var jch_form_id = 'jform_params';                        
JCHSCRIPT;

                $oDocument->addScriptDeclaration($sScript);
                $oDocument->addStyleSheet('//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css');
                JHtml::script('plg_jchoptimize/jquery.collapsible.js', FALSE, TRUE);

##<procode>##   
                $uri         = clone JUri::getInstance();
                $domain      = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port')) . JchOptimizeHelper::getBaseFolder();
                $plugin_path = 'plugins/system/jch_optimize/';

                if (version_compare(JVERSION, '3.0', '<'))
                {
                        JHtml::stylesheet('plg_jchoptimize/css-lib/pro-jquery.filetree.css', array(), TRUE);
                        JHtml::script('plg_jchoptimize/js-lib/pro-jquery.filetree.js', FALSE, TRUE);
                        JHtml::script('plg_jchoptimize/js-lib/pro-admin-utility.js', FALSE, TRUE);

                        JHtml::script('plg_jchoptimize/pro-jquery.ui.core.js', FALSE, TRUE);
                        JHtml::script('plg_jchoptimize/pro-jquery.ui.widget.js', FALSE, TRUE);

                        $domain = str_replace('/administrator', '', $domain);

                        $ajax_url     = $domain . $plugin_path . 'ajax.php?action=optimizeimages&format=raw';
                        $fileTreePath = $domain . $plugin_path . 'ajax.php?action=filetree&format=raw';
                        $iconOffset   = 485;
                        $classSfx     = '25';
                }
                else
                {
                        $oDocument->addStyleSheetVersion(JUri::root(true) . '/media/plg_jchoptimize/css/css-lib/pro-jquery.filetree.css', JCH_VERSION);
                        $oDocument->addScriptVersion(JUri::root(true) . '/media/plg_jchoptimize/js/js-lib/pro-jquery.filetree.js', JCH_VERSION);
                        $oDocument->addScriptVersion(JUri::root(true) . '/media/plg_jchoptimize/js/js-lib/pro-admin-utility.js', JCH_VERSION);

                        JHtml::script('jui/jquery.min.js', FALSE, TRUE);
                        JHtml::script('jui/jquery.ui.core.js', FALSE, TRUE);

                        $ajax_url     = $domain . '/administrator/index.php?option=com_ajax&plugin=optimizeimages&format=raw';
                        $fileTreePath = $domain . '/administrator/index.php?option=com_ajax&plugin=filetree&format=raw';
                        $iconOffset   = 330;
                        $classSfx     = '30';
                }

                JHtml::stylesheet('plg_jchoptimize/pro-jquery-ui-progressbar.css', array(), TRUE);

                JHtml::script('plg_jchoptimize/pro-jquery.ui.progressbar.js', FALSE, TRUE);


                $message = addslashes(JchPlatformUtility::translate('Please select files or subfolders to optimize'));
                $noproid = addslashes(JchPlatformUtility::translate('Please enter your Download ID on the Pro Options tab'));

                $sScript = <<<JCHSCRIPT
                
jQuery(document).ready( function() {
        jQuery("#file-tree-container").fileTree({
                root: "",
                script: "$fileTreePath",
                expandSpeed: 100,
                collapseSpeed: 100,
                multiFolder: false
        }, function(file) {});
                        
//        var \$window = jQuery(window), 
//                \$stickyIcon = jQuery('#optimize-images-container .icon');
//                        
//        \$window.scroll(function() {
//                var windowTop = \$window.scrollTop(); 
//                \$stickyIcon.toggleClass('sticky-$classSfx', windowTop > $iconOffset);
//        });                        
});

var ajax_url = '$ajax_url';
var message = '$message';   
var noproid = '$noproid';        
                        
JCHSCRIPT;
                $oDocument->addScriptDeclaration($sScript);

##</procode>##                
        }

}
