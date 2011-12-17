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

class Wikula_Handler_ModifyConfig  extends Zikula_Form_AbstractHandler
{

    function initialize(Zikula_Form_View $view)
    {
        $this->view->caching = false;
        $this->view->assign($this->getVars());
        $editors[] = array(
            'text' => $this->__('Simple'),
            'value' => 'simple'
        );
        $editors[] = array(
            'text' => $this->__('Advanced'),
            'value' => 'advanced'
        );
        $this->view->assign('editors', $editors);
        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'cancel') {
            $url = ModUtil::url('Wikula', 'admin', 'modifyconfig' );
            return $view->redirect($url);
        }
        
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        
        $data = $view->getValues();
        $this->setVars($data);


        return true;



    }

}
