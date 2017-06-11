<?php
namespace Sapwood\Library;

use Sapwood\Library;

/**
* action component/load
*
* action component/register
* filter component/validate_data
* action component/invalid
* filter component/format_attributes
* filter component/format_element
* filter component/format_data
* action component/prefix
* action component/before
* action component/after
* action component/suffix
* action component/enqueue_styles
* action component/enqueue_scripts
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Components {

  var $names = array();

  /**
  * Loads, instantiates, registers, and renders the component. Runs all
  * necessary hooks for a component to run its methods.
  */
  function __construct() {

    // vars
    $this->names = array();

    $hooks = array(
      'sapwood/component/_register' => array('filter', 'register', 20, 1),
      'sapwood/component/_get_data' => array('action', 'prepare', 20, 3),
      'sapwood/component/_setup' => array('action', 'setup', 20, 3),
      'sapwood/component/_teardown' => array('action', 'teardown', 20, 3)
    );

    // Get the component library
    $components = sapwood_get_component_dirs();

    // Handle methods that should always run
    array_walk($components, function($component) {
      $name = sapwood_get_component_name($component);
      $class = sapwood_get_component_classname($component);

      $this->names[$name] = array(
        'folder' => $component,
        'class' => $class
      );

      $this->load($name);
      $this->register_fields($name);
      $this->enqueue_assets($name);
    });

    array_walk($hooks, function($args, $hook) {
      $funcs = array(
        'filter' => 'add_filter',
        'action' => 'add_action'
      );
      $type = $funcs[$args[0]];
      $method = array($this, $args[1]);
      $priority = $args[2];
      $num_args = $args[3];

      call_user_func($type, $hook, $method, $priority, $num_args);
    });
  }

  /**
  * Loads a component, finding the template path based off the name.
  * @param  string  $name      The name of the template
  * @return null
  */
  function load($name = '') {
    $location = sapwood_get_component_dir($name) . "/{$name}.php";
    sapwood_maybe_load($location);
  }


  /**
  * Load and register acf fields and fieldgroup
  * @param  Sapwood/Component $instance The instance of the component class
  * @param  string            $name     The name of this component
  * @return null                        Does not return anything
  */
  function register_fields($name = '') {
    $location = sapwood_get_component_dir($name) . "/{$name}.json";

    if(!file_exists($location)) {
      return;
    }

    $fieldgroup = json_decode(file_get_contents($location), true);
    $fieldgroup = sapwood_apply_filters('component/fields', $name, (array) $fieldgroup);

    if(empty($fieldgroup)) {
      return;
    }

    sapwood_add_field_group($fieldgroup);
  }


  function enqueue_assets($name) {
    $cls = $this->names[$name]['class'];

    $methods = array(
      'sapwood/admin/styles' => array($cls, 'admin_styles'),
      'sapwood/admin/scripts' => array($cls, 'admin_scripts'),
      'sapwood/front/syles' => array($cls, 'front_styles'),
      'sapwood/front/scripts' => array($cls, 'front_scripts')
    );

    array_walk($methods, function($method, $action) {
      if(is_callable($method)) {
        add_action($action, function() use ($method) {
          call_user_func($method);
        });
      }
    });
  }

  /**
  * Instantiates the class for the component if it exists and adds it to the registry.
  * @param  string  $name      The name of the component
  * @return Sapwood\Component  The newly instantiated instance
  */
  function register($name = '') {
    $classname = sapwood_get_component_classname($name);

    if(!$classname) {
      return;
    }

    // instantiate
    $instance = new $classname();
    $this->names[$name]['instance'] = $instance;
    sapwood_do_action('component/register', $name, array());

    return $instance;
  }

  function validate_object($object, $instance, $name) {
    $valid = sapwood_component_validate($name, $object['data']);

    return $valid;
  }

  /**
  * Sets up some utility data for the Template and ensures element, attribute, and data exists on the object. Then allows third-party validation via filters
  * @param  array  $object The object passed to _base
  * @return array|null         The object if it was valid, otherwise null
  */
  function prepare($object = array(), $instance, $name = '') {
    if(empty($name)) return null;

    // adding fallbacks for required object keys
    $object = sapwood_component_sanitize_object($object, $name);
    $instance->object = $object;

    if(!sapwood_component_validate_data($name, $object['data'])) {
      return;
    }

    // object may have changed in filter
    $object = $instance->object;

    $data = $object['data'];
    $data = sapwood_component_get_data($data, $name);

    $element = $object['element'];
    $element = sapwood_component_get_element($element, $name);

    $attributes = $object['attributes'];
    $attributes = sapwood_component_get_attributes($attributes, $name);

    $instance->object['attributes'] = $attributes;

    return $instance->object;
  }

  /**
  * Fires off internal hooks for running style enqueuing and beginning the element.
  * @param  array             $object   The object passed to _base after validation
  * @param  Sapwood\Component $instance The instance of the component class
  * @param  string            $name     The name of the component
  * @return null                        Does not return anything
  */
  function setup($object, $instance, $name = '') {
    sapwood_do_action('component/prefix', $name, array($object));

    if($object['element']) {
      $element = "<{$object['element']} ";
      $attributes = $object['attributes'];
      array_walk($attributes, function($value, $property) use (&$element) {
        if(!empty($value)) {
          $element .= " {$property}='{$value}'";
        } else {
          $element .= " {$property}";
        }
      });
      $element .= ">";
      echo $element;
    }

    sapwood_do_action('component/before', $name, array($object));
  }

  function teardown($object = array(), $instance, $name = '') {
    sapwood_do_action('component/after', $name, array($object));

    if($object['element']) {
      echo "</{$object['element']}>";
    }

    sapwood_do_action('component/suffix', $name, array($object));
  }

}

// initialize
sapwood()->components = new Components();
