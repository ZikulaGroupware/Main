<?php

/**
 * Copyright Files Team 2011
 *
 * @license GNU/AGPLv3 (or at your option, any later version).
 * @package Files
 * @link https://github.com/ZikulaGroupware/Files
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */


class Files_Installer extends Zikula_AbstractInstaller
{
    /**
    * initialise the template module
    * This function is only ever called once during the lifetime of a particular
    * module instance
    */
    public function install()
    {
        
        return true;
    }

    /**
    * Upgrade the errors module from an old version
    *
    * This function must consider all the released versions of the module!
    * If the upgrade fails at some point, it returns the last upgraded version.
    *
    * @param        string   $oldVersion   version number string to upgrade from
    * @return       mixed    true on success, last valid version string or false if fails
    */
    public function upgrade($oldversion)
    {
        // Update successful
        return true;
    }

    /**
    * delete the errors module
    * This function is only ever called once during the lifetime of a particular
    * module instance
    */
    public function uninstall()
    {
        return true;
    }

}
