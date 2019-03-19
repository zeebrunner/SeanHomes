<?php
/**
  * @version   $Id: Processor.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

interface RokGallery_Job_Processor
{
    /**
     * @param $jobid the
     * @return RokGallery_Job_Type
     *
     */
    public function __construct(RokGallery_Job &$job);

    /**
     */
    public function process();
}
