<?php

/**
 * Retrieves a string representation of the provided data.
 *
 * @param mixed $data The data to be processed. Can be a string, array, or object.
 * @return string|null The string representation of the data or null if no data is provided.
 */
function getError(mixed $data = null): string | null
{
    if ($data == null) {
        return null;
    }

    if (is_array($data) || is_object($data)) {
        $err = print_r($data, true);
    } else {
        $err = $data;
    }

    return $err;
}

/**
 * Logs the provided data to the error log.
 *
 * @param mixed $log The data to be logged. Can be a string, array, or object.
 * @return void
 */
function _log(mixed $data = null)
{
    date_default_timezone_set('Europe/Paris');
    $date = date('h:i:s', time());

    return error_log(sprintf('%s', getError($data)));
}

/**
 * Dumps the provided data in a styled <pre> tag and optionally terminates the script.
 *
 * @param mixed $data The data to be dumped. Can be a string, array, or object.
 * @param bool $die If true, the script will terminate after dumping the data. Default is false.
 * @return void
 */
function _dd(mixed $data, bool $die = false): void
{
    $class = "background:#333; color:white;border:1px dashed red; font-family: monospace; font-size: 12px; padding: 1em;";
    $error = getError($data);

    print <<<HTML
    <pre style="{$class}">
        {$error}
    </pre>
    HTML;

    if ($die === true) {
        die();
    }
}

/**
 * Checks if an action is registered in the WordPress action hook system.
 *
 * @param string $action The action to check for.
 * @return bool True if the action is registered, false otherwise.
 */
function checkRegisteredActions(string $action): bool
{
    global $wp_filter;

    if (isset($wp_filter[$action])) {
        $actions   = $wp_filter[$action];
        $callbacks = $actions->callbacks;

        _log($callbacks);

    } else {
        _log("L'action $action n'est pas enregistrée.");
    }
}

/**
 * Retrieves the SVG path for a given name.
 *
 * @param string $name The name of the SVG.
 * @return string|null The SVG path if found, or null if not found.
 */
function getSvgPath(string $name): string | null
{
    $svgPaths = include CONTENTS .'/svg/svg.php';

    if (array_key_exists($name, $svgPaths)) {
        return $svgPaths[$name];
    }

    return null;
}

/**
 * Generates an SVG element with the specified attributes.
 *
 * @param string $name The name of the SVG to retrieve.
 * @param array $args An associative array of attributes for the SVG element.
 *                    - 'size' (int): The width and height of the SVG. Default is 24.
 *                    - 'color' (string): The fill color of the SVG. Default is '#00000'.
 *                    - 'class' (string): Additional CSS classes to apply to the SVG element.
 *                    - 'viewbox' (int): The viewBox attribute of the SVG. Default is 24.
 * @return string The generated SVG element as a string.
 */
function _svg($name, $args = [])
{
    $defaults = [
        'size' => 24,
        'color' => '#00000',
        'class' => '',
        'viewbox' => 24,
    ];

    $args = array_merge($defaults, $args);
    $svg = getSvgPath($name);

    if ($svg === null) {
        return null;
    }

    return <<<HTML
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="{$args['size']}"
            height="{$args['size']}"
            viewBox="0 0 {$args['viewbox']} {$args['viewbox']}"
            style="fill: {$args['color']}; --darkreader-inline-fill: #ffffff;"
            data-darkreader-inline-fill=""
            class="{$args['class']}"
        >
            {$svg}
        </svg>
    HTML;
}


/**
 * Retrieves an excerpt of the specified length.
 *
 * @param int $length The number of words to include in the excerpt. Default is 15.
 * @return string The trimmed excerpt.
 */
function getExcerpt(int $lenght = 15): string
{
    return wp_trim_words(
        get_the_excerpt(),
        $lenght
    );
}

/**
 * Checks if the current environment is a development environment.
 *
 * @return bool True if the environment is 'dev', false otherwise.
 */
function isDev(): bool
{
    return defined('WP_ENV') && WP_ENV === 'dev';
}

/**
 * Resizes an image to the specified width and height, and caches the result.
 *
 * @param string $imagePath The path to the original image.
 * @param int $width The desired width of the resized image.
 * @param int $height The desired height of the resized image.
 * @return string|false The URL of the cached resized image, or false on failure.
 */
function resizeImage(string $imagePath, int $width, int $height): string | false
{
    $cacheDir    =  sprintf('%s/cache/', wp_upload_dir()['basedir']);
    $cachedImage = sprintf('%s%sx%s_%s.webp',
        $cacheDir,
        $width,
        $height,
        pathinfo($imagePath, PATHINFO_FILENAME)
    );

    if (file_exists($cachedImage)) {
        return wp_upload_dir()['baseurl'] . '/cache/' . basename($cachedImage);
    }

    $originalImagePath = wp_upload_dir()['basedir'] . '/' . $imagePath;
    list(
        $originalWidth,
        $originalHeight,
        $imageType) = getimagesize($originalImagePath);

    $newImage = imagecreatetruecolor($width, $height);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($originalImagePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($originalImagePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($originalImagePath);
            break;
        default:
            return false;
    }

    imagecopyresampled(
        $newImage,
        $source,
        0, 0, 0, 0,
        $width,
        $height,
        $originalWidth,
        $originalHeight
    );

    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    imagewebp($newImage, $cachedImage, 90);

    imagedestroy($newImage);
    imagedestroy($source);

    return wp_upload_dir()['baseurl'] . '/cache/' . basename($cachedImage);
}
