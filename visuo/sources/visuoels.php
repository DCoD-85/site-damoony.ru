<?PHP

if( ! defined( 'DS' ) ) define( 'DS' , DIRECTORY_SEPARATOR );
if( ! defined( 'DSA' ) ) define( 'DSA' , '/' );

# --- visuotag stack (visuoel depends on it) ---
( include_once $file = __DIR__ . DS . 'visuoattrs' . DS . 'visuoattrsclass.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = __DIR__ . DS . 'visuoattrs' . DS . 'visuoattrsstyle.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = __DIR__ . DS . 'visuoattrs' . DS . 'visuoattrsactions.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = __DIR__ . DS . 'visuoattrs' . DS . 'visuoattrsdata.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = __DIR__ . DS . 'visuoattrs.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = __DIR__ . DS . 'visuotag.php' ) or error_file( $file , __FILE__ , __LINE__ );

# --- visuoel base ---
( include_once $file = __DIR__ . DS . 'visuoel.php' ) or error_file( $file , __FILE__ , __LINE__ );

$visuoelpath = __DIR__ . DS . 'visuoel' . DS;

# --- block ---
( include_once $file = $visuoelpath . 'visuoel_group.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_group_element.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuoelpath . 'visuoel_block__element.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_block' . DS . 'visuoel_block_text.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_block' . DS . 'visuoel_block_html.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_block.php' ) or error_file( $file , __FILE__ , __LINE__ );

# --- message ---
( include_once $file = $visuoelpath . 'visuoel_message__element.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_message' . DS . 'visuoel_message_info.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_message' . DS . 'visuoel_message_success.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_message' . DS . 'visuoel_message_warning.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_message' . DS . 'visuoel_message_error.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_message.php' ) or error_file( $file , __FILE__ , __LINE__ );

# --- form ---
( include_once $file = $visuoelpath . 'visuoel_form' . DS . 'visuoel_form_fieldset.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_form' . DS . 'visuoel_form_form.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_form.php' ) or error_file( $file , __FILE__ , __LINE__ );

# --- tabs ---
( include_once $file = $visuoelpath . 'visuoel_tabs' . DS . 'visuoel_tabs_page.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_tabs.php' ) or error_file( $file , __FILE__ , __LINE__ );

# --- input ---
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
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_checkbox_group.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input' . DS . 'visuoel_input_radio_group.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_input.php' ) or error_file( $file , __FILE__ , __LINE__ );

# --- field ---
( include_once $file = $visuoelpath . 'visuoel_field__element.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field__button.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_block.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_text.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_password.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_email.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_file.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_textarea.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_select.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_checkbox.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_radio.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_checkbox_group.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_radio_group.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_submit.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_reset.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field' . DS . 'visuoel_field_button.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_field.php' ) or error_file( $file , __FILE__ , __LINE__ );

# --- button ---
( include_once $file = $visuoelpath . 'visuoel_button__element.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_button' . DS . 'visuoel_button_submit.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_button' . DS . 'visuoel_button_reset.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_button' . DS . 'visuoel_button_button.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_button.php' ) or error_file( $file , __FILE__ , __LINE__ );

# --- table ---
( include_once $file = $visuoelpath . 'visuoel_table' . DS . 'visuoel_table_section.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_table' . DS . 'visuoel_table_row.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_table' . DS . 'visuoel_table_cell.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_table.php' ) or error_file( $file , __FILE__ , __LINE__ );

?>