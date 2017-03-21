<?php

function sapwood_add_twig_filter($twig, $str, $callable) {
  if(!is_callable($callable)) {
    return false;
  }

  if(empty($str)) {
    return false;
  }

  $twig = sapwood_add_twig_stringloader($twig);

  $filter = new \Twig_SimpleFilter($str, $callable, array('needs_context' => true));

  $twig->addFilter($twig_string, $filter);

  return $twig;
}

function sapwood_add_twig_function($twig, $str, $callable) {
  if(!is_callable($callable)) {
    return false;
  }

  if(empty($str)) {
    return false;
  }

  $twig = sapwood_add_twig_stringloader($twig);

  $filter = new \Twig_SimpleFunction($str, $callable, array('needs_context' => true));

  $twig->addFunction($twig_string, $filter);

  return $twig;
}

function sapwood_add_twig_test($twig, $str, $callable) {
  if(!is_callable($callable)) {
    return false;
  }

  if(empty($str)) {
    return false;
  }

  $twig = sapwood_add_twig_stringloader($twig);

  $filter = new \Twig_SimpleTest($str, $callable);
  $twig->addTest($twig_string, $filter);

  return $twig;
}

function sapwood_add_twig_stringloader($twig) {
  if(!sapwood()->twig_extension_loaded) {
    $twig->addExtension(new \Twig_Extension_StringLoader());
    sapwood()->twig_extension_loaded = true;
  }

  return $twig;
}

function sapwood_add_to_twig($twig, $type, $string, $callable) {
  $types = array(
    'function' => 'sapwood_add_twig_function',
    'filter' => 'sapwood_add_twig_filter',
    'test' => 'sapwood_add_twig_test'
  );

  if(!in_array($type, $types)) return false;

  if(!is_callable($callable)) return false;

  if(empty($string)) return false;

  return call_user_func($types[$type], $twig, $string, $callable);
}
