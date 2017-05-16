<?php

namespace Sapwood;

if (!function_exists('sapwood_sanitize_ajax')) :
  function sapwood_sanitize_ajax($data) {
    if(!isset($_GET['sapwood'])) return;
    if(!isset($_GET['action'])) return;
    if(!isset($_GET['data'])) return;

    $sapwood = sanitize_text_field( $_GET['sapwood'] );
    $action = sanitize_text_field( $_GET['action'] );
    $data = json_decode( $_GET['data']} );

    if($sapwood !== 'ajax') die "variable 'sapwood' must equal 'ajax'";
  }
endif;
