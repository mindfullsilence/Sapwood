<?php

namespace Sapwood;


if(!function_exists('sapwood_get_component_dir')):
  function sapwood_get_component_dir($name) {
    $dir = sapwood_get_setting('component_dir') . "/{$name}";

    return $dir;
  }
endif;

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
