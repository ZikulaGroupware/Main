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