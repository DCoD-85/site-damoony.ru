<?PHP

class DomainList {

    public static function plan ( array $params ) : array { return [ ]; }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        try {
            $cfg = new Config( Paths::config() );
        } catch ( Throwable $e ) {
            $r->error( "Не открыть site.config: " . $e->getMessage( ) );
            return $r;
        }

        $domains = (array) $cfg->get( 'domains_real' );

        if ( empty( $domains ) ) {
            $r->step( 'Доменов нет' , true , '' );
            return $r;
        }

        Cli::out( '' );
        Cli::info( 'Домены (' . count( $domains ) . '):' );
        Cli::out( '' );

        foreach ( $domains as $domain => $real ) {
            $ns       = $cfg->getKeys( [ 'domains_namespace' , $real ] ) ?? '(нет)';
            $avail    = ! empty( $cfg->getKeys( [ 'domains_available' , $real ] ) ) ? '✓' : '✗';
            $alliases = (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] );

            $al_list = [ ];
            foreach ( $alliases as $alias => $target ) {
                if ( $alias === '' ) continue;
                $al_list[ ] = $alias;
            }
            $al_str = empty( $al_list ) ? '(нет)' : implode( ', ' , $al_list );

            $path = Paths::domain( $domain );
            $fs   = is_dir( $path ) ? '✓' : '✗';

            $marker = ( $domain === $real ) ? ' ' : '→' . $real;

            Cli::out( sprintf(
                '  %-22s %-14s  ns=%-12s  avail=%s  fs=%s  alliases=[%s]' ,
                $domain , $marker , $ns , $avail , $fs , $al_str
            ) );
        }

        $r->step( 'Список выведен' , true , count( $domains ) . ' доменов' );
        return $r;
    }
}