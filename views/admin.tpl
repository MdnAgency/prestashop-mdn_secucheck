<h2>Security Check by MDN</h2>
<div class="alert alert-info">All our data is from <a target="_blank" href="https://security.friendsofpresta.org/feed.xml">https://security.friendsofpresta.org/feed.xml</a></div>

<div class="card">
    <div class="card-header">
        <h3 class="d-inline-block card-header-title">Modules</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th>Module</th>
                <th>Description</th>
                <th>Publication</th>
                <th>Status</th>
            </tr>
            {foreach from=$modules_entries item=entry}
                <tr>
                    <td>
                        <strong>{$entry.module}</strong>
                        {if $entry.is_installed}
                            <strong style="color: red">/!\</strong><br/>
                            Installed : {$entry.module_version}
                        {/if}
                    </td>
                    <td>
                        <p class="">{$entry.summary}</p>
                        <a href="{$entry.url}" target="_blank">More details</a>
                    </td>
                    <td>
                        {$entry.published}
                    </td>
                    <td>
                        {if $entry.state == 0}
                            <form method="post">
                                <input type="hidden" name="entry" value="{$entry.id}">
                                <button name="check_security" value="1" class="badge badge-critical">TO CHECK</button>
                            </form>
                        {elseif $entry.state == 1}
                            <label class="badge badge-success">CHECKED</label>
                        {elseif $entry.state == 2}
                            <label class="badge badge-dark">NOT CONCERNED</label>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="d-inline-block card-header-title">Core</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th>Exploit</th>
                <th>Description</th>
                <th>Publication</th>
                <th>Status</th>
            </tr>
            {foreach from=$other_entries item=entry}
                <tr>
                    <td>
                        <strong>{$entry.module}</strong>
                    </td>
                    <td>
                        <p class="">{$entry.summary}</p>
                        <a href="{$entry.url}" target="_blank">More details</a>
                    </td>
                    <td>
                        {$entry.published}
                    </td>
                    <td>
                        {if $entry.state == 0}
                            <form method="post">
                                <input type="hidden" name="entry" value="{$entry.id}">
                                <button name="check_security" value="1" class="badge badge-critical">TO CHECK</button>
                            </form>
                        {elseif $entry.state == 1}
                            <label class="badge badge-success">CHECKED</label>
                        {elseif $entry.state == 2}
                            <label class="badge badge-dark">NOT CONCERNED</label>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>