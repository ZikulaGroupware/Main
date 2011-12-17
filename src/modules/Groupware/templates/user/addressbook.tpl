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