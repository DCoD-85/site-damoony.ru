<?PHP

# ============================================================
#   Runner — discovery, setup, run, stats.
# ============================================================

class Runner {

    # --- discovery: cases/*.php → [ 'name' => Suite, ... ] ---
    public static function discover ( string $dir ) : array {
        $suites = [];
        foreach ( glob( $dir . '/*.php' ) as $path ) {
            $spec = require $path;
            if ( ! is_array( $spec ) || ! isset( $spec[ 'tests' ] ) ) continue;

            $name  = $spec[ 'title' ] ?? basename( $path , '.php' );
            $setup = isset( $spec[ 'setup' ] ) && $spec[ 'setup' ] instanceof Closure
                   ? $spec[ 'setup' ]
                   : null;

            $suites[ $name ] = new Suite( $name , $setup , $spec[ 'tests' ] );
        }
        ksort( $suites );
        return $suites;
    }

    # --- run: [ CaseResult , ... ] ---
    public static function run ( Suite $suite ) : array {
        $results = [];

        # setup один раз перед всеми кейсами
        if ( $suite->setup !== null ) {
            try {
                ( $suite->setup )( );
            } catch ( \Throwable $e ) {
                # setup упал — все кейсы failed
                foreach ( array_keys( $suite->tests ) as $name ) {
                    $cr = new CaseResult( $name );
                    $cr->fail( 'SETUP FAILED: ' . $e->getMessage( ) , 0.0 );
                    $results[ ] = $cr;
                }
                return $results;
            }
        }

        foreach ( $suite->tests as $name => $fn ) {
            $cr = new CaseResult( $name );
            $t0 = microtime( true );

            try {
                $fn( );
                $cr->pass( ( microtime( true ) - $t0 ) * 1000 );
            } catch ( AssertException $e ) {
                $cr->fail( $e->getMessage( ) , ( microtime( true ) - $t0 ) * 1000 );
            } catch ( \Throwable $e ) {
                $msg  = get_class( $e ) . ': ' . $e->getMessage( );
                $msg .= "\n  at " . $e->getFile( ) . ':' . $e->getLine( );
                $cr->fail( $msg , ( microtime( true ) - $t0 ) * 1000 );
            }

            $results[ ] = $cr;
        }

        return $results;
    }

    # --- stats ---
    public static function stats ( array $results ) : array {
        $passed = 0;
        $failed = 0;
        foreach ( $results as $r ) {
            $r->ok ? $passed++ : $failed++;
        }
        return [ 'passed' => $passed , 'failed' => $failed , 'total' => $passed + $failed ];
    }
}