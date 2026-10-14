<?PHP

class AlliasList {

    public static function plan ( array $params ) : array {
        return [ 'domain' => $params[ 'domain' ] ?? 'damoony.ru' ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );

        try {
            $cfg = new Config( Paths::config() );
        } catch ( Throwable $e ) {
            $r->error( "Не открыть site.config: " . $e->getMessage( ) );
            return $r;
        }

        $real = Paths::real( $domain );

        $alliases  = (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] );

        if ( empty( $alliases ) ) {
            $r->step( "Аллиасов нет для {$real}" , true , '' );
            return $r;
        }

        Cli::out( '' );
        Cli::info( "Аллиасы домена {$real}:" );
        Cli::out( '' );

        $available = (array) $cfg->getKeys( [ 'alliases_available' , $real ] , [ ] );

        foreach ( $alliases as $alias => $target ) {
            if ( $alias === '' ) continue;

            $avail = ! empty( $available[ $alias ] ) ? '✓' : '✗';

            $fs_path = Paths::allias( $domain , $alias );
            $fs      = is_dir( $fs_path ) ? '✓' : '✗';

            $f     = (array) ( $cfg->getKeys( [ 'fillers' , $real , $alias ] , [ ] ) );
            $f_on  = 0;
            foreach ( $f as $v ) if ( $v ) $f_on++;
            $f_str = $f_on . '/' . count( $f );

            Cli::out( sprintf(
                '  %-20s  avail=%s  fs=%s  fillers=%s' ,
                $alias , $avail , $fs , $f_str
            ) );
        }

        $r->step( 'Список выведен' , true , count( $alliases ) . ' аллиасов' );
        return $r;
    }
}