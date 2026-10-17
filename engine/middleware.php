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

        if ( $is_https ) {
            header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
        }
    }

    # rate limiting по IP (для логина + API)
    public static function rateLimit ( string $key , int $max = 60 , int $window = 60 , int $ban = 900 ) : void {
        $ip = $_SERVER[ 'REMOTE_ADDR' ] ?? '0.0.0.0';
        $file = sys_get_temp_dir( ) . '/simplcms_rl_' . md5( $key . ':' . $ip );
        $now = time( );

        $data = [ 'count' => 0 , 'start' => $now , 'banned_until' => 0 ];
        if ( is_file( $file ) ) {
            $raw = @file_get_contents( $file );
            $parsed = @json_decode( $raw , true );
            if ( is_array( $parsed ) ) $data = $parsed;
        }

        if ( $data[ 'banned_until' ] > $now ) {
            http_response_code( 429 );
            header( 'Retry-After: ' . ( $data[ 'banned_until' ] - $now ) );
            die( 'Too Many Requests' );
        }

        if ( ( $now - $data[ 'start' ] ) > $window ) {
            $data[ 'count' ] = 0;
            $data[ 'start' ] = $now;
        }

        $data[ 'count' ]++;
        if ( $data[ 'count' ] > $max ) {
            $data[ 'banned_until' ] = $now + $ban;
        }

        @file_put_contents( $file , json_encode( $data ) , LOCK_EX );
    }

    # HSTS only (уже в securityHeaders)
    # csrf (уже в admin — оставляем)
    # auth (уже в admin)
}