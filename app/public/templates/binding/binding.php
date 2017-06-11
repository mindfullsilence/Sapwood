<?php

namespace Sapwood/Template;

use Sapwood\Template\Base;

class Binding extends Base {

  public $name = 'binding';
  public $extends = '_base';

  function __construct() {
    parent::__construct();
  }

}

new Binding();
