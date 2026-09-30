<?PHP

namespace damoony;

class alliasscheme {
	# vars
	public \main $main;
	public string $schemename;
	public bool $autoload = false;
	public null|alliasscheme_login $login = null;
	public null|alliasscheme_error $error = null;
	public null|alliasscheme_min $min = null;
	public null|alliasscheme_www $www = null;
	public null|alliasscheme_visuo $visuo = null;
	# methods
	public function __construct ( \main $main ) {
		$this->main = $main;
		
		$this->autoload ( );
		
		
	}
	public function autoload ( bool $autoload = true , bool $init = true ) {
		$this->autoload = $init;

		# autoload
		if( $autoload === true ){
			( include_once $path = __DIR__ . DS . 'alliasscheme' . DS . 'alliasscheme_login.php' ) or error_file( $path , __FILE__ , __LINE__ );
			( include_once $path = __DIR__ . DS . 'alliasscheme' . DS . 'alliasscheme_error.php' ) or error_file( $path , __FILE__ , __LINE__ );
			( include_once $path = __DIR__ . DS . 'alliasscheme' . DS . 'alliasscheme_min.php' ) or error_file( $path , __FILE__ , __LINE__ );
			# oxydog
			( include_once $path = __DIR__ . DS . 'alliasscheme' . DS . 'alliasscheme_www.php' ) or error_file( $path , __FILE__ , __LINE__ );
			( include_once $path = __DIR__ . DS . 'alliasscheme' . DS . 'alliasscheme_visuo.php' ) or error_file( $path , __FILE__ , __LINE__ );
		}

		# init
		if( $init === true ){
			$this->init( );
		}

	}
	
	public function init ( ) {
		$this->login = new alliasscheme_login( $this );
		$this->error = new alliasscheme_error( $this );
		$this->min = new alliasscheme_min( $this );
		# 
		$this->www = new alliasscheme_www( $this );
		$this->visuo = new alliasscheme_visuo( $this );
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

?>