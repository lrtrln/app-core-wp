<?php
use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make(__('Info'))
    ->add_fields([
        Field::make('radio', 'block_type', __('Type'))
            ->set_options([
                'info'  => 'Info',
                'alert' => 'Alerte',
            ]),
        Field::make('rich_text', 'block_info', __('Info')),
    ])
    ->set_icon('info-outline')
    ->set_category('custom-category', BLOCK_CAT, 'smiley')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $message = $fields['block_info'];
        $icon    = isset($fields['block_type']) ? $fields['block_type'] : 'info';
        ?>
    <div class="info-box">
        <?=_svg($icon)?>
        <?=$message?>
    </div>
<?php
});