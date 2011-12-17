<h3>
    {gt text='Dates in the next 7 days'}
    <a href="{modurl modname='PostCalendar' viewtype='xml' theme='rss'}" title="RSS">
        {img modname='wikula' src='rss.png' __title='RSS' __alt='RSS'}
    </a>
</h3>
{foreach from=$events item="event"}
<a href="{modurl modname='PostCalendar' type='user' viewtype='details' eid=$event.eid}">
    {$event.title}: {$event.eventDate} {$event.startTime}, {$event.location_info.event_location}
</a><br />
{foreachelse}
{gt text='No dates in the next 7 days!'}<br />
{/foreach}