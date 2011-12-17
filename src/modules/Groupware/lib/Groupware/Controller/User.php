<?php

/**
 * Copyright Groupware Team 2011
 *
 * This work is licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Groupware
 * @link https://github.com/phaidon/Groupware
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Groupware_Controller_User extends Zikula_AbstractController
{

    //-----------------------------------------------------------//
    //-- Main ---------------------------------------------------//
    //-----------------------------------------------------------//

    
    public function main()
    {
        return $this->start();
    }

  
    public function start()
    {
        // Security check
        if (!SecurityUtil::checkPermission('Groupware::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        
        
        if( !ModUtil::apiFunc($this->name, 'user', 'isAvailable' ) ) {
            LogUtil::registerStatus('It seems that Groupware is not configured!');
        }
        
        
        $output = ''; 
    
        if( ModUtil::available('Tasks') ) {
            // tasks
            //-------------------------
            $tasks = ModUtil::apiFunc('Tasks','user','getTasks', array(
               'mode'  => 'undone',
               'limit' => 4,
               'onlyMyTasks' => true
            ) );
            $this->view->assign('tasks', $tasks);
            $finished_tasks = ModUtil::apiFunc('Tasks','user','getTasks', array(
               'mode'  => 'done',
               'limit' => 4,
               'orderBy' => 'done_date desc'
            ) );
            $this->view->assign('finished_tasks', $finished_tasks);
            $output .= $this->view->fetch('user/tasks.tpl');

        }


        if( ModUtil::available('Wikula') ) {    
            //wiki
            //-------------------------
            $wiki_pages = ModUtil::apiFunc('Wikula', 'user', 'LoadRecentlyChanged', array(
                'numitems' => 3,
                'formated' => true
            ) );
            $this->view->assign('wiki_pages', $wiki_pages);
            $output .= $this->view->fetch('user/wikula.tpl');
        }


        if( ModUtil::available('PostCalendar') ) {  
            //events
            //-------------------------
            $start = date('m/d/Y');
            $oneweeklater = mktime(0, 0, 0, date("m"), date("d")+14, date("y"));
            $end = date("m/d/Y", $oneweeklater);
            $events0 = ModUtil::apiFunc('PostCalendar', 'event', 'getEvents', array(
                'start' => $start,
                'end' => $end,
            ) );
            $events = array();
            foreach($events0 as $events1) {
                 foreach($events1 as $event) {
                     $events[] = $event;
                 }
            }
            $this->view->assign('events', $events);
            $output .= $this->view->fetch('user/postcalendar.tpl');
        }

        if( ModUtil::available('Dizkus') ) { 
            //forum
            //-------------------------        

            list($last_visit, $last_visit_unix) = ModUtil::apiFunc('Dizkus', 'user', 'setcookies');
            list($posts, $m2fposts, $rssposts, $text) = ModUtil::apiFunc('Dizkus', 'user', 'get_latest_posts', array('selorder'   => 7,
               'nohours'    => 336,
               'amount'     => 100,
               'unanswered' => 0,
               'last_visit' => $last_visit,
               'last_visit_unix' => $last_visit_unix
            ));
            $this->view->assign('forum_posts', $posts );
            $output .= $this->view->fetch('user/dizkus.tpl');
        }


        if( ModUtil::available('AddressBook') ) { 
            //birthdays
            $birthdays = ModUtil::apiFunc('AddressBook','user','getBirthdays');
            $this->view->assign('birthdays',  $birthdays );
            $output .= $this->view->fetch('user/addressbook.tpl');
        }

        if( ModUtil::available('EZComments') ) {
            // comments
            //------------------------- 
            $options = array(
                'status' => 0,
                'numitems' => 3,
            );
            $items = ModUtil::apiFunc('EZComments', 'user', 'getall', $options);
            $comments = ModUtil::apiFunc('EZComments', 'user', 'prepareCommentsForDisplay', $items);
            $this->view->assign('comments', $comments);
            $output .= $this->view->fetch('user/ezcomments.tpl');
        }
        
        
        return '<h2>'.$this->__('Overview').'</h2>'.$output;
    }
    
    public function initialise()
    {
    	// Security check
        if (!SecurityUtil::checkPermission('Groupware::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        
        System::setVar('startpage',     'Groupware');
        System::setVar('starttype',     'user');
        System::setVar('startfunc',     'start');        

        // Now we've initialised the dependencies initialise the main module
        
        if(!$this->getVar('initialised', false))  {  
            
            $res = ModUtil::apiFunc('Extensions', 'admin', 'initialise', array(
                'id' => ModUtil::getIdFromName('AlternativeCategories'),
                'interactive_init' => false)
            );
            
            $groupwareModules = ModUtil::apiFunc($this->name, 'user', 'getModules');
            foreach ($groupwareModules as $modname => $title) {
                $res = ModUtil::apiFunc('Extensions', 'admin', 'initialise', array(
                    'id' => ModUtil::getIdFromName($modname),
                    'interactive_init' => false)
                );
            }
            $this->setVar('initialised', true);
        }
        
        
        
        return System::redirect(ModUtil::url($this->name, 'user', 'start'));

    	
    	
    }

}