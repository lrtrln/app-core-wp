<?php

/*
Plugin Name:  Application Core
Plugin URI:   https://lrtrln.fr
Description:  Application core for custom CPT, config, menus, tax, etc.
Version:      1.1.0
Author URI:   https://lrtrln.fr
Text Domain:  lrtrln
License:      GPL
Requires PHP: 8.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// autoloader
require_once 'vendor/autoload.php';

define('APPDIR', WPMU_PLUGIN_DIR . '/app-core');
define('APPURL', WPMU_PLUGIN_URL . '/app-core');
define('CONTENTS', APPDIR . '/contents');

define('BLOCK_CAT', 'custom');

// project
define('PROJECT', 'ProjectName');

// CPT
define('CPT_PARTNERS', 'partners');
// TAX
define('TAX_DOCTYPES', 'doctypes');

$namespace = '\App\Classes';
$loadClass = [
    'Config',
    'Menus',
    'Cpt',
    'CptFields',
    'Blocks',
    'Taxonomies',
    'Admin',
];

foreach ($loadClass as $class) {
    $c = $namespace . '\\' . $class;
    $c::getInstance();
}

require_once 'src/Classes/helpers.php';
