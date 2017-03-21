<?php

namespace Sapwood;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Templates {


  /**
  * Handles registration and storage of new component types
  *
  */
  function __construct() {

    // vars
    $this->names = array();

    add_filter('sapwood/template_hierarchy',          array($this, '_template_hierarchy'),  20, 1);
    add_action('sapwood/template/register',           array($this, 'render_template'),      20, 2);
    add_action('sapwood/template_include',            array($this, 'load_template'),        20, 2);
    add_filter('sapwood/template_include',            array($this, '_blank_php'),           20, 1);

    add_action('sapwood/template/get_dependencies',   array($this, 'inherit'),              20, 2);
    add_action('sapwood/template/assets',             array($this, 'enqueue_assets'),       20, 2);
    add_action('sapwood/template/load',               array($this, 'register_template'),    20, 2);
    add_filter('sapwood/template/get_data',           array($this, 'get_valid_template'),   20, 2);

  }


  /**
  * Used for wordpress internals. This method should be considered private
  * @return string The path to the index.php file in the root of the theme.
  */
  function _blank_php() {
    return get_theme_file_path('index.php');
  }

  /**
   * Used for wordpress internals. This method should be considered private.
   * @param  array $template The possible template direcotry and filenames
   * @return array           The filenames to search for when loading a template.
   */
  function _template_hierarchy($templates) {
    $mapped = call_user_func_array("array_merge", array_map(
      function ($template) {
        return [
          sapwood_get_setting('template_dir') . "/{$template}/{$template}.php",
          "{$template}.php"
        ];
      },
      $templates)
    );

    return $mapped;
  }

  /**
   * Loads any templates that this template requires.
   * @param  array  $templates The templates to load
   * @return null
   */
  function inherit($templates = array()) {
    array_walk($templates, function($template) {
      $this->load_template(true, $template);
    });
  }

  /**
   * Loads a template, finding the template path based off the name.
   * @param  string  $name      The name of the template
   * @param  boolean $inherited Whether this template is being inherited by another
   * @return null
   */
  function load_template($inherited = false, $name = '') {
    $name = basename($name, '.php'); // ensure always single name rather than dir
    $location = sapwood_get_template_dir($name) . "/{$name}.php";

    add_filter('sapwood/timber/locations', function($locations) use ($location) {
      array_push($locations, dirname($location));
      return $locations;
    }, 20, 1);

    if(sapwood_maybe_load($location)) {
      sapwood_do_action('template/load', $name, array($inherited));
    }
  }

  /**
   * Gets the required styles and scripts for the template, and enqueues them.
   * @param  array $data Data from get_context call
   * @param  string $name The name of the template
   * @return null
   */
  function enqueue_assets($data = array(), $name = '') {
    sapwood_do_action('template/enqueue_scripts', $name, array($data));
    sapwood_do_action('template/enqueue_styles', $name, array($data));
  }

  /**
   * Renders the twig file associated with the template.
   * @param  Sapwood/Template $instance The instance of the template
   * @param  string $name     The name of the template
   * @return null
   */
  function render_template($instance, $name = '') {
    $template = sapwood_get_template_dir($name) . "/{$name}.twig";
    if( !file_exists($template) ) return false;

    if( !is_a($instance, "\\Sapwood\\Template" ) ) return false;

    $instance->object = sapwood_get_context();
    $valid = sapwood_apply_filters('template/validate_object', $name, array(true, $instance->object));

    if(!$valid) {
      sapwood_do_action('tempalte/invalid', $name, array($instance->object));
      return;
    }

    $data = sapwood_apply_filters('template/format_data', $name, array($instance->data));
    $instance->object = $data;

    $this->enqueue_assets($data, $name);

    sapwood_do_action('template/prefix', $name, array($data));

    if(!$instance->_inherited) {
      sapwood_render_template($data, $name);
    }

    sapwood_do_action('template/suffix', $name, array($data));
  }

  /**
   * Sets up some utility data for the Template for general differentiation purposes.
   * @param  array  $data The data from get_context
   * @return array       The data after modification
   */
  function get_valid_template($data = array(), $name = '') {
    if(empty($name)) return;
    $data['_sapwood'] = array(
      '_name' => $name,
      '_type' => 'template'
    );
    return $data;
  }

  /**
   * Instantiates the class for the template if it exists.
   * @param  boolean $inherited Whether this template was loaded as a dependency of another
   * @param  string  $name      The name of the template
   * @return mixed             Null if already register, true if registered succesfully
   */
  function register_template($inherited = false, $name) {
    // bail early if already registered
    if(sapwood_template_registered($name)) return;

    $class = $name;
    $class = str_replace('-', '_', $class);
    $class = trim($class, '_');
    $class = ucfirst($class);
    $class = "\\Sapwood\\".$class;

    // no need to instantiate
    if(!class_exists($class)) return;

    // instantiate
    $ref = new \ReflectionClass($class);
    $instance = $ref->newInstance();
    $instance->_inherited = $inherited;

    // load required templates
    $extensions = sapwood_apply_filters('template/inheritance', $name, array(array()));
    sapwood_do_action('template/get_dependencies', $name, array($extensions));

    // register
    $this->names[$name] = $instance;

    sapwood_do_action('template/register', $name, array($instance));
  }

}



// initialize
sapwood()->templates = new Templates();
