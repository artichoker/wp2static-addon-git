<?php

namespace WP2StaticGit;
class Deployer {

    const DEFAULT_NAMESPACE = 'wp2static-addon-git/default';

    public function __construct() {}

    public function commit( string $processed_site_path ) : void {
        // check if dir exists
        if ( ! is_dir( $processed_site_path ) ) {
            \WP2Static\WsLog::l( 'path not found. ' . $processed_site_path );
            return;
        }else{
            \WP2Static\WsLog::l( 'local repo:' . $processed_site_path );
        }

        $options = \WP2StaticGit\Controller::getOptions();
        $remoteName = print_r($options['remoteName']->value, true);
        $commitMessage = print_r($options['commitMessage']->value, true);
        $userName = print_r($options['userName']->value, true);
        $userEmail = print_r($options['userEmail']->value, true);

        // Using 2 library for git operation. It is very dirty but work. 
        $repo = new \Cz\Git\GitRepository($processed_site_path);
        $gitWrapper  = new \GitWrapper\GitWrapper();
        $gitWrapper ->setPrivateKey('/home/c2452357/.ssh/id_rsa');
        $git = $gitWrapper->workingCopy($processed_site_path);

        if ( ! $git ) {
            $err = 'Trying to get repo: ' . $processed_site_path;
            \WP2Static\WsLog::l( $err );
        }

        if ( ! $git->hasChanges()) {
            $err = 'Repo has no change: ' . $processed_site_path;
            \WP2Static\WsLog::l( $err );
        }else{

            $branch = $repo->getCurrentBranchName();
            $repo->addAllChanges();

            $git->config('user.name', $userName);
            $git->config('user.email', $userEmail);

            try {
                $git->commit($commitMessage);
                $git->push($remoteName, $branch);
            } catch (\Throwable $e) {
                \WP2Static\WsLog::l( $e );
            }

            \WP2Static\WsLog::l( 'push ' . $remoteName . ' ' . $branch . ' done!'   );

        }

    }

}
