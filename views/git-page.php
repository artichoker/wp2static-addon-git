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
</form>

