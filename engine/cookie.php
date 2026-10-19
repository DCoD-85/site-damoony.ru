<?PHP
# engine/cookie.php — единая точка установки cookie.
# Безопасные дефолты: httponly, samesite=Lax, secure по HTTPS.
# Для SSO передаётся domain в $opts.

class cookie {

    private const DEFAULTS = [
        'expires'  => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => null,        // null = auto по HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ];

    public static function set ( string $name , string $value , array $opts = [] ) : bool {
        if ( headers_sent( ) ) return false;

        $final = array_merge( self::DEFAULTS , $opts );
        if ( $final[ 'secure' ] === null ) {
            $final[ 'secure' ] = ( ( $_SERVER[ 'HTTPS' ] ?? '' ) === 'on' );
        }

        return setcookie( $name , $value , $final );
    }

    public static function delete ( string $name , array $opts = [] ) : bool {
        $opts[ 'expires' ] = time( ) - 3600;
        return self::set( $name , '' , $opts );
    }
}