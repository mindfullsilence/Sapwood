<?php
namespace Sapwood\Library;

use Sapwood\Library;



class Template {

	// vars
	var $name = '',
		$defaults = array(),
		$_inherited = false;


	/**
	 * Handles function hooks for all child templates.
	 */
	function __construct() {
		sapwood_set_setting('template/name', $this->name);
	}


	/**
	 * Adds a filter hook
	 * @param string  $tag             The name of the filter to fire
	 * @param string  $function_to_add The callable to run when this filter is fired
	 * @param integer $priority        Where in the queue this callable should run
	 * @param integer $accepted_args   The number of arguments the callable accepts
	 */
	function add_filter( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {
		if( !is_callable($function_to_add) ) return;

		// append
		$tag = "sapwood/template/{$tag}/name={$this->name}";


		// add
		add_filter( $tag, $function_to_add, $priority, $accepted_args );

	}


	/**
	 * Adds an action hook
	 * @param string  $tag             The name of the action to register
	 * @param string  $function_to_add The callable to run when the hook is fired
	 * @param integer $priority        Where in the queue this callable should run
	 * @param integer $accepted_args   The number of arguments this callable accepts
	 */
	function add_action( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

		// bail early if no callable
		if( !is_callable($function_to_add) ) return;

		$tag = "sapwood/template/{$tag}/name={$this->name}";

		// add
		add_action( $tag, $function_to_add, $priority, $accepted_args );

	}

}
