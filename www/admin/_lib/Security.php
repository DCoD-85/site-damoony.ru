<?PHP

class Security {

    private static ?array $cfg = null;
    private static ?array $admins = null;

    private static function cfg () : array {
        if ( self::$cfg !== null ) return self::$cfg;
        $path = PROJECT_ROOT . '/config/admin.security.config';
        if ( ! is_file( $path ) ) return self::$cfg = [ 'mode' => 'whitelist' , 'require_fingerprint' => true ];
        $data = json_decode( file_get_contents( $path ) , true );
        return self::$cfg = is_array( $data ) ? $data : [ 'mode' => 'whitelist' ];
    }

    private static function admins () : array {
        if ( self::$admins !== null ) return self::$admins;
        $path = PROJECT_ROOT . '/secure/admins.config';
        if ( ! is_file( $path ) ) return self::$admins = [ 'users' => [] , 'invites' => [] ];
        $data = json_decode( file_get_contents( $path ) , true );
        return self::$admins = is_array( $data ) ? $data : [ 'users' => [] , 'invites' => [] ];
    }

    public static function get_data () : array {
        return self::admins();
    }

    public static function save_admins ( array $data ) : void {
        $path = PROJECT_ROOT . '/secure/admins.config';
        self::write_atomic( $path , json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        self::$admins = null;
    }

    private static function write_atomic ( string $path , string $content ) : void {
        $tmp = $path . '.tmp.' . bin2hex( random_bytes( 4 ) );
        $fp = @fopen( $tmp , 'cb' );
        if ( $fp === false ) throw new RuntimeException( "Cannot open: $tmp" );
        flock( $fp , LOCK_EX );
        fwrite( $fp , $content );
        fflush( $fp );
        flock( $fp , LOCK_UN );
        fclose( $fp );
        @rename( $tmp , $path );
    }

    public static function is_whitelisted ( string $ip ) : bool {
        $cfg = self::cfg();
        $whitelist = $cfg['ip_whitelist'] ?? [ '127.0.0.1' , '::1' ];
        if ( in_array( $ip , $whitelist , true ) ) return true;
        if ( in_array( $ip , [ '127.0.0.1' , '::1' ] , true ) ) return true;
        return false;
    }

    # --- Fingerprint ---
    public static function find_device ( array $current_hashes ) : ?array {
        if ( empty( $current_hashes ) ) return null;

        $admins = self::admins();
        foreach ( $admins['users'] as $login => $user ) {
            foreach ( $user['devices'] ?? [] as $device ) {
                $match = false;
                foreach ( [ 'canvas' , 'webgl' , 'system' ] as $type ) {
                    if ( ! empty( $current_hashes[ $type ] ) && ! empty( $device[ $type ] ) && hash_equals( $device[ $type ] , $current_hashes[ $type ] ) ) {
                        $match = true;
                        break;
                    }
                }
                if ( $match ) {
                    return [
                        'login'  => $login ,
                        'role'   => $user['role'] ?? 'admin' ,
                        'device' => $device['id'] ?? '' ,
                        'label'  => $device['label'] ?? '' ,
                    ];
                }
            }
        }
        return null;
    }

    public static function add_device ( string $login , array $hashes , string $ua , string $ip , string $label = '' ) : string {
        $admins = self::admins();
        if ( ! isset( $admins['users'][$login] ) ) throw new RuntimeException( "User not found: $login" );

        $device_id = 'dev_' . bin2hex( random_bytes( 4 ) );
        $admins['users'][$login]['devices'][] = [
            'id'          => $device_id ,
            'canvas'      => $hashes['canvas'] ?? '' ,
            'webgl'       => $hashes['webgl'] ?? '' ,
            'system'      => $hashes['system'] ?? '' ,
            'ua'          => substr( $ua , 0 , 255 ) ,
            'last_ip'     => $ip ,
            'last_seen'   => time() ,
            'label'       => $label !== '' ? $label : self::guess_label( $ua ) ,
        ];

        self::save_admins( $admins );
        return $device_id;
    }

    public static function remove_device ( string $login , string $device_id ) : bool {
        $admins = self::admins();
        if ( ! isset( $admins['users'][$login]['devices'] ) ) return false;

        $admins['users'][$login]['devices'] = array_values(
            array_filter( $admins['users'][$login]['devices'] , fn( $d ) => $d['id'] !== $device_id )
        );
        self::save_admins( $admins );
        return true;
    }

    public static function reset_user_devices ( string $login ) : void {
        $admins = self::admins();
        if ( isset( $admins['users'][$login] ) ) {
            $admins['users'][$login]['devices'] = [];
            self::save_admins( $admins );
        }
    }

    private static function guess_label ( string $ua ) : string {
        if ( stripos( $ua , 'iphone' ) !== false )   return 'iPhone';
        if ( stripos( $ua , 'ipad' ) !== false )     return 'iPad';
        if ( stripos( $ua , 'android' ) !== false )  return 'Android';
        if ( stripos( $ua , 'mac os' ) !== false )   return 'Mac';
        if ( stripos( $ua , 'windows' ) !== false )  return 'Windows';
        if ( stripos( $ua , 'linux' ) !== false )    return 'Linux';
        return 'Unknown device';
    }

    # --- Invites ---
    public static function create_invite ( string $role = 'admin' , ?int $ttl = null ) : string {
        $cfg = self::cfg();
        $ttl = $ttl ?? ( $cfg['invites']['ttl'] ?? 86400 );

        $token = bin2hex( random_bytes( 24 ) );
        $admins = self::admins();
        $admins['invites'][$token] = [
            'role'       => $role ,
            'created_at' => time() ,
            'expires_at' => time() + $ttl ,
            'used'       => false ,
        ];
        self::save_admins( $admins );
        return $token;
    }

    public static function validate_invite ( string $token ) : ?array {
        $admins = self::admins();
        $inv = $admins['invites'][$token] ?? null;
        if ( $inv === null ) return null;
        if ( ! empty( $inv['used'] ) ) return null;
        if ( ( $inv['expires_at'] ?? 0 ) < time() ) return null;
        return $inv;
    }

    public static function consume_invite ( string $token ) : void {
        $admins = self::admins();
        if ( isset( $admins['invites'][$token] ) ) {
            $admins['invites'][$token]['used'] = true;
            $admins['invites'][$token]['used_at'] = time();
            self::save_admins( $admins );
        }
    }

    public static function revoke_invite ( string $token ) : void {
        $admins = self::admins();
        unset( $admins['invites'][$token] );
        self::save_admins( $admins );
    }

    # --- Logs ---
    public static function log ( string $event , array $data = [] ) : void {
        $path = PROJECT_ROOT . '/secure/logs/security.log';
        @mkdir( dirname( $path ) , 0755 , true );
        $entry = [
            'ts'    => date( 'Y-m-d H:i:s' ) ,
            'ip'    => $_SERVER['REMOTE_ADDR'] ?? '?' ,
            'ua'    => substr( (string)( $_SERVER['HTTP_USER_AGENT'] ?? '' ) , 0 , 200 ) ,
            'event' => $event ,
            'data'  => $data ,
        ];
        $line = json_encode( $entry , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        if ( $line === false ) return;
        @file_put_contents( $path , $line . "\n" , FILE_APPEND | LOCK_EX );
    }
}