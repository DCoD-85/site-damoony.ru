<?PHP

namespace <domain_ns>;

class alliasscheme {
	# --- refs ---
	public \main $main;
	public string $schemename = '';
	public bool $autoload = false;

	# --- schemes ---
	public null|alliasscheme_default $default = null;
	public null|alliasscheme_error   $error   = null;
	public null|alliasscheme_min     $min     = null;

	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->autoload( );
	}

	public function autoload ( bool $autoload = true , bool $init = true ) {
		$this->autoload = $init;

		if ( $autoload === true ) {
			( include_once $path = __DIR__ . DS . 'alliasscheme' . DS . 'alliasscheme_default.php' ) or error_file( $path , __FILE__ , __LINE__ );
			( include_once $path = __DIR__ . DS . 'alliasscheme' . DS . 'alliasscheme_error.php' )   or error_file( $path , __FILE__ , __LINE__ );
			( include_once $path = __DIR__ . DS . 'alliasscheme' . DS . 'alliasscheme_min.php' )     or error_file( $path , __FILE__ , __LINE__ );
		}

		if ( $init === true ) $this->init( );
	}

	public function init ( ) {
		$this->default = new alliasscheme_default( $this );
		$this->error   = new alliasscheme_error( $this );
		$this->min     = new alliasscheme_min( $this );
	}

	public function load ( string $schemename ) {
		$this->schemename = $schemename;
		$this->{$schemename}->load( );
	}

	public function reload ( ) {
		$schemename = $this->schemename;
		$this->{$schemename}->reload( );
	}
}