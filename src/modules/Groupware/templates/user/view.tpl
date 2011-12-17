<h2>{gt text='Overview'}</h2>

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

<h3>{gt text='New posts in the forum'}</h3>

{foreach from=$forum_posts item="post"}
{$post.forum_name}:
<a href="{$post.last_post_url_anchor}">{$post.topic_title}</a>
<span class="z-sub">
    {gt text='by' comment="e.g. written by Drak"} {$post.poster_name}
    ({$post.topic_time|date_format:'%d.%m %H:%M'})
</span><br />
{/foreach}


<h3>{gt text="To-do"}</h3>


{foreach from=$tasks item="task"}
<a href="{modurl modname="Tasks" type='user' func='view' tid=$task.tid}">{$task.title}</a><br />
{foreachelse}
{gt text='There are no undone tasks!'}<br />
{/foreach}


<h3>{gt text="Last finished tasks"}</h3>


{foreach from=$finished_tasks item="finished_task"}
<a href="{modurl modname="Tasks" type='user' func='view' tid=$finished_task.tid}">{$finished_task.title}</a> 
<span class="z-sub">({$finished_task.done_date->format('d.m')})</span><br />
{foreachelse}
{gt text='There are no finished tasks!'}<br />
{/foreach}


<h3>
    {gt text="Last changes in the wiki"} 
    <a href="{modurl modname='Wikula' type='user' func='recentchangesxml' theme='rss'}" title="RSS">
        {img modname='wikula' src='rss.png' __title='RSS' __alt='RSS'}
    </a>
</h3>

{assign var='currentdate' value=''}
{foreach from=$wiki_pages key='date' item='pages'}
{foreach from=$pages item='page'}
    <a href="{modurl modname='Wikula' type='user' func='main' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
    <span class="z-sub">{gt text='by' comment="e.g. written by Drak"} {$page.user} ({$page.time->format('d.m')})</span>
    {if $page.note neq ''}<br /><span class="pagenote">[ {$page.note} ]</span>{/if}
    <br />
{/foreach}
{foreachelse}
{gt text='There are no recent changes'}
{/foreach}


<h3>{gt text="Last comments"}</h3>

{foreach from=$comments item=comment}
{if $comment.subject neq ''}
<a href="{$comment.url|safetext}#comment{$comment.id|safetext}">{$comment.subject|safetext}</a>
{else}
<a href="{$comment.url|safetext}#comment{$comment.id|safetext}">{$comment.comment|strip_tags:false|truncate:30|safetext}</a>
{/if}
<span class="z-sub">
{if $comment.uname neq ''}
{gt text="by"}
{if $linkusername}
{$comment.uid|profilelinkbyuid}
{else}
{$comment.uname|safetext}
{/if}
{/if}
&nbsp;({$comment.date|date_format:'%d.%m'|safehtml})</span>
<br />

{/foreach}




<h3>
    {gt text="Birthdays in the next 7 days"}
</h3>

{foreach from=$birthdays item="birthday"}
<a href="{modurl modname="AddressBook" type='user' func='getBirthdays'}">
    {$birthday.bday|dateformat} {$birthday.firstname} {$birthday.lastname}
</a><br />
{foreachelse}
{gt text='There are no birthdays!'}<br />
{/foreach}