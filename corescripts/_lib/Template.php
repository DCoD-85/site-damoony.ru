<?PHP

class Template {

	public static function render ( string $tpl , array $vars ) : string {
		if ( ! is_file( $tpl ) ) {
			throw new RuntimeException( "Template not found: {$tpl}" );
		}
		$content = file_get_contents( $tpl );
		if ( $content === false ) {
			throw new RuntimeException( "Cannot read: {$tpl}" );
		}

		$search  = [ ];
		$replace = [ ];
		foreach ( $vars as $k => $v ) {
			$search[ ]  = '<' . $k . '>';
			$replace[ ] = (string) $v;
		}

		return str_replace( $search , $replace , $content );
	}

	public static function scan ( string $dir ) : array {
		if ( ! is_dir( $dir ) ) return [ ];
		$out = [ ];
		$it  = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir , FilesystemIterator::SKIP_DOTS )
		);
		foreach ( $it as $f ) {
			if ( ! $f->isFile( ) ) continue;
			if ( substr( $f->getFilename( ) , -4 ) !== '.tpl' ) continue;
			$rel    = substr( $f->getPathname( ) , strlen( $dir ) + 1 );
			$out[ ] = str_replace( '\\' , '/' , $rel );
		}
		sort( $out );
		return $out;
	}

	public static function lint ( string $file ) : ?string {
		$out  = [ ];
		$code = 0;
		exec( 'php -l ' . escapeshellarg( $file ) . ' 2>&1' , $out , $code );
		return $code === 0 ? null : implode( "\n" , $out );
	}
}