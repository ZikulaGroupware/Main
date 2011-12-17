<?php

/**
 * post pending content to pending_content Event handler
 *
 * @author Craig Heydenburg
 */
class PostCalendar_Handlers {

    public static function pendingContent(Zikula_Event $event)
    {
        $dom = ZLanguage::getModuleDomain('PostCalendar');
        ModUtil::dbInfoLoad('PostCalendar');
        $dbtables = DBUtil::getTables();
        $columns = $dbtables['postcalendar_events_column'];
        $count = DBUtil::selectObjectCount('postcalendar_events', "WHERE $columns[eventstatus]=0");
        if ($count > 0) {
            $collection = new Zikula_Collection_Container('PostCalendar');
            $collection->add(new Zikula_Provider_AggregateItem('submission', _n('Calendar event', 'Calendar events', $count, $dom), $count, 'admin', 'listevents'));
            $event->getSubject()->add($collection);
        }
    }
    public static function getTypes(Zikula_Event $event) {
        $types = $event->getSubject();
        $types->add('PostCalendar_ContentType_PostCalEvent');
        $types->add('PostCalendar_ContentType_PostCalEvents');
    }
}