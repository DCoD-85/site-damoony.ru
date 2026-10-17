<?PHP

# ============================================================
#   AuditLog — журнал действий админки.
#   Пишет в secure/logs/admin.log (JSON-строки).
# ============================================================

class AuditLog {

    private const LOG_PATH = '/secure/logs/admin.log';
    private const MAX_SIZE = 10 * 1024 * 1024;   # 10 MB

    # --- запись ---
    public static function write ( string $action , array $data = [] , bool $ok = true ) : void {
        $path = PROJECT_ROOT . self::LOG_PATH;
        $dir  = dirname( $path );

        if ( ! is_dir( $dir ) ) {
            @mkdir( $dir , 0755 , true );
        }

        # ротация по размеру
        if ( is_file( $path ) && filesize( $path ) > self::MAX_SIZE ) {
            @rename( $path , $path . '.' . date( 'YmdHis' ) );
        }

        $entry = [
            'ts'   => date( 'Y-m-d H:i:s' ) ,
            'ip'   => $_SERVER[ 'REMOTE_ADDR' ] ?? '?' ,
            'ua'   => substr( (string) ( $_SERVER[ 'HTTP_USER_AGENT' ] ?? '' ) , 0 , 200 ) ,
            'user' => $_SESSION[ 'admin_login' ] ?? '?' ,
            'act'  => $action ,
            'ok'   => $ok ,
            'data' => $data ,
        ];

        $line = json_encode( $entry , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        if ( $line === false ) return;

        @file_put_contents( $path , $line . "\n" , FILE_APPEND | LOCK_EX );
    }

    # --- сокращённые хелперы ---
    public static function login_ok ( )      : void { self::write( 'login:ok' ); }
    public static function login_fail ( )    : void { self::write( 'login:fail' , [] , false ); }
    public static function logout ( )        : void { self::write( 'logout' ); }

    public static function action_ok ( string $action , array $data = [] ) : void {
        self::write( $action , $data , true );
    }

    public static function action_fail ( string $action , array $data = [] ) : void {
        self::write( $action , $data , false );
    }
}