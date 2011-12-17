<?php


/**
 * Copyright Groupware Team 2011
 *
 * @license GNU/AGPLv3 (or at your option, any later version).
 * @package Groupware
 * @link https://github.com/ZikulaGroupware/GroupwareModule
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Groupware_Api_User extends Zikula_AbstractApi 
{

    /**
    * Return all groupware modules
    *
    * @return array list of all groupware modules
    */

    public function getModules()
    {
        return array(
            'Tasks'        => $this->__('Tasks'),
            'Dizkus'       => $this->__('Forum'), 
            'Wikula'       => $this->__('Wiki'),
            'PostCalendar' => $this->__('Calendar'),
            'AddressBook'  => $this->__('Adressbook'),
            'Files'        => $this->__('Files'), 
        );
    }
    
    public function isAvailable()
    {
        $isAvailable = false;
        $groupwareModules = $this->getModules();
        while ($module = current($groupwareModules) and !$isAvailable) {
            
            $isAvailable = ModUtil::available(key($groupwareModules));
            next($groupwareModules);
        }
        return $isAvailable;
    }

    
}