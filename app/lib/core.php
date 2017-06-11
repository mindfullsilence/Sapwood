<?php

namespace Sapwood;

class Sapwood {

    function initialize() {

		// vars
		$this->settings = array(

        // basic
        'name'	        => __('Sapwood', 'sapwood'),
        'version'       => '1.0.0',
        'theme'         => get_stylesheet_directory(),
        'views'         => get_stylesheet_directory() . '/app/public',
        'component_dir' => get_stylesheet_directory() . '/app/public/components',
        'template_dir'  => get_stylesheet_directory() . '/app/public/templates',
        'lib'           => dirname(__FILE__),
        'api'           => dirname(__FILE__) . '/api',
        'core'          => dirname(__FILE__) . '/core',
        'ajax'          => wp_create_nonce('sapwood')
		);

        // api
        require_once($this->settings['api'] . '/api-helpers.php');

        sapwood_maybe_load(sapwood_get_setting('core') . '/hook-aliases.php');

        sapwood_maybe_load(sapwood_get_setting('api') . '/api-acf.php');
        sapwood_maybe_load(sapwood_get_setting('api') . '/api-twig.php');
        sapwood_maybe_load(sapwood_get_setting('api') . '/api-timber.php');
        sapwood_maybe_load(sapwood_get_setting('api') . '/api-template.php');
        sapwood_maybe_load(sapwood_get_setting('api') . '/api-component.php');

        sapwood_maybe_load(sapwood_get_setting('core') . '/Utilities.php');

        // components
        sapwood_maybe_load(sapwood_get_setting('core') . '/Component.php');
        sapwood_maybe_load(sapwood_get_setting('core') . '/Components.php');
        sapwood_do_action('components/loaded', null, null);

        // templates
        sapwood_maybe_load(sapwood_get_setting('core') . '/TemplateHierarchy.php');
        sapwood_maybe_load(sapwood_get_setting('core') . '/Templates.php');
        sapwood_maybe_load(sapwood_get_setting('core') . '/Template.php');
        sapwood_do_action('templates/loaded', null, null);

        \Timber\Timber::$locations = (array) \Timber\Timber::$locations;
        array_push(\Timber\Timber::$locations, sapwood_get_setting('views'));
        array_push(\Timber\Timber::$locations, sapwood_get_setting('template_dir'));
        array_push(\Timber\Timber::$locations, sapwood_get_setting('component_dir'));
        array_push(\Timber\Timber::$locations, get_template_directory() . '/app/public');
        array_push(\Timber\Timber::$locations, get_template_directory() . '/app/public/templates');
        array_push(\Timber\Timber::$locations, get_template_directory() . '/app/public/components');
        sapwood_do_action('core/loaded', null, null);
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
