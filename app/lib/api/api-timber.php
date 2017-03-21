<?php

namespace Sapwood;

function sapwood_add_context($data) {
  add_filter('sapwood/timber/context', function($context) use ($data) {
    $context = array_merge($context, $data);
    return $context;
  });
}

function sapwood_timber_render($path, $data) {
  \Timber\Timber::render($path, $data);
}

function sapwood_get_context() {

  $context = \Timber\Timber::get_context();

  $context = sapwood_apply_filters('sapwood/timber/context', '', array($context));

  return $context;
}

function sapwood_get_post($id = null) {
  $p = new \Timber\Post($id);
  $p = apply_filters('sapwood/timber/post', $p);
  $p = apply_filters('sapwood/timber/post/type=' . $p->post_type, $p);
  $p = apply_filters('sapwood/timber/post/id=' . $p->ID, $p);

  return $p;
}

function sapwood_get_site() {
  $s = new \Timber\Site();
  $s = apply_filters('sapwood/timber/site', $s);

  return $s;
}

function sapwood_get_theme() {
  $t = new \Timber\Theme();
  $t = apply_filters('sapwood/timber/theme', $t);

  return $t;
}
