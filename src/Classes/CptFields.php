<?php

namespace App\Classes;

use App\Traits\Singleton;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class CptFields
{
    use Singleton;

    protected function init()
    {
        // do stuff here
        add_action('carbon_fields_register_fields', [$this, 'register']);
    }

    /**
     * Registers custom fields using Carbon_Fields.
     *
     * This function iterates through the files located in the "contents/fields" directory,
     * collects field data from each file, and then creates and registers the custom fields
     * using the Carbon_Fields Container and Field classes.
     *
     * @return void
     */
    public function register()
    {
        //$fields = [];
        $files = glob(CONTENTS . '/fields/*.php');

        foreach ($files as $file) {
            $fields = require($file);

            if (is_callable($fields)) {
                $fields();
            }
        }
    }
}
