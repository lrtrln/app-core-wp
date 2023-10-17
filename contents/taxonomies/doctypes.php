<?php

return [
    'taxonomy'    => TAX_DOCTYPES,
    'object_type' => ['resources'], //Object type or array of object types with which the taxonomy should be associated.
    'args' => [
        'labels'            => ['name' => __('Types de document'), 'menu_name' => __('Types de document')],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_menu'      => 'edit.php?post_type=' . TAX_DOCTYPES,
        'query_var'         => true,
        'rewrite'           => ['slug' => TAX_DOCTYPES],
        'show_in_rest'      => true,
    ],
];
