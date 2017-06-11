<?php

namespace Sapwood/Template;

use Sapwood\Template;


class Base extends Template {

    public $name = '_base';

    function __construct() {
        echo "loaded";
        parent::construct();
    }
}

new Base();