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
{if isset($linkusername) and linkusername}
{$comment.uid|profilelinkbyuid}
{else}
{$comment.uname|safetext}
{/if}
{/if}
&nbsp;({$comment.date|date_format:'%d.%m'|safehtml})</span>
<br />

{/foreach}