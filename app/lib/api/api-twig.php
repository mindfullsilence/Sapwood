<?php
namespace Sapwood\Library;


/**
 * Manipulate twig
 * @var Twig_Environment $twig The instance of twig used for rendering
 * @return TwigEnvironment $twig The manipulated twig environment
 */
add_filter('timber/twig', function($twig) {
  $twig = sapwood_apply_filters('twig', false, array($twig));
  return $twig;
}, PHP_INT_MAX, 1);


function sapwood_add_twig_filter($str, $callable) {
  if(!is_callable($callable)) {
    return false;
  }

  if(empty($str)) {
    return false;
  }

  add_filter('sapwood/twig', function($twig) use ($str, $callable) {
    $twig = sapwood_add_twig_stringloader($twig);

    $filter = new \Twig_SimpleFilter($str, $callable, array('needs_context' => true));

    $twig->addFilter($filter);

    return $twig;
  });

  return true;
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
  if(!sapwood_set_setting('stringloader', true)) {
    $twig->addExtension(new \Twig_Extension_StringLoader());
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
