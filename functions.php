<?php
ini_set( 'error_log', __DIR__ . '/debug.log' );

/**
 * Do not edit anything in this file unless you know what you're doing
 */

/**
 * Helper function for prettying up errors
 * @param string $messapwood
 * @param string $subtitle
 * @param string $title
 */
$sapwood_error = function ($message, $subtitle = '', $title = '') {
    $title = $title ?: __('sapwood &rsaquo; Error', 'sapwood');
    $footer = '<a href="https://github.com/mindfullsilence/sapwood">github.com/mindfullsilence/sapwood</a>';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
    wp_die($message, $title);
};

if(!defined('ABSPATH')) {
  $sapwood_error(__('This file should not be accessed directly.', 'sapwood'));
}

/**
 * Ensure dependencies are loaded
 */
require_once __DIR__.'/vendor/autoload.php';

/**
 * Ensure compatible version of PHP is used
 */
if (version_compare('7.0.0', phpversion(), '>=')) {
    $sapwood_error(__('You must be using PHP 7.0.0 or greater.', 'sapwood'), __('Invalid PHP version', 'sapwood'));
}

/**
 * Ensure required plugins are loaded
 */
if(!function_exists('acf')) {
  if(!is_admin()) {
    $sapwood_error(__('ACF must be installed and activated to use this theme.', 'sapwood'));
  }
}
if(!class_exists('Timber\\Timber')) {
  if(!is_admin()) {
    $sapwood_error(__('Timber version 1.0 or greater must be installed and activated to use this theme.', 'sapwood'));
  }
}

/**
 * Here's what's happening with these hooks:
 * 1. WordPress initially detects theme in themes/sapwood
 * 2. Upon activation, we tell WordPress that the theme is actually in themes/sapwood/public/templates
 * 3. When we call get_template_directory() or get_template_directory_uri(), we point it back to themes/sapwood
 *
 * We do this so that the Template Hierarchy will look in themes/sapwood/public/templates for core WordPress themes
 * But functions.php, style.css, and index.php are all still located in themes/sapwood
 *
 * This is not compatible with the WordPress Customizer theme preview prior to theme activation
 *
 * get_template_directory()   -> /srv/www/example.com/current/web/app/themes/sapwood
 * get_stylesheet_directory() -> /srv/www/example.com/current/web/app/themes/sapwood
 * locate_template()
 * ├── STYLESHEETPATH         -> /srv/www/example.com/current/web/app/themes/sapwood
 * └── TEMPLATEPATH           -> /srv/www/example.com/current/web/app/themes/sapwood/app/public/templates
 */
if (is_customize_preview() && isset($_GET['theme'])) {
  $sapwood_error(__('Theme must be activated prior to using the customizer.', 'sapwood'));
}
add_filter('template', function ($stylesheet) {
  return dirname($stylesheet, 3);
});
if (basename($stylesheet = get_option('template')) !== "templates") {
  update_option('template', "{$stylesheet}/app/public/templates");
  wp_redirect($_SERVER['REQUEST_URI']);
  exit();
}



/**
 * sapwood required files
 *
 * The mapped array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 */
array_map(function ($file) use ($sapwood_error) {
    $file = "app/lib/{$file}.php";
    if (!locate_template($file, true, true)) {
        $sapwood_error(sprintf(__('Error locating <code>%s</code> for inclusion.', 'sapwood'), $file), 'File not found');
    }
}, [
  'core',
  // 'templates',
  // 'twig'
]);
