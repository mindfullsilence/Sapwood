<?php

namespace Sapwood;

class Sapwood {

  function initialize() {

		// vars
		$this->settings = array(

			// basic
			'name'				  => __('Sapwood', 'sapwood'),
			'version'       => '1.0.0',
      'theme'         => get_template_directory(),
      'views'         => get_template_directory() . '/app/public',
      'component_dir' => get_template_directory() . '/app/public/components',
      'template_dir'  => get_template_directory() . '/app/public/templates',
      'lib'           => dirname(__FILE__),
      'api'           => dirname(__FILE__) . '/api',
      'core'          => dirname(__FILE__) . '/core',
      'ajax'          => wp_create_nonce('sapwood')

		);

    // api
    include_once($this->settings['api'] . '/api-helpers.php');


    sapwood_maybe_load(sapwood_get_setting('lib') . 'templates.php');

    sapwood_maybe_load(sapwood_get_setting('core') . '/hook-aliases.php');
    sapwood_maybe_load(sapwood_get_setting('api') . '/api-twig.php');
    sapwood_maybe_load(sapwood_get_setting('api') . '/api-timber.php');
    sapwood_maybe_load(sapwood_get_setting('api') . '/api-template.php');
    sapwood_maybe_load(sapwood_get_setting('api') . '/api-component.php');

    // components
    sapwood_maybe_load(sapwood_get_setting('core') . '/Components.php');
    sapwood_maybe_load(sapwood_get_setting('core') . '/Component.php');
    do_action('sapwood/components/loaded');

    // templates
    sapwood_maybe_load(sapwood_get_setting('core') . '/Templates.php');
    sapwood_maybe_load(sapwood_get_setting('core') . '/Template.php');
    do_action('sapwood/templates/loaded');

    \Timber\Timber::$locations = (array) \Timber\Timber::$locations;
    array_push(\Timber\Timber::$locations, sapwood_get_setting('template_dir'));
    array_push(\Timber\Timber::$locations, sapwood_get_setting('component_dir'));

    do_action('sapwood/core/loaded');


  }

}

function sapwood() {

	global $sapwood;

	if( !isset($sapwood) ) {

		$sapwood = new Sapwood();

		$sapwood->initialize();

	}

	return $sapwood;

}

// initialize
sapwood();
