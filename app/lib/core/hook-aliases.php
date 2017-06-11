<?php
namespace Sapwood;
    /**
     * If a base (WP or seperate plugin) hook is used, it is first aliased here.
     * This is to allow logic to be applied globally before sending off to
     * sapwood api
     */


/**
 * @filter sapwood/template_hierarchy
 * @filter sapwood/{$type}_template_hierarchy
 *
 * An extremely messy hook that should really have a simpler hook called before
 * it that is more generalized, which is what we're doing here.
 * @var string $type The type of template being searched for.
 *
 * @see https://developer.wordpress.org/reference/hooks/type_template_hierarchy/
 */
array_map(function ($type) {
    add_filter("{$type}_template_hierarchy", function ($templates) use ($type) {
        $templates = sapwood_apply_filters('template/hierarchy', $type, array($templates));

        return $templates;
    }, PHP_INT_MAX, 1);
}, [
    '404', 'tag', 'date', 'home',
    'page', 'index', 'paged', 'author',
    'search', 'single', 'archive', 'category',
    'singular', 'taxonomy', 'frontpage', 'attachment'
]);


/**
 * @action sapwood/template/include
 * @filter sapwood/template/include
 *
 * Preparing template for Templates.php class
 * @see https://developer.wordpress.org/reference/hooks/template_include/
 */
add_action('template_include', function ($template) {
    $template = $template ?: 'index.php';
    $name = sapwood_get_template_name($template);

    $template = sapwood_apply_filters('template/include', $name, array($template));

    return $template;
}, PHP_INT_MAX, 1);


add_action('wp_head', function () {
    sapwood_do_action('front/styles');
}, PHP_INT_MAX, 0);

add_action('wp_footer', function () {
    if (!is_admin()) {
        sapwood_do_action('front/scripts');
    }
});


add_action('admin_enqueue_scripts', function () {
    sapwood_do_action('admin/styles');
}, PHP_INT_MAX, 0);

add_action('admin_footer', function () {
    sapwood_do_action('admin/scripts');
}, PHP_INT_MAX, 0);
