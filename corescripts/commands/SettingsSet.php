<?PHP

class SettingsSet {

    private const WHITELIST = [
        'title'        => 'string' ,
        'skin'         => 'skin' ,
        'logic'        => 'logic' ,
        'connect_type' => 'connect_type' ,
    ];

    public static function plan ( array $params ) : array {
        return [ 'params' => $params ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        # Собираем пары key=value из --key=value и позиционных
        $pairs = [ ];
        foreach ( $params as $k => $v ) {
            if ( $k === 'name' && strpos( (string) $v , '=' ) !== false ) {
                [ $kk , $vv ] = explode( '=' , (string) $v , 2 );
                $pairs[ trim( $kk ) ] = trim( $vv );
            } elseif ( $k !== 'name' ) {
                $pairs[ $k ] = $v;
            }
        }

        if ( empty( $pairs ) ) {
            $r->error( 'Не указаны параметры. Пример: settings:set title=My Site' );
            return $r;
        }

        # Валидация
        foreach ( $pairs as $key => $value ) {
            if ( ! isset( self::WHITELIST[ $key ] ) ) {
                $r->error( "Ключ '{$key}' не разрешён. Допустимые: " . implode( ', ' , array_keys( self::WHITELIST ) ) );
                return $r;
            }
            $err = self::validate( $key , (string) $value );
            if ( $err !== null ) {
                $r->step( 'Валидация' , false , "{$key}: {$err}" );
                return $r;
            }
            $r->step( 'Валидация' , true , "{$key} = {$value}" );
            $ctx?->step( 'Валидация' , true , "{$key} = {$value}" );
        }

        # Запись в config (под lock)
        try {
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $pairs ) {
                foreach ( $pairs as $key => $value ) {
                    $cfg->setKeys( [ $key ] , (string) $value );
                }
            } );

            foreach ( $pairs as $key => $value ) {
                $r->step( "site.config: {$key}" , true , (string) $value );
                $ctx?->step( "site.config: {$key}" , true , (string) $value );
            }
            $r->step( 'site.config сохранён' , true , '' );
        } catch ( Throwable $e ) {
            $r->error( 'Не сохранить: ' . $e->getMessage( ) );
        }

        return $r;
    }

    private static function validate ( string $key , string $value ) : ?string {
        switch ( self::WHITELIST[ $key ] ) {

            case 'string' :
                if ( $value === '' ) return 'не может быть пустым';
                if ( strlen( $value ) > 100 ) return 'максимум 100 символов';
                return null;

            case 'connect_type' :
                if ( ! in_array( $value , [ 'http://' , 'https://' ] , true ) ) {
                    return 'допустимо: http:// или https://';
                }
                return null;

            case 'skin' :
                $cfg = new Config( Paths::config() );
                $domains = array_keys((array)$cfg->get('domains_real', []));
                $def_domain = $domains[0] ?? 'damoony.ru';
                $p = Paths::skins( $def_domain ) . '/' . $value;
                if ( ! is_dir( $p ) ) {
                    return "тема '{$value}' не найдена ({$p})";
                }
                return null;

            case 'logic' :
                $p = Paths::project() . '/app/' . $value . '/domain';
                if ( ! is_dir( $p ) ) {
                    return "движок '{$value}' не найден (нет app/{$value}/domain/)";
                }
                return null;
        }
        return null;
    }
}