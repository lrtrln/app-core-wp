<?php

/**
 * Class Optimize
 *
 * This class provides a set of optimization options for WordPress.
 *
 * @version 1.2
 */

namespace App\Classes;

use \WP_Error as WP_Error;

if (!defined('ABSPATH')) {
    exit;
}

class Optimize
{
    /**
     * Holds the configurations for the optimizations.
     *
     * @var array $optimize An array of optimization settings.
     */
    private $optimize = [];

    /**
     * Constructor.
     *
     * @param array $optimizations An array of optimization options to conduct.
     *
     * @since 1.0
     */
    public function __construct(array $optimizations = [])
    {
        $defaults = [
            'blockExternalHTTP'      => false,
            'deferCSS'               => false,
            'deferJS'                => true,
            'disableComments'        => false,
            'disableEmbed'           => false,
            'disableEmoji'           => true,
            'disableFeeds'           => false,
            'disableHeartbeat'       => false,
            'disablejQuery'          => false,
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
            'blockUserEnumeration'   => false,
            'redirect404ToHomepage'  => false,
            'enableSvgUpload'        => false,
            'removeDashboardWidgets' => false,
            'disableThemeEditor'     => true,
            'disableBlockCssInline'  => true,
            'disableBlockJsInline'   => true
        ];

        $this->optimize = wp_parse_args($optimizations, $defaults);
        $this->optimize();
    }

    /**
     * Hit it! Runs eachs of the functions if enabled
     *
     * @since 1.0
     * @return void
     */
    private function optimize()
    {
        foreach ($this->optimize as $key => $value) {

            if ($value === true && method_exists($this, $key)) {
                $this->$key();
            }
        }
    }

    /**
     * Adds a filter to block external HTTP requests.
     *
     * This function uses WordPress's add_filter() function to add a filter to block external HTTP requests.
     * The filter is added to the 'pre_http_request' hook and returns a new WP_Error object with the message
     * 'http_request_failed'. The filter is only added if the current request is not an admin request.
     *
     * @since 1.0
     * @return void
     */
    private function blockExternalHTTP()
    {
        if (!is_admin()) {
            add_filter('pre_http_request', function () {
                return new WP_Error('http_request_failed', __('Request blocked by WP Optimize.'));
            }, 100);
        }
    }

    /**
     * Defers all CSS using loadCSS
     *
     * @since 1.0
     * @return void
     */
    private function deferCSS()
    {

        // Rewrite our object context
        $object = $this;

        // Dequeue our CSS and save our styles. Please note - this function removes conditional styles for older browsers
        add_action('wp_enqueue_scripts', function () use ($object) {

            // Bail out if we are uzing the customizer preview
            if (is_customize_preview()) {
                return;
            }

            global $wp_styles;

            // Save the queued styles
            foreach ($wp_styles->queue as $style) {
                $object->styles[] = $wp_styles->registered[$style];
                $dependencies     = $wp_styles->registered[$style]->deps;

                if (!$dependencies) {
                    continue;
                }

                // Add dependencies, but only if they are not included yet
                foreach ($dependencies as $dependency) {
                    $object->styles[] = $wp_styles->registered[$dependency];
                }
            }

            // Remove duplicate values because of the dependencies
            $object->styles = array_unique($object->styles, SORT_REGULAR);

            // Dequeue styles and their dependencies except for conditionals
            foreach ($object->styles as $style) {
                wp_dequeue_style($style->handle);
            }
        }, 9999);

        // Load our CSS using loadCSS
        add_action('wp_head', function () use ($object) {

            // Bail out if we are uzing the customizer preview
            if (is_customize_preview()) {
                return;
            }

            $output = '<script>function loadCSS(a,b,c,d){"use strict";var e=window.document.createElement("link"),f=b||window.document.getElementsByTagName("script")[0],g=window.document.styleSheets;return e.rel="stylesheet",e.href=a,e.media="only x",d&&(e.onload=d),f.parentNode.insertBefore(e,f),e.onloadcssdefined=function(b){for(var c,d=0;d<g.length;d++)g[d].href&&g[d].href.indexOf(a)>-1&&(c=!0);c?b():setTimeout(function(){e.onloadcssdefined(b)})},e.onloadcssdefined(function(){e.media=c||"all"}),e}';
            foreach ($object->styles as $style) {
                if (isset($style->extra['conditional'])) {
                    continue;
                }

                // Load local assets
                if (strpos($style->src, 'http') === false) {
                    $style->src = site_url() . $style->src;
                }

                $output .= 'loadCSS("' . $style->src . '", "", "' . $style->args . '");';
            }
            $output .= '</script>';

            echo $output;
        }, 9999);
    }

