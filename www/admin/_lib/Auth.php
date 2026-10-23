<?PHP

class Auth {

    private string $path;
    private array  $users = [];

    # конфиг brute-force
    private const FAILS_FILE  = '/secure/admin_fails.json';
    private const MAX_FAILS   = 5;
    private const BAN_SECONDS = 900;
    private const DELAY_AFTER = 3;
    private const DELAY_USEC  = 500_000;

    public function __construct ( ) {
        $this->path = PROJECT_ROOT . '/secure/admins.config';
        $this->load_users();
    }

    private function load_users() : void {
        if ( ! is_file( $this->path ) ) return;
        $data = json_decode( file_get_contents( $this->path ) , true );
        $this->users = $data['users'] ?? [];
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

        $this->load_users();

        if ( ! isset( $this->users[$login] ) ) {
            $this->record_fail( $ip );
            return false;
        }

        $user = $this->users[$login];
        $hash = $user['password_hash'] ?? '';

        if ( $hash === '' || ! password_verify( $password , $hash ) ) {
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
        $_SESSION[ 'admin_login' ] = $login;
        $_SESSION[ 'admin_role' ]  = $user['role'] ?? 'admin';
        
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
    
    public function role ( ) : string {
        return (string) ( $_SESSION[ 'admin_role' ] ?? 'admin' );
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
        $fp = @fopen( $path , 'r' );
        if ( $fp === false ) return [ ];
        flock( $fp , LOCK_SH );
        $raw = stream_get_contents( $fp );
        flock( $fp , LOCK_UN );
        fclose( $fp );
        $data = @json_decode( (string) $raw , true );
        return is_array( $data ) ? $data : [ ];
    }

    private function save_fails ( array $data ) : void {
        $path = $this->fails_path( );
        $dir  = dirname( $path );
        if ( ! is_dir( $dir ) ) @mkdir( $dir , 0755 , true );

        $json = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
        if ( $json === false ) return;

        $tmp = $path . '.tmp.' . bin2hex( random_bytes( 4 ) );
        $fp = @fopen( $tmp , 'wb' );
        if ( $fp === false ) return;
        flock( $fp , LOCK_EX );
        fwrite( $fp , $json );
        fflush( $fp );
        flock( $fp , LOCK_UN );
        fclose( $fp );
        @rename( $tmp , $path );
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
        $path = $this->fails_path( );
        $dir  = dirname( $path );
        if ( ! is_dir( $dir ) ) @mkdir( $dir , 0755 , true );

        $fp = @fopen( $path , 'c+' );
        if ( $fp === false ) return;

        flock( $fp , LOCK_EX );

        $raw  = stream_get_contents( $fp );
        $data = @json_decode( (string) $raw , true );
        if ( ! is_array( $data ) ) $data = [ ];

        $cur = $data[ $ip ] ?? [ 'count' => 0 , 'last_ts' => 0 , 'banned_until' => 0 ];
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

        $out = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
        if ( $out !== false ) {
            ftruncate( $fp , 0 );
            rewind( $fp );
            fwrite( $fp , $out );
            fflush( $fp );
        }

        flock( $fp , LOCK_UN );
        fclose( $fp );

        error_log( 'admin_login_fail ip=' . $ip . ' login=' . ( $_POST[ 'login' ] ?? '?' ) );
    }

    private function reset_fails ( string $ip ) : void {
        $path = $this->fails_path( );
        if ( ! is_file( $path ) ) return;

        $fp = @fopen( $path , 'c+' );
        if ( $fp === false ) return;

        flock( $fp , LOCK_EX );

        $raw  = stream_get_contents( $fp );
        $data = @json_decode( (string) $raw , true );
        if ( is_array( $data ) && isset( $data[ $ip ] ) ) {
            unset( $data[ $ip ] );
            $out = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
            if ( $out !== false ) {
                ftruncate( $fp , 0 );
                rewind( $fp );
                fwrite( $fp , $out );
                fflush( $fp );
            }
        }

        flock( $fp , LOCK_UN );
        fclose( $fp );
    }

    private function delay ( int $usec ) : void {
        usleep( $usec );
    }
}