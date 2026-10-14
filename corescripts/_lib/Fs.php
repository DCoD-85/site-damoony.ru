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

    # --- атомарная запись: temp + rename ---
    public static function writeAtomic ( string $path , string $content ) : void {
        self::mkdir( dirname( $path ) );

        $tmp = $path . '.tmp.' . bin2hex( random_bytes( 4 ) );

        $fp = @fopen( $tmp , 'cb' );
        if ( $fp === false ) {
            throw new RuntimeException( "Cannot open: {$tmp}" );
        }

        if ( ! flock( $fp , LOCK_EX ) ) {
            fclose( $fp );
            @unlink( $tmp );
            throw new RuntimeException( "Cannot lock: {$tmp}" );
        }

        $written = fwrite( $fp , $content );
        fflush( $fp );
        flock( $fp , LOCK_UN );
        fclose( $fp );

        if ( $written === false || $written !== strlen( $content ) ) {
            @unlink( $tmp );
            throw new RuntimeException( "Short write: {$tmp}" );
        }

        if ( ! @rename( $tmp , $path ) ) {
            @unlink( $tmp );
            throw new RuntimeException( "Cannot rename: {$tmp} → {$path}" );
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

    public static function copy_recursive ( string $src , string $dst ) : void {
        if ( ! is_dir( $src ) ) {
            self::mkdir( dirname( $dst ) );
            @copy( $src , $dst );
            return;
        }
        self::mkdir( $dst );
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $src , FilesystemIterator::SKIP_DOTS ) ,
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ( $it as $f ) {
            $rel = substr( $f->getPathname( ) , strlen( $src ) + 1 );
            $out = $dst . '/' . $rel;
            if ( $f->isDir( ) ) {
                self::mkdir( $out );
            } else {
                @copy( $f->getPathname( ) , $out );
            }
        }
    }
}