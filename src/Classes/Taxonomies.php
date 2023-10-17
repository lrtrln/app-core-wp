<?php

namespace App\Classes;

use App\Traits\Singleton;

class Taxonomies
{
    use Singleton;

    protected function init()
    {
        add_action('init', [$this, 'register']);
    }

    /**
     * Registers the taxonomies defined in the files within the /src/taxonomies/ directory.
     *
     * This function retrieves PHP files within the /src/taxonomies/ directory and registers the taxonomies
     * defined in each file using the register_taxonomy() function.
     *
     * @return void
     */
    public function register(): void
    {
        $taxonomies      = [];
        $taxonomiesFiles = glob(CONTENTS . '/taxonomies/*.php');

        foreach ($taxonomiesFiles as $file) {
            $taxonomies[] = require $file;
        }

        foreach ($taxonomies as $taxonomy) {
            if (isset($taxonomy['taxonomy']) && isset($taxonomy['args'])) {
                register_taxonomy(
                    $taxonomy['taxonomy'],
                    $taxonomy['object_type'],
                    $taxonomy['args']
                );
            }
        }
    }
}
