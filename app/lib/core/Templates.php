<?php

namespace Sapwood\Library;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Templates {


  /**
  * Handles registration and storage of new component types
  *
  */
  function __construct() {

    // vars
    $this->names = array();

    add_action('sapwood/template/load', array($this, 'getContext'), 20, 1);
  }

  /**
   * Loads a template, finding the template path based off the name.
   * @param  string  $name      The name of the template
   * @param  boolean $inherited Whether this template is being inherited by another
   * @return null
   */
  function getContext($name = '') {
    $context = array();
    $context = sapwood_get_context($name);
    $twig = "templates/{$name}/{$name}.twig";
    sapwood_timber_render($twig, $context);
  }

}



// initialize
sapwood()->templates = new Templates();
