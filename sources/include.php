<?PHP

# --- --- SCRIPT INFO --- ---
# --- --- SCRIPT INFO --- ---

# other
# ( include_once $path = __DIR__ . DS . 'other' . DS . 'debug.php' ) or error_file( $path , __FILE__ , __LINE__);
# ( include_once $path = __DIR__ . DS . 'other' . DS . 'errors.php' ) or error_file( $path , __FILE__ , __LINE__ );
# ( include_once $path = __DIR__ . DS . 'other' . DS . 'includer.php' ) or error_file( $path , __FILE__ , __LINE__ );
# ( include_once $path = __DIR__ . DS . 'other' . DS . 'loader.php' ) or error_file( $path , __FILE__ , __LINE__ );

# system classes
( include_once $path = __DIR__ . DS . 'render.php' ) or error_file( $path , __FILE__ , __LINE__);
( include_once $path = __DIR__ . DS . 'user.php' ) or error_file( $path , __FILE__ , __LINE__);
( include_once $path = __DIR__ . DS . 'blacklist.php' ) or error_file( $path , __FILE__ , __LINE__);
( include_once $path = __DIR__ . DS . 'vardump.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'log.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'core.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'config.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'db.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'library.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'system.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'language.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'messages.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'visuo.php' ) or error_file( $path , __FILE__ , __LINE__ );

# modules
( include_once $path = __DIR__ . DS . 'gmod.php' ) or error_file( $path . __FILE__ . ' : ' . __LINE__ , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'mod.php' ) or error_file( $path . __FILE__ . ' : ' . __LINE__ , __FILE__ , __LINE__ );

?>