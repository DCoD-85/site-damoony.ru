<?PHP

class core_const {
	public core $core;
	public \main $main;
	
	public function __construct ( core $core ) {
		$this->core = $core;
		$this->main = $core->main;
		$this->init( );
	}
	
	public function init ( ) {
		if ( ! defined( 'DS' ) )  define( 'DS' , DIRECTORY_SEPARATOR );
		if ( ! defined( 'DSA' ) ) define( 'DSA' , '/' );
		if ( ! defined( 'BR' ) )  define( 'BR' , PHP_EOL );
		
		if ( ! defined( 'GL_LOG' ) )       define( 'GL_LOG' , false );
		if ( ! defined( 'GL_DEBUG' ) )     define( 'GL_DEBUG' , false );
		if ( ! defined( 'GL_DEBUGHARD' ) ) define( 'GL_DEBUGHARD' , false );
	}
	
	public function get ( string $name ) {
		return defined( $name ) ? constant( $name ) : null;
	}
}