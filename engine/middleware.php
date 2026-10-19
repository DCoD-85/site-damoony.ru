<?PHP

# ============================================================
#   sources/middleware.php — pipeline обработчиков запроса.
#   Вызывается из index.php до main::main().
#
#   Каждый middleware — callable, возвращает void или Response.
#   Останавливает pipeline через исключение (StopMiddleware).
# ============================================================

class StopMiddleware extends Exception {}

class middleware {

    private array $stack = [ ];

    public function add ( callable $fn , string $name = '' ) : self {
        $this->stack[ ] = [ $fn , $name !== '' ? $name : 'm' . count( $this->stack ) ];
        return $this;
    }

    public function run ( ) : void {
        foreach ( $this->stack as [ $fn , $name ] ) {
            $fn( );
        }
    }

    # --- статические middleware ---

    # security headers (уже в index.php — оставляем там; middleware не дублирует)
    public static function securityHeaders ( bool $is_https ) : void {
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
        header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );

		// ДОБАВИТЬ CSP (разрешаем inline-скрипты для visuo)
    	header( "Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:" );
		
        if ( $is_https ) {
            header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
        }
    }

    # rate limiting по IP (для логина + API)
     # rate limiting по IP (для логина + API)
    public static function rateLimit ( string $key , int $max = 60 , int $window = 60 , int $ban = 900 ) : void {
        $ip = $_SERVER[ 'REMOTE_ADDR' ] ?? '0.0.0.0';
        $file = sys_get_temp_dir( ) . '/simplcms_rl_' . md5( $key . ':' . $ip );
        $now  = time( );

        # --- атомарный read-modify-write (M2) ---
        $fp = @fopen( $file , 'c+' );
        if ( $fp === false ) {
            # fail-open: не можем открыть — не блокируем
            return;
        }
        if ( ! flock( $fp , LOCK_EX ) ) {
            fclose( $fp );
            return;
        }

        $raw  = stream_get_contents( $fp );
        $data = @json_decode( $raw , true );
        if ( ! is_array( $data ) ) $data = [ ];
        $data += [ 'count' => 0 , 'start' => $now , 'banned_until' => 0 ];

        if ( $data[ 'banned_until' ] > $now ) {
            $retry = $data[ 'banned_until' ] - $now;
            flock( $fp , LOCK_UN ); fclose( $fp );
            http_response_code( 429 );
            header( 'Retry-After: ' . $retry );
            throw new StopMiddleware( 'Too Many Requests' );
        }

        if ( ( $now - $data[ 'start' ] ) > $window ) {
            $data[ 'count' ] = 0;
            $data[ 'start' ] = $now;
        }

        $data[ 'count' ]++;
        if ( $data[ 'count' ] > $max ) {
            $data[ 'banned_until' ] = $now + $ban;
        }

        ftruncate( $fp , 0 );
        rewind( $fp );
        fwrite( $fp , json_encode( $data ) );

        flock( $fp , LOCK_UN );
        fclose( $fp );
    }

    # HSTS only (уже в securityHeaders)
    # csrf (уже в admin — оставляем)
    # auth (уже в admin)
}