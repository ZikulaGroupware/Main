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