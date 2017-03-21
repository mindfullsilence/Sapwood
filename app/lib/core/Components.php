<?php

namespace Sapwood;

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

    // load
    add_action('sapwood/components/_load',          array($this, 'load'), 20, 1);

    // instantiate
    add_filter('sapwood/component/_register',       array($this, 'register'), 20, 1);

    // prepare object
    add_action('sapwood/component/_get_data',       array($this, 'get_valid'), 20, 3);

    // render
    add_action('sapwood/component/_before',         array($this, 'setup'),  20, 3);
    add_action('sapwood/component/_after',          array($this, 'teardown'), 20, 3);
  }

  /**
   * Loads a template, finding the template path based off the name.
   * @param  string  $name      The name of the template
   * @param  boolean $inherited Whether this template is being inherited by another
   * @return null
   */
  function load($name = '') {
    $name = basename($name, '.twig'); // ensure always single name rather than dir or file
    $location = sapwood_get_component_dir($name) . "/{$name}.php";

    sapwood_maybe_load($location);

    sapwood_do_action('component/load', $name, array());
  }

  /**
   * Instantiates the class for the component if it exists and adds it to the registry.
   * @param  string  $name      The name of the component
   * @return Sapwood\Component  The newly instantiated instance
   */
  function register($name = '') {
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

    // register
    if(empty($this->names[$name])) {
      $this->names[$name] = array();
    }
    $this->names[$name][] = $instance;

    sapwood_do_action('component/register', $name, array());

    do_action('sapwood/component/load/id=' . $instance->uuid);

    return $instance;
  }

  /**
   * Sets up some utility data for the Template and ensures elemnt, attribute, and data exists on the object. Then allows third-party validation via filters
   * @param  array  $object The object passed to _base
   * @return array|null         The object if it was valid, otherwise null
   */
  function get_valid($object = array(), $instance, $name = '') {
    if(empty($name)) return null;

    // adding fallbacks for required object keys
    $object = $this->sanitize($object, $name);

    $instance->object = $object;

    // third-party validation. Start as valid.
    $valid = sapwood_apply_filters(
      'component/validate_object',
      $name,
      array(
        true,
        $object
      )
    );
    $valid = apply_filters('sapwood/component/validate_object/id=' . $instance->uuid, $valid);

    // object may have changed in filter
    $object = $instance->object;

    // must be a boolean return value
    $valid = (bool) $valid;

    if(!$valid) {
      sapwood_do_action('component/invalid', $name, array($object));
      do_action('sapwood/component/invalid/id=' . $instance->uuid);
      return null;
    }

    $instance->object['data']       = sapwood_apply_filters('component/format_data',        $name, array($object['data']));
    $instance->object['data']       = apply_filters('sapwood/component/format_data/id=' . $instance->uuid, $object['data']);

    $instance->object['element']    = sapwood_apply_filters('component/format_element',     $name, array($object['element']));
    $instance->object['element']    = apply_filters('sapwood/component/format_element/id=' . $instance->uuid, $object['element']);

    $instance->object['attributes'] = sapwood_apply_filters('component/format_attributes',  $name, array($object['attributes']));
    $instance->object['attributes'] = apply_filters('sapwood/component/format_attributes/id=' . $instance->uuid, $object['attributes']);

    return $instance->object;
  }

  /**
   * Ensures an object is valid by using coercion of each relative key.
   *
   * Adds _sapwood key to the object to store it's name and type (component).
   * Ensures attributes is an array.
   * Ensures element is either boolean false or a string
   * Ensures data is an array.
   * @param  array $object The object to validate/sanitize
   * @param  string $name  The name of the component
   * @return array         The sanitized and validated object.
   */
  function sanitize($object = array(), $name = '') {
    $object['_sapwood'] = array(
      '_name' => $name,
      '_type' => 'component'
    );

    // make sure they are defined
    $object['element'] = $object['element'] ?? false;
    $object['attributes'] = $object['attributes'] ?? array();
    $object['data'] = $object['data'] ?? array();

   // attributes must be an array
   // Convert object properties to an assoc array
   // Coerce everything else to an array
   $object['attributes'] = !is_array($object['attributes']) // if not an array
    ? !is_object($object['attributes'])
      ? (array) $object['attributes']
      : get_object_vars($object['attributes'])
    : $object['attributes'];

    // attributes must contain class. class must contain component name
    $object['attributes']['class'] = $attributes['attributes']['class'] ?? '';
    $object['attributes']['class'] = "c-{$name} {$object['attributes']['class']}";

    // element must be a string. Empty '' means no element should be rendered
   $object['element'] = (string) $object['element'];

    // data must be an array
   $object['data'] = !is_array($object['data'])
    ? !is_object($object['data'])
      ? (array) $object['data']
      : get_object_vars($object['data'])
    : $object['data'];

    return $object;
  }

  /**
   * Fires off internal hooks for running style enqueuing and beginning the element.
   * @param  array             $object   The object passed to _base after validation
   * @param  Sapwood\Component $instance The instance of the component class
   * @param  string            $name     The name of the component
   * @return null                        Does not return anything
   */
  function setup($object, $instance, $name = '') {
    sapwood_do_action('component/enqueue_styles', $name, array($object));
    do_action('sapwood/component/enqueue_styles/id=' . $instance->uuid);

    sapwood_do_action('component/prefix', $name, array($object));
    do_action('sapwood/component/prefix/id=' . $instance->uuid);

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
    do_action('sapwood/component/before/id=' . $instance->uuid);
  }

  function teardown($object = array(), $instance, $name = '') {
    // handling registering/enqueueing required scripts
    sapwood_do_action('component/enqueue_scripts', $name, array($object));
    do_action('sapwood/component/enqueue_scripts/id=' . $instance->uuid);

    sapwood_do_action('component/after', $name, array($object));
    do_action('sapwood/component/after/id=' . $instance->uuid);

    if($object['element']) {
      echo "</{$object['element']}>";
    }

    sapwood_do_action('component/suffix', $name, array($object));
    do_action('sapwood/component/suffix/id=' . $instance->uuid);
  }

}

// initialize
sapwood()->components = new Components();
