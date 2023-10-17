<?php

namespace App\Classes;

use App\Traits\Singleton;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Admin
{
    use Singleton;

    protected function init()
    {
        // do stuff here
        add_action('admin_menu', [$this, 'initMenu']);

        //carbon fields
        add_action('carbon_fields_register_fields', [$this, 'siteOptions']);
        add_action('after_setup_theme', [$this, 'crbLoad']);

        add_action('admin_enqueue_scripts', [$this, 'styles']);
    }

    public function initMenu()
    {
        $pageRole    = 'edit_pages';
        $iconBase64  = 'iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAFN++nkAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAgRJREFUeNpi/P//PwMInGVkhDCgwPj/f0YQzcRAANBZASMxJjDCXA8XgHkTFwAIIEYc4eAINOkAE9mup44kI0H/Eu1XgADCkESPdzQA9j/REUVRLNNV8wNKNCtCgxyEEylx9gJQOoVhdEmAACIpqtANGEZRNaqZnppBqWgDJTYH4iqkSXE2IzSXkednYGZ4AM0QHwjmKlIAQAAR1EygNGUgtqSlajQPWPoatXjU4lGLSbUYVGwZDpSPLyA1bQQHKqg/IDmCkZTGGlXjGFgeKyK1zRZQrZIYdqkaIIDw+piCKpEBW7N4tAAZtXjU4lGLRy0etXjU4lGLibLYcUAshvboQU2YAwMV1I7UasiTE8ewhnwjzVuZ0JYmLqn3QCwwEK1MQUoSH6XZ6QDUZwcGJB8DLSc58VGtAAFa/gHq+0aqJK7RsppaACDAKApqSnqTFAC8g7ODNqRHXNIa9fCoh0c9POrhUQ+PenjUw6MeHvXwqIdHPTzq4VEPj3p4qHoYOooFGkZrHEkxDBo7bmBALAIpZMCymm84J+kJ0FgHeR40kPpgJOXhAwyIZdaKDDSYPxnMhdYDaIzD8v2CkVRKg/I4aFE9IynD98OmWgJ6ugFp0VkiLQu9QVcPAz0N2hEhCPU81Qs9lsFcZ0JnGMCr3s8yMioAqfmUmjlgs1qjTctRD9MGAAAMKaGGUDUgFgAAAABJRU5ErkJggg==';
        $iconDataUri = 'data:image/svg+xml;base64,' . $iconBase64;

        add_menu_page(PROJECT, PROJECT, $pageRole, PROJECT, PROJECT, $iconDataUri, 4);
    }

    public function siteOptions()
    {
        Container::make('theme_options', __('Paramètres site'))
            ->add_fields([
                Field::make('rich_text', 'opt_ban', 'Bannière alerte'),
                Field::make('radio', 'opt_ban_on', 'Afficher bannière')
                    ->set_options([
                        '0' => 'Non',
                        '1' => 'Oui',
                    ]),
                Field::make('rich_text', 'opt_site_info', 'Info siège'),
            ]);
    }

    public function crbLoad()
    {
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public static function getThemeName()
    {
        return wp_get_theme()
            ->get('Name');
    }

    function styles(): void
    {
        global $wp_styles;

        add_theme_support('editor-styles');
        wp_enqueue_style('admin-custom', APPURL . '/assets/css/editor.css');
    }
}
