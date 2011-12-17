<h3>{gt text='New posts in the forum'}</h3>

{foreach from=$forum_posts item="post"}
{$post.forum_name}:
<a href="{$post.last_post_url_anchor}">{$post.topic_title}</a>
<span class="z-sub">
    {gt text='by' comment="e.g. written by Drak"} {$post.poster_name}
    ({$post.topic_time|date_format:'%d.%m %H:%M'})
</span><br />
{/foreach}