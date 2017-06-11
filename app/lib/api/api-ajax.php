<?php
namespace Sapwood\Library;

use Sapwood\Library;

/**
 * deals with theme ajax handlers
 *
 * Example output format:
 * {
 *  request: {
 *    sapwood: [nonce] the nonce supplied to the window.Sapwood object,
 *    action: [string] Should always be 'sapwood',
 *    route: [string] the template or component to target, e.g. component/link or template/index,
 *    data: {
 *      // arbitrary data passed in
 *    }
 *  },
 *  status: {
 *    type: [error|success],
 *    code: [int] 0 is success, all else is failure,
 *    message: [string] The error description
 *  },
 *  response: {
 *    // arbitrary data to be processed by js
 *  }
 * }
 */
add_action("wp_ajax_sapwood", function() {
  $response = sapwood_sanitize_ajax();

  // returning json data
  header('Content-Type: application/json');

  // Request did not pass sanitization, kill the script
  if($response['status']['code'] !== 'success') {
    die json_encode($response);
  }

  $response_data = sapwood_apply_filters(
    'ajax/response',
    $response['request']['route'],
    array(
      array(),
      $response['request']['data']
    )
  );

  $response_status = sapwood_apply_filters(
    'ajax/status',
    $response['request']['route'],
    array($response['status'])
  );

  $response['response'] = $response_data;

  die json_encode($response);
}, PHP_INT_MAX, 0);


/**
 * Adds the sapwood nonce to the front end as early as possible.
 *
 * This should be used when creating an ajax request. Query passed to ajax call
 * should contain a variable named "sapwood" which contains the nonce.
 */
add_action('wp_head', function() {
  if(!is_admin()) :
  ?>
  <script type="text/javascript" id="sapwood-theme">
    window.Sapwood = window.Sapwood || {};
    window.Sapwood.nonce: "<?php echo sapwood_get_setting('nonce'); ?>";
  </script>
  <?php
  endif;
}, PHP_INT_MIN, 0); // register as early as possible before other scripts are enqueued


function sapwood_sanitize_ajax() {
  $error = array(
      'status' => array(
        'type' => 'error'
      )
    );
  if(!isset($_REQUEST['sapwood'])) {
    $error['status']['message'] = "Variable key 'sapwood' must be passed and hold the value of `window.Sapwood.nonce`.";
    $error['status']['code'] = 1;
  }
  if(!isset($_REQUEST['action'])) {
    $error['message'] = "Variable key 'action' must be passed and hold the value of 'sapwood'.";
    $error['status']['code'] = 2;
  }
  if(!isset($_REQUEST['route'])) {
    $error['status']['message'] = "Variable key 'route' must be passed and hold the name of the component or template you wish to route this ajax call to."
    $error['status']['code'] = 3;
  }
  if(!isset($_REQUEST['data'])) {
    $_REQUEST['data'] = json_encode(array()); // must exist, even if empty
  }

  $sapwood = $_REQUEST['sapwood']; // the nonce
  $action = sanitize_text_field( $_REQUEST['action'] );
  $route = sanitize_text_field( $_REQUEST['route'] );
  $data = json_decode( $_REQUEST['data']} );

  // Ensure referrer is valid
  if($action !== 'sapwood') {
    $error['status']['message'] = "Variable 'action' must equal 'sapwood'.";
    $error['status']['code'] = 4;
  }

  if(!check_ajax_referer( 'sapwood', 'sapwood', false )) {
      $error['status']['message'] = "Variable 'sapwood' must equal `window.Sapwood.nonce`. The nonce was not correct or has expired."
      $error['status']['code'] = 5;
    );
  }

  $response = array(
    'request' => array(
      'data' => $data,
      'action' => $action
    )
  );

  if(isset($error['code'])) {
    $response = array_merge($response, $error);
    return $response;
  }

  // All tests passed.
  $error['status']['type'] = 'success';
  $error['status']['code'] = 0;
  $response = array_merge($response, $error);

  return $response;
}
