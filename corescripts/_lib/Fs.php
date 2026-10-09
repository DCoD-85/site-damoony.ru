<?PHP

class Fs {

	public static function mkdir ( string $path ) : void {
		if ( is_dir( $path ) ) return;
		if ( ! mkdir( $path , 0755 , true ) ) {
			throw new RuntimeException( "Cannot create dir: {$path}" );
		}
	}

	public static function write ( string $path , string $content ) : void {
		self::mkdir( dirname( $path ) );
		if ( file_put_contents( $path , $content ) === false ) {
			throw new RuntimeException( "Cannot write file: {$path}" );
		}
	}

	public static function exists ( string $path ) : bool {
		return file_exists( $path );
	}

	public static function rel ( string $path ) : string {
		$path = str_replace( '\\' , '/' , $path );
		$base = str_replace( '\\' , '/' , PROJECT_ROOT ) . '/';
		if ( str_starts_with( $path , $base ) ) {
			return substr( $path , strlen( $base ) );
		}
		return $path;
	}

	public static function rmrf ( string $path ) : void {
		if ( ! is_dir( $path ) ) {
			if ( is_file( $path ) ) @unlink( $path );
			return;
		}
		$it = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $path , FilesystemIterator::SKIP_DOTS ) ,
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ( $it as $f ) {
			$f->isDir( ) ? @rmdir( $f->getPathname( ) ) : @unlink( $f->getPathname( ) );
		}
		@rmdir( $path );
	}
	
	public static function scan_recursive ( string $dir ) : array {
		if ( ! is_dir( $dir ) ) return [ ];
		$out = [ ];
		$it  = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir , FilesystemIterator::SKIP_DOTS )
		);
		foreach ( $it as $f ) {
			if ( ! $f->isFile( ) ) continue;
			$rel    = substr( $f->getPathname( ) , strlen( $dir ) + 1 );
			$out[ ] = str_replace( '\\' , '/' , $rel );
		}
		sort( $out );
		return $out;
	}
}