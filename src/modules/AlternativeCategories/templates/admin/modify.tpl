{pageaddvar name="stylesheet" value="modules/AlternativeCategories/style/style.css"}

{if $type == 'admin'}
{adminheader}
{/if}

<div class="z-admin-content-pagetitle">
    {icon type="info" size="small"}
    <h3>{gt text="Categories"}</h3>
</div>

{form cssClass="z-form"}
{formvalidationsummary}

<table class="z-datatable">
    <thead>
    <tr>
        <th>{gt text='Id'}</th>
        <th width=100%>{gt text='Name'}</th>
        <th>{gt text='Actions'}</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$categories item='category'}
    <tr class="{cycle values='z-odd,z-even'}">
        <td>{$category.id}</td>
        <td>{$category.name}</td>
        <td>
            {assign value=$category.id var="id"}
            {formbutton id="edit_$id" class="AlternativeCategories_edit" commandName="edit_$id"}
            {formbutton id="remove_$id" class="AlternativeCategories_remove" commandName="remove_$id"}
            <script type="text/javascript">
                Event.observe("remove_{{$id}}", "click", function(event){
                   var response = confirm("Are you sure you want to remove {{$id}}?");
                   if(!response){
                      event.stop();
                   }
                });
            </script>
        </td>
    </tr>
    {/foreach}
</tbody>
</table>



<fieldset>
    <legend>{$action}</legend>
    <div class="z-formrow">
        {formlabel for="name"  __text='Name'}
        {formtextinput size="40" maxLength="255" id="name"}
    </div>

    <div class="z-formbuttons z-buttons">
        {formbutton id="test" class="z-bt-ok" commandName="save" __text="Save"}
        {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
    </div>
</fieldset>


{/form}

{if $type == 'admin'}
{adminfooter}
{/if}