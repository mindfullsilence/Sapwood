<?php

namespace Sapwood;


/**
* [sapwood_get_template_dir description]
* @param  [type] $name [description]
* @return [type]       [description]
*/
function sapwood_get_template_dir($name) {
  $dir = sapwood_get_setting('template_dir') . "/{$name}";

  return $dir;
}

function sapwood_template_registered($name) {
  if(!isset(sapwood()->templates->names[$name])) return false;

  return true;
}

function sapwood_load_template($name) {
  return sapwood()->templates->load_template($name);
}

function sapwood_render_template($data, $name) {
  $template = "{$name}/{$name}.twig";
  sapwood_timber_render($template, $data);
}
