<?php
/**
 * @version   $Id: view.html.php 10868 2013-05-30 04:05:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.view');
include_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/legacy_class.php');

class RokgalleryViewDefault extends RokGalleryLegacyJView
{
    public function __construct($config = array())
    {
        $option = JFactory::getApplication()->input->getCmd('option');
        parent::__construct($config);
    }

    function display($tpl = null)
    {
        JHtml::_('behavior.framework', true);
        JHtml::_('behavior.keepalive');

        $option = JFactory::getApplication()->input->getCmd('option');
        $document = JFactory::getDocument();
        $session = JFactory::getSession();

        $model = new RokGallery_Admin_MainPage();
        $current_page = 1;
        $items_per_page = RokGallery_Config::getOption(RokGallery_Config::OPTION_ADMIN_ITEMS_PER_PAGE, 6);
        $items_per_row = RokGallery_Config::getOption(RokGallery_Config::OPTION_ADMIN_ITEMS_PER_ROW, 3);

        $files = $model->getFiles($current_page, $items_per_page * 2);
        $pager = $model->getPager($current_page, $items_per_page * 2);

        $next_page = ($current_page == 1) ? 3 : $current_page + 1;
        $next_page = ($current_page == $pager->getLastPage()) ? false : $next_page;

        $more_pages = ($next_page == false) ? "false" : "true";

        $application = JURI::root(true) . '/administrator/components/' . $option . '/assets/application/';
        $images = JURI::root(true) . '/administrator/components/' . $option . '/assets/images/';
        $url = JURI::root(true) . '/administrator/index.php?option=com_rokgallery&task=ajax&format=raw'; // debug: &XDEBUG_SESSION_START=default


        $document->addScriptDeclaration('var RokGallerySettings = {
			application: "' . $application . '",
			images: "' . $images . '",
			next_page: "' . $next_page . '",
            last_page: "' . $pager->getLastPage() . '",
			more_pages: ' . $more_pages . ',
			items_per_page: "' . $items_per_page . '",
            total_items: ' . $pager->getNumResults() . ',
			url: "' . $url . '",
			token: "' . JSession::getFormToken() . '",
			session: {
				name: "' . $session->getName() . '",
				id: "' . $session->getId() . '"
			},
			order: ["order-created_at", "order-desc"]
		};');

        //TODO create better loader for versioned css an js files
        $browser = new RokCommon_Browser();
        if ($browser->getShortName() == 'ie8') {
            $document->addStyleSheet('components/' . $option . '/assets/styles/internet-explorer-8.css');
        }

        $required_css = array();
        $required_css[] = 'master.css';

        $required_js = array();
        $required_js[] = 'Common.js';
        $required_js[] = 'RokGallery.js';
        $required_js[] = 'RokGallery.Filters.js';
        $required_js[] = 'RokGallery.Blocks.js';
        $required_js[] = 'RokGallery.FileSettings.js';
        $required_js[] = 'RokGallery.Edit.js';
        $required_js[] = 'MainPage.js';
        $required_js[] = 'Tags.js';
        $required_js[] = 'Tags.Slice.js';
        $required_js[] = 'Tags.Ajax.js';
        $required_js[] = 'Scrollbar.js';
        $required_js[] = 'Popup.js';
        $required_js[] = 'Progress.js';
        $required_js[] = 'Job.js';
        $required_js[] = 'JobsManager.js';
        $required_js[] = 'MassTags.js';
        $required_js[] = 'GalleriesManager.js';
        $required_js[] = 'Swiff.Uploader.js';
        $required_js[] = 'Uploader.js';
        $required_js[] = 'Rubberband.js';
        $required_js[] = 'Marquee.js';
        $required_js[] = 'Marquee.Crop.js';

        $container = RokCommon_Service::getContainer();
        /** @var $header RokCommon_IHeader */
        $header = $container->header;

        foreach ($required_css as $filename) {
            $header->addStyle(RokCommon_Composite::get('rokcommon_styles')->getURL($filename));
        }

        foreach ($required_js as $filename) {
            $header->addScript(RokCommon_Composite::get('rokcommon_scripts')->getURL($filename));
        }

        $galleries = RokGallery_Model_GalleryTable::getAll();
        if ($galleries === false) {
            $galleries = array();
        }

        $this->assign('total_items_in_filter', $pager->getNumResults());
        $this->assign('items_to_be_rendered', $pager->getResultsInPage());
        $this->assign('next_page', $next_page);
        $this->assign('items_per_page', $items_per_page);
        $this->assign('items_per_row', $items_per_row);
        $this->assign('currently_shown_items', $pager->getLastIndice());
        $this->assign('totalFilesCount', $pager->getNumResults());

        $this->assignRef('files', $files);
        $this->assignRef('galleries', $galleries);

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since    1.6
     */
    protected function addToolbar()
    {

        JHtml::_('behavior.modal');
        JHtml::_('behavior.framework');
        //$this->_replaceMooTools();

        JToolBarHelper::title(JText::_('RokGallery'), 'generic.png');
        //JToolBarHelper::preferences('com_rokgallery');
        //JToolBarHelper::custom('', '', '', 'custom');

        JHTML::_('behavior.modal');
        $this->_replaceMooTools();

        $toolbar = JToolBar::getInstance('toolbar');
        $toolbar->addButtonPath(JPATH_COMPONENT.DS.'buttons');

        $toolbar->appendButton('rokgallery', 'publish');
        $toolbar->appendButton('rokgallery', 'unpublish');
        $toolbar->appendButton('rokgallery', 'tag');
        $toolbar->appendButton('rokgallery', 'delete');

        $toolbar->appendButton('Separator');

        $toolbar->appendButton('rokgallery', 'jobs');
        $toolbar->appendButton('rokgallery', 'galleries');

        $jversion = new JVersion();
        if (version_compare($jversion->getShortVersion(), '3.0', '<')) {
            $toolbar->appendButton('rokgallery', 'settings', 'index.php?option=com_config&view=component&layout=modal&component=com_rokgallery&tmpl=component&path=', '', "{handler: 'iframe', size: {x: 570, y: 300}}", 'modal');
        } else {
            $path = '';
            $uri = (string) JUri::getInstance();
            $return = urlencode(base64_encode($uri));
            $toolbar->appendButton('rokgallery', 'settings', 'index.php?option=com_config&amp;view=component&amp;component=com_rokgallery&amp;path=' . $path . '&amp;return=' . $return);
        }

        //$toolbar->appendButton('rokgallery', 'settings', 'index.php?option=com_config&view=component&layout=modal&component=com_rokgallery&tmpl=component&path=', '', "{handler: 'iframe', size: {x: 570, y: 300}}", 'modal');
        $toolbar->appendButton('rokgallery', 'upload', '#', 'ok');

    }

    protected function _replaceMooTools()
    {
        $option = JFactory::getApplication()->input->getCmd('option');
        $document = JFactory::getDocument();


        // mootools
        $mootools_r = array();
        $mootools_r[] = JURI::root(true) . '/media/system/js/core.js';
        $mootools_r[] = JURI::root(true) . '/media/system/js/core-uncompressed.js';
        $mootools_r[] = JURI::root(true) . '/media/system/js/mootools-core.js';
        $mootools_r[] = JURI::root(true) . '/media/system/js/mootools-core-uncompressed.js';
        $mootools_r[] = JURI::root(true) . '/media/system/js/mootools-more.js';
        $mootools_r[] = JURI::root(true) . '/media/system/js/mootools-more-uncompressed.js';

        $mootools13 = 'components/' . $option . '/assets/js/mootools.js';

        // modal
        $modal_r = array();
        $modal_r[] = JURI::root(true) . '/media/system/js/modal.js';
        $modal_r[] = JURI::root(true) . '/media/system/js/modal-uncompressed.js';

        $modal13 = 'components/' . $option . '/assets/js/modal-1.3.js';

        $scripts = array();
        foreach ($document->_scripts as $key => $value) {
            if (in_array($key, $mootools_r)) $scripts[$mootools13] = $value;
            else if (in_array($key, $modal_r)) $scripts[$modal13] = $value;
            else {
                $scripts[$key] = $value;
            }
        }

        $document->_scripts = $scripts;
    }

}
