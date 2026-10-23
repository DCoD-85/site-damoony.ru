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

    public static function get_data () : array { return self::admins(); }

    public static function save_admins ( array $data ) : void {
        $path = PROJECT_ROOT . '/secure/admins.config';
        $json = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        if ( $json === false ) return;
        self::write_atomic( $path , $json );
        self::$admins = null;
    }

    public static function get_config () : array { return self::cfg( ); }

    public static function save_config ( array $data ) : void {
        $path = PROJECT_ROOT . '/config/admin.security.config';
        $json = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        if ( $json === false ) return;
        self::write_atomic( $path , $json );
        self::$cfg = null;
    }

    private static function write_atomic ( string $path , string $content ) : void {
        # Если загружен Fs (admin bootstrap) — используем его (единый путь записи).
        if ( class_exists( 'Fs' , false ) && method_exists( 'Fs' , 'writeAtomic' ) ) {
            \Fs::writeAtomic( $path , $content );
            return;
        }
        # Fallback для контекста www/index.php, где Fs ещё не подключён.
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

    /**
     * IP разрешён по конфигу?
     * mode=whitelist — только ip_whitelist (плюс localhost всегда).
     * mode=blacklist — пускаем всех (fingerprint-контроль отдельным флагом).
     */
    public static function is_whitelisted ( string $ip ) : bool {
        $cfg  = self::cfg();
        $mode = (string) ( $cfg['mode'] ?? 'whitelist' );
        if ( $mode === 'blacklist' ) return true;
        $whitelist = $cfg['ip_whitelist'] ?? [ '127.0.0.1' , '::1' ];
        if ( in_array( $ip , $whitelist , true ) ) return true;
        if ( in_array( $ip , [ '127.0.0.1' , '::1' ] , true ) ) return true;
        return false;
    }

    public static function requires_fingerprint () : bool {
        return ! empty( self::cfg()['require_fingerprint'] );
    }

    public static function find_device ( array $current_hashes ) : ?array {
        if ( empty( $current_hashes[ 'canvas' ] ) ) return null;
        $admins = self::admins();
        foreach ( $admins['users'] as $login => $user ) {
            foreach ( $user['devices'] ?? [] as $device ) {
                $matches = 0; $canvas_ok = false;
                foreach ( [ 'canvas' , 'webgl' , 'system' ] as $type ) {
                    if ( empty( $current_hashes[ $type ] ) || empty( $device[ $type ] ) ) continue;
                    if ( ! hash_equals( (string) $device[ $type ] , (string) $current_hashes[ $type ] ) ) continue;
                    $matches++;
                    if ( $type === 'canvas' ) $canvas_ok = true;
                }
                if ( $matches >= 2 && $canvas_ok ) {
                    return [ 'login' => $login , 'role' => $user['role'] ?? 'admin' , 'device' => $device['id'] ?? '' , 'label' => $device['label'] ?? '' ];
                }
            }
        }
        return null;
    }

    public static function touch_device ( string $login , string $device_id , string $ip = '' ) : void {
        $admins = self::admins();
        if ( ! isset( $admins['users'][ $login ]['devices'] ) ) return;
        $changed = false;
        foreach ( $admins['users'][ $login ]['devices'] as &$d ) {
            if ( ( $d['id'] ?? '' ) !== $device_id ) continue;
            # уже обновляли <5 минут назад — ничего не пишем
            if ( ( $d['last_seen'] ?? 0 ) > time() - 300 ) { unset( $d ); return; }
            $d['last_seen'] = time();
            if ( $ip !== '' ) $d['last_ip'] = $ip;
            $changed = true; break;
        }
        unset( $d );
        if ( $changed ) self::save_admins( $admins );
    }

    public static function add_device ( string $login , array $hashes , string $ua , string $ip , string $label = '' ) : string {
        $admins = self::admins();
        if ( ! isset( $admins['users'][$login] ) ) throw new RuntimeException( "User not found: $login" );
        $device_id = 'dev_' . bin2hex( random_bytes( 4 ) );
        $admins['users'][$login]['devices'][] = [
            'id' => $device_id , 'canvas' => $hashes['canvas'] ?? '' , 'webgl' => $hashes['webgl'] ?? '' ,
            'system' => $hashes['system'] ?? '' , 'ua' => substr( $ua , 0 , 255 ) , 'last_ip' => $ip ,
            'last_seen' => time() , 'label' => $label !== '' ? $label : self::guess_label( $ua ) ,
        ];
        self::save_admins( $admins );
        return $device_id;
    }

    /**
     * Сброс всех устройств пользователя.
     * Если передан $guard_login (логин текущей сессии) и он совпадает с $login —
     * запрещаем: это выкинет текущую сессию из whitelist.
     */
    public static function reset_user_devices ( string $login , ?string $guard_login = null ) : bool {
        if ( $guard_login !== null && $guard_login !== '' && $login === $guard_login ) {
            self::log( 'device:reset_self_blocked' , [ 'login' => $login ] );
            return false;
        }
        $admins = self::admins();
        if ( ! isset( $admins['users'][$login] ) ) return false;
        $admins['users'][$login]['devices'] = [];
        self::save_admins( $admins );
        return true;
    }

    private static function guess_label ( string $ua ) : string {
        if ( stripos( $ua , 'iphone' ) !== false ) return 'iPhone';
        if ( stripos( $ua , 'ipad' ) !== false ) return 'iPad';
        if ( stripos( $ua , 'android' ) !== false ) return 'Android';
        if ( stripos( $ua , 'mac os' ) !== false ) return 'Mac';
        if ( stripos( $ua , 'windows' ) !== false ) return 'Windows';
        if ( stripos( $ua , 'linux' ) !== false ) return 'Linux';
        return 'Unknown device';
    }

    /**
     * @throws RuntimeException если bind_only без target_user или такого юзера нет.
     */
    public static function create_invite ( string $role = 'admin' , ?int $ttl = null , ?string $target_user = null ) : string {
        $cfg = self::cfg();
        $ttl = $ttl ?? ( $cfg['invites']['ttl'] ?? 86400 );
        $admins = self::admins();

        if ( $role === 'bind_only' ) {
            if ( $target_user === null || $target_user === '' ) {
                throw new RuntimeException( 'bind_only invite requires target_user' );
            }
            if ( ! isset( $admins['users'][$target_user] ) ) {
                throw new RuntimeException( "target_user not found: $target_user" );
            }
        }

        $token = bin2hex( random_bytes( 24 ) );
        $admins['invites'][$token] = [
            'role' => $role , 'target_user' => $target_user , 'created_at' => time() ,
            'expires_at' => time() + $ttl , 'used' => false ,
        ];
        self::save_admins( $admins );
        return $token;
    }

    public static function validate_invite ( string $token ) : ?array {
        $admins = self::admins();
        $inv = $admins['invites'][$token] ?? null;
        if ( $inv === null || ! empty( $inv['used'] ) || ( $inv['expires_at'] ?? 0 ) < time() ) return null;
        if ( ( $inv['role'] ?? '' ) === 'bind_only' ) {
            $target = (string) ( $inv['target_user'] ?? '' );
            if ( $target === '' || ! isset( $admins['users'][$target] ) ) return null;
        }
        return $inv;
    }

    public static function revoke_invite ( string $token ) : void {
        $admins = self::admins();
        unset( $admins['invites'][$token] );
        self::save_admins( $admins );
    }

    public static function create_user_from_invite ( string $token , string $login , string $pass , string $role , array $fp , string $ua , string $ip ) : bool {
        $admins = self::admins();
        if ( isset( $admins['users'][$login] ) ) return false;

        $admins['users'][$login] = [ 'password_hash' => password_hash( $pass , PASSWORD_DEFAULT ) , 'role' => $role , 'devices' => [] ];
        $admins['users'][$login]['devices'][] = [
            'id' => 'dev_' . bin2hex( random_bytes( 4 ) ) , 'canvas' => $fp['canvas'] ?? '' , 'webgl' => $fp['webgl'] ?? '' ,
            'system' => $fp['system'] ?? '' , 'ua' => substr( $ua , 0 , 255 ) , 'last_ip' => $ip ,
            'last_seen' => time() , 'label' => self::guess_label( $ua ) ,
        ];
        $admins['invites'][$token]['used'] = true;
        $admins['invites'][$token]['used_at'] = time();
        self::save_admins( $admins );
        return true;
    }

    public static function bind_device_from_invite ( string $token , string $pass , array $fp , string $ua , string $ip ) : bool {
        $admins = self::admins();
        $inv = $admins['invites'][$token] ?? null;
        if ( ! $inv || $inv['role'] !== 'bind_only' ) return false;
        $login = $inv['target_user'] ?? '';
        if ( ! isset( $admins['users'][$login] ) ) return false;
        if ( ! password_verify( $pass , $admins['users'][$login]['password_hash'] ?? '' ) ) return false;

        $admins['users'][$login]['devices'][] = [
            'id' => 'dev_' . bin2hex( random_bytes( 4 ) ) , 'canvas' => $fp['canvas'] ?? '' , 'webgl' => $fp['webgl'] ?? '' ,
            'system' => $fp['system'] ?? '' , 'ua' => substr( $ua , 0 , 255 ) , 'last_ip' => $ip ,
            'last_seen' => time() , 'label' => self::guess_label( $ua ) ,
        ];
        $admins['invites'][$token]['used'] = true;
        $admins['invites'][$token]['used_at'] = time();
        self::save_admins( $admins );
        return true;
    }

    /**
     * Append-only лог безопасности. Намеренно НЕ атомарный: файл растёт,
     * file_put_contents + FILE_APPEND | LOCK_EX — достаточно и дёшево.
     */
    public static function log ( string $event , array $data = [] ) : void {
        $path = PROJECT_ROOT . '/secure/logs/security.log';
        @mkdir( dirname( $path ) , 0755 , true );
        $entry = [
            'ts' => date( 'Y-m-d H:i:s' ) , 'ip' => $_SERVER['REMOTE_ADDR'] ?? '?' ,
            'ua' => substr( (string)( $_SERVER['HTTP_USER_AGENT'] ?? '' ) , 0 , 200 ) , 'event' => $event , 'data' => $data ,
        ];
        $line = json_encode( $entry , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        if ( $line !== false ) @file_put_contents( $path , $line . "\n" , FILE_APPEND | LOCK_EX );
    }
}