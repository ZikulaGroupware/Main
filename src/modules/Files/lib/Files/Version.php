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

class Files_Version extends Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $meta = array();
        $meta['displayname']    = $this->__('Files');
        $meta['description']    = $this->__('A simple file manger');
        //! module url (lower case without spaces and different to displayname)
        $meta['url']            = $this->__('Files');
        $meta['version']        = '1.0.0';
        $meta['author']         = 'Fabian Wuertz';
        $meta['contact']        = 'http://fabian.wuertz.org';
        $meta['securityschema'] = array('Files::'  => '::');
        return $meta;
    }
    
}
