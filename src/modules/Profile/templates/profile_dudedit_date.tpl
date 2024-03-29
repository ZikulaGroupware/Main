<div class="{$class|default:'z-formrow'}">
    {if $required}
    <p id="advice-required-prop_{$attributename}" class="custom-advice z-formnote" style="display:none">
        {gt text='Sorry! A required personal info item is missing. Please correct and try again.'}
    </p>
    {/if}

    <label for="prop_{$attributename}">
        {gt text=$proplabeltext}
        {if $required}<span class="z-mandatorysym">{gt text='*'}</span>{/if}
    </label>

    <span class="z-formnote">
        {gt text='%Y-%m-%d' domain='zikula' comment='This is from the core domain' assign='duddateformat'}
        <input class="profile_dateinput {if $required}required{/if} {if $error}z-form-error{/if}" id="prop_{$attributename}" name="dynadata[{$attributename}]" value="{dateformat datetime=$timestamp|default:'-' format=$duddateformat}" size="30" />
        {calendarinput objectname='' htmlname="prop_$attributename" dateformat=$dudformat ifformat='%Y-%m-%d' defaultdate=$value}
    </span>

    {if $note neq ''}
    <em class="z-sub z-formnote">{$note}</em>
    {/if}
    <p id="prop_{$attributename}_error" class="z-formnote z-errormsg {if !$error}z-hide{/if}">{if $error}{$error}{/if}</p>
</div>
