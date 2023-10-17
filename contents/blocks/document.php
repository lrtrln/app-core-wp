<?php
use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make(__('Document'))
    ->add_fields([
        Field::make('file', 'block_file', __('Fichier')),
    ])
    ->set_icon('media-document')
    ->set_category('custom-category', BLOCK_CAT, 'smiley')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $fileId   = $fields['block_file'];
        $file     = wp_get_attachment_url($fields['block_file']);
        $fileMeta = getFileInfoById($fields['block_file']);
        $size     = size_format($fileMeta['size']);
        $type     = strtoupper(wp_check_filetype($file)['ext']);
        $metas    = sprintf('(%s - %s)', $type, $size);
        ?>
    <div class="block-document">
        <a href="<?=$file?>" title="Télécharger le fichier">
            <?=get_the_title($fileId)?> <?=$metas?>
        </a>
    </div>
<?php
});