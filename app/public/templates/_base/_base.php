<?php

namespace Sapwood\Template;

use Sapwood;
use Sapwood\Library;


class Base extends Template {

    public $name = '_base';

    function __construct() {
        echo "loaded";
        parent::construct();
    }
}

new Base();