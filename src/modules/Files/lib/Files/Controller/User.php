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

class Files_Controller_User extends Zikula_AbstractController
{

    //-----------------------------------------------------------//
    //-- View ---------------------------------------------------//
    //-----------------------------------------------------------//

    /**
    * Main page - Redirect to viewAll
    *
    * @return The view redirect
    */

    public function main()
    {
        return $this->view();
    }

    /**
    * View all Files
    *
    * @return The view var
    */

    public function view()
    {
        
        // Security check
        if (!SecurityUtil::checkPermission('Files::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        
        
       
            
        $pathWidthCommas = FormUtil::getPassedValue('path', '');
        if($pathWidthCommas == ',') {
            $pathWidthCommas = '';
        }
        $path = str_replace(',', '/', $pathWidthCommas);
        
        if(!empty($pathWidthCommas)) {
            $pathAsArray = explode(',', $pathWidthCommas);
            $pathWidthCommas .= ',';
        } else {
            $pathAsArray = array();
        }
        $this->view->assign('pathWidthCommas', $pathWidthCommas);
        $this->view->assign('path', $path);
        $this->view->assign('pathAsArray', $pathAsArray);
        
        
        // current full path
        $path = 'files/'.$path.'/';
        
        
        // new folder
        $newfolder = FormUtil::getPassedValue('newfolder', '');
        ModUtil::apiFunc($this->name, 'user', 'newDir', array(
            'name' => $newfolder,
            'path' => $path
        ));
        
        // rmfile
        $rmfile = FormUtil::getPassedValue('rmfile', '');
        ModUtil::apiFunc($this->name, 'user', 'rmFile', array(
            'name' => $rmfile,
            'path' => $path
        ));
        
        
        // rmfir
        $rmdir = FormUtil::getPassedValue('rmdir', '');
        ModUtil::apiFunc($this->name, 'user', 'rmDir', array(
            'name' => $rmdir,
            'path' => $path
        ));
        
        
        // uploadfile
        ModUtil::apiFunc($this->name, 'user', 'uploadFile', array(
            'name' => 'uploadfile',
            'path' => $path
        ));

        
        // get files and folders
        $files = ModUtil::apiFunc($this->name, 'user', 'listDir', $path);
        $this->view->assign('files', $files);

        
        return $this->view->fetch('user/view.tpl');
     
        
    }
 
    
    public function download()
    {
        
        

        $pathWidthCommas = FormUtil::getPassedValue('path', '');
        $path = str_replace(',', '/', $pathWidthCommas);
        $filename = FormUtil::getPassedValue('filename', '');

        
        if(!empty($path)) {
            $path .= '/';
        }
        $file = 'files/'.$path.$filename;
        
        
        
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        readfile($file);

        
        System::shutDown();
               
    
    }
}