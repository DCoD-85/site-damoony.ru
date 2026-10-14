<?PHP

# ============================================================
#   Json — единый JSON-helper.
#   Заменяет 3 копии preg_replace_callback('/^( +)/m', ...)
#   + даёт атомарную запись.
# ============================================================

class Json {

    # --- encode с отступами табами ---
    public static function encode ( mixed $data ) : string {
        $json = json_encode(
            $data ,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ( $json === false ) {
            throw new RuntimeException( 'json_encode failed: ' . json_last_error_msg() );
        }

        # JSON_PRETTY_PRINT → 4 пробела, заменяем на табы
        return preg_replace_callback(
            '/^( +)/m' ,
            fn( $m ) => str_repeat( "\t" , (int) ( strlen( $m[ 1 ] ) / 4 ) ) ,
            $json
        );
    }

    # --- decode с fallback ---
    public static function decode ( string $json , mixed $default = null ) : mixed {
        if ( $json === '' ) return $default;
        $data = @json_decode( $json , true );
        return $data === null ? $default : $data;
    }

    # --- атомарная запись ---
    public static function writeAtomic ( string $path , mixed $data ) : void {
        $json = self::encode( $data );

        Fs::mkdir( dirname( $path ) );

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

        $written = fwrite( $fp , $json );
        fflush( $fp );
        flock( $fp , LOCK_UN );
        fclose( $fp );

        if ( $written === false || $written !== strlen( $json ) ) {
            @unlink( $tmp );
            throw new RuntimeException( "Short write: {$tmp}" );
        }

        if ( ! @rename( $tmp , $path ) ) {
            @unlink( $tmp );
            throw new RuntimeException( "Cannot rename: {$tmp} → {$path}" );
        }
    }

    # --- чтение ---
    public static function read ( string $path , mixed $default = null ) : mixed {
        if ( ! is_file( $path ) ) return $default;
        $raw = @file_get_contents( $path );
        if ( $raw === false ) return $default;
        return self::decode( $raw , $default );
    }
}