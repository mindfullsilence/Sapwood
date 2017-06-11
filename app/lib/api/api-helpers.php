<?php
namespace Sapwood\Library;

/**
 * Loads a php script
 * @param  string $path An absolute path
 * @return string       [description]
 */
function sapwood_maybe_load($path = '') {
  $path = (string) $path;

  if(empty($path)) return false;
  if(!file_exists($path)) return false;

  include_once($path);

  return true;
}

/**
 * Get a setting from the core class
 * @param  string $name The setting key
 * @return mixed       the value from the key lookup
 */
function sapwood_get_setting($name) {

  if(isset(sapwood()->settings[$name])) {
    $setting = sapwood()->settings[$name];
    return apply_filters("sapwood/setting/get/name={$name}", $setting);
  }

  return null;
}

/**
 * Set a setting on the core class
 * @param string $name  The key of the setting
 * @param string $value The value of the setting
 */
function sapwood_set_setting($name, $value) {

  if(!isset(sapwood()->settings[$name])) {
    sapwood()->settings[$name] = apply_filters("sapwood/setting/set/name={$name}", $value);
    return true;
  }

  return false;
}

/**
 * Updates an already existing setting on the core class
 * @param  string $name  The key of the setting
 * @param  mixed $value The value to update to
 * @return bool        whether the setting was updated
 */
function sapwood_update_setting($name, $value) {
  if(!sapwood_has_setting($name)) return false;

  sapwood()->settings[$name] = apply_filters("sapwood/setting/update/name={$name}", $value);

  return true;
}

/**
 * Determine if a setting exists on the core class
 * @param  string  $name The key of the setting
 * @return boolean       Whether the setting exists
 */
function sapwood_has_setting($name) {
  if(!isset(sapwood()->setting[$name])) return false;

  return true;
}



function sapwood_do_action($tag = '', $name = '', $args = array()) {
  if(empty($tag)) return;

  if(!empty($name)) array_push($args, $name);

  $action = "sapwood/{$tag}";

  do_action_ref_array($action, $args);


  if(!empty($name)) {
    $action = "sapwood/{$tag}/name={$name}";

    do_action_ref_array($action, $args);
  }

  return;
}

/**
 * Applies filters with multiple arguments or a single argument for a given tag using the sapwood and type namespaces
 * @param  string $tag  The tag to fire
 * @param  array  $data The arguments to pass through apply_filters
 * @return [type]       [description]
 */
function sapwood_apply_filters($tag = '', $name = '', $args = array()) {
 if(empty($tag)) return;

 if(!is_array($args)) $args = (array) $args;

 if(!empty($name)) array_push($args, $name);

 $action = "sapwood/{$tag}";

 $args[0] = apply_filters_ref_array($action, $args);

 if(!empty($name)) {
   $action = "sapwood/{$tag}/name={$name}";

   $args[0] = apply_filters_ref_array($action, $args);
 }

 return $args[0];
}

function sapwood_enqueue_style($handle, $path) {
  if( wp_script_is($handle, 'registered')
   || wp_script_is($handle, 'enqueued') ) {
     return false;
  }

  wp_enqueue_style(
    $handle,
    $path,
    array(),
    sapwood_get_setting('version'),
    'screen'
  );
}

function sapwood_enqueue_script($handle, $path) {
  if( wp_script_is($handle, 'registered')
   || wp_script_is($handle, 'enqueued') ) {
     return false;
  }

  wp_enqueue_script(
    $handle,
    $path,
    array(),
    sapwood_get_setting('version'),
    true
  );
  return true;
}

function sapwood_localize_data($handle, $data) {
  if(  wp_script_is( $handle, 'registered' )
    || wp_script_is( $handle, 'enqueued' )
  ) {
    return wp_localize_script( $handle, $data, 'sapwood' );
  }
  return false;
}

function sapwood_log($message) {
  error_log($message);
  error_log(PHP_EOL);
}


function sapwood_console($message = '', $id = '') {
    ?>
    <script type="text/javascript">
      console.group('<?php echo $id; ?>');
      console.log(<?php echo json_encode($message); ?>);
      console.groupEnd();
    </script>
    <?php
}
