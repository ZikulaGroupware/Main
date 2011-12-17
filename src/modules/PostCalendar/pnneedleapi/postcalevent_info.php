<?php
/**
 * @package     PostCalendar
 * @copyright   Copyright (c) 2009, Craig Heydenburg, Sound Web Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Multihook event needle info
 * @param none
 * @return array()
 */
function postcalendar_needleapi_postcalevent_info()
{
    $info = array(
        'module'        => 'PostCalendar', // module name
        'info'          => 'POSTCALEVENT-{eventid-displaytype}', // possible needles
        'inspect'       => true,
        //'needle'        => array('http://', 'https://', 'ftp://', 'mailto://'),
        //'function'      => 'http',
        //'casesensitive' => false,
    );
    return $info;
}