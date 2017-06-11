<?php
namespace Sapwood\Component;

use Sapwood;
use Sapwood\Library;
use Sapwood\Library\Component;

/**
 * The Class name, twig file, php file, the $name variable below, and the
 * containing folder must all share the same name. The class can be capitalized,
 * but all else should be lowercase. All should use snake_case.
 */
class Layout_builder extends Component {

  // Component must have a name
  var $name = 'layout-builder',
      $object = array();

  function __construct() {

    // always call parent construct.
    parent::__construct();
  }

  /**
   * This method is called just after the class is instantiated
   *
   * This is called via an action and should not return a value.
   */
  public function register() {

  }

  /**
   * This method is called during validation of the object.
   *
   * This function should return true or false depending on if the object passed
   * is valid. This is a good place to ensure the appropriate required fields
   * are available to the component.
   *
   * @return boolean Whether the object passed validation
   */
  public function validate_object() {
    return true;
  }

  /**
   * This method is run when the object did not pass validation. The component
   * will not be displayed. This is a good place to console log information
   * about why the validation failed, or render a fallback.
   *
   * This is called via an action and should not return a value.
   */
  public function invalid() {
    sapwood_console($this->object, $this->name . ' is invalid. The following object was passed in.');
  }

  /**
   * This method is applied to the data before it is used in the component.
   * This is where manipulations on the data can be done to ensure it is
   * returned to the component in the appropriate format, and to run any
   * complex logic that shouldn't be housed in the twig file.
   *
   * @param array $data The data passed in by the component.
   * @return array The formatted data
   */
  public function format_data($data = array()) {

    // strip name from each section. not needed.
    $data = array_map(function($layout) {
      return $layout['section'];
    }, $data);

    $data = array_map(function($section) {
      // sane defaults
      $clean_section = array(
        'id' => $section['id'] ?? '',
        'animate' => $section['animate'] ?? false,
        'animation' => $section['animation'] ?? '',
        'css' => $section['css'] ?? '',
        'advanced' => $section['has_advanced'] ?? false,
        'number_of_columns' => $section['number_of_columns'] ?? 1,
        'sidebar' => $section['sidebar'] ?? 0,
        'columns' => $section['columns'] ?? array()
      );

      // convert numeric representation to human readable
      if($clean_section['sidebar'] === 0) $clean_section['sidebar'] = 'none';
      if($clean_section['sidebar'] === 1) $clean_section['sidebar'] = 'left';
      if($clean_section['sidebar'] === 2) $clean_section['sidebar'] = 'right';

      // ensure anything other than 2 columns, there can't be a sidebar
      if($clean_section['number_of_columns'] !== 2) $clean_section['sidebar'] = 'none';

      // convert assoc array to indexed
      $clean_section['columns'] = array_values($clean_section['columns']);

      // remove unecessary columns
      $clean_section['columns'] = array_slice($clean_section['columns'], 0, $clean_section['number_of_columns']);

      return $clean_section;
    }, $data);


    if(!empty($data['css'])) {
      $data['css'] = "<style type='text/css' rel='stylesheet'>\n{$data['css']}\n</style>";
    }

    // now we add the column widths
    foreach($data as &$section) {
      $clean_columns = array_map(function($column, $k) use ($section) {
        // class dictionary
        $classes = array(
          1 => array(                     // one column
            'none' => array_fill(0, 1, 'col-xs-12')
          ),
          2 => array(                     // two column
            'none' => array_fill(0, 2, 'col-xs-12 col-md-6'),

            'left' => array(              // left sidebar
              0 => 'col-xs-12 col-md-4',    // left column
              1 => 'col-xs-12 col-md-8'     // right column
            ),
            'right' => array(
              0 => 'col-xs-12 col-md-8',    // left column
              1 => 'col-xs-12 col-md-4'     // right column
            )
          ),
          3 => array(                     // one column
            'none' => array_fill(0, 3, 'col-xs-12 col-md-4')
          ),
          4 => array(                     // one column
            'none' => array_fill(0, 4, 'col-xs-12 col-sm-6 col-md-3')
          )
        );

        $col_num = $section['number_of_columns'];
        $sidebar = $section['sidebar'];

        // get the classes for this column from the array
        $column['class'] = $classes[$col_num][$sidebar][$k];

        return $column;

      }, $section['columns'], array_keys($section['columns']));

      $section['columns'] = $clean_columns;
    }

    return $data;
  }

