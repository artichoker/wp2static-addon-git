<?php

namespace WP2StaticGit;

class Controller {
    public function run() : void {
        add_filter(
            'wp2static_add_menu_items',
            [ 'WP2StaticGit\Controller', 'addSubmenuPage' ]
        );

        add_action(
            'admin_post_wp2static_git_save_options',
            [ $this, 'saveOptionsFromUI' ],
            15,
            1
        );

        add_action(
            'wp2static_deploy',
            [ $this, 'deploy' ],
            15,
            2
        );

        add_action(
            'admin_menu',
            [ $this, 'addOptionsPage' ],
            15,
            1
        );

        do_action(
            'wp2static_register_addon',
            'wp2static-addon-git',
            'deploy',
            'Git',
            'https://github.com/artichoker/wp2static-addon-git',
            'Commit & Push when static site processed'
        );

        if ( defined( 'WP_CLI' ) ) {
            \WP_CLI::add_command(
                'wp2static git',
                [ CLI::class, 'git' ]
            );
        }
    }

    /**
     *  Get all add-on options
     *
     *  @return mixed[] All options
     */
    public static function getOptions() : array {
        global $wpdb;
        $options = [];

        $table_name = $wpdb->prefix . 'wp2static_addon_git_options';

        $rows = $wpdb->get_results( "SELECT * FROM $table_name" );

        foreach ( $rows as $row ) {
            $options[ $row->name ] = $row;
        }

        return $options;
    }

    /**
     * Seed options
     */
    public static function seedOptions() : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_git_options';

        $query_string =
            "INSERT IGNORE INTO $table_name (name, value, label, description) " .
            'VALUES (%s, %s, %s, %s);';
        
        $query = $wpdb->prepare(
            $query_string,
            'remoteName',
            'origin',
            'remoteName',
            'remoteName'
        );

        $wpdb->query( $query );
        $query = $wpdb->prepare(
            $query_string,
            'commitMessage',
            'commit from wp2static',
            'commitMessage',
            'commitMessage'
        );

        $wpdb->query( $query );

    }

    /**
     * Save options
     *
     * @param mixed $value option value to save
     */
    public static function saveOption( string $name, $value ) : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_git_options';

        $query_string = "INSERT INTO $table_name (name, value) VALUES (%s, %s);";
        $query = $wpdb->prepare( $query_string, $name, $value );

        $wpdb->query( $query );
    }

    public static function renderGitPage() : void {
        self::createOptionsTable();
        self::seedOptions();

        $view = [];
        $view['nonce_action'] = 'wp2static-git-options';
        $view['uploads_path'] = \WP2Static\SiteInfo::getPath( 'uploads' );

        $git_path = \WP2Static\SiteInfo::getPath( 'uploads' ) . 'wp2static-processed-site';
        $view['git_path'] = $git_path;

        $view['options'] = self::getOptions();

        $repo = new \Cz\Git\GitRepository($git_path);

        if ( ! $repo ) {
            $err = 'Trying to get repo: ' . $git_path;
            \WP2Static\WsLog::l( $err );
            $view['currentBranch'] = '<strong style="color:red">git repo not found!</strong>';
            $view['currentBranch'] = '<strong style="color:red">git repo not found!</strong>';
            $view['status'] = '<strong style="color:red">git repo not found!</strong>';
            $view['remoteBranches'] = '<strong style="color:red">git repo not found!</strong>';
        }else{
            $view['currentBranch'] = $repo->getCurrentBranchName();
            $view['localBranches'] = "['" . implode("','", $repo->getLocalBranches()) . "']";
            $view['status'] = implode("<br>", $repo->execute(array('status')));
            $remote = $repo->execute(array('remote'));
            if(!$remote){
                $view['remoteBranches'] = '<strong style="color:red">remote not found!</strong>';
            }else{
                $view['remoteBranches'] = implode("<br>", $repo->execute(array('remote')));
            }
        }


        require_once __DIR__ . '/../views/git-page.php';
    }


    public function deploy( string $processed_site_path, string $enabled_deployer ) : void {
        if ( $enabled_deployer !== 'wp2static-addon-git' ) {
            return;
        }

        \WP2Static\WsLog::l( 'Git Addon start commit and push' );

        $git_deployer = new Deployer();
        $git_deployer->commit( $processed_site_path );
    }

    public static function createOptionsTable() : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_git_options';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(191) NOT NULL,
            value VARCHAR(255) NOT NULL,
            label VARCHAR(255) NULL,
            description VARCHAR(255) NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        // dbDelta doesn't handle unique indexes well.
        $indexes = $wpdb->query( "SHOW INDEX FROM $table_name WHERE key_name = 'name'" );
        if ( 0 === $indexes ) {
            $result = $wpdb->query( "CREATE UNIQUE INDEX name ON $table_name (name)" );
            if ( false === $result ) {
                \WP2Static\WsLog::l( "Failed to create 'name' index on $table_name." );
            }
        }
    }

    public static function activateForSingleSite(): void {
        self::createOptionsTable();
        self::seedOptions();
    }

    public static function deactivateForSingleSite() : void {
    }

    public static function deactivate( bool $network_wide = null ) : void {
        if ( $network_wide ) {
            global $wpdb;

            $query = 'SELECT blog_id FROM %s WHERE site_id = %d;';

            $site_ids = $wpdb->get_col(
                sprintf(
                    $query,
                    $wpdb->blogs,
                    $wpdb->siteid
                )
            );

            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::deactivateForSingleSite();
            }

            restore_current_blog();
        } else {
            self::deactivateForSingleSite();
        }
    }

    public static function activate( bool $network_wide = null ) : void {
        if ( $network_wide ) {
            global $wpdb;

            $query = 'SELECT blog_id FROM %s WHERE site_id = %d;';

            $site_ids = $wpdb->get_col(
                sprintf(
                    $query,
                    $wpdb->blogs,
                    $wpdb->siteid
                )
            );

            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::activateForSingleSite();
            }

            restore_current_blog();
        } else {
            self::activateForSingleSite();
        }
    }

    /**
     * Add WP2Static submenu
     *
     * @param mixed[] $submenu_pages array of submenu pages
     * @return mixed[] array of submenu pages
     */
    public static function addSubmenuPage( array $submenu_pages ) : array {
        $submenu_pages['git'] = [ 'WP2StaticGit\Controller', 'renderGitPage' ];

        return $submenu_pages;
    }

    public static function saveOptionsFromUI() : void {
        check_admin_referer( 'wp2static-git-options' );

        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_git_options';

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['remoteName'] ) ],
            [ 'name' => 'remoteName' ]
        );

        $wpdb->update(
            $table_name,
            [ 'value' => sanitize_text_field( $_POST['commitMessage'] ) ],
            [ 'name' => 'commitMessage' ]
        );

        wp_safe_redirect( admin_url( 'admin.php?page=wp2static-addon-git' ) );
        exit;
    }

    /**
     * Get option value
     *
     * @return string option value
     */
    public static function getValue( string $name ) : string {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_addon_git_options';

        $sql = $wpdb->prepare(
            "SELECT value FROM $table_name WHERE" . ' name = %s LIMIT 1',
            $name
        );

        $option_value = $wpdb->get_var( $sql );

        if ( ! is_string( $option_value ) ) {
            return '';
        }

        return $option_value;
    }

    public function addOptionsPage() : void {
        add_submenu_page(
            '',
            'Git Commit Options',
            'Git Commit Options',
            'manage_options',
            'wp2static-addon-git',
            [ $this, 'renderGitPage' ]
        );
    }
}

