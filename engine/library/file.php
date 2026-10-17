<?PHP

class library_file {
	private null|library $library = null;
	
	public function __construct( null|library $library ) {
		$this->library = $library;
	}

	public function isfile ( string $filepath ) {
		return is_file( $filepath );
	}
	public function read ( string $filepath ) {
		return file_get_contents( $filepath );
	}
	public function write ( string $filepath , string $data ) {
		return file_put_contents( $filepath , $data );
	}
}

?>