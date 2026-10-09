<?PHP

if( ! defined( 'DS' ) ) define( 'DS' , DIRECTORY_SEPARATOR );
if( ! defined( 'DSA' ) ) define( 'DSA' , '/' );

$visuosources = __DIR__ . DS . 'sources' . DS;

( include_once $file = $visuosources . 'visuorelease_settings.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuosources . 'visuolog.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuolink.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuosources . 'visuotag.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuosources . 'visuoattrs.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuoattrs' . DS . 'visuoattrsclass.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuoattrs' . DS . 'visuoattrsstyle.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuoattrs' . DS . 'visuoattrsactions.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuoattrs' . DS . 'visuoattrsdata.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuometa.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuosources . 'visuostruct.php' ) or error_file( $file , __FILE__ , __LINE__ );

# visuoel: base + group + element
( include_once $file = $visuosources . 'visuoel.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuoels.php' ) or error_file( $file , __FILE__ , __LINE__ );

( include_once $file = $visuosources . 'visuorender.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuopage.php' ) or error_file( $file , __FILE__ , __LINE__ );
( include_once $file = $visuosources . 'visuorelease.php' ) or error_file( $file , __FILE__ , __LINE__ );

?>