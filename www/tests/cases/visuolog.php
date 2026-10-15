<?PHP

# ============================================================
#   Suite: visuolog
# ============================================================

return [

    'title' => 'visuolog',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuotag.php';
        require_once VISUO_SRC . '/visuolog.php';
    },

    'tests' => [

        'construct' => function ( ) {
            $log = new visuolog( );
            Assert::same( 0 , $log->rows_count );
            Assert::count( 0 , $log->rows );
        },

        'add increments rows_count' => function ( ) {
            $log = new visuolog( );
            $log->add( 'test 1' , __METHOD__ , __FILE__ , __LINE__ );
            Assert::same( 1 , $log->rows_count );
            Assert::instance( 'visuolog_row' , $log->rows[ 0 ] );
        },

        'add twice' => function ( ) {
            $log = new visuolog( );
            $log->add( 'a' , 'm' , '/f' , '1' );
            $log->add( 'b' , 'm' , '/f' , '2' );
            Assert::same( 2 , $log->rows_count );
        },

        'as_tag empty' => function ( ) {
            $log = new visuolog( );
            $vt = $log->as_tag( );
            Assert::instance( 'visuotag' , $vt );
        },

        'release empty contains message' => function ( ) {
            $log = new visuolog( );
            Assert::contains( 'Лог пустой' , $log->release( ) );
        },

        'release with rows' => function ( ) {
            $log = new visuolog( );
            $log->add( 'msg-A' , 'm' , '/f' , '1' );
            $log->add( 'msg-B' , 'm' , '/f' , '2' );
            $out = $log->release( );
            Assert::contains( 'msg-A' , $out );
            Assert::contains( 'msg-B' , $out );
        },

        'row hrtime and datetime' => function ( ) {
            $log = new visuolog( );
            $log->add( 'x' , 'm' , '/f' , '1' );
            $row = $log->rows[ 0 ];
            Assert::true( $row->hrtime > 0 );
            Assert::instance( 'DateTime' , $row->datetime );
        },

        'row as_tag(0) fallback' => function ( ) {
            $log = new visuolog( );
            $log->add( 'x' , 'm' , '/f' , '1' );
            $GLOBALS[ 'start_hr' ] = (int) $log->rows[ 0 ]->hrtime;
            $t0 = $log->rows[ 0 ]->as_tag( 0 );
            Assert::instance( 'visuotag' , $t0 );
        },

        'row as_tag(1) previous hrtime' => function ( ) {
            $log = new visuolog( );
            $log->add( 'a' , 'm' , '/f' , '1' );
            $log->add( 'b' , 'm' , '/f' , '2' );
            $t1 = $log->rows[ 1 ]->as_tag( 1 );
            Assert::instance( 'visuotag' , $t1 );
        },

        'add without DOCUMENT_ROOT' => function ( ) {
            $saved = $_SERVER[ 'DOCUMENT_ROOT' ] ?? null;
            unset( $_SERVER[ 'DOCUMENT_ROOT' ] );
            $log = new visuolog( );
            $log->add( 'no-dr' , 'm' , '/some/path' , '1' );
            if ( $saved !== null ) $_SERVER[ 'DOCUMENT_ROOT' ] = $saved;
            Assert::same( 1 , $log->rows_count );
        },

        'final online=false prints release' => function ( ) {
            $log = new visuolog( false );
            $log->add( 'final-msg' , 'm' , '/f' , '1' );
            ob_start( );
            $log->final( );
            $out = ob_get_clean( );
            Assert::contains( 'final-msg' , $out );
        },

        'visuolog_row::release without add' => function ( ) {
            $log = new visuolog( );
            $row = new visuolog_row( $log );
            Assert::true( is_string( $row->release( ) ) );
        },

    ],
];