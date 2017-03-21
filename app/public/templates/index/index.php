<?php

namespace Sapwood;

class Index extends Template {

    function __construct() {
      $this->name = 'index';

      parent::__construct();
    }

    function get_data($data = array(), $name = '') {
      return $data;
    }

    function extends($templates, $name) {
      $templates = array_merge($templates, array(
        'binding'
      ));

      return $templates;
    }

}
