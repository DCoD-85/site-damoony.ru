<?PHP

if( ! defined( 'DS' ) ) define( 'DS' , DIRECTORY_SEPARATOR );
if( ! defined( 'DSA' ) ) define( 'DSA' , '/' );

$visuoelpath = __DIR__ . DS . 'visuoel' . DS;

( include_once $file = $visuoelpath . 'visuoel_group.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_group_element.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuoelpath . 'visuoel_form' . DS . 'visuoel_form_form.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_form.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuoelpath . 'visuoel_tabs' . DS . 'visuoel_tabs_page.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuoelpath . 'visuoel_tabs.php' ) or error_file( $file , __FILE__ , __LINE__ );

?>