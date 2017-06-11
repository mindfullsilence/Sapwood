<?php

namespace Sapwood;



function sapwood_get_component_dir($name) {
  $dir = sapwood_get_setting('component_dir') . "/{$name}";

  return $dir;
}

function sapwood_get_component_dirs() {
  $dirs = array_diff(
    array_filter(glob(sapwood_get_setting('component_dir') . '/*'), 'is_dir'),
    array(sapwood_get_setting('component_dir') . "/_template")
  );

  return $dirs;
}

function sapwood_get_component_names() {
  $dirs = sapwood_get_component_dirs();
  $names = array();

  array_walk($dirs, function($dir) use (&$names) {
    $name = sapwood_get_component_name($dir);
    array_push($names, $name);
  });

  return $names;
}

function sapwood_get_component_name($directory = '') {
    $name = pathinfo($directory, PATHINFO_FILENAME);

    return $name;
}

function sapwood_get_component_classname($name = '') {
    $class = str_replace('-', '_', $name);
    $class = trim($class, '_');
    $class = ucfirst($class);
    $class = "\\Sapwood\\".$class;

    if(class_exists($class)) {
      return $class;
    }
    return false;
}

function sapwood_component_registered($name) {
  if(!isset(sapwood()->components->names[$name])) return false;

  return true;
}

function sapwood_load_component($name) {
  return sapwood()->components->load($name);
}

function sapwood_render_component($data, $name) {
  $component = "{$name}/{$name}.twig";
  sapwood_timber_render($component, $data);
}

function sapwood_component_get_data($data = array(), $name = '') {
    $data = sapwood_apply_filters(
      'component/format_data',
      $name,
      array($data)
    );

    return $data;
}

function sapwood_component_get_element($element = '', $name = '') {
  $element = sapwood_apply_filters(
    'sapwood/component/format_element',
    $name,
    array($element)
  );

  return $element;
}

function sapwood_component_get_attributes($attributes = array(), $name = '') {
  $attributes = sapwood_apply_filters(
    'component/format_attributes',
    $name,
    array($attributes)
  );
  return $attributes;
}

function sapwood_component_validate_data($name, $data = array()) {
    $valid = true;
    $valid = sapwood_apply_filters(
      'component/validate',
      $name,
      array($valid, $data)
    );

    if(!$valid) {
      sapwood_do_action(
        'component/invalid',
        $name,
        array($data)
      );
    }

    return $valid;
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
function sapwood_component_sanitize_object($object = array(), $name = '') {
  $object['_sapwood'] = array(
    '_name' => $name,
    '_type' => 'component'
  );

  // make sure they are defined
  $element = $object['element'] ?? false;
  $attributes = $object['attributes'] ?? array();
  $data = $object['data'] ?? array();

  // attributes must be an array
  $attributes = !is_array($attributes) // if not an array
  ? !is_object($attributes)
    ? (array) $attributes
    : get_object_vars($attributes)
  : $attributes;

  // attributes must contain class. class must contain component name
  $classes = $attributes['class'] ?? '';
  $classes = str_replace("c-{$name}", '', $classes);
  $classes = "c-{$name} {$classes}";

  $object['attributes']['class'] = $classes;

  // element must be a string. Empty '' means no element should be rendered
  $object['element'] = (string) $element;

  // data must be an array
  $data = !is_array($data)
  ? !is_object($data)
    ? (array) $data
    : get_object_vars($data)
  : $data;

  $object['data'] = $data;

  return $object;
}
