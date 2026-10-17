<?PHP

class library {
	# vars
	public null|main $main = null;
	# vars : library
	public null|library_gen $gen = null;
	public null|library_dir $dir = null;
	public null|library_url $url = null;
	public null|library_link $link = null;
	public null|library_vars $vars = null;
	public null|library_file $file = null;
	public null|library_image $image = null;
	# methods
	public function __construct ( null|main $main = null ) {
		$this->main = $main;
		
		( include_once $path = __DIR__ . DS . 'library' . DS . 'gen.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = __DIR__ . DS . 'library' . DS . 'dir.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = __DIR__ . DS . 'library' . DS . 'url.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = __DIR__ . DS . 'library' . DS . 'link.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = __DIR__ . DS . 'library' . DS . 'vars.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = __DIR__ . DS . 'library' . DS . 'file.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = __DIR__ . DS . 'library' . DS . 'image.php' ) or error_file( $path , __FILE__ , __LINE__ );
		
		$this->gen   = self::make( 'library_gen'   , $this );
		$this->dir   = self::make( 'library_dir'   , $this );
		$this->url   = self::make( 'library_url'   , $this );
		$this->link  = self::make( 'library_link'  , $this );
		$this->vars  = self::make( 'library_vars'  , $this );
		$this->file  = self::make( 'library_file'  , $this );
		$this->image = self::make( 'library_image' , $this );

		// init library classes
		$this->url->init( );
	}
	
	private static function make ( string $class , self $library ) : object {
		if ( ! class_exists( $class ) ) {
			error_class( $class , __FILE__ , __LINE__ );
			throw new \LogicException( "Class {$class} not found" );
		}
		return new $class( $library );
	}

	public function gen ( ) {
		return $this->gen;
	}
	public function dir ( ) {
		return $this->dir;
	}

	public function url ( ) {
		return $this->url;
	}

	public function link ( ) {
		return $this->link;
	}

	public function vars ( ) {
		return $this->vars;
	}

	public function file ( ) {
		return $this->file;
	}

	public function image ( ) {
		return $this->image;
	}
}

?>