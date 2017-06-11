<?php

namespace Sapwood;

use Sapwood\Binding;

class Index extends Binding {

    public $name = 'index';
    public $extends = 'binding';

    function __construct() {
      parent::__construct();
    }

    function get_data($data = array(), $name = '') {
      $data['post'] = new \Timber\Post();
      return $data;
    }

}

new Index();