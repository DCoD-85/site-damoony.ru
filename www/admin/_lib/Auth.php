<?PHP

class Auth {

    private string $login = 'admin';
    private string $hash  = '';
    private string $path;

    # конфиг brute-force
    private const FAILS_FILE  = '/secure/admin_fails.json';
    private const MAX_FAILS   = 5;
    private const BAN_SECONDS = 900;
    private const DELAY_AFTER = 3;
    private const DELAY_USEC  = 500_000;

    public function __construct ( ) {
        $this->path = PROJECT_ROOT . '/config/admin.config';
        if ( is_file( $this->path ) ) {
            $json = file_get_contents( $this->path );
            $data = json_decode( $json , true );
            if ( isset( $data[ 'login' ] ) )         $this->login = (string) $data[ 'login' ];
            if ( isset( $data[ 'password_hash' ] ) ) $this->hash  = (string) $data[ 'password_hash' ];
        }
    }

    public function check ( ) : bool {
        return ! empty( $_SESSION[ 'admin_ok' ] );
    }

    public function login ( string $login , string $password ) : bool {

        $ip = $this->remote_ip( );

        if ( $this->is_banned( $ip ) ) {
            $this->delay( self::DELAY_USEC );
            return false;
        }

        if ( $this->hash === '' ) {
            $this->record_fail( $ip );
            return false;
        }

        $ok = hash_equals( $this->login , $login )
           && password_verify( $password , $this->hash );

        if ( ! $ok ) {
            $this->record_fail( $ip );

            if ( $this->fails_count( $ip ) >= self::DELAY_AFTER ) {
                $this->delay( self::DELAY_USEC );
            }
            return false;
        }

        $this->reset_fails( $ip );

        if ( session_status( ) === PHP_SESSION_ACTIVE ) {
            session_regenerate_id( true );
        }
        $_SESSION[ 'admin_ok' ]    = true;
        $_SESSION[ 'admin_login' ] = $this->login;
        return true;
    }

    public function logout ( ) : void {
        $_SESSION = [ ];

        if ( ini_get( 'session.use_cookies' ) ) {
            $params = session_get_cookie_params( );
            setcookie(
                session_name( ) ,
                '' ,
                time( ) - 42000 ,
                $params[ 'path' ] ,
                $params[ 'domain' ] ,
                $params[ 'secure' ] ,
                $params[ 'httponly' ]
            );
        }

        session_destroy( );
    }

    public function login_name ( ) : string {
        return (string) ( $_SESSION[ 'admin_login' ] ?? '' );
    }

    # ============================================================
    #   BRUTE-FORCE
    # ============================================================

    private function remote_ip ( ) : string {
        $ip = $_SERVER[ 'REMOTE_ADDR' ] ?? '';
        return is_string( $ip ) && $ip !== '' ? $ip : '0.0.0.0';
    }

    private function fails_path ( ) : string {
        return PROJECT_ROOT . self::FAILS_FILE;
    }

    private function load_fails ( ) : array {
        $path = $this->fails_path( );
        if ( ! is_file( $path ) ) return [ ];
        $raw = @file_get_contents( $path );
        if ( $raw === false ) return [ ];
        $data = @json_decode( $raw , true );
        return is_array( $data ) ? $data : [ ];
    }

    private function save_fails ( array $data ) : void {
        $path = $this->fails_path( );
        $dir  = dirname( $path );
        if ( ! is_dir( $dir ) ) @mkdir( $dir , 0755 , true );

        $json = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
        @file_put_contents( $path , $json , LOCK_EX );
    }

    private function is_banned ( string $ip ) : bool {
        $data = $this->load_fails( );
        $ban  = $data[ $ip ][ 'banned_until' ] ?? 0;
        return $ban > time( );
    }

    private function fails_count ( string $ip ) : int {
        $data = $this->load_fails( );
        return (int) ( $data[ $ip ][ 'count' ] ?? 0 );
    }

    private function record_fail ( string $ip ) : void {
        $data = $this->load_fails( );
        $cur  = $data[ $ip ] ?? [ 'count' => 0 , 'last_ts' => 0 , 'banned_until' => 0 ];

        $cur[ 'count' ]   = (int) $cur[ 'count' ] + 1;
        $cur[ 'last_ts' ] = time( );

        if ( $cur[ 'count' ] >= self::MAX_FAILS ) {
            $cur[ 'banned_until' ] = time( ) + self::BAN_SECONDS;
        }

        $data[ $ip ] = $cur;

        $cutoff = time( ) - 7 * 86400;
        foreach ( $data as $k => $v ) {
            if ( ! is_array( $v ) ) { unset( $data[ $k ] ); continue; }
            $last = (int) ( $v[ 'last_ts' ] ?? 0 );
            $ban  = (int) ( $v[ 'banned_until' ] ?? 0 );
            if ( $last < $cutoff && $ban < time( ) ) unset( $data[ $k ] );
        }

        $this->save_fails( $data );

        error_log( 'admin_login_fail ip=' . $ip . ' login=' . ( $_POST[ 'login' ] ?? '?' ) );
    }

    private function reset_fails ( string $ip ) : void {
        $data = $this->load_fails( );
        unset( $data[ $ip ] );
        $this->save_fails( $data );
    }

    private function delay ( int $usec ) : void {
        usleep( $usec );
    }
}