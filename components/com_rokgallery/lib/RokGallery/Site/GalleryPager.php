<?php
 /**
  * @version   $Id: GalleryPager.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
 
class RokGallery_Site_GalleryPager extends Doctrine_Pager
{
    /** @var RokGallery_Site_GalleryFilter */
    protected $filter;

    /**
     * @param $gallery_id
     * @param int $page
     * @param int $maxPerPage
     * @param string $order_by
     * @param string $order_direction
     */
    public function __construct(RokGallery_Site_GalleryFilter $filter, $page=1, $maxPerPage = 0)
    {
        $this->filter = $filter;
        parent::__construct($this->filter->getQuery(), $page, $maxPerPage);
    }
}
