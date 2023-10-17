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
                'blockExternalHTTP'     => false, // Block requests to external http on the front-end side. Blocks all request that are done by plugins to external addresses.
                'deferCSS'              => false, // Adds defer="defer" to all enqueued JavaScript files.
                'deferJS'               => false, // Defers all registered scripts using the loadCSS function from the Filament Group.
                'disableComments'       => true,  // Disables the comments functionality and removes it from the admin menu.
                'disableEmbed'          => false, // Removes the script files that are enqueued by the WordPress media embed system.
                'disableEmoji'          => true,  // Removes the scripts that are enqueued for displaying emojis.
                'disableFeeds'          => false,  // Removes the post feeds.
                'disableHeartbeat'      => false, // Unregister the heartbeat scripts, which is usually responsible for autosaves.
                'disablejQuery'         => true,  // Removes the default jQuery script.
                'disablejQueryMigrate'  => true,  // Removes the jQuery Migrate script.
                'disableRestApi'        => false, // Disables the rest api.
                'disableRSD'            => true,  // Removes the RDS link in the head section of the site.
                'disableShortlinks'     => true,  // Removes the shortlinks in the head section of the site.
                'disableVersionNumbers' => true,  // Removes the version trail in enqueued scripts and styles.
                'disableWLWManifest'    => true,  // Removes the WLW Manifest links in the head section of the site.
                'disableWPVersion'      => true,  // Removes the WP version from the head section of the site.
                'disableXMLRPC'         => true,  // Disables the xmlrpc functionality.
                'jqueryToFooter'        => true,  // Moves the default jQuery script to the footer.
                'limitCommentsJS'       => true,  // Limits the JS for comments only to singular entities
                'limitRevisions'        => true,  // Limits the number of revisions to 5
                'removeCommentsStyle'   => true,  // Removes the .recentcomments styling in the head section
                'slowHeartbeat'         => true,  // Slows the heartbeat down to one per minute
                'removeQueryString'     => false, // Remove query strings from static resources
                'disableDashicons'      => false, // Dashicons are utilized in the admin console, and if not using them to load any icons on front-end then you may want to disable it
                'blockUserEnumeration'  => true, // Disable User Numeration
                'redirect404ToHomepage' => false,
                'enableSvgUpload'       => true,
                'removeDashboardWidgets'=> false,
                'disableThemeEditor'    => true
            ]
        );
    }
}
