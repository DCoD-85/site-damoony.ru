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
		
		$this->gen = class_exists( 'library_gen' ) ?  new library_gen( $this ) : error_class( 'library_gen' , __FILE__ , __LINE__ );
		$this->dir = class_exists( 'library_dir' ) ?  new library_dir( $this ) : error_class( 'library_dir' , __FILE__ , __LINE__ );
		$this->url = class_exists( 'library_url' ) ?  new library_url( $this ) : error_class( 'library_url' , __FILE__ , __LINE__ );
		$this->link = class_exists( 'library_link' ) ?  new library_link( $this ) : error_class( 'library_link' , __FILE__ , __LINE__ );
		$this->vars = class_exists( 'library_vars' ) ?  new library_vars( $this ) : error_class( 'library_vars' , __FILE__ , __LINE__ );
		$this->file = class_exists( 'library_file' ) ?  new library_file( $this ) : error_class( 'library_file' , __FILE__ , __LINE__ );
		$this->image = class_exists( 'library_image' ) ?  new library_image( $this ) : error_class( 'library_image' , __FILE__ , __LINE__ );

		// init library classes
		$this->url->init( );
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