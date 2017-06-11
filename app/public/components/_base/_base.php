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
class Base extends Component {

  // Component must have a name
  var $name = 'base',
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
    ?>
    <script type="text/javascript">
      console.group('<?php echo $this->name; ?> is invalid. The following object was passed in.');
      console.log(<?php echo json_encode($this->object); ?>);
      console.endGroup();
    </script>
    <?php
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
   * @param  array $attributes The attributes passed in by the component
   * @return array             An associative array for use when printing the opening element
   */
  public function format_attributes($attributes = array()) {
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
