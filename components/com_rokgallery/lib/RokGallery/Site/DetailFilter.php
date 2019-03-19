<?php
 /**
 * @version   $Id: DetailFilter.php 10871 2013-05-30 04:06:26Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Site_DetailFilter extends RokGallery_Site_GalleryFilter
{
    /**
     * @return \RokGallery_Site_GalleryFilter
     */
    public function &reset()
    {
        $this->_query = Doctrine_Query::create()
                ->select('s.id')
                ->from('RokGallery_Model_Slice s')
                ->where('s.published = ?', true);
        $this->_orderby_run = false;
        return $this;
    }
}

