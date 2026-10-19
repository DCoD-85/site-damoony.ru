<?PHP

# ============================================================
#   SOURCES INCLUDE — switchboard
#   Loads each module from sources\
#   Order is preserved from the original engine.
# ============================================================

$sources_dir = __DIR__;

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
	'language' ,
	'messages' ,
	'visuo' ,
	'mod' ,
	'cookie' ,
];

# --- load each module ---
foreach ( $sources_modules as $sources_module ) {

	$path = $sources_dir . DS . $sources_module . '.php';

	if ( is_file( $path ) ) {
		( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
	} else {
		error_file( $path , __FILE__ , __LINE__ );
	}
}

unset(
	$sources_dir ,
	$sources_modules ,
	$sources_module ,
	$path
);