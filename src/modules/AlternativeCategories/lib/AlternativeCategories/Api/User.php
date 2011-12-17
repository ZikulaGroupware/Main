<?php

/**
 * AlternativeCategories
 *
 * @copyright (c) 2009, Fabian Wuertz
 * @author Fabian Wuertz
 * @link http://fabian.wuertz.org
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package AlternativeCategories
 */


class AlternativeCategories_Api_User extends Zikula_AbstractApi 
{
    
    /**
     * Get all categories
     *
     * @param $outputStyle string output style (query|list|select|formdropdownlist)
     * @return array query array | formdropdownlist array
     */    
    public function getCategories( $args )
    {        
        
        extract($args);
        
        if(empty($entity)) {
            $entity = ModUtil::getName().'_Entity_Categories';
        }
       
                
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('c')
           ->from($entity, 'c')
           ->orderBy('c.name');
        $query= $qb->getQuery();
        $categories = $query->getArrayResult();  
        
        switch ($outputStyle) {
            case 'query':
                return $categories;
            case 'formdropdownlist':

                foreach($categories as $category) {
                    $formdropdownlist[] = array(
                        'text'  => $category['name'],
                        'value' => $category['id']
                    );
                }
                return $formdropdownlist;
            case 'formdropdownlistAll':
                $formdropdownlist[] = array(
                    'text'  => $this->__('All'),
                    'value' => 'all'
                );
                foreach($categories as $category) {
                    $formdropdownlist[] = array(
                        'text'  => $category['name'],
                        'value' => $category['id']
                    );
                }
                return $formdropdownlist;
                
            case 'list':
                foreach($categories as $category) {
                    $list[$category['id']] = $category['name'];
                }
                return $list;
            case 'select':
                $select['all'] = $this->__('All');
                foreach($categories as $category) {
                    $select[$category['id']] = $category['name'];
                }
                return $select;
            default:
                return $categories;
        }        
        
    }
    
    
    
}