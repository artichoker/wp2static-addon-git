<?php

namespace WP2StaticGit;
class Deployer {

    const DEFAULT_NAMESPACE = 'wp2static-addon-git/default';

    public function __construct() {}

    public function commit( string $processed_site_path ) : void {
        // check if dir exists
        if ( ! is_dir( $processed_site_path ) ) {
            return;
        }

        $namespace = self::DEFAULT_NAMESPACE;

        // get repository
        $repo = new \Cz\Git\GitRepository($processed_site_path);


        if ( ! $repo ) {
            $err = 'Trying to get repo: ' . $processed_site_path;
            \WP2Static\WsLog::l( $err );
        }

        if ( ! $repo->hasChanges()) {
            $err = 'Repo has no change: ' . $processed_site_path;
            \WP2Static\WsLog::l( $err );
        }

    }

}
