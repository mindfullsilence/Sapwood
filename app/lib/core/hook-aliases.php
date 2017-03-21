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
 * @var string  $type The type of template being searched for.
 *
 * @see https://developer.wordpress.org/reference/hooks/type_template_hierarchy/
 */
 array_map(function ($type) {
     add_filter("{$type}_template_hierarchy", function ($templates) use ($type) {
       $templates = sapwood_apply_filters('template_hierarchy', false, array($templates));
       $templates = sapwood_apply_filters("{$type}_template_hierarchy", false, array($templates));

       return $templates;
     }, PHP_INT_MAX, 1);
 }, [
   '404',       'tag',      'date',      'home',
   'page',      'index',    'paged',     'author',
   'search',    'single',   'archive',   'category',
   'singular',  'taxonomy', 'frontpage', 'attachment'
 ]);


/**
 * @action sapwood/template_include
 * @filter sapwood/template_include
 *
 * Preparing template for Templates.php class
 * @see https://developer.wordpress.org/reference/hooks/template_include/
 */
add_action('template_include', function($template) {
  $template = $template ?: 'index.php';
  $name = basename($template, '.php');

  sapwood_do_action('template_include', $name, array(false));
  $template = sapwood_apply_filters('template_include', false, $name, array());

  return $template;
}, PHP_INT_MAX, 1);

/**
 * @filter sapwood/enqueue_scripts
 * @filter sapwood/enqueue_styles
 *
 * Run a hook specifically for scripts and for styles. Wordpress provides a
 * single hook for both.
 * @see https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
 */
add_action('wp_enqueue_scripts', function() {
  array_map(function($hook) {
    sapwood_do_action($hook, false, array());
  }, [
    'enqueue_scripts',
    'enqueue_styles'
  ]);
}, PHP_INT_MAX, 0);

/**
 * @filter sapwood/timber/locations
 *
 * Add new locations to timber for searching out twig files when extending or
 * including other twigs
 * @var [type]
 */
add_filter('timber/locations', function($locations) {
  $locations = sapwood_apply_filters('timber/locations', false, array($locations));
  return $locations;
}, PHP_INT_MAX, 1);

/**
 * Manipulate twig
 * @var Twig_Environment $twig The instance of twig used for rendering
 * @return TwigEnvironment $twig The manipulated twig environment
 */
add_filter('timber/twig', function($twig) {
  $twig = sapwood_apply_filters('twig', false, array($twig));
  return $twig;
}, PHP_INT_MAX, 1);


/**
 * deals any theme ajax handlers
 */
add_action("wp_ajax_sapwood", function() {
  // sapwood should equal nonce
  // data must exist
  if(!isset($_REQUEST['sapwood'])) return;
  if(!isset($_REQUEST['data'])) return;

  check_ajax_referer( 'sapwood', 'sapwood', true );

  $data = json_decode($_REQUEST['data']);
  sapwood_do_action( 'ajax', $_REQUEST['name'], array( array($data) ) );
}, PHP_INT_MAX, 0);


add_action('wp_head', function() {
  if(!is_admin()) :
  ?>
  <script type="text/javascript" id="sapwood-theme">
    window.Sapwood = {
      nonce: "<?php echo sapwood_get_setting('nonce'); ?>"
    };
  </script>
  <?php
  sapwood_do_action('head', false);
  endif;
}, PHP_INT_MIN, 0); // register as early as possible before other scripts are enqueued
