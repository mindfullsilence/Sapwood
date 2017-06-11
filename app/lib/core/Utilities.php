<?php
////////////////////////////////////////////////////////////////////////////////
// Add any utility functions here for cross-template use. These are used in
// the php files, not the twig files. If you need functions for twig, see
// Theme_Filters.php
////////////////////////////////////////////////////////////////////////////////
namespace Sapwood\Library;

use Sapwood\Library;

if (!defined('ABSPATH')) {
	header('Location: /');
	die;
}

class Utilities {
	protected static $instance = null;
	protected $messages = array();
	static $foot = array();

	protected function __construct() {

    sapwood_add_twig_filter('debug', array($this, 'print_debug_messages'));

    add_action('sapwood/template/binding/body/after', array($this, 'print_debug_messages'));
	}

	/**
	 * Sends a message or data to the browser console. This is used by the debug twig filter.
	 * @param string $message
	 * @param string $id
	 */
	public function display($message = '', $id = '') {
		$message = json_encode($message);
		?><script type='text/javascript'>
    	console.group('$id');
    	console.dir( <?php echo $message; ?> );
    	console.groupEnd();
    </script><?php
	}

	/**
	 * Takes any message and sends it through self::display() to be output in the browser console.
	 * @param string $message Echoed to the screen
	 */
	public function debug($message = '', $id = '')
	{
		if( !$id ) {
			$id = strval(rand());
		}
		$this->messages[$id] = $message;
	}

	/**
	 * Lists all hooks that run on the given page this is called in.
	 * @param bool $sofar wether to print all hooks, or just the ones that have run until this function is called.
	 */
	public function list_hooks($sofar = false) {
		if($sofar) {
			$this->display($GLOBALS['wp_actions'], 'actions');
		}
		else {
			add_action('shutdown', function () {
				$this->display($GLOBALS['wp_actions'], 'actions');
			});
		}
	}

	public function print_debug_messages() {
		foreach ($this->messages as $id => $message) {
				$this->display($message, $id);
				unset($this->messages[$id]);
		}
	}

	public static function get_instance() {
		if(self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

function sapwood_utilities() {
  if(WP_ENV !== 'production') {
    sapwood_set_setting('util', Utilities::get_instance());
    return sapwood_get_setting('util');
  }
  return false;
}

sapwood_utilities();