    /**
     * Defers all JS
     *
     * @since 1.0
     * @return void
     */
    private function deferJS()
    {
        // Defered JS breaks the customizer or the Gutenberg Editor, hence we skip it here
        if (is_customize_preview() || is_admin()) {
            return;
        }

        add_filter('script_loader_tag', function ($tag) {
            return str_replace(' src', ' defer="defer" src', $tag);
        }, 10, 1);
    }

    /**
     * Disables the support and appearance of comments
     *
     * @since 1.0
     * @return void
     */
    private function disableComments()
    {

        // by default, comments are closed.
        if (is_admin()) {
            update_option('default_comment_status', 'closed');
        }

        // Closes plugins
        add_filter('comments_open', '__return_false', 20, 2);
        add_filter('pings_open', '__return_false', 20, 2);

        // Disables admin support for post types and menus
        add_action('admin_init', function () {

            $post_types = get_post_types();

            foreach ($post_types as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        });

        // Removes menu in left dashboard meun
        add_action('admin_menu', function () {
            remove_menu_page('edit-comments.php');
        });

        // Removes comment menu from admin bar
        add_action('wp_before_admin_bar_render', function () {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('comments');
        });
    }

    /**
     * Removes the Embed Javascript and References
     *
     * @since 1.0
     * @return void
     */
    private function disableEmbed()
    {

        add_action('wp_enqueue_scripts', function () {
            wp_deregister_script('wp-embed');
        }, 100);

        add_action('init', function () {

            // Removes the oEmbed JavaScript.
            remove_action('wp_head', 'wp_oembed_add_host_js');

            // Removes the oEmbed discovery links.
            remove_action('wp_head', 'wp_oembed_add_discovery_links');

            // Remove the oEmbed route for the REST API epoint.
            remove_action('rest_api_init', 'wp_oembed_register_route');

            // Disables oEmbed auto discovery.
            remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

            // Turn off oEmbed auto discovery.
            add_filter('embed_oembed_discover', '__return_false');
        });
    }

    /**
     * Disables the access to Rest API
     *
     * @since 1.0
     * @return void
     */
    private function disableRestApi()
    {

        // Remove the references to the JSON api
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
        remove_action('rest_api_init', 'wp_oembed_register_route');
        add_filter('embed_oembed_discover', '__return_false');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_action('template_redirect', 'rest_output_link_header', 11, 0);

        // Disable the API completely
        add_filter('json_enabled', '__return_false');
        add_filter('json_jsonp_enabled', '__return_false');
        add_filter('rest_enabled', '__return_false');
        add_filter('rest_jsonp_enabled', '__return_false');
    }

    /**
     * Removes WP Emoji
     *
     * @since 1.0
     * @return void
     */
    private function disableEmoji()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

        /**
         * Removes Emoji from the TinyMCE Editor
         *
         * @param array $plugins The plugins hooked onto the TinyMCE Editor
         */
        add_filter('tiny_mce_plugins', function ($plugins) {
            if (!is_array($plugins)) {
                return [];
            }

            return array_diff($plugins, ['wpemoji']);
        }, 10, 1);
    }

