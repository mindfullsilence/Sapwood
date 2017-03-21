<?php

namespace Sapwood;

class Binding extends Template {

  function __construct() {
    $this->name = 'binding';

    parent::__construct();
  }

  function extends($templates, $name) {
    return $templates;
  }

}
