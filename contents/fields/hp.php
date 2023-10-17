<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

return function () {

    $ns = 'hp';

    Container::make('post_meta', __('Champs ressources'))
        ->where('post_id', '=', get_option('page_on_front'))
        ->add_fields([
            Field::make('image', "{$ns}_image", 'Image'),
            Field::make('text', "{$ns}_section_about_title", "Section présentation - Titre"),
            Field::make('complex', "{$ns}_section_about_cols", 'Colonnes')
                ->add_fields([
                    Field::make('text', "{$ns}_section_about_title", 'Titre'),
                    Field::make('textarea', "{$ns}_section_about_text", 'Paragraphe'),
                    Field::make('image', "{$ns}_section_about_image", 'Image'),
                    Field::make('text', "{$ns}_section_about_link", 'Lien'),
                ]),
        ]);
};
