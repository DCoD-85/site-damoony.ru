<?PHP

class AlliasToggle {

    public static function plan ( array $params ) : array {
        return [
            'domain' => $params[ 'domain' ] ?? '' ,
            'name'   => $params[ 'name' ]   ?? '' ,
            'value'  => $params[ 'value' ]  ?? '' ,
        ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $domain = (string) ( $params[ 'domain' ] ?? '' );
        $name   = (string) ( $params[ 'name' ]   ?? '' );
        $value  = (string) ( $params[ 'value' ]  ?? '' );

        if ( $err = Validate::domain( $domain ) ) {
            $r->step( 'Валидация домена' , false , $err );
            return $r;
        }
        if ( $err = Validate::allias_name( $name ) ) {
            $r->step( 'Валидация аллиаса' , false , $err );
            return $r;
        }

        $cfg  = new Config( Paths::config() );
        $real = Paths::real( $domain );

        if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) {
            $r->error( "Домен не найден: {$domain}" );
            return $r;
        }

        $flag = ( $value === '0' ) ? false : true;

        try {
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $name , $flag ) {
                $cfg->setKeys( [ 'alliases_available' , $real , $name ] , $flag );
            } );

            $r->step( "alliases_available[{$real}][{$name}]" , true , $flag ? 'true' : 'false' );
            $ctx?->step( 'Сохранено' , true , $name );
        } catch ( Throwable $e ) {
            $r->error( 'Не сохранить: ' . $e->getMessage( ) );
        }

        return $r;
    }
}