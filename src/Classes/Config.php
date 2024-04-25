<?php

/**
 * Class wrapper for removing unnecessary WP functions
 *
 */

namespace App\Classes;

use App\Traits\Singleton;

if (!defined('ABSPATH')) {
    exit;
}

class Config
{
    use Singleton;

    protected function init()
    {
        add_action('init', [$this, 'optimize']);
    }

    public function optimize()
    {
        /*------------------------------
        OPTIMIZATIONS
        -------------------------------*/
        $optimize = new \App\Classes\Optimize(
            [
                'blockExternalHTTP'      => false,
                'deferCSS'               => false,
                'deferJS'                => false,
                'disableComments'        => true,
                'disableEmbed'           => false,
                'disableEmoji'           => true,
                'disableFeeds'           => false,
                'disableHeartbeat'       => false,
                'disablejQuery'          => true,
                'disablejQueryMigrate'   => true,
                'disableRestApi'         => false,
                'disableRSD'             => true,
                'disableShortlinks'      => true,
                'disableVersionNumbers'  => true,
                'disableWLWManifest'     => true,
                'disableWPVersion'       => true,
                'disableXMLRPC'          => true,
                'jqueryToFooter'         => true,
                'limitCommentsJS'        => true,
                'limitRevisions'         => true,
                'removeCommentsStyle'    => true,
                'slowHeartbeat'          => true,
                'removeQueryString'      => false,
                'disableDashicons'       => false,
                'blockUserEnumeration'   => true,
                'redirect404ToHomepage'  => false,
                'enableSvgUpload'        => true,
                'removeDashboardWidgets' => false,
                'disableThemeEditor'     => true,
                'disableBlockCssInline'  => true,
                'disableBlockJsInline'   => true,
                'disableFontLibrary'     => false,
            ]
        );
    }
}
