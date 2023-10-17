<?php

$menuIcon = 'dashicons-tickets-alt';

return [
    'postType' => CPT_PARTNERS,
    'args'     => [
        'label'               => __('Partenaires'),
        'labels'              => [
            'name'               => __('Partenaires'),
            'singular_name'      => __('Partenaire'),
            'add_new'            => __('Ajouter'),
            'add_new_item'       => __('Ajouter partenaire'),
            'edit'               => __('Edition'),
            'edit_item'          => __('Editer partenaire'),
            'new_item'           => __('Créer partenaire'),
            'view'               => __('Voir'),
            'view_item'          => __('Voir partenaire'),
            'search_items'       => __('Recherche'),
            'not_found'          => __('Aucun partenaire trouvé'),
            'parent'             => __('Parent'),
            'not_found_in_trash' => __('Aucun partenaire trouvé dans la corbeille'),
        ],
        'description'         => '',
        'public'              => true,
        'menu_position'       => 3,
        'show_in_menu'        => PROJECT,
        'show_in_nav_menus'   => true,
        'supports'            => ['title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'author', 'revisions'],
        'rewrite'             => ['slug' => CPT_PARTNERS, 'with_front' => true],
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierachical'         => false,
        'can_export'          => true,
        'exclude_from_search' => false,
        'menu_icon'           => $menuIcon,
        'show_in_rest'        => true,
    ],
];
