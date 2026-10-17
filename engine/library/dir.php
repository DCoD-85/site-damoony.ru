<?PHP

class library_dir {
	private null|library $library = null;
	
	public function __construct( null|library $library ) {
		$this->library = $library;
	}
	
	# scan directory
	# $dir          - path to scan
	# $included     - recurse into subdirectories
	# $dirs         - include directories in result
	# $files        - include files in result
	# $dir_result   - accumulator (by reference, used in recursion)
	public function get (
		string $dir = DSA ,
		bool $included = false ,
		bool $dirs = true ,
		bool $files = true ,
		array &$dir_result = [ ]
	) {
		$dir_current = $dir;
		
		if ( is_dir( $dir_current ) ) {
			if ( $dh = opendir( $dir_current ) ) {
				while ( ( $file = readdir( $dh ) ) !== false ) {
					switch ( $file ) {
						case '.' :
						case '..' :
							break;
						default :
							$dir_current = $dir . $file;
							$is_dir  = is_dir( $dir_current );
							$is_file = is_file( $dir_current );
							
							if ( ( $dirs === true && $is_dir === true ) || ( $files === true && $is_file === true ) ) {
								$dir_result[ $dir_current ] = $file;
							}
							
							if ( $included && $is_dir ) {
								$this->get( $dir_current . DS , $included , $dirs , $files , $dir_result );
							}
							
							break;
					}
				}
				
				closedir( $dh );
			}
		}
		
		return $dir_result;
	}
}