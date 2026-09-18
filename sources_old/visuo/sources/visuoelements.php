<?PHP

if( ! defined( 'DS' ) ) define( 'DS' , DIRECTORY_SEPARATOR );
if( ! defined( 'DSA' ) ) define( 'DSA' , '/' );

$path_base = 'visuoelements' . DS;

# #form : 
( include_once $path = $path_base . 'form.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'form' . DS . 'form.php' ) or error_file( $path , __FILE__ , __LINE__ );

# # input : 
( include_once $path = $path_base . 'input.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'form.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'hidden.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'text.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'password.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'file.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'button.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'submit.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'reset.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'checkbox.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'radio.php' ) or error_file( $path , __FILE__ , __LINE__ );
# ( include_once $path = $path_base . 'input' . DS . 'number.php' ) or error_file( $path , __FILE__ , __LINE__ );
# ( include_once $path = $path_base . 'input' . DS . 'date.php' ) or error_file( $path , __FILE__ , __LINE__ );
# ( include_once $path = $path_base . 'input' . DS . 'range.php' ) or error_file( $path , __FILE__ , __LINE__ );
# ( include_once $path = $path_base . 'input' . DS . 'color.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'select.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'input' . DS . 'textarea.php' ) or error_file( $path , __FILE__ , __LINE__ );
# table
( include_once $path = $path_base . 'table.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'table' . DS . 'tablebox.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'table' . DS . 'table.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'table' . DS . 'block.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'table' . DS . 'blocktext.php' ) or error_file( $path , __FILE__ , __LINE__ );
# block
( include_once $path = $path_base . 'block.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'block' . DS . 'text.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'block' . DS . 'block.php' ) or error_file( $path , __FILE__ , __LINE__ );
# ( include_once $path = $path_base . 'block' . DS . 'block.php' ) or error_file( $path , __FILE__ , __LINE__ );

( include_once $path = $path_base . 'link.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'link' . DS . 'simple.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'link' . DS . 'load.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $path_base . 'link' . DS . 'popup.php' ) or error_file( $path , __FILE__ , __LINE__ );

# : form

/*
$path_input = $path . 'input';
$path_text = $path . 'text';
$files = [
	[ $path . 'form' , 'visuoelement_form' ],
	[ $path_input , 'input' ] ,
	# $path_input . 'hidden' ,
	[ $path_input . DS . 'text' , 'visuoelement_input_text' ] ,
	[ $path_input . DS . 'hidden' , 'visuoelement_input_hidden' ] ,
	# $path_input . 'password' ,
	# $path_input . 'textarea' ,
	# $path_input . 'checkbox' ,
	# $path_input . 'radio' ,
	# $path_input . 'radiogroup' ,
];

foreach( $files as $file ) {
	if( ! include_once $file_include = $file[ 0 ] . '.php' )
		die( '<div>Не возможно полключить файл: ' . $file_include . ', или отсутствует класс: ' . $file[ 1 ] . ' <[ ' . __FILE__ . ' ]</div>' );
}
*/

?>