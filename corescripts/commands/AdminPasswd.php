<?PHP

class AdminPasswd {

    public static function plan ( array $params ) : array {
        return [ 'login' => $params[ 'name' ] ?? 'admin' ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $login = (string) ( $params[ 'name' ] ?? 'admin' );
        if ( $login === '' ) {
            $r->error( 'Логин не может быть пустым' );
            return $r;
        }

        # пароль — из --password=... или спросить дважды
        $password = (string) ( $params[ 'password' ] ?? '' );
        if ( $password === '' ) {
            Cli::out( '' );
            $password = Cli::ask( 'Пароль' );
            if ( $password === '' ) {
                $r->error( 'Пароль не может быть пустым' );
                return $r;
            }
            $password2 = Cli::ask( 'Пароль (повтор)' );
            if ( $password !== $password2 ) {
                $r->error( 'Пароли не совпадают' );
                return $r;
            }
        }

        $hash = password_hash( $password , PASSWORD_DEFAULT );
        if ( $hash === false ) {
            $r->error( 'Не удалось создать хеш пароля' );
            return $r;
        }

        try {
            Config::mutate( PROJECT_ROOT . '/config/admin.config' , function ( Config $cfg ) use ( $login , $hash ) {
                $cfg->setKeys( [ 'login' ]         , $login );
                $cfg->setKeys( [ 'password_hash' ] , $hash );
            } );

            $r->step( 'Логин' , true , $login );
            $r->step( 'Хеш пароля сохранён в site.config.admin' , true , '' );
            $ctx?->step( 'Пароль установлен' , true , $login );
        } catch ( Throwable $e ) {
            $r->error( 'site.config: ' . $e->getMessage( ) );
        }

        return $r;
    }
}