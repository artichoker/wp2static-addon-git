<?php
// phpcs:disable Generic.Files.LineLength.MaxExceeded
// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @var mixed[] $view
 */
?>

<h2>Options</h2>

<h3>git</h3>

<form
    name="wp2static-git-save-options"
    method="POST"
    action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

    <?php wp_nonce_field( $view['nonce_action'] ); ?>
    <input name="action" type="hidden" value="wp2static_git_save_options" />

<table class="widefat striped">
    <tbody>

        <tr>
            <td style="width:50%;">
                <label
                    for="git_path"
                >local repository path</label>
            </td>
            <td>
                <?php echo $view['git_path']; ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['remoteName']->name; ?>"
                ><?php echo $view['options']['remoteName']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['remoteName']->name; ?>"
                    name="<?php echo $view['options']['remoteName']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['remoteName']->value !== '' ? $view['options']['remoteName']->value : ''; ?>"
                />
            </td>
        </tr>
        <tr>
            <td style="width:50%;">
                current branch
            </td>
            <td>
                <?php echo $view['currentBranch']; ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">
                local branches
            </td>
            <td>
                <?php echo $view['localBranches']; ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">
                status
            </td>
            <td>
                <?php echo $view['status']; ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">
                remote branches
            </td>
            <td>
                <?php echo $view['remoteBranches']; ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['commitMessage']->name; ?>"
                ><?php echo $view['options']['commitMessage']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['commitMessage']->name; ?>"
                    name="<?php echo $view['options']['commitMessage']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['commitMessage']->value !== '' ? $view['options']['commitMessage']->value : ''; ?>"
                />
            </td>
        </tr>

    </tbody>
</table>

<br>

    <button class="button btn-primary">Save git Options</button>

<div>
<h2>How to add remote github repository</h2>
<h3>1.Generate an SSH key</h3>
<pre>
cd ~                 #Your home directory
ssh-keygen -t rsa    #Press enter for all values
</pre>
<h3>2. Registor the SSH key to your github</h3>
<p>If it is a GitHub repository and you have administrative privileges, go to <a href="https://github.com/settings/ssh">settings</a> and click 'add SSH key'. Copy the contents of your ~/.ssh/id_rsa.pub into the field labeled 'Key'.</p>

<h3>3.Set your remote URL</h3>
<pre>git remote add git+ssh://git@github.com/username/reponame.git</pre>
<pre>git remote show origin</pre>
<pre>git push origin branch</pre>
</div>

</form>

