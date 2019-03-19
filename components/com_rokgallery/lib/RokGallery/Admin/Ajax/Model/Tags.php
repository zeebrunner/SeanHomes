<?php
/**
  * @version   $Id: Tags.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
class RokGalleryAdminAjaxModelTags extends RokCommon_Ajax_AbstractModel
{
    public function getall($params)
    {
        try
        {
            $result = new RokCommon_Ajax_Result();
            $tags = array();
            $query = Doctrine_Query::create()
                    ->select('ft.tag as tag')
                    ->from('RokGallery_Model_FileTags ft')
                    ->groupBy('tag')
                    ->orderBy('tag');

            $file_tags = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            $query->free();
            foreach($file_tags as $tag){
                $tags[$tag['tag']]=$tag['tag'];
            }

            $query = Doctrine_Query::create()
                    ->select('st.tag as tag')
                    ->from('RokGallery_Model_SliceTags st')
                    ->groupBy('tag')
                    ->orderBy('tag');

            $slice_tags = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            $query->free();
            foreach($slice_tags as $tag){
                $tags[$tag['tag']]=$tag['tag'];
            }
            $tags = array_keys($tags);
            sort($tags);
            $result->setPayload(array('tags'=>$tags));
        }
        catch (Exception $e)
        {
            throw $e;
        }

        return $result;
    }

    public function get($params)
    {
    }
}
