<?php
 /**
 * @version   $Id: Platform.php 10871 2013-05-30 04:06:26Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
interface RokGallery_Link_Type_Article_Platform
{
    /**
     * @abstract
     * @param $id
     * @return RokGallery_Link_Type_Article_Info
     */
    public function &getArticleInfo($id);
}