    /**
     * Removes links to RSS feeds
     *
     * @since 1.0
     * @return void
     */
    private function disableFeeds()
    {
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'feed_links', 2);
        add_action('do_feed', [$this, 'disableFeedsHook'], 1);
        add_action('do_feed_rdf', [$this, 'disableFeedsHook'], 1);
        add_action('do_feed_rss', [$this, 'disableFeedsHook'], 1);
        add_action('do_feed_rss2', [$this, 'disableFeedsHook'], 1);
        add_action('do_feed_atom', [$this, 'disableFeedsHook'], 1);
    }

    /**
     * Removes the actual feed links
     *
     * @since 1.0
     * @return void
     */
    public function disableFeedsHook()
    {
        wp_die('<p>' . __('Feed disabled by WP Optimize.') . '</p>');
    }

    /**
     * Removes the WP Heartbeat Api. Caution: this disables the autosave functionality
     *
     * @since 1.0
     * @return void
     */
    private function disableHeartbeat()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_deregister_script('heartbeat');
        });
    }

    /**
     * Deregisters jQuery.
     *
     * @since 1.0
     * @return void
     */
    private function disablejQuery()
    {
        add_action('wp_enqueue_scripts', function () {
            wp_deregister_script('jquery');
        }, 100);
    }

    /**
     * Deregisters jQuery Migrate by removing the dependency.
     *
     * @since 1.0
     * @return void
     */
    private function disablejQueryMigrate()
    {

        add_filter('wp_default_scripts', function ($scripts) {
            if (!empty($scripts->registered['jquery'])) {
                $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
            }
        });
    }

    /**
     * Disables RSD Links, used by pingbacks
     *
     * @since 1.0
     * @return void
     */
    private function disableRSD()
    {
        remove_action('wp_head', 'rsd_link');
    }

    /**
     * Removes the WP Shortlink
     *
     * @since 1.0
     * @return void
     */
    private function disableShortlinks()
    {
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    }

    /**
     * Removes the version hook on scripts and styles
     *
     * @since 1.0
     * @uses MT_WP_Optimize::no_scripts_styles_version_hook
     */
    private function disableVersionNumbers()
    {
        add_filter('style_loader_src', [$this, 'disableVersionNumbersHook'], 9999);
        add_filter('script_loader_src', [$this, 'disableVersionNumbersHook'], 9999);
    }

    /**
     * Removes version numbers from scripts and styles.
     * The absence of version numbers increases the likelyhood of scripts and styles being cached.
     *
     * @since 1.0
     * @param string @target_url The url of the script
     */
    public function disableVersionNumbersHook($src)
    {

        if (strpos($src, 'ver=' . get_bloginfo('version'))) {
            $src = remove_query_arg('ver', $src);
        }

        return $src;
    }

    /**
     * Removes WLW manifest bloat
     *
     * @since 1.0
     * @return void
     */
    private function disableWLWManifest()
    {
        remove_action('wp_head', 'wlwmanifest_link');
    }

    /**
     * Removes the WP Version as generated by WP
     *
     * @since 1.0
     * @return void
     */
    private function disableWPVersion()
    {
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', '__return_null');
    }

    /**
     * Disables XML RPC. Warning, makes some functions unavailable!
     *
     * @since 1.0
     * @return void
     */
    private function disableXMLRPC()
    {

        if (is_admin()) {
            update_option('default_ping_status', 'closed'); // Might do something else here to reduce our queries
        }

        add_filter('xmlrpc_enabled', '__return_false');
        add_filter('pre_update_option_enable_xmlrpc', '__return_false');
        add_filter('pre_option_enable_xmlrpc', '__return_zero');

        /**
         * Unsets xmlrpc headers
         *
         * @since 1.0
         * @param array $headers The array of wp headers
         */
        add_filter('wp_headers', function ($headers) {
            if (isset($headers['X-Pingback'])) {
                unset($headers['X-Pingback']);
            }

            return $headers;
        }, 10, 1);

        /**
         * Unsets xmlr methods for pingbacks
         *
         * @since 1.0
         * @param array $methods The array of xmlrpc methods
         */
        add_filter('xmlrpc_methods', function ($methods) {
            unset($methods['pingback.ping']);
            unset($methods['pingback.extensions.getPingbacks']);

            return $methods;
        }, 10, 1);
    }

    /**
     * Puts jquery inside the footer
     *
     * @since 1.0
     * @return void
     */
    private function jqueryToFooter()
    {
        add_action('wp_enqueue_scripts', function () {
            wp_deregister_script('jquery');
            wp_register_script('jquery', includes_url('/js/jquery/jquery.js'), false, null, true);
            wp_enqueue_script('jquery');
        });
    }

    /**
     * Limits the comment reply JS to the places where it's needed
     *
     * @since 1.0
     * @return void
     */
    private function limitCommentsJS()
    {

        add_action('wp_print_scripts', function () {
            if (is_singular() && (get_option('thread_comments') == 1) && comments_open() && get_comments_number()) {
                wp_enqueue_script('comment-reply');
            } else {
                wp_dequeue_script('comment-reply');
            }
        }, 100);
    }

    /**
     * Limits post revisions
     *
     * @since 1.0
     * @return void
     */
    private function limitRevisions()
    {

        if (defined('WP_POST_REVISIONS') && (WP_POST_REVISIONS != false)) {
            add_filter('wp_revisions_to_keep', function ($num, $post) {
                return 5;
            }, 10, 2);
        }
    }

    /**
     * Removes the styling added to the header for recent comments
     *
     * @since 1.0
     * @return void
     */
    private function removeCommentsStyle()
    {
        add_action('widgets_init', function () {
            global $wp_widget_factory;
            remove_action('wp_head', [$wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style']);
        });
    }

    /**
     * Adds a filter to modify the heartbeat settings.
     *
     * This function uses WordPress's add_filter() function to add a filter to modify the heartbeat settings.
     * The filter is added to the 'heartbeat_settings' hook and sets the interval to 60 seconds.
     *
     * @since 1.0
     * @return void
     */
    private function slowHeartbeat()
    {

        add_filter('heartbeat_settings', function ($settings) {
            $settings['interval'] = 60;

            return $settings;
        });
    }

    /**
     * Adds filters to remove query strings from script and style loader URLs.
     *
     * This function uses WordPress's add_filter() function to add two filters to remove query strings from
     * script and style loader URLs. The filters are added to the 'script_loader_src' and 'style_loader_src' hooks.
     * The 'removeQueryStringsSplit' method is used as the callback function for both filters.
     * The filters are only added if the current request is not an admin request.
     *
     * @since 1.0
     * @return void
     */
    private function removeQueryString()
    {
        if (!is_admin()) {
            add_filter('script_loader_src', [$this, 'removeQueryStringsSplit'], 15);
            add_filter('style_loader_src', [$this, 'removeQueryStringsSplit'], 15);
        }
    }

    /**
     * Removes query strings from a URL.
     *
     * This function uses regular expressions to remove query strings from a URL.
     * The function takes a single parameter, which is the URL to remove query strings from.
     * The function splits the URL into two parts using the '?' character as the delimiter.
     * The function then splits the first part into two parts using the '&' character as the delimiter.
     * The function returns the first part of the URL, which is the URL without the query strings.
     *
     * @param string $src The URL to remove query strings from.
     * @since 1.0
     * @return string The URL without the query strings.
     */
    public function removeQueryStringsSplit($src)
    {
        $output = preg_split("/(&ver|\?ver)/", $src);

        return $output[0];
    }

    /**
     * Disable Dashicons on Front-end
     * Dashicons are utilized in the admin console, and if not using them to
     * load any icons on front-end then you may want to disable it.
     *
     * @since 1.0
     * @return void
     */
    private function disableDashicons()
    {
        add_action('wp_enqueue_scripts', function () {
            if (current_user_can('update_core')) {
                wp_dequeue_style('dashicons');
                wp_deregister_style('dashicons');
            }
        });
    }

    /**
     * Prevents user enumeration on author pages.
     *
     * This function hooks into the 'template_redirect' action in WordPress
     * and redirects users to the home page if the current page is an author
     * page, effectively preventing user enumeration.
     *
     * @since 1.1
     * @return void
     */
    private function blockUserEnumeration()
    {
        add_action('template_redirect', function () {
            if (is_author()) {
                $redirect_url = home_url('/');
                wp_redirect($redirect_url, 301);

                exit;
            }
        });
    }

    /**
     * Redirects 404 errors to the homepage.
     *
     * This function hooks into the 'wp' action in WordPress and checks if the
     * current page is a 404 error, and if it's not an admin page, a cron job,
     * or an XML-RPC request, it performs a 301 permanent redirect to the site's
     * homepage.
     *
     * @since 1.1
     * @return void
     */
    private function redirect404ToHomepage()
    {
        add_filter('wp', function () {
            if (!is_404()
                || is_admin()
                || (defined('DOING_CRON') && DOING_CRON)
                || (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST)) {
                return;
            } else {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . home_url());

                exit();
            }
        });
    }

    /**
     * Enables SVG file uploads in WordPress.
     *
     * This function hooks into WordPress filters to allow the uploading of SVG
     * files. It modifies the file type and extension checks to include SVG
     * files and adds the SVG MIME type to the allowed list.
     *
     * @since 1.1
     * @return void
     */
    private function enableSvgUpload()
    {
        add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
            $filetype = wp_check_filetype($filename, $mimes);

            return [
                'ext'             => $filetype['ext'],
                'type'            => $filetype['type'],
                'proper_filename' => $data['proper_filename'],
            ];
        }, 10, 4);

        add_filter('upload_mimes', function ($mimes) {
            $mimes['svg'] = 'image/svg+xml';

            return $mimes;
        });
    }

    /**
     * Removes unnecessary widgets from the WordPress dashboard.
     *
     * This function removes specific dashboard widgets using the global
     * variable $wp_meta_boxes. It targets widgets like the welcome panel,
     * quick press, primary, right now, and activity feed.
     *
     * @since 1.1
     * @return void
     */
    private function removeDashboardWidgets()
    {
        add_action('wp_dashboard_setup', function () {
            global $wp_meta_boxes;
            remove_action('welcome_panel', 'wp_welcome_panel');
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
        });
    }

    /**
     * Disables the theme editor in WordPress.
     *
     * This function defines the 'DISALLOW_FILE_EDIT' constant, which prevents
     * users from accessing the theme editor in the WordPress admin panel.
     *
     * @since 1.1
     * @return void
     */
    private function disableThemeEditor()
    {
        define('DISALLOW_FILE_EDIT', true);
    }

    /**
     * Disable block CSS inline styles.
     *
     * This function dequeues specific stylesheets related to Gutenberg blocks
     * in order to prevent them from being included inline in the HTML output.
     * These stylesheets are dequeued during the 'wp_enqueue_scripts' action hook.
     *
     * @since 1.2
     * @access private
     * @return void
     */
    private function disableBlockCssInline()
    {
        add_action('wp_enqueue_scripts', function() {
            wp_dequeue_style('global-styles');
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-paragraph');
            wp_dequeue_style('core-block-supports');
            wp_dequeue_style('wp-block-navigation');
            wp_dequeue_style('wp-block-navigation-link');
            wp_dequeue_style('wp-block-template-skip-link');
            wp_dequeue_style('wp-block-group');
            wp_dequeue_style('wp-block-post-title');
            wp_dequeue_style('wp-block-site-title');
        });
    }

    /**
     * Disable block JavaScript inline scripts.
     *
     * This function dequeues a specific JavaScript file related to Gutenberg blocks
     * in order to prevent it from being included inline in the HTML output.
     * This JavaScript file is dequeued during the 'wp_enqueue_scripts' action hook.
     *
     * @since 1.2
     * @access private
     *
     * @return void
     */
    private function disableBlockJsInline()
    {
        add_action('wp_enqueue_scripts', function() {
            wp_dequeue_script('wp-block-template-skip-link');
        });
    }
}
