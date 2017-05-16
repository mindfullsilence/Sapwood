<?php
namespace Sapwood;

/**
 * Loads a php script
 * @param  string $path An absolute path
 * @return string       [description]
 */
if(!function_exists('sapwood_maybe_load')):
  function sapwood_maybe_load($path = '') {
    $path = (string) $path;

    if(empty($path)) return false;
    if(!file_exists($path)) return false;

    include_once($path);

    return true;
  }
endif;

/**
 * Get a setting from the core class
 * @param  string $name The setting key
 * @return mixed       the value from the key lookup
 */
if(!function_exists('sapwood_get_setting')):
  function sapwood_get_setting($name) {

    if(isset(sapwood()->settings[$name])) {
      $setting = sapwood()->settings[$name];
      return apply_filters("sapwood/setting/get/name={$name}", $setting);
    }

    return null;
  }
endif;

/**
 * Set a setting on the core class
 * @param string $name  The key of the setting
 * @param string $value The value of the setting
 */
if(!function_exists('sapwood_set_setting')):
  function sapwood_set_setting($name, $value) {

    if(!isset(sapwood()->settings[$name])) {
      sapwood()->settings[$name] = apply_filters("sapwood/setting/set/name={$name}", $value);
      return true;
    }

    return false;
  }
endif;

/**
 * Updates an already existing setting on the core class
 * @param  string $name  The key of the setting
 * @param  mixed $value The value to update to
 * @return bool        whether the setting was updated
 */
if(!function_exists('sapwood_update_setting')):
  function sapwood_update_setting($name, $value) {
    if(!sapwood_has_setting($name)) return false;

    sapwood()->settings[$name] = apply_filters("sapwood/setting/update/name={$name}", $value);

    return true;
  }
endif;

/**
 * Determine if a setting exists on the core class
 * @param  string  $name The key of the setting
 * @return boolean       Whether the setting exists
 */
if(!function_exists('sapwood_has_setting')):
  function sapwood_has_setting($name) {
    if(!isset(sapwood()->setting[$name])) return false;

    return true;
  }
endif;


/**
 * Namespaces actions to sapwood
 * @param  string $tag  The action to run
 * @param  string $name A more specific name of the action
 * @param  array  $args Arguments to pass to attached action hooks
 * @return null       Returns nothing
 */
if(!function_exists('sapwood_do_action')):
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
endif;


/**
 * Applies filters with multiple arguments or a single argument for a given tag using the sapwood and type namespaces
 * @param  string $tag  The tag to fire
 * @param  array  $data The arguments to pass through apply_filters
 * @return mixed        The value after filters have been applied
 */
if(!function_exists('sapwood_apply_filters')):
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
endif;


/**
 * Enqueues stylesheets if their handle does not exist in the asset queue
 * @param  string $handle The handle/name for the stylesheet
 * @param  string $path   The path or url to the stylesheet
 * @return boolean         Whether the stylesheet was successfully enqueued
 */
if(!function_exists('sapwood_enqueue_style')):
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

    return true;
  }
endif;


/**
 * Enqueue scripts if their handle does not exist in the asset queue
 * @param  string $handle The handle/name for the scripts
 * @param  string $path   The path or url to the script
 * @return boolean         Whether the script was successfully enqueued
 */
if(!function_exists('sapwood_enqueue_script')):
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
endif;


/**
 * Renders json data to the page just before a registered script
 * @param  string $handle The handle/name of the script that should be printed after this data.
 * @param  array $data   The array of data to be json encoded onto the page.
 * @return boolean         Whether the data was succesfully localized.
 */
if(!function_exists('sapwood_localize_data')):
  function sapwood_localize_data($handle, $data) {
    if(  wp_script_is( $handle, 'registered' )
      || wp_script_is( $handle, 'enqueued' )
    ) {
      return wp_localize_script( $handle, $data, 'sapwood' );
    }
    return false;
  }
endif;


/**
 * Logs an error to the screen or the error log, depending on wordpress debugging constants
 * @param  string $message The message to log
 * @return null          No return value
 */
if(!function_exists('sapwood_log')):
  function sapwood_log($message) {
    error_log($message);
    error_log(PHP_EOL);
    return;
  }
endif;


/**
 * Prints messages to the browser console for development purposes.
 * @param  string $message The message to display in the console.
 * @param  string $id      A namespace to easily identify this error message in a group.
 * @return null          No return value provided.
 */
if(!function_exists('sapwood_console')):
  function sapwood_console($message = '', $id = '') {
      ?>
      <script type="text/javascript">
        console.group('<?php echo $id; ?>');
        console.log(<?php echo json_encode($message); ?>);
        console.groupEnd();
      </script>
      <?php
      return;
  }
endif;
