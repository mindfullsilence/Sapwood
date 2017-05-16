<?php

namespace Sapwood;


/**
 * Get the fully qualified path to a template folder in the theme.
 * E.g.: .../sapwood/public/tempalates/page
 * @param  string $name The folder to get the path to, relative to the theme root.
 * @return string       The fully qualified path to the folder.
 */
if(!function_exists('sapwood_get_template_dir')):
  function sapwood_get_template_dir($name) {
    $dir = sapwood_get_setting('template_dir') . "/{$name}";

    return $dir;
  }
endif;


/**
 * Checks if a template folder and appropriate files exist and are registered in the system.
 * @param  string $name The name of the template
 * @return boolean       Whether the template is registered in the system
 */
if(!function_exists('sapwood_template_registered')):
  function sapwood_template_registered($name) {
    if(!isset(sapwood()->templates->names[$name])) return false;

    return true;
  }
endif;


/**
 * Loads a template, registering it in the system.
 * @param  string $name The name of the template
 * @return boolean       Whether the template was loaded successfully.
 */
if(!function_exists('sapwood_load_template')):
  function sapwood_load_template($name) {
    return sapwood()->templates->load_template($name);
  }
endif;


/**
 * Renders a template's associated twig file.
 * @param  array $data The context data to supply to the template
 * @param  string $name The name of the template
 * @return null       Does not return anything
 */
function sapwood_render_template($data, $name) {
  $template = "{$name}/{$name}.twig";
  sapwood_timber_render($template, $data);
}
