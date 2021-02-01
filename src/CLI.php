<?php

namespace WP2StaticGit;

use WP_CLI;


/**
 * WP2StaticGit WP-CLI commands
 *
 * Registers WP-CLI commands for WP2StaticGit under main wp2static cmd
 *
 * Usage: wp wp2static options set GitBucket mybucketname
 */
class CLI {

    /**
     * Git commands
     *
     * @param string[] $args CLI args
     * @param string[] $assoc_args CLI args
     */
    public static function Git(
        array $args,
        array $assoc_args
    ) : void {
        $action = isset( $args[0] ) ? $args[0] : null;

        if ( empty( $action ) ) {
            WP_CLI::error( 'Missing required argument: <options>' );
        }

        if ( $action === 'options' ) {
            WP_CLI::line( 'TBC setting options for Git addon' );
        }
    }
}

