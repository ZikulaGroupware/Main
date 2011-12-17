{include file='user/menu.tpl' tag=$tag}

{modurl modname='Wikula' type='user' func='main' tag=$tag|urlencode assign='pageurl'}
<div class="z-informationmsg">
    {gt text='Pages linking to <a href="%1$s">%2$s</a>' tag1=$pageurl|safehtml tag2=$tag|hyphen2space|safehtml}
</div>

<div id="wikula">
    <div class="page">
        {foreach from=$pages item='page'}
        <a href="{modurl modname='Wikula' type='user' func='main' tag=$page.from_tag|urlencode}" title="{$page.from_tag|safehtml}">{$page.from_tag|safehtml}</a><br />
        {foreachelse}
        <em class="wikula_error">{gt text='There are no backlinks to this page'}</em>
        {/foreach}
    </div>
</div>


