<?PHP

class visuodata_addclass {
	# vars : public
	public string $href = '';
	public string $class_name = '';
	public string $class_folder = '';
	public string $class_file = '';
	# methods
	public function __construct( ){
		
	}
	public function add( string $class_folder , string $class_file , string $class_name ){
		$this->class_folder = $class_folder;
		$this->class_file = $class_file;
		$this->class_name = $class_name;
	}
	public function is_path( ){
		$path = $this->class_folder . $this->class_file;
		return is_file( $path );
	}
	public function is_class( ){
		$class = $this->class_name;
		return class_exists( $class );
	}
	public function return_class( ){
		return new $this->class_name( );
	}
	public function return_classpath( ){
		return $this->class_folder . $this->class_file;
	}
	public function return_classname( ){
		return $this->class_name;
	}
}

?>