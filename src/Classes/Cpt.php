<?php

namespace App\Classes;

use App\Traits\Singleton;

class Cpt
{
    use Singleton;

    protected function init()
    {
        // do stuff here
        add_action('init', [$this, 'register']);
    }

    /**
     * Registers the 'partners' custom post type.
     *
     * This function uses WordPress's register_post_type() function to register a new custom post type
     * with the identifier defined by the CPT_PARTNERS constant. The post type is registered with a set of labels,
     * public visibility, a menu position, support for various features, a rewrite slug, and other settings.
     *
     * @return void
     */
    public function register() :void
    {
        $postTypes = [];
        $postTypesFiles = glob(CONTENTS . '/post-types/*.php');

        foreach ($postTypesFiles as $file) {
            $postTypes[] = require($file);
        }

        foreach ($postTypes as $cpt) {
            if (isset($cpt['postType']) && isset($cpt['args'])) {
                register_post_type($cpt['postType'], $cpt['args']);
            }
        }
    }
}