  /**
   * This method is applied to the element of the object. This is a good
   * place to mutate the element before it is created, or set it to an empty
   * string to avoid printing an opening element for this component.
   *
   * @param string $element The element passed in by the component
   * @return string The element to use as a wrapper around the component.
   */
  public function format_element($element = '') {
    return $element;
  }

  /**
   * This method is applied to the attributes of the object. This is a good
   * place to format the necessary attributes for the wrapper element to
   * contain. This should be return as an associative array that will be
   * transformed into html property="values" inside the opening element.
   *
   * @Note: This method runs *after* the element and data have been formatted above
   *
   * @param  array $attributes The attributes passed in by the component
   * @return array             An associative array for use when printing the opening element
   */
  public function format_attributes($attributes = array()) {
    $data = $this->object['data'];
    $data_atts = $data['attributes'] ?? array();

    if(!empty($data_atts['id'])) {
      $attributes['id'] = $data_atts['id'];
      unset($data_atts['id']);
    }
    if(!empty($data_atts['class'])) {
      $attributes['class'] .= " {$data_atts['class']}";
      unset($data_atts['class']);
    }

    // merge in the rest
    $attributes = wp_parse_args($data_atts, $attributes);

    return $attributes;
  }

  /**
   * This method can be used for enqueueing styles required by the component.
   * @Note Because a component is rendered after wp_head has run, styles will
   * be printed in the footer. This can cause a FOUC, and it is recommended that
   * the theme fade in after styles have loaded.
   *
   * This is called via an action and should not return a value.
   */
  public function enqueue_styles() {

  }

  /**
   * This method can be used for enqueueing scripts required by the component.
   * @Note Because a component is rendered after wp_head has run, scripts will
   * be printed in the footer.
   *
   * This is called via an action and should not return a value.
   */
  public function enqueue_scripts() {

  }

  /**
   * This method is run just before the opening element tag. This is a good
   * place to print any additional opening wrapper tags, or elements you
   * wish to be placed as a top sibling to the component.
   *
   * This is called via an action and should not return a value.
   */
  public function prefix() {
  }

  /**
   * This method is run just after the opening element tag, and before the
   * content of the component is printed. This is a good
   * place to print any additional elements you wish to exist inside the wrapper
   * element.
   *
   * This is called via an action and should not return a value.
   */
  public function before() {

  }

  /**
   * This method is run just before the closing element tag. This is a good
   * place to print any additional elements you wish to exist inside the wrapper
   * element, after the content.
   *
   * This is called via an action and should not return a value.
   */
  public function after() {

  }

  /**
   * This method is run just after the closing element tag. This is a good
   * place to print any additional closing tags, or elements that should be
   * placed as bottom siblings to the component element.
   *
   * This is called via an action and should not return a value.
   */
  public function suffix() {

  }

  /**
   * This method is applied when an ajax call is made from a component script.
   * The ajax call should contain the following information as its post data:
   * $.post(document.location, {
   *  action: 'sapwood',
   *  name: 'component-name' // such as "link" or similar
   *  sapwood: Sapwood.nonce,
   *  data: {} // any arbitrary data you would like. This data is passed to the method
   * }, success, 'json');
   *
   * Before this script is called, the nonce is checked and validated, and
   * needn't be done here. However, sanitization should still be handled within
   * this method. No sanitization is run on $data prior to being passed to this
   * method.
   *
   * @Note: The method should die after it has printed its response.
   *
   * This is called via an action and should not return a value.
   *
   * @param   array  $data The data that was sent through the ajax request
   */
  public function ajax_response($data = array()) {
    die;
  }

}
