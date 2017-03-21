<?php

namespace Sapwood;



class Component {

	// vars
	var $name = '',
		$defaults = array();


  /**
   * Handles function hooks for all child components.
   */
	function __construct() {
    # action sapwood/component/register/name=#{name}
    # action sapwood/component/validate_object/name=#{name}
    # action sapwood/component/invalid/name=#{name}
    # filter sapwood/component/format_element/name=#{name}
    # filter sapwood/component/format_attributes/name=#{name}
    # filter sapwood/component/format_data/name=#{name}
    # action sapwood/component/enqueue_styles/name=#{name}
    # action sapwood/component/prefix/name=#{name}
    # action sapwood/component/before/name=#{name}
    # action sapwood/component/enqueue_scripts/name=#{name}
    # action sapwood/component/after/name=#{name}
    # action sapwood/component/suffix/name=#{name}

    $this->uuid = uniqid('c-');

		// info
		$this->add_filter('get_component_types',  array($this, 'get_component_type'), 20, 1);

    // initialize
    $this->add_action('register',             array($this, 'register'), 20, 1);

    // validate
    $this->add_filter('validate_object',      array($this, 'validate_object'), 20, 2);
    $this->add_filter('invalid',              array($this, 'invalid'), 20, 2);

    // format
    $this->add_filter('format_data',          array($this, 'format_data'), 20, 3);
    $this->add_filter('format_element',       array($this, 'format_element'), 20, 3);
    $this->add_filter('format_attributes',    array($this, 'format_attributes'), 20, 3);

    // assets
    $this->add_action('enqueue_styles',       array($this, 'enqueue_styles'), 20, 2);
    $this->add_action('enqueue_scripts',      array($this, 'enqueue_scripts'), 20, 2);

    // render
    $this->add_action('prefix',               array($this, 'prefix'), 20, 2);
    $this->add_action('before',               array($this, 'before'), 20, 2);
    $this->add_action('after',                array($this, 'after'), 20, 2);
    $this->add_action('suffix',               array($this, 'suffix'), 20, 2);

    // Remote action
    $this->add_action('ajax',             array($this, 'ajax_response'), 20, 1);
	}


	/**
	 * Adds a filter hook
	 * @param string  $tag             The name of the filter to fire
	 * @param string  $function_to_add The callable to run when this filter is fired
	 * @param integer $priority        Where in the queue this callable should run
	 * @param integer $accepted_args   The number of arguments the callable accepts
	 */
	function add_filter( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

    // bail early if no callable
    if( !is_callable($function_to_add) ) return;

    $tag = "sapwood/component/{$tag}/id={$this->uuid}";

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

    $tag = "sapwood/component/{$tag}/id={$this->uuid}";

		// add
		add_action( $tag, $function_to_add, $priority, $accepted_args );

	}


	/**
	 * Appends this component to the list of registered component types
	 * @param  [type] $types [description]
	 * @return [type]        [description]
	 */
	function get_component_type( $types ) {
		$types[ $this->name ] = $this;

		return $types;

	}


	/**
	 * Returns the default, minimal data needed to render a component
	 * @param  array  $data The data to use as an override to the defaults
	 * @return array       The final data array
	 */
	function get_valid_component( $data = array() ) {

		// bail early if no defaults
		if( !is_array($this->defaults) ) return array();


		// merge in defaults
		return array_merge($this->defaults, $data);

	}

}
