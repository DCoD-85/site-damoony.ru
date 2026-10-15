<?PHP

# ============================================================
#   View — helpers для сборки тест-страницы через visuo.
#   Работает с Suite и CaseResult.
# ============================================================

class View {

    # --- сводка по всем suites ---
    public static function summary ( array $by_suite ) : visuotag {
        $total_p = 0;
        $total_f = 0;
        foreach ( $by_suite as $cases ) {
            $s = Runner::stats( $cases );
            $total_p += $s[ 'passed' ];
            $total_f += $s[ 'failed' ];
        }
        $total = $total_p + $total_f;
        $ok    = $total_f === 0;

        $vt = new visuotag( );
        $box = $vt->tag( 'div' , class : 'test-summary ' . ( $ok ? 'ok' : 'err' ) );

        $box->tag( 'div' , class : 'summary-line' )
            ->content( $ok ? '✓ Все тесты прошли' : '✗ Есть провалы' );

        $box->tag( 'div' , class : 'summary-line' )
            ->content( "Total: {$total_p} / {$total} passed, {$total_f} failed" );

        return $vt->root( );
    }

    # --- содержимое вкладки: список кейсов ---
    public static function cases ( array $results ) : visuotag {
        $vt = new visuotag( );
        $list = $vt->tag( 'div' , class : 'test-cases' );

        foreach ( $results as $cr ) {
            $cls = $cr->ok ? 'case ok' : 'case err';
            $row = $list->tag( 'div' , class : $cls );

            $row->tag( 'div' , class : 'case-title' )
                ->content( ( $cr->ok ? '✓ ' : '✗ ' ) . $cr->name );

            $row->tag( 'div' , class : 'case-time' )
                ->content( number_format( $cr->time_ms , 2 ) . ' ms' );

            if ( ! $cr->ok ) {
                $row->tag( 'pre' , class : 'case-msg' )->content( $cr->message );
            }
        }

        return $vt->root( );
    }

    # --- заголовок вкладки: name (passed/total) ---
    public static function tab_title ( string $name , array $results ) : string {
        $s = Runner::stats( $results );
        return $name . '  (' . $s[ 'passed' ] . '/' . $s[ 'total' ] . ')';
    }
}