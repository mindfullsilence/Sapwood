<?php

namespace Sapwood;



class Template {

	// vars
	var $name = '',
		$defaults = array(),
    $_inherited = false;


  /**
   * Handles function hooks for all child templates.
   */
	function __construct() {
    $this->add_filter('register',          array($this, '_initialize'));
		// info
		$this->add_filter('get_data',         array($this, 'get_data'), 20, 1);
		$this->add_filter('validate_data',    array($this, 'validate_data'), 20, 1);
		$this->add_filter('format_data',      array($this, 'format_data'), 20, 1);
    $this->add_filter('get_twig',         array($this, 'twig'), 20, 1);

    // rendering
    $this->add_action('render_before',    array($this, 'render_before'), 20, 1);
    $this->add_action('render_after',     array($this, 'render_after'), 20, 1);

    // assets
    $this->add_filter('inheritance',      array($this, 'extends'), 20, 2);
    $this->add_filter('styles',           array($this, 'styles'), 20, 1);
    $this->add_filter('scripts',          array($this, 'scripts'), 20, 1);
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
