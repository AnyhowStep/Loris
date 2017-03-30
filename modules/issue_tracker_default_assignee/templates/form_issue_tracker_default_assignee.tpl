{literal}
    <style>
        th {
            background-color:#064785;
        }
        .row {
            margin: 5px;
        }
    </style>
{/literal}
<section class="list-group">
    {foreach from=$center_assignee_arr item=center_assignee}
        {if $center_assignee.arr|@count > 0}
            <div class="list-group-item">
                <strong>{$center_assignee.name}</strong>
                <div class="row">
                    <div class="col-lg-2 bg-primary">
                        Category
                    </div>
                    <div class="col-lg-2 bg-primary">
                        User
                    </div>
                    <div class="col-lg-2 bg-primary">
                        Remove
                    </div>
                </div>
                {foreach from=$center_assignee.arr item=assignee}
                    <div class="row">
                        <div class="col-lg-2">
                            {$assignee.issue_category_name}
                        </div>
                        <div class="col-lg-2">
                            {$assignee.username} ({$assignee.real_name})
                        </div>
                        <div class="col-lg-2">
                            {assign var="delete_id" value="delete-`$center_assignee.id`-`$assignee.issue_category_id`-`$assignee.user_id`"}
                            <!--Why are all our endpoints treated as directories?-->
                            <form id="{$delete_id}" method="POST" action="/issue_tracker_default_assignee/delete/">
                                <input type="hidden" name="center_id" value="{$center_assignee.id}"/>
                                <input type="hidden" name="issue_category_id" value="{$assignee.issue_category_id}"/>
                                <input type="hidden" name="user_id" value="{$assignee.user_id}"/>
                                <span class="glyphicon glyphicon-remove btn btn-primary" onclick="if (confirm('Remove default assignee?')) { this.parentNode.submit(); }"></span>
                            </form>
                        </div>
                    </div>
                {/foreach}
            </div>
        {/if}
    {/foreach}
</section>
<section>
    <form method="POST" action="/issue_tracker_default_assignee/replace/" class="row">
        <!--Should really be a PUT but, IIRC, HTML forms don't exactly do PUT requests...-->
        <div class="col-lg-4">
            <p>Center</p>
            <select name="center_id" class="form-control">
                {foreach from=$center_arr item=item}
                    <option value="{$item.id}">{$item.name}</option>
                {/foreach}
            </select>
        </div>
        <div class="col-lg-4">
            <p>Category</p>
            <select name="issue_category_id" class="form-control">
                {foreach from=$category_arr item=item}
                    <option value="{$item.id}">{$item.name}</option>
                {/foreach}
            </select>
        </div>
        <div class="col-lg-4">
            <p>User</p>
            <select name="user_id" class="form-control">
                {foreach from=$user_arr item=item}
                    <option value="{$item.id}">{$item.username} ({$item.real_name})</option>
                {/foreach}
            </select>
        </div>
        <div class="col-lg-12">
            <input type="submit" class="form-control" value="Add/Replace"/>
        </div>
    </form>
</section>