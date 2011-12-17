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

/**
 * Smarty function to display a login in the menu
 *
 * Example
 * {adminlink}
 *
 * Two additional defines need adding to a xanthia theme for this plugin
 * _CREATEACCOUNT and _YOURACCOUNT
 *
 * @author       Fabian Wuertz
 * @since        21/10/03
 * @see          function.myuserPageEdit.php::smarty_function_myuserPageEdit()
 * @param        array       $params      All attributes passed to this function from the template
 * @param        object      &$smarty     Reference to the Smarty object
 * @param        string      $start       start delimiter
 * @param        string      $end         end delimiter
 * @param        string      $seperator   seperator
 * @return       string      user links
 */
function smarty_function_groupwaremenu($params, &$smarty)
{

    // Security check
    if ( !SecurityUtil::checkPermission('User::', '::', ACCESS_READ) ) {
        return LogUtil::registerPermissionError();
    }
    
    $dom = ZLanguage::getThemeDomain('Groupware');
    
    $output  = '<nav><div id="navigation"><ul id="apps" class="svg">';
        
    $output .= '<li>'.
               '<a style="background-image:url('.System::getBaseUrl().'themes/Groupware/images/home.png);" href="'.System::getHomepageUrl().'">'.__('Home', $dom).'</a>'.
               '</li>';   
        
    $modules['Tasks']  = array(
        'title' => __('Tasks', $dom),
        'icon' => 'dialog-ok.png',
    ); 
    $modules['Dizkus'] = array(
        'title' => __('Forum', $dom),
        'icon' => 'empathy-away.png',
    );
    $modules['Wikula'] = array(
        'title' => __('Wiki', $dom),
        'icon' => 'gtk-edit.png',
    );
    $modules['PostCalendar'] = array(
        'title' => __('Calendar', $dom),
        'icon' => 'appointment-soon.png',
    );    
    $modules['AddressBook'] = array(
        'title' => __('Adressbook', $dom),
        'icon' => 'stock_people.png',
    );
    $modules['Files'] = array(
        'title' => __('Files', $dom),
        'icon' => 'folder.png',
    );
    $modules['Users'] = array(
        'title' => __('Settings', $dom),
        'icon' => 'avatar-default.png',
    );
    foreach($modules as $modname => $value) {
        if( ModUtil::available($modname) ) {
            $title = $value['title'];
            $icon  = $value['icon'];
            $style = '';
            if(!empty($icon)) {
                $style = 'background-image:url('.System::getBaseUrl().'/themes/Groupware/images/'.$icon.');';
            }
            $active = '';
            if(ModUtil::getName() == $modname) {
                $active = 'class="active"';
            }            
            if( $modname == 'Users') {
                 $output .= '</ul><ul id="settings" class="svg">'."\n";
            }
            $output .= '<li>'.
                       '<a style="'.$style.'" '.$active.' href="'.ModUtil::url($modname, 'user', 'main').'">'.$title.'</a>'.
                       '</li>'."\n\n";
        }
    }
    
   
    
    
    // Security check
    if (SecurityUtil::checkPermission( '.*', '.*', ACCESS_ADMIN)) {
        $type = FormUtil::getPassedValue('type',   null, "GET");
        $classname = 'groupware_mainmenu';
            if($type == 'admin') {
                $classname .= '2';
            }
        $output .= '<li>'.
                   '<a style="background-image:url('.System::getBaseUrl().'themes/Groupware/images/settings.png);" href="'.ModUtil::url('adminpanel', 'admin', 'adminpanel').'">'.
                   __('Administration', $dom).
                    '</a>'.
                   '</li>';
    }
    
    if(UserUtil::isLoggedIn()) {
        $output .= '<li>'.
               '<a style="background-image:url('.System::getBaseUrl().'themes/Groupware/images/system-log-out.png);" href="'.ModUtil::url('Users', 'user', 'logout').'">'.
               __('Log-out', $dom).
               '</a>'.
               '</li>';
    }
    
    
    $output .= '</ul></div></nav>';
    return $output;
    
}
