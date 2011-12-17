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


class AlternativeCategories_Handler_Modify extends Zikula_Form_AbstractHandler
{
    
    private $entity;

    function setEntity($entity) {
        $this->entity = $entity;
    }
    

    function initialize(Zikula_Form_View $view)
    {   
       
        
        
        if ((!UserUtil::isLoggedIn()) || (!SecurityUtil::checkPermission('AlternativeCategories::', '::', ACCESS_ADMIN))) {
            return LogUtil::registerPermissionError();
        }
        
        if(empty($tthis->entitiy)) {
            $tthis->entitiy = ModUtil::getName().'_Entity_Categories';
        }

        $categories = ModUtil::apiFunc($this->name,'user','getCategories', array( 'outputStyle' => 'query') );
        $this->view->assign('categories', $categories);
        
                
        
        $id = FormUtil::getPassedValue('id', null, "GET", FILTER_SANITIZE_NUMBER_INT);
        $type   = FormUtil::getPassedValue('type',   null, "GET");
        $this->view->assign('type', $type);
        

        if ($id) {
            // load user with id
            $this->_category = $this->entityManager->find($this->entity, $id);
            $this->view->assign('action', $this->__('Edit category'));

            if ($this->_category) {
                $this->view->assign($this->_category->toArray()); 
            } else {
                return LogUtil::registerError($this->__f('Category with id %s not found', $id));
            }
        } else {
            $this->view->assign('action', $this->__('New category'));
            $this->_category  = new $this->entity(); // new ModName_Enity_Categories
        }
        
        return true;
    }

    

    function handleCommand(Zikula_Form_View $view, &$args)
    {   
        $module = FormUtil::getPassedValue('module', null, "GET");
        $type   = FormUtil::getPassedValue('type',   null, "GET");
        $func   = FormUtil::getPassedValue('func',   null, "GET");

        $url = ModUtil::url($module, $type, $func);
        if ($args['commandName'] == 'cancel') {
            return $view->redirect($url);
        } else if (substr($args['commandName'], 0, 6) == 'remove') {
            $id = substr($args['commandName'], 7);
            $category = $this->entityManager->find($this->entity, $id);
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            return $this->view->redirect($url);
        } else if (substr($args['commandName'], 0, 4) == 'edit') {
            $id = substr($args['commandName'], 5);
            $url = ModUtil::url($module, $type, $func, array( 'id' => $id));
            return $this->view->redirect($url);
        }
        
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        

        // load form values
        $data = $view->getValues();
        

        $this->_category->merge($data);
        $this->entityManager->persist($this->_category);
        $this->entityManager->flush();
        

        return $this->view->redirect($url);
    }

}
