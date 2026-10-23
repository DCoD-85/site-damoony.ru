<?PHP

class DbSave {
    public static function run( array $params, ?Context $ctx = null ) : Result {
        $r = new Result();
        $path = PROJECT_ROOT . '/secure/db.config';

        $bases = [];
        if ( is_file($path) ) {
            $data = json_decode(file_get_contents($path), true);
            $bases = $data['bases'] ?? [];
        }

        // Пустой пароль = «не менять существующий»
        $new_pass   = (string) ( $params['pass'] ?? '' );
        $change_pass = ( $new_pass !== '' );

        $target = trim($params['localname'] ?? 'default_db');
        $found  = false;

        foreach ( $bases as &$base ) {
            if ( ( $base['localname'] ?? '' ) === $target ) {
                $base['dbname']   = trim( $params['name'] ?? '' );
                $base['dbhost']   = trim( $params['host'] ?? 'localhost' );
                $base['username'] = trim( $params['user'] ?? '' );
                if ( $change_pass ) {
                    $base['password'] = $new_pass;
                } elseif ( ! isset( $base['password'] ) ) {
                    $base['password'] = '';
                }
                $found = true;
                break;
            }
        }
        unset($base);

        if ( ! $found ) {
            $bases[] = [
                'localname' => $target,
                'dbname'    => trim( $params['name'] ?? '' ),
                'dbhost'    => trim( $params['host'] ?? 'localhost' ),
                'username'  => trim( $params['user'] ?? '' ),
                'password'  => $new_pass,
                'default'   => empty( $bases ),
            ];
        }

        Fs::mkdir( dirname( $path ) );
        Json::writeAtomic( $path, [ 'bases' => $bases ] );

        $r->step( "Настройки для {$target} сохранены" . ( $change_pass ? '' : ' (пароль не изменён)' ), true );
        return $r;
    }
}