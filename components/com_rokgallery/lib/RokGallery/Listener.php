<?php
 /**
  * @version   $Id: Listener.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

class RokGallery_Listener extends Doctrine_EventListener
{
    /**
     * @param Doctrine_Event $event
     */
    public function postTransactionCommit(Doctrine_Event $event)
    {
        if ($event->getInvoker()->getConnection()->getTransactionLevel() == 1)
        {
            // process the file delete queue
            RokGallery_Queue_FileDelete::process();

            // process the Direcotry Delete queue
            RokGallery_Queue_DirectoryDelete::process();
        }
    }

    /**
     * @param Doctrine_Event $event
     */
    public function postTransactionRollback(Doctrine_Event $event)
    {
        RokGallery_Queue_FileDelete::clear();
        RokGallery_Queue_DirectoryDelete::clear();

        RokGallery_Queue_FileCreate::process();
        RokGallery_Queue_DirectoryCreate::process();
    }
}
