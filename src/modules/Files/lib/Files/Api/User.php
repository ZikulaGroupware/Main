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

class Files_Api_User extends Zikula_AbstractApi 
{

    /**
    * Select an category by the given category ID
    *
    * @param int $cid category ID
    * @return category data
    */

    public function newDir($args)
    {
        $path = $args['path'];
        $name = $args['name'];
        
        if(empty($name)) {
            return false;
        }

        $status = mkdir($path.$name);
        if($status) {
            return true;
        }
        
        LogUtil::registerError($this->__('Could not create folder!'));
        return $false;
    }
    
    
    public function rmFile($args)
    {
        $path = $args['path'];
        $name = $args['name'];
        
        if(empty($name)) {
            return false;
        }

        $status = unlink($path.$name);
        if($status) {
            return true;
        }
        
        if(!file_exists($path.$name)){
            LogUtil::registerError($this->__('Could not delete file! File does not exist!'));
        }
        
        LogUtil::registerError($this->__('Could not delete file!'));
        return $false;
    }
    
    
    public function rmDir($args)
    {
        $path = $args['path'];
        $name = $args['name'];
        
        if(empty($name)) {
            return false;
        }

        $status = rmdir($path.$name);
        if($status) {
            return true;
        }
        
        
        $list = glob($path.'*');
        if( !empty($list)) {
            LogUtil::registerError($this->__('Dir is not empty!'));
            return false;
        }


        LogUtil::registerError($this->__('Could not remove dir!'));
        return $false;
    }
    
    
    public function uploadFile($args)
    {
        $path = $args['path'];
        $name = $args['name'];
        
        if(empty($name) or empty($_FILES[$name])) {
            return false;
        }
        
        
        $target_path = $path . basename( $_FILES[$name]['name']); 

        $status = move_uploaded_file($_FILES[$name]['tmp_name'], $target_path);
        if($status) {
            return true;
        }



        LogUtil::registerError($this->__('Could not upload file!'));
        return $false;
    }
    
    
    
            
        
    
    
    
    public function listDir($path)
    {    
        if ($handle = opendir($path)) {

            $files = array();
            while (false !== ($filename = readdir($handle))) {
                if($filename != '.' and $filename != '..' and $filename != '.htaccess' and $filename != '.DS_Store') {
                    $icon = 'file.png';
                    $mime = mime_content_type($path.$filename);
                    if($mime == 'directory') {
                        $icon = 'folder.png';
                    } else if( substr($mime, 0, 5)  == 'image') {
                        $icon = 'image.png';
                    }

                    $size = $this->human_filesize( filesize($path.$filename), 1);
                    
                    $timeformat= 'F j, Y, H:i';
                    $mdate = date($timeformat, filemtime($path.$filename));
                    $mdiff = $this->timesince(filemtime($path.$filename));


                    
                    $files[] = array(
                        'name'  => $filename,
                        'mime'  => $mime,
                        'icon'  => 'filetypes/'.$icon,
                        'size'  => $size,
                        'mdate' => $mdate,
                        'mdiff' => $mdiff
                    );
                }
            }
            closedir($handle);
        }
        return $files;
    }
    
    function human_filesize($bytes, $decimals = 2) {
          $sz = 'BKMGTP';
          $factor = floor((strlen($bytes) - 1) / 3);
          return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' '. @$sz[$factor];
    }
    
    
    function timesince( $stime ) {

        $diffu = array(  'seconds'=>2, 'minutes' => 120, 'hours' => 7200, 'days' => 172800, 'months' => 5259487,  'years' =>  63113851 );
        $diff = time() - $stime;
        $dt = '0 seconds ago';
        foreach($diffu as $u => $n){ if($diff>$n) {$dt = floor($diff/(.5*$n)).' '.$u.' ago';} }

        return $dt;
    }
    
}