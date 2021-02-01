<?php

/**
 * Plugin Name:       WP2Static Add-on: git
 * Plugin URI:        https://wp2static.com
 * Description:       Git add-on for WP2Static.
 * Version:           1.0.0-dev
 * Requires PHP:      7.3
 * Author:            Yuichiro Kato
 * Author URI:        https://www.sociohealth.co.jp
 * License:           Unlicense
 * License URI:       http://unlicense.org
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WP2STATIC_GIT_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP2STATIC_GIT_VERSION', '1.0.0-dev' );

if ( file_exists( WP2STATIC_GIT_PATH . 'vendor/autoload.php' ) ) {
    require_once WP2STATIC_GIT_PATH . 'vendor/autoload.php';
}

function run_wp2static_addon_git() : void {
    $controller = new WP2StaticGit\Controller();
    $controller->run();
}

register_activation_hook(
    __FILE__,
    [ 'WP2StaticGit\Controller', 'activate' ]
);

register_deactivation_hook(
    __FILE__,
    [ 'WP2StaticGit\Controller', 'deactivate' ]
);

run_wp2static_addon_git();

