<?PHP

class FillerToggle {

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $domain = (string) ( $params[ 'domain' ] ?? '' );
        $allias = (string) ( $params[ 'allias' ] ?? '' );
        $block  = (string) ( $params[ 'block' ]  ?? '' );
        $value  = (string) ( $params[ 'value' ]  ?? '' );

        if ( $err = Validate::domain( $domain ) )     { $r->error( $err ); return $r; }
        if ( $err = Validate::allias_name( $allias ) ){ $r->error( $err ); return $r; }
        if ( ! Blocks::isBlock( $block ) )            { $r->error( "Неверный блок: {$block}" ); return $r; }

        $real = Paths::real( $domain );
        $flag = ( $value === '0' ) ? false : true;

        try {
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $allias , $block , $flag ) {
                $cfg->setKeys( [ 'fillers' , $real , $allias , $block ] , $flag );
            } );
            $r->step( "fillers[{$real}][{$allias}][{$block}]" , true , $flag ? 'true' : 'false' );
        } catch ( Throwable $e ) {
            $r->error( 'Не сохранить: ' . $e->getMessage( ) );
        }

        return $r;
    }
}
