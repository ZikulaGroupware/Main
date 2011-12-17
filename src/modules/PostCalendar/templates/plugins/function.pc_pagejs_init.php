<?php
/**
 * @package     PostCalendar
 * @copyright   Copyright (c) 2009, Craig Heydenburg, Sound Web Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
/**
 * pc_pagejs_init: include the required javascript in header if needed
 *
 * @author Craig Heydenburg
 * @param  none
 */
function smarty_function_pc_pagejs_init($params, &$smarty)
{
    unset($params);
    $dom = ZLanguage::getModuleDomain('PostCalendar');
    if (_SETTING_OPEN_NEW_WINDOW) {
        $javascript = "
            $$('.event_details').each(function(link){
                new Zikula.UI.Window(link, {title:'" . __('PostCalendar Event', $dom) ."'});
            });";
        PageUtil::addVar("footer", "<script type='text/javascript'>$javascript</script>");
    }
    if (_SETTING_USE_POPUPS) {
        $javascript = "
            Zikula.UI.Tooltips($$('.tooltips'));
            ";
        PageUtil::addVar("footer", "<script type='text/javascript'>$javascript</script>");
    }
    return;
}
