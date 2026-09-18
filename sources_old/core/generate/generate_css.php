<?PHP

class generate_css {
	# vars
	public generate $generate;
	public array $files = [ ];
	# methods
	public function __construct ( generate $generate ) {
		$this->generate = $generate;
	}
	
	public function file_parsing ( string $file_path ) : generate_css_file {
		$files = $this->files;
		
		if( ! isset( $files[ $file_path ] ) ){
			$files[ $file_path ] = new generate_css_file( $this , $file_path );
			$files[ $file_path ]->parsing( );
		}
		
		return $this->files[ $file_path ] ?: new generate_css_file( $this , $file_path );
	}
	
	public function in_file( string $file_path , string $css_name , string $css_value , bool $replace = false ) {
		$file = $this->file_parsing( $file_path );
	}
}

class generate_css_file {
	# vars
	public generate_css $css;
	public string $file_path = '';
	public array $strings = [ ];
	# methods
	public function __construct ( generate_css $css , string $file_path ) {
		$this->css = $css;
		$this->file_path = $file_path;
	}
	public function parsing ( ) {
		
	}
}
?>