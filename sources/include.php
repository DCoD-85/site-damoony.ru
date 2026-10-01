<?PHP

# ============================================================
#   SOURCES INCLUDE — switchboard
#   Loads each module from sources\ if exists, else from sources_old\
#   Order is preserved from the original engine.
# ============================================================

$sources_dir_new = __DIR__;
$sources_dir_old = dirname( __DIR__ ) . DS . 'sources_old';

# --- module list in load order ---
$sources_modules = [
	'render' ,
	'user' ,
	'blacklist' ,
	'vardump' ,
	'log' ,
	'core' ,
	'db' ,
	'library' ,
	'system' ,
	'language' ,
	'messages' ,
	'visuo' ,
	'gmod' ,
	'mod' ,
];

# --- load each module ---
foreach ( $sources_modules as $sources_module ) {

	$path_new = $sources_dir_new . DS . $sources_module . '.php';
	$path_old = $sources_dir_old . DS . $sources_module . '.php';

	if ( is_file( $path_new ) ) {
		( include_once $path = $path_new ) or error_file( $path , __FILE__ , __LINE__ );
	} else if ( is_file( $path_old ) ) {
		( include_once $path = $path_old ) or error_file( $path , __FILE__ , __LINE__ );
	} else {
		error_file( $path_new . ' | ' . $path_old , __FILE__ , __LINE__ );
	}
}

unset(
	$sources_dir_new ,
	$sources_dir_old ,
	$sources_modules ,
	$sources_module ,
	$path_new ,
	$path_old ,
	$path
);

?>