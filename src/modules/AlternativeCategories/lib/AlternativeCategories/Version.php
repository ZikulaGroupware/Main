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

class AlternativeCategories_Version extends Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $meta = array();
        $meta['description']    = __('An alternative categories module');
        $meta['displayname']    = __('Alternative categories');
        //!url must be different to displayname
        $meta['url']            = __('alternativecategories');
        $meta['version']        = '0.1.0';
        $meta['author']         = 'Fabian Wuertz';
        $meta['contact']        = '';
        // recommended and required modules
        $meta['dependencies'] = array();
        return $meta;
    }
}