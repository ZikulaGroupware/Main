<?php

/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Piwik
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

use Doctrine\ORM\Query;

class Wikula_Api_User extends Zikula_AbstractApi
{
    private $_em;
    
    
    function __autoload($class_name) {
        require_once 'modules/Wikula/lib/Wikula/Common.php';
        $this->_em = $this->getService('doctrine.entitymanager');
    }
    
 
    
    
    /**
    * Validate a PageName
    */
    public function isValidPagename($args)
    {
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        return preg_match(VALID_PAGENAME_PATTERN, $args['tag']);
    }

    /**
    * Check access to edit a page
    */
    public function isAllowedToEdit($args)
    {
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }
        
        return ModUtil::apiFunc($this->name, 'Permission', 'canEdit', $args['tag']); 
    }

    /**
    * Load a wiki page with the given tag
    *
    * @param string $args['tag'] tag of the wiki page to get
    * @param string $args['time'] (optional) update time, latest page if not defined
    * @return array wiki page data
    */
    public function LoadPage($args)
    {
        
        extract($args);
        unset($arg);
        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }
        
        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ)) {
            return LogUtil::registerError(__('You do not have the authorization to read this page!'), 403);
        }
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('u')
           ->from('Wikula_Entity_Pages', 'u')
           ->where('u.tag = :tag')
           ->setParameter('tag', $tag)
           ->setMaxResults(1);

        if (isset($time) && !empty($time)) {
            $qb->andWhere('where', 'u.time = :time')
               ->setParameter('time', $args['time']);

        } else {
            $qb->andWhere('u.latest = :latest')
               ->setParameter('latest', 'Y');
        }
        //$qb->where(implode(' AND ', $where));

        
        // return the page or false if failed
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if(count($result) > 0) {
            return $result[0];
        } else {
            return false;
        }
        

    }


    /**
    * Check if a page exist
    *
    * @param $args['tag'] tag of the page to check
    * @return id of the page, false if doesn't exists
    */
    public function PageExists($args)
    {
        extract($args);
        unset($arg);
        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }
        
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        if(array_key_exists($tag, $specialPages)) {
            return true;
        }
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->where('p.tag = :tag')
           ->setParameter('tag', $tag)
           ->andWhere('p.latest = :latest')
           ->setParameter('latest', 'Y')
           ->setMaxResults(1);

        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if(!$result) {
            return false;
        }
        return $result[0]['id'];

    }

    public function LoadPagebyId($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($id) || !is_numeric($id)) {
            return LogUtil::registerArgsError();
        }
        

        $page = $this->entityManager->find('Wikula_Entity_Pages', $id);

        if ($page === false) {
            return LogUtil::registerError(__('Error! Getting the this page by id failed.'));
        }
        
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $page['tag']); 


        return $page;

    }

    /**
    * Get the Revisions of a Wiki Page
    *
    * @param string $args['tag']
    * @param string $args['startnum'] (optional) start offset
    * @param string $args['numitems'] (optional) number of items to fetch
    * @param string $args['orderby'] (optional) sort by fieldname
    * @param string $args['orderdir'] (optional) sort direction (ASC or DESC)
    * @param string $args['loadbody'] (optional) flag to include the body in the results
    * @param string $args['getoldest'] (optional) flag to fetch the oldes revision only
    * @return array of revisions
    */
    public function LoadRevisions($args)
    {
        // Security check will be done by LoadRevisions()
        
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        $args['latest'] = false;
        



        // build the order by
        if (!isset($args['orderby'])) {
            $args['orderBy'] = 'time';
        } else {
            $args['orderBy'] = $orderby;
        }
        if (!isset($args['orderdir']) || !in_array(strtoupper($args['orderdir']), array('ASC', 'DESC'))) {
            $args['orderBy'] .= ' DESC';
        } else {
            $args['orderBy'] .= ' '.strtoupper($orderdir);
        }

        // check if we want to get the latest only
        if (isset($getoldest) && $getoldest) {
            $args['orderBy'] = $args['time'].' DESC';
            $numitems = 1;
        }

        
        $revisions = $this->LoadPages($args);
        
        // return the results
        if (isset($revisions[0]) && isset($getoldest) && $getoldest) {
            $revisions = $revisions[0];
        }

        
        $objects  = array();
        $previous = array();
        foreach ($revisions as $page) {

            if (empty($previous)) {
                // We filter the first one as we don't want to check it
                $previous = $page;
                continue;
            }

            $bodylast = explode("\n", $previous['body']);

            $bodynext = explode("\n", $page['body']);

            $added   = array_diff($bodylast, $bodynext);
            $deleted = array_diff($bodynext, $bodylast);

            if ($added) {
                $newcontent = implode("\n", $added)/*."\n"*/;
            } else {
                $newcontent = '';
                $added = false;
            }

            if ($deleted) {
                $oldcontent = implode("\n", $deleted)/*."\n"*/;
            } else {
                $oldcontent = '';
                $deleted = false;
            }

            
            
            $objects[] = array(
                //TODO zikula dateformat
                'pageAtime'    => $previous['time'],
                'pageBtime'    => $page['time'],
                'pageAtimeurl' => urlencode(DateUtil::formatDatetime($previous['time'])),
                'pageBtimeurl' => urlencode(DateUtil::formatDatetime($page['time'])),
                'EditedByUser' => $previous['user'],
                'note'         => $previous['note'],
                'newcontent'   => $newcontent,
                'oldcontent'   => $oldcontent,
                'added'        => $added,
                'deleted'      => $deleted
            );

            $previous = $page;
        }

        
        return array(
            'objects' => $objects,
            'oldest'  => $page
        );

    }

    public function LoadPagesLinkingTo($tag)
    {
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $tag);
        
        
        $em = $this->getService('doctrine.entitymanager');
        $query = $em->createQueryBuilder();
        $query->select('l')
           ->from('Wikula_Entity_Links', 'l')
           ->where('l.to_tag = :to_tag')
           ->setParameter('to_tag', $tag);
        $links = $query->getQuery()->execute();        
        
        if ($links === false) {
            return LogUtil::registerError(__('Error! Getting the links for this page failed.'));
        }

        return $links;
    }
    

    public function CountBackLinks($tag)
    {
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $tag);
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('count(l.from_tag)')
           ->from('Wikula_Entity_Links', 'l')
           ->where('l.to_tag = :to_tag')
           ->setParameter('to_tag', $tag);
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();

    }

    /**
    * Load the recently changed pages
    *
    * @param unknown_type $args
    * @return unknown
    */
    public function LoadRecentlyChanged($args)
    {
        
        extract($args);
        unset($args);


        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->where("p.latest = 'Y'")
           ->orderBy('p.time', 'DESC');
        $query = $qb->getQuery();
        
        
        
        if (isset($startnum) and is_numeric($startnum) and $startnum > 1) {
            $query->setFirstResult($offset = $startnum-1);
        }
        if (isset($numitems) and is_numeric($numitems) and $numitems > 0) {
            $query->setMaxResults($limit = $numitems);

        }    
        


        // return the page or false if failed
        $revisions = $query->getArrayResult();
        
        if ($revisions === false) {
            return LogUtil::registerError(__('Error! Getting revisions failed.'));
        }

        
        if(!empty($formated) and $formated) {
            $curday = '';
            $pagelist = array();
            foreach ($revisions as $page)
            {
                $day  = $page['time']->format('Y-m-d');
                if ($day != $curday) {
                    $dateformatted = $page['time']->format('D, d M Y');
                    $curday = $day;
                }

                if ($page['user'] == System::getVar('anonymous')) {
                    $page['user'] .= ' ('.$this->__('anonymous user').')'; // anonymous user
                }

                $pagelist[$dateformatted][] = $page;
            }
            return $pagelist;
        }


        return $revisions;

    }


    public function LoadAllPages($args)
    {
        
        $args['orderBy'] = 'p.tag';         
        $pages = $this->LoadPages($args);

        if ($pages === false) {
            return LogUtil::registerError(__('Error! Getting all pages failed.'));
        }

        return $pages;

    }

    
    
    public function LoadPages($args)
    {
        extract($args);
        unset($args);
        
        if(!isset($tag)) {
            $tag = null;
        }
        
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $tag);
  
        
        $em = $this->getService('doctrine.entitymanager');
        $query = $em->createQueryBuilder();
        $query->select('p')
              ->from('Wikula_Entity_Pages', 'p');
                
        
        if(isset($orphaned) and  $orphaned) { 
            $query->leftJoin('p.links', 'l');
            $query->where("p.links IS EMPTY");    
        }

        
        if(!isset($latest) or $latest) {
            $query->andWhere("p.latest = 'Y'");        
        }
        
        
        if (isset($startnum) and is_numeric($startnum) and $startnum > 1) {
            $query->setFirstResult($offset = $startnum-1);
        }
        if (isset($numitems) and is_numeric($numitems) and $numitems > 0) {
            $query->setMaxResults($limit = $numitems);

        }
        
        return $query->getQuery()->getArrayResult();                
    }
    

    public function LoadAllPagesEditedByUser($args)
    {        
        extract($args);
        unset($args);

        if (!isset($uname)) {
            return false;
        }
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->where('p.user = :user')
           ->setParameter('user', $uname);        


        if (!isset($all) || (isset($all) && !$all)) {
            $qb->andWhere("p.latest = 'Y'");
        }
        if (isset($alpha) && $alpha == 1) {
            $qb->add('orderBy', 'p.tag ASC, p.time DESC');
        } else {
            $qb->add('orderBy', 'p.time DESC, p.tag ASC');
        }
        $query = $qb->getQuery();

        
        if (isset($startnum) and is_numeric($startnum) and $startnum > 1) {
            $query->setFirstResult($offset = $startnum-1);
        }
        if (isset($numitems) and is_numeric($numitems) and $numitems > 0) {
            $query->setMaxResults($limit = $numitems);

        }    
        
        
        $pages = $query->getArrayResult();
        

        if ($pages === false) {
            return LogUtil::registerError(__('Error! Getting all pages by user failed.'));
        }

        return $pages;

    }

    /**
    * Count the total number of active pages in the Wiki
    *
    * @return integer total wiki pages
    */
    public function CountAllPages()
    {                  
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('count(p.id) as num')
           ->from('Wikula_Entity_Pages', 'p')
           ->where("p.latest = 'Y'" );
        $query = $qb->getQuery();
        $count = $query->getSingleScalarResult();
        return $count;

    }

    /**
    * Load all the pages owned by a specific User
    *
    * @param string  $args['uname'] username to search
    * @param integer $args['startnum'] (optional) start point
    * @param integer $args['numitems'] (optional) number of items to fetch
    * @param integer $args['justcount'] (optional) flag to perform just a page count and not return the wiki pages
    */
    public function LoadAllPagesOwnedByUser($args)
    {
        extract($args);
        unset($args);
        if (!isset($uname) || empty($uname)) {
            return LogUtil::registerArgsError();
        }
       
        // defaults
        if (!isset($justcount) || !is_bool($justcount)) {
            $justcount = false;
        }
        if (!isset($orderby)) {
            $orderby = 'tag';
        }
        if (!isset($orderdir) || !in_array(strtoupper($orderdir), array('ASC', 'DESC'))) {
            $orderdir = 'ASC';
        }

        
                 
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->orderBy('p.'.$orderby, $orderdir)
           ->where("p.latest = 'Y' AND p.owner = :owner" )
           ->setParameter('owner', $uname);
        
        
        if (isset($startnum) and is_numeric($startnum) and $startnum > 1) {
            $query->setFirstResult($offset = $startnum-1);
        }
        if (isset($numitems) and is_numeric($numitems) and $numitems > 0) {
            $query->setMaxResults($limit = $numitems);
        }  

        

       
       if ($justcount) {
            $qb->select('count(p.id) as num');
            $query = $qb->getQuery();
            $count = $query->getSingleScalarResult();
        } else {
            $query = $qb->getQuery();
            $pages = $query->getArrayResult();;
        }
     

        
        // build the result array
        $result = array();

        if ($justcount) {
            $result['count'] = $count;
        } else {
            $result['pages'] = $pages;
            $result['count'] = count($pages);
        }
        $result['total'] = $this->CountAllPages();

        return $result;
    }

    
    public function FullTextSearch($args)
    {
        return $this->Search($args);
    }
    
    
    public function Search($args)
    {
        
        extract($args);
        unset($args);
        if (!isset($phrase)) {
            return LogUtil::registerArgsError();
        }
        
        
        $phrase = DataUtil::formatForStore($phrase);
        
        
        // ToDo: FullTextSearch
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p')
           ->from('Wikula_Entity_Pages', 'p')
           ->orderBy('p.time', 'DESC')
           ->where("p.latest = 'Y'" )
           ->andWhere( $qb->expr()->like('p.tag', '?1') )
           ->setParameter(1, '%'.$phrase.'%');
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
       
        
        $pages = array();


        // ToDo: Rewrite page permission
        foreach ($result as $value) {
           extract($value);

            if (SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ))  {
                $pages[] = array(
                    'page_id'         => $id,
                    'page_tag'        => $tag,
                    'page_time'       => $time,
                    'page_body'       => $body,
                    'page_owner'      => $owner,
                    'page_user'       => $user,
                    'page_handler'    => $handler
                );
            }

        }
        
                
        return $pages;

    }

    public function LoadWantedPages($args)
    {
        
        
        // TODO remove old DB query 
        $pages0 = $this->LoadAllPages(array());
        $pages  = array();
        foreach ($pages0 as $key => $value) {
            $pages[] = $value['tag'];
        }
        
        
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('l')
           ->from('Wikula_Entity_Links2', 'l')
           ->groupBy('l.to_tag');
        $query = $qb->getQuery();
        $links = $query->getArrayResult();
        

        foreach ($links as $key => $value) {
            if(in_array($value['to_tag'], $pages)) {
                unset($links[$key]);
                unset($pages[$key]); 
            }
        }
                
        return $links;
        

        
        
        /*


        $sql = 'SELECT DISTINCT '.$linkcol['from_tag'].', '
                                .$linkcol['to_tag'].', '
                                .'COUNT('.$linkcol['to_tag'].') as count '


                .' ORDER BY count DESC, ';

        //$result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1);
        $result =& $dbconn->Execute($sql);

        if ($dbconn->ErrorNo() != 0) {
            return LogUtil::registerError(__('Getting matches for this page failed!').' - '.$dbconn->ErrorMsg());
        }

        if ($result->EOF) {
            return LogUtil::registerError(__('No items found.'));
        }
        $pages = array();

        for (; !$result->EOF; $result->MoveNext()) {

            list($from_tag, $to_tag, $count) = $result->fields;

            if (SecurityUtil::checkPermission('Wikula::', 'page::'.$to_tag, ACCESS_READ))  {
            if ($from_tag != 'WantedPages') {
                $pages[] = array('from_tag' => $from_tag,
                                'to_tag'   => $to_tag,
                                'count'    => $count);
            }
            }

        }

        $result->Close();

        return $pages; */

    }
    
    
    
    public function LoadOrphanedPages($args)
    {
        $args['orphaned'] = true;        
        return $this->LoadPages($args);
    }
    
    

    public function IsOrphanedPage($args)
    {
        
        // ToDo Remove old DB query
        
        extract($args);
        unset($args);

        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }

        if (!isset($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        if (!isset($numitems) || !is_numeric($numitems)) {
            $numitems = -1;
        }

        $dbconn  =& pnDBGetConn(true);
        $table =& DBUtil::getTables();

        $pagetbl = &$table['wikula_pages'];
        $pagecol = &$table['wikula_pages_column'];
        $linktbl = &$table['wikula_links'];
        $linkcol = &$table['wikula_links_colum'];

        $sql = 'SELECT DISTINCT '.$pagecol['tag']
            .' FROM '.$pagetbl
            .' LEFT JOIN '.$linktbl.' ON '.$pagecol['tag'].' = '.$linkcol['to_tag']
            .' WHERE '.$linkcol['to_tag'].' IS NULL '
            .' AND '.$pagecol['comment_on'].' = "" '
            .' AND '.$pagecol['tag'].' = "'.DataUtil::formatForStore($tag).'" '
            .' ORDER BY '.$pagecol['tag'];

        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1);

        if ($dbconn->ErrorNo() != 0) {
            return LogUtil::registerError(__('Getting matches for this page failed!').' - '.$dbconn->ErrorMsg());
        }

        $pages = array();

        for (; !$result->EOF; $result->MoveNext()) {

            list($tag) = $result->fields;

            if (SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ))  {
                $pages[] = array('tag' => $tag);
            }

        }

        $result->Close();

        return $pages;

    }

    public function DeleteOrphanedPage($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }

        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_DELETE))  {
            return LogUtil::registerError(__('You do not have the right to delete this page!'), 403);
        }

        $table =& DBUtil::getTables();

        $pagecol   =& $table['wikula_pages_column'];
        $pagewhere = 'WHERE '.$pagecol['tag'].' = "'.DataUtil::formatForStore($tag).'"';
        if (!DBUtil::deleteWhere('wikula_pages', $pagewhere)) {
            return LogUtil::registerError(__('Error! Deleting this page failed.'));
        }

        $linkcol   =& $table['wikula_links_colum'];
        $linkwhere = 'WHERE '.$linkcol['from_tag'].' = "'.DataUtil::formatForStore($tag).'"';
        if (!DBUtil::deleteWhere('wikula_links', $linkwhere)) {
            return LogUtil::registerError(__('Error! Deleting links for this page failed.'));
        }

        $aclscol   =& $table['wikula_acls_column'];
        $aclswhere = 'WHERE '.$aclscol['page_tag'].' = "'.DataUtil::formatForStore($tag).'"';
        if (!DBUtil::deleteWhere('wikula_acls', $aclswhere)) {
            return LogUtil::registerError(__('Error! Deleting ACLS for this page failed.'));
        }

        return LogUtil::registerStatus(__('Done! Page deleted with success.'));
    }

    public function SavePage($args)
    {
        
        extract($args);
        unset($args);
        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }
        

        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canEdit', $tag);

        $user = UserUtil::getVar('uname');

        // Check if page is new
        $oldpage =  ModUtil::apiFunc($this->name, 'user', 'LoadPage', array('tag' => $tag));

        // only save if new body differs from old body
        if ($oldpage && $oldpage['body'] == $body) {
            return LogUtil::registerError(__('The content of the page to save is the same of the current revision. New page not saved.'));
        }

        if (!$oldpage) {
            $owner = $user;
        } else {
            $owner = $oldpage['owner'];
        }

        // set all other revisions to old
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->update('Wikula_Entity_Pages', 'p')
           ->where("p.tag = :tag AND p.latest = 'Y'")
           ->setParameter('tag', $tag)
           ->set('p.latest', ':newlatest')
           ->setParameter('newlatest', 'N');
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
                
        
 
        // add new revision
        $tag = DataUtil::formatForStore($tag);
        $newrev = array(
            'tag'    => $tag,
            'body'   => $body,
            'note'   => DataUtil::formatForStore($note),
            'time'   => DateUtil::getDatetime(),
            'owner'  => $owner,
            'user'   => $user,
            'latest' => 'Y'
        );        
       

        $res = new Wikula_Entity_Pages();
        $res->merge($newrev);
        $this->entityManager->persist($res);


        if ($res === false) {
            return LogUtil::registerError(__('Saving revision failed!'));
        }


        // add links to other page into the link table
        $oldlinks = $this->entityManager->getRepository('Wikula_Entity_Links2')
                                        ->findBy(array('from_tag' => $tag));
        foreach($oldlinks as $oldlink) {
            $this->entityManager->remove($oldlink);
            $this->entityManager->flush();
        }
        
                
        $pagelinks = ModUtil::apiFunc(
            'LuMicuLa',
            'Transform',
            'get_pagelinks',
            array(
                'content' => $body,
                'tag'     => $tag,
                'modname' => $this->name
            )
        );
        foreach($pagelinks as $pagelink) {
           $d = new Wikula_Entity_Links2();
           $d->merge($pagelink);
           $this->entityManager->persist($d);
           $this->entityManager->flush();
        }


        if (!$oldpage) {
            LogUtil::registerStatus(__('New page created!'));
             ModUtil::apiFunc($this->name, 'user', 'NotificateNewPage', $tag);
        } else {
            LogUtil::registerStatus(__('New revision saved!'));
             ModUtil::apiFunc($this->name, 'user', 'NotificateNewRevsion', $tag);
        }

        
        $this->entityManager->flush();

        
        return true;
    }


    public function NotificateNewPage($tag)
    {

        if (empty($tag) or !$this->getVar('subscription') ) {
            return false;
        }

        // prepare email design
        $view = Zikula_View::getInstance($this->name, false);
        $view->assign('baseUrl', System::getBaseUrl());
        $view->assign('tag', $tag);
        $view->assign('uname', UserUtil::getVar('uname') );
        $message = $view->fetch('notification/newPage.tpl');
        $subject = $this->__('Wiki update');


        // find all subscriptions
        $em = $this->getService('doctrine.entitymanager');
        $query = $em->createQuery('SELECT u FROM Wikula_Entity_Subscriptions u');        
        $users = $query->getArrayResult();
        
        // send emails        
        foreach($users as $user) {
            $uid = $user['uid'];
            $toaddress = UserUtil::getVar('email', $uid);
            ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array(
                'toaddress' => $toaddress,
                'subject'   => $subject,
                'body'      => $message,
                'html'      => true
            ));
        }
        return true;
        
    }


    public function NotificateNewRevsion($tag)
    {
        if (empty($tag) or !$this->getVar('subscription') ) {
            return false;
        }

        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->from('Wikula_Entity_Pages', 'p')
           ->where("p.user = :user AND p.latest = 'N'")
           ->setParameter('user', UserUtil::getVar('uname'))
           ->orderBy('time desc')
           ->setMaxResults($limit = 1)
           ->setParameter('newlatest', 'N');
        $query = $qb->getQuery();
        $lastEdit = $query->getArrayResult();
        

        $notification = false;
        if(count($lastEdit) == 0 ) {
            $notification = true;
        } else {
            $lastEdit = $lastEdit[0]['time'];
            $timeDiff = DateUtil::getDatetimeDiff($lastEdit, DateUtil::getDatetime());
            $minutesSinceLastEdit = 0;
            if(array_key_exists('m', $timeDiff) ) {
                $minutesSinceLastEdit = $timeDiff['m'];
            }
            if(array_key_exists('h', $timeDiff) ) {
                $minutesSinceLastEdit = $minutesSinceLastEdit + $timeDiff['h'] * 60;

            }
            if(array_key_exists('d', $timeDiff) ) {
                $minutesSinceLastEdit = $minutesSinceLastEdit + $timeDiff['d'] * 60 * 24;
            }
            if($minutesSinceLastEdit > 20) {
                $notification = true;
            }
        }

        

        if($notification) {
            $view = Zikula_View::getInstance($this->name, false);
            $view->assign('baseUrl', System::getBaseUrl());
            $view->assign('tag', $tag);
            $view->assign('uname', UserUtil::getVar('uname') );
            $message = $view->fetch('notification/newRevision.tpl');
            $subject = $this->__('Wiki update');

            
            // find all subscriptions
            $em = $this->getService('doctrine.entitymanager');
            $query = $em->createQuery('SELECT u FROM Wikula_Entity_Subscriptions u');        
            $users = $query->getArrayResult();
        
            // send emails
            foreach($users as $user) {
                $uid = $user['uid'];
                $toaddress = UserUtil::getVar('email', $uid);
                ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array(
                    'toaddress' => $toaddress,
                    'subject'   => $subject,
                    'body'      => $message,
                    'html'      => true
                ));
            }
        }
    }


    // Disabled function for now...
    public function PurgePages($args)
    {
        

        extract($args);
        unset($args);

        $days = $this->getVar('pages_purge_time');

        if ($days) {
            // Get all pages with an interval greater than pages_purge_time
            // $pages = All page blabla
            // TODO
        }

        return true;

    }

    // INTERWIKI STUFF
    public function ReadInterWikiConfig()
    {
        
        static $interwiki = array();

        if (!empty($interwiki)) {
            return $interwiki;
        }

        if (file_exists('modules/Wikula/pnincludes/interwiki.conf') && $lines = file('modules/Wikula/pnincludes/interwiki.conf')) {
            foreach($lines as $line) {
                if ($line = trim($line)) {
                    list($wikiName, $wikiUrl) = explode(' ', trim($line));

                    $interwiki[strtoupper($wikiName)] = $wikiUrl;
                }
            }
        } else {
            $interwiki['WIKULA'] = 'https://github.com/phaidon/Wikula/wiki/';
            $interwiki['ZIKULA'] = 'http://community.zikula.org/index.php?module=Wikula&tag=';
        }

        return $interwiki;

    }

    public function AddInterWiki($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($wikiname) || !isset($wikiurl)) {
            return LogUtil::registerError(__('Adding InterWiki failed due to missing arguments!'));
        }

        $interwiki = unserialize($this->getVar('interwiki'));

        if (!is_array($interwiki)) {
            $interwiki = array();
            $interwiki[strtoupper($wikiname)] = $wikiurl;

        } elseif (!isset($interwiki[strtoupper($wikiname)])) {
            $interwiki[strtoupper($wikiname)] = $wikiurl;
        }

        if (!$this->setVar('interwiki', serialize($interwiki))) {
            return LogUtil::registerError(__('Adding interwiki failed!'));
        }

        return LogUtil::registerStatus(__('Interwiki added with success!'));
    }

    public function GetInterWikiUrl($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($name) || !isset($tag)) {
            return LogUtil::registerError(__('Error! Invalid arguments.'));
        }

        $interwiki =  ModUtil::apiFunc($this->name, 'user', 'ReadInterWikiConfig');

        if (!$interwiki || !is_array($interwiki)) {
            return 'http://'.$tag;
        }

        if (isset($interwiki[strtoupper($name)])) {
            return $interwiki[strtoupper($name)].$tag;
        }

        return 'http://'.$tag; //avoid xss by putting http:// in front of JavaScript:()

    }

    /**
    * Build a wiki link code
    * @todo needs rework
    * @todo can we index all the Links and check if exists in the DB once?
    */
    public function Link($args)
    {
        
        if (!isset($args['tag'])) {
            return false;
        }

        if (!isset($args['text']) || empty($args['text'])) {
            // No text, we fill the page with at least its tag
            $args['text'] = $args['tag'];
        }
        if (!isset($args['title'])) {
            // No text, we fill the page with at least its tag
            $args['title'] = $args['tag'];
        }

        // is this an interwiki link?
        if (preg_match('/^([A-Z][A-Z,a-z]+)[:]([A-Z,a-z,0-9]*)$/s', $args['tag'], $matches)) {

            $link =  ModUtil::apiFunc($this->name, 'user', 'GetInterWikiUrl',
                                array('name' => $matches[1],
                                    'tag'  => isset($matches[2]) ? $matches[2] : ''));

            $textlink = (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : $matches[1];

            return '<a class="ext" href="'.$link.'" title="'.$matches[1].' - '.$matches[2].'">'.$textlink.'</a><span class="exttail">&#8734;</span>';

        } else if (preg_match('/[^[:alnum:]]/', $args['tag'])) {

            // is this a full link? i.e., does it contain non alpha-numeric characters?
            // Note : [:alnum:] is equivalent [0-9A-Za-z]
            //        [^[:alnum:]] means : some caracters other than [0-9A-Za-z]
            // For example : "www.address.com", "mailto:address@domain.com", "http://www.address.com"

            // check for email addresses
            if (preg_match('/^.+\@.+$/', $args['tag'])) {
                // Building spam safe email link and text
                if ($args['text'] == $args['tag']) {
                    $args['text'] = htmlspecialchars(str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $args['text']));
                }
                $mailto = '&#109;&#97;&#105;&#108;&#116;&#111;&#58;';
                $address = htmlspecialchars($args['tag']);
                $address_encode = '';
                for ($x=0; $x < strlen($address); $x++) {
                    if (preg_match('!\w!',$address[$x])) {
                        $address_encode .= '%' . bin2hex($address[$x]);
                    } else {
                        $address_encode .= $address[$x];
                    }
                }
                $args['tag'] = $mailto . $address_encode;

            } else if (!preg_match('/:\/\//', $args['tag'])) {
                // check for protocol-less URLs
                $args['tag'] = 'http://'.$args['tag'];  // Very important for xss (avoid javascript:() hacking)
            }

            if ($args['text'] != $args['tag'] && preg_match('/.(gif|jpeg|png|jpg)$/i', $args['tag'])) {
                return '<img src="'.DataUtil::formatForDisplay($args['tag']).'" alt="'.DataUtil::formatForDisplay($args['text']).'" />';
            }

        } else {
            // it's a Wiki link!
            $pageid =  ModUtil::apiFunc($this->name, 'user', 'PageExists',
                                array('tag' => $args['tag']));

            $linktable = SessionUtil::getVar('linktable');
            if (is_array(unserialize($linktable))) {
                $linktable = unserialize($linktable);
            }
            $linktable[] = $args['tag']; //$args['page']['tag'];
            SessionUtil::setVar('linktable', serialize($linktable));

            if (!empty($pageid)) {
                //$pnurl = urlencode( ModUtil::url('Wikula', 'user', 'main', array('tag' => $args['tag'])));
                //$text = DataUtil::formatForDisplay($args['text']);
                return '<a href="'. ModUtil::url('Wikula', 'user', 'main', array('tag' => DataUtil::formatForDisplay(urlencode($args['tag'])))).'" title="'.$args['text'].'">'.$args['text'].'</a>';
            } else {
                return '<span class="missingpage">'.$args['text'].'</span><a href="'. ModUtil::url('Wikula', 'user', 'edit', array('tag' => urlencode($args['tag']))).'" title="'.DataUtil::formatForDisplay($args['tag']).'">?</a>';
            }
        }

        // Non Wiki external link ?
        $external_link_tail = '<span class="exttail">&#8734;</span>';
        return !empty($args['tag']) ? '<a title="'.$args['text'].'" href="'.$args['tag'].'">'.$args['text'].'</a>'.$external_link_tail : $args['text']; //// ?????
    }



   
    public function Action($args)
    {
        
        if (!isset($args['action'])) {
            return LogUtil::registerError(__('Action argument missing!'));
        }

        $action = trim($args['action']);
        unset($args['action']);

        $vars   = array();
        // only search for parameters if there is a space
        if (strpos($action, ' ') !== false) {
            // treat everything after the first whitespace as parameter
            preg_match('/^([A-Za-z0-9]*)\s+(.*)$/', $action, $matches);

            // extract $action and $vars_temp ("raw" attributes)
            $action    = isset($matches[1]) ? $matches[1] : '';
            $vars_temp = isset($matches[2]) ? $matches[2] : '';

            if (!empty($action)) {
                // match all attributes (key and value)
                preg_match_all('/([A-Za-z0-9]*)="(.*)"/U', $vars_temp, $matches);

                // prepare an array for extract() to work with (in $this->IncludeBuffered())
                if (is_array($matches)) {
                    for ($a = 0; $a < count($matches[0]); $a++) {
                        $vars[$matches[1][$a]] = $matches[2][$a];
                    }
                }
                //$vars['wikka_vars'] = trim($vars_temp); // <<< add the buffered parameter-string to the array
            } else {
                return '<span class="error"><em>'.__f('Unknown action %s; the action name must not contain special characters', DataUtil::formatForDisplay($action)).'.</em></span>'; // <<< the pattern ([A-Za-z0-9])\s+ didn't match!
            }
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $action)) {
            return '<span class="error"><em>'.__f('Unknown action %s; the action name must not contain special characters', DataUtil::formatForDisplay($action)).'.</em></span>';
        }

        $vars = array_merge($args, $vars);


        // return the Action result
        return  ModUtil::apiFunc($this->name, 'action', strtolower($action), $vars);
    }

    public function FullCategoryTextSearch($args)
    {
        
        $args['orderBy'] = 'tag';

        if (!isset($args['phrase'])) {
            return false;
        }

        //TODO LIKE
        $this->LoadPages($args);
        
        //$q->addWhere('body LIKE ?', array('%[['.$phrase.'%'));

        
        if ($result === false) {
            return LogUtil::registerError(__('Error! Happened during element fetching.'));
        }

        $pages = array();
        foreach ($result as $id => $page) {
            $pages[$id]['page_id']  = $page['id'];
            $pages[$id]['page_tag'] = $page['tag'];
        }

        return $pages;
    }
  
    
    public function CheckTag($tag = null)
    {
        if(is_null($tag)) {
            $tag = $this->getVar('root_page');
        }
        
        // redirect if tag contains spaces
        if (strpos($tag, ' ') !== false) {
            $arguments = array(
                'tag'  => str_replace(' ', '_', $tag),
            );
            $redirecturl = ModUtil::url($this->name, 'user', 'show', $arguments);
            System::redirect($redirecturl);
        }
    }

    public function LoadCategory($category)
    {
        $em = $this->getService('doctrine.entitymanager');
        $query = $em->createQueryBuilder();
        $query->select('p')
              ->from('Wikula_Entity_Pages', 'p')
              ->where("p.body LIKE :category OR p.body LIKE :category2")
              ->setParameter('category', "%\nCategory".'PHP')
              ->setParameter('category2', "%\nCategory".'PHP'."\n%")
              ->andWhere("p.latest = 'Y'");
         return $query->getQuery()->getArrayResult(); 
    }
    
    
}




