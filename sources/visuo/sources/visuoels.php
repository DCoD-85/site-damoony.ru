<?PHP

if( ! defined( 'DS' ) ) define( 'DS' , DIRECTORY_SEPARATOR );
if( ! defined( 'DSA' ) ) define( 'DSA' , '/' );

$visuoelpath = __DIR__ . DS . 'visuoel' . DS;

( include_once $file = $visuoelpath . 'visuoel_group.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_group_element.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuoelpath . 'visuoel_input__element.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_text.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_hidden.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_password.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_submit.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_reset.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_button.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_email.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_file.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_textarea.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_select.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_checkbox.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_radio.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input.php' ) or error_file( $file , __FILE__ , __LINE__ );


( include_once $file = $visuoelpath . 'visuoel_field__element.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_text.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_password.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_email.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_file.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_textarea.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_select.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_checkbox.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_radio.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuoelpath . 'visuoel_form' . DS . 'visuoel_form_form.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_form.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuoelpath . 'visuoel_tabs' . DS . 'visuoel_tabs_page.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_tabs.php' ) or error_file( $file , __FILE__ , __LINE__ );

?>