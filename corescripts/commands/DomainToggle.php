<?PHP

class DomainToggle {

    public static function plan ( array $params ) : array {
        return [
            'domain' => $params[ 'domain' ] ?? '' ,
            'value'  => $params[ 'value' ]  ?? '' ,
        ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $domain = (string) ( $params[ 'domain' ] ?? '' );
        $value  = (string) ( $params[ 'value' ]  ?? '' );

        if ( $err = Validate::domain( $domain ) ) {
            $r->step( 'Валидация домена' , false , $err );
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
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $flag ) {
                $cfg->setKeys( [ 'domains_available' , $real ] , $flag );
            } );

            $r->step( "domains_available[{$real}]" , true , $flag ? 'true' : 'false' );
            $ctx?->step( 'Сохранено' , true , $real );
        } catch ( Throwable $e ) {
            $r->error( 'Не сохранить: ' . $e->getMessage( ) );
        }

        return $r;
    }
}