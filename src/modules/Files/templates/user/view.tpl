<h2>    
    {if count($pathAsArray) == 0}
        {gt text="Files"}
    {else}
        <a href="{modurl modname='Files' type='user' func='view'}">
            {gt text="Files"}
        </a>


        {foreach from="$pathAsArray" item='directory' name='f'}
            {if $smarty.foreach.f.first}
                {assign value="$directory" var="backlink"}
            {else}
                {assign value="$backlink,$directory" var="backlink"}    
            {/if}
            &#187; 
            {if $smarty.foreach.f.last}
                {$directory}
            {else}
                <a href="{modurl modname='Files' type='user' func='view' path="$backlink"}">
                    {$directory}
                </a>
            {/if}
        {/foreach}
    {/if}

</h2><br />


{insert name='getstatusmsg'}

<form class="z-form" action="{modurl modname="Files" type="user" func="view"  path=$path}" method="post" enctype="application/x-www-form-urlencoded">
    <fieldset>
        <input type="text" name="newfolder" value="" />

        <span class="z-buttons">
            <input class="z-bt-small" type="submit" name="submit" value="{gt text='New folder'}" />
        </span>

    </fieldset>

</form>

        
<form class="z-form" action="{modurl modname="Files" type="user" func="view"  path=$path}" method="post" enctype="multipart/form-data">
    <fieldset>

        
        <input type="file" name="uploadfile" value="" />

        <span class="z-buttons">
            <input class="z-bt-small" type="submit" name="submit" value="{gt text='Add file'}" />
        </span>

    </fieldset>

</form>



<table class="z-datatable">
    <tbody>
     
       {if isset($upperpath)}
        <tr class="{cycle values='z-odd,z-even'}">
            <td width="18"></td>
            <td>
                <a href="{modurl modname='Files' type='user' func='view' path=$upperpath}">
                    ..
                </a>
            </td>
        </tr>
        {/if}
        
        {foreach from="$files" item="file}
         <tr class="{cycle values='z-odd,z-even'}">
            <td width="18">{img src=$file.icon __alt=$file.mime __title=$file.mime modname='Files'}</td>
            <td width="100%">
                {if $file.mime == 'directory'}
                    {assign value=$file.name var="name"}
                    <a href="{modurl modname='Files' type='user' func='view' path="$pathWidthCommas$name"}">
                        {$file.name}
                    </a>
                {else}
                    {$file.name}
                {/if}
            </td>
            <td nowrap>
                 {if $file.mime != 'directory'}
                     {$file.size}
                 {/if}
            </td>
            <td nowrap>
                 {if $file.mime != 'directory'}
                     <span title="{$file.mdate}">
                        {$file.mdiff
                     </span>}
                 {/if}
            </td>
            <td nowrap>
                 {if $file.mime != 'directory'}
                    <form class="z-form" action="{modurl modname="Files" type="user" func="view" path=$path}" method="post" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="rmfile" value="{$file.name}">
                        <input type="image" src="http://localhost/~bart/zikula/modules/Files/images/actions/remove.png" alt="Absenden">                    
                        <a href="{modurl modname='Files' type='user' func='download' path=$pathWidthCommas filename=$file.name}">
                            {img src='actions/download.png' __alt='Download' __title='Download' modname='Files'}
                        </a>
                    </form>
                {else}
                    <form class="z-form" action="{modurl modname="Files" type="user" func="view" path=$path}" method="post" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="rmdir" value="{$file.name}">
                        <input type="image" src="http://localhost/~bart/zikula/modules/Files/images/actions/remove.png" alt="Absenden">                    
                    </form>
                 {/if}
            </td>
         <tr />
         {foreachelse}
         <tr class="{cycle values='z-odd,z-even'}">
            <td>{gt text='No files available.'}</td>
         <tr />
        {/foreach}
    </tbody>
</table>