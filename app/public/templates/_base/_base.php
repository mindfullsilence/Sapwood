<?php

// Extends from binding
load_template(get_template_directory() . '/app/public/templates/binding/binding.php', true);

add_action('sapwood/template/head/prefix/name=spine', function($context) {
  $subcontext = array(
    '_object' => $context,
    'template' => 'base'
  );
  $parts = array(
    'parts/title',
    'parts/meta',
    'parts/links',
    'parts/wp_head'
  );

  array_map(function($template) use ($subcontext) {
    \Timber\Timber::render($template . '.twig', $subcontext);
  }, $parts);
}, 20, 1);

/**
 * Google analytics tracking code
 * @var [type]
 */
add_action('sapwood/template/head/prefix/name=spine', function($context) {
  $uaid = (string) get_field('uaid', 'options');

  if(strpos($uaid, 'UA') === 0) {
    $uacontext = array('code' => $uaid);
    Timber::render('parts/google-analytics.twig', $uacontext);
  }
}, 0, 1);

/**
 * Google tag manager script
 * @var [type]
 */
add_action('sapwood/template/head/prefix/name=spine', function($context) {
  $gtmid = (string) get_field('gtmid', 'options');

  if(strpos($gtmid, 'GTM') === 0) {
    $gtmcontext = array('code' => $gtmid);
    Timber::render('parts/gtm.twig', $gtmcontext);
  }
}, 0, 1);

/**
 * Google tag manager noscript frame
 * @var [type]
 */
add_action('sapwood/template/body/prefix/name=spine', function($context) {
  $gtmid = (string) get_field('gtmid', 'options');

  if(strpos($gtmid, 'GTM') === 0) {
    $gtmcontext = array('code' => $gtmid);
    Timber::render('parts/gtm-nojs.twig', $gtmcontext);
  }
}, 0, 1);

/**
 * wp_footer function
 */
add_action('sapwood/template/body/suffix/name=spine', function($context) {
  $subcontext = array(
    '_object' => $context,
    'template' => 'base'
  );
  $parts = array(
    'parts/wp_footer.twig'
  );

  array_map(function($template) use ($subcontext) {
    \Timber\Timber::render($template, $subcontext);
  }, $parts);
}, 20, 1);
