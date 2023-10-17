<?php

namespace App\Classes;

use App\Traits\Singleton;

class Menus
{
    use Singleton;

    protected function init()
    {
        add_action('init', 			[$this, 'register']);
        add_action('footerNav', 	[$this, 'footerNav']);
        add_action('postFooterNav', [$this, 'postFooterNav']);
        add_action('headerNav', 	[$this, 'headerNav']);
        add_action('headerBtns', 	[$this, 'headerBtns']);
        add_action('footerBtns', 	[$this, 'footerBtns']);
    }

    /**
     * Registers navigation menus.
     *
     * This static function uses WordPress's register_nav_menus() function to register navigation menus
     * at the specified theme locations.
     */
    public static function register()
    {
        register_nav_menus(
            [
                'primary_navigation'    => __('Navigation principale'),
                'footer_navigation'     => __('Footer principal'),
                'footer_secondary' 	    => __('Footer secondaire'),
                'header_buttons' 	    => __('Header boutons'),
                'footer_buttons' 	    => __('Footer boutons'),
            ]
        );
    }

    /**
     * Retrieves and displays the footer navigation menu.
     *
     * This function uses WordPress's wp_nav_menu()
     *
     * @return string|false|void The navigation menu formatted in HTML.
     */    
    public function footerNav()
    {
        return wp_nav_menu([
            'theme_location' 	=> 'footer_navigation',
            'container' 		=> false,
            'container_class' 	=> 'nav',
            'menu_class' 		=> 'nav-footer',
        ]);
    }

    /**
     * Retrieves and displays the post footer navigation menu.
     *
     * This function uses WordPress's wp_nav_menu()
     *
     * @return string|false|void The navigation menu formatted in HTML.
     */
    public function postFooterNav()
    {
        return wp_nav_menu([
            'theme_location' 	=> 'footer_secondary',
            'container' 		=> false,
            'container_class' 	=> 'nav',
            'menu_class' 		=> 'nav-postfooter',
        ]);
    }

    /**
     * Retrieves and displays the header navigation menu.
     *
     * This function uses WordPress's wp_nav_menu()
     *
     * @return string|false|void The navigation menu formatted in HTML.
     */
    public function headerNav()
    {
        return wp_nav_menu([
            'theme_location' 	=> 'primary_navigation',
            'container' 		=> false,
            'container_class' 	=> 'nav',
            'menu_class' 		=> 'nav-header',
        ]);
    }

    /**
     * Retrieves and displays the header buttons navigation menu.
     *
     * This function uses WordPress's wp_nav_menu()
     *
     * @return string|false|void The navigation menu formatted in HTML.
     */
    public function headerBtns()
    {
        return wp_nav_menu([
            'theme_location' 	=> 'header_buttons',
            'container' 		=> false,
            'container_class' 	=> 'nav',
            'menu_class' 		=> 'nav-header-btn',
        ]);
    }

    /**
     * Retrieves and displays the footer buttons navigation menu.
     *
     * This function uses WordPress's wp_nav_menu()
     *
     * @return string|false|void The navigation menu formatted in HTML.
     */
    public function footerBtns()
    {
        return wp_nav_menu([
            'theme_location' 	=> 'footer_buttons',
            'container' 		=> false,
            'container_class' 	=> 'nav',
            'menu_class' 		=> 'nav-footer-btn',
        ]);
    }
}
