<?php

namespace Sapwood\Library;

class TemplateHierarchy
{

    private static $instance;

    /**
     * TemplateHierarchy constructor.
     */
    private function __construct()
    {
        // filters
        add_filter('sapwood/template/hierarchy', array($this, 'templateHierarchy'), 20, 1);
        add_filter('sapwood/template/include', array($this, 'blankPHP'), 20, 2);

        // actions
        add_action('sapwood/template/register', array($this, 'renderTemplate'), 20, 2);
        add_filter('sapwood/template/include', array($this, 'loadTemplate'), 20, 2);
    }

    private function getTemplateLocations(string $name)
    {
        $name = sapwood_get_template_name($name);
        $locations = array(
            "{$name}/{$name}.php",
            "{$name}.php"
        );

        return $locations;
    }

    function blankPHP($templates)
    {
        $path = get_theme_file_path('index.php');
        return $path;
    }

    function templateHierarchy($templates)
    {
        $mapped = array_map(
            array($this, 'getTemplateLocations'),
            $templates
        );
        $merged = call_user_func_array('array_merge', $mapped);

        return $merged;
    }

    function loadTemplate($template)
    {
        $name = sapwood_get_template_name($template);
        sapwood_load_template($name);
        echo sapwood_get_setting('template/name');
        sapwood_do_action('template/load', $name, array($name));
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

function sapwood_template_hierarchy()
{
    return TemplateHierarchy::getInstance();
}

sapwood_set_setting('template_hierarchy', sapwood_template_hierarchy());