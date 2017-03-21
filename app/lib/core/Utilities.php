<?php
////////////////////////////////////////////////////////////////////////////////
// Add any utility functions here for cross-template use. These are used in
// the php files, not the twig files. If you need functions for twig, see
// Theme_Filters.php
////////////////////////////////////////////////////////////////////////////////
namespace Sapwood;

use \Timber\Timber;

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

    add_action('sapwood/template/body/suffix', array($this, 'print_debug_messages'));
	}

	/**
	 * Sends a message or data to the browser console. This is used by the debug twig filter.
	 * @param string $message
	 * @param string $id
	 */
	public function display($message = '', $id = '') {
		$message = json_encode($message);
		echo <<<CONSOLE
<script type='text/javascript'>
	console.group('$id');
	console.dir( $message );
	console.groupEnd();
</script>
CONSOLE;
	}

	/**
	 * Takes any message and sends it through self::display() to be output in the browser console.
	 * @param string $message Echoed to the screen
	 */
	public function debug($message = '', $id = '')
	{
		if($id === '') {
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

	/**
	 * List all possible twig files that the page can render to. This is useful if you want to know what twig file to
	 * create for the given page.
	 */
	public function list_templates() {
		if(class_exists(array('Mindfullsilence', 'Hierarchy'))) {
			Utilities::debug($context['templates'], 'templates');
		} else {
			Utilties::debug('You must have Mindfullsilence\Hierarchy installed to see the templates');
		}
	}

	public function print_debug_message() {
		foreach ($this->messages as $id => $message) {
				Utilities::display($message, $id);
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
  if(defined('WP_DEBUG') && WP_DEBUG === true) {
    sapwood()->util = Utilities::get_instance();
  } else {
    sapwood()->util = false;
  }

  return sapwood()->util;
}
