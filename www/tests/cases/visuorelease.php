<?PHP

# ============================================================
#   Suite: visuorelease — финальный релиз дерева
# ============================================================

return [

    'title' => 'visuorelease',

    'setup' => function ( ) {
        # полный visuo-стек, от которого зависит visuorelease
        require_once VISUO_SRC . '/visuotag.php';
        require_once VISUO_SRC . '/visuolink.php';
        require_once VISUO_SRC . '/visuometa.php';
        require_once VISUO_SRC . '/visuolog.php';
        require_once VISUO_SRC . '/visuorender.php';
        require_once VISUO_SRC . '/visuorelease_settings.php';
        require_once VISUO_SRC . '/visuopage.php';
        require_once VISUO_SRC . '/visuostruct.php';
        require_once VISUO_SRC . '/visuorelease.php';
    },

    'tests' => [

        'construct without args' => function ( ) {
            $r = new visuorelease( );
            Assert::null( $r->main );
            Assert::instance( 'visuolog'              , $r->visuolog );
            Assert::instance( 'visuorelease_settings' , $r->settings );
            Assert::instance( 'visuometa'             , $r->visuometa );
            Assert::instance( 'visuotag'              , $r->visuotag );
        },

        'init tree' => function ( ) {
            $r = new visuorelease( );
            Assert::true( $r->visuotag->root( ) !== null );
            Assert::true( $r->visuotag->find( 'html' ) !== null );
            Assert::true( $r->visuotag->find( 'head' ) !== null );
            Assert::true( $r->visuotag->find( 'body' ) !== null );
        },

        'settings() accessor' => function ( ) {
            $r = new visuorelease( );
            Assert::instance( 'visuorelease_settings' , $r->settings( ) );
        },

        'meta() accessor' => function ( ) {
            $r = new visuorelease( );
            Assert::instance( 'visuometa' , $r->meta( ) );
        },

        'visuotag() accessor' => function ( ) {
            $r = new visuorelease( );
            $vt = $r->visuotag( );
            Assert::instance( 'visuotag' , $vt );
            Assert::same( 'body' , $vt->tagname );
        },

        'visuotag_set' => function ( ) {
            $r = new visuorelease( );
            $vt = new visuotag( );
            Assert::same( $r , $r->visuotag_set( $vt ) );
            Assert::same( $vt , $r->visuotag );
        },

        'visuotag_clear' => function ( ) {
            $r = new visuorelease( );
            $r->visuotag_clear( );
            Assert::count( 0 , $r->visuotag->root( )->elements );
        },

        'final no log no debug' => function ( ) {
            $m = new main( );
            $r = new visuorelease( $m );

            ob_start( );
            $r->final( );
            $out = ob_get_clean( );

            Assert::contains( '<! doctype' , $out );
            Assert::contains( '<html'      , $out );
            Assert::contains( '<head'      , $out );
            Assert::contains( '<body'      , $out );
        },

        'final без аргументов' => function ( ) {
            $m = new main( );
            $r = new visuorelease( $m );

            ob_start( );
            $r->final( );
            $out = ob_get_clean( );

            Assert::true( strlen( $out ) > 0 );
        },

        'final_ajax data-ajax' => function ( ) {
            $m = new main( );
            $r = new visuorelease( $m );

            ob_start( );
            $r->final_ajax( );
            $out = ob_get_clean( );

            $html = $r->visuotag->find( 'html' );
            Assert::true( $html->data( )->has( 'ajax' ) );
            Assert::true( $html->data( )->get( 'ajax' ) );
            Assert::false( $html->data( )->get( 'ajax-init' ) );
            Assert::contains( 'data-ajax' , $out );
        },

        'meta + title' => function ( ) {
            $m = new main( );
            $r = new visuorelease( $m );
            $r->meta( )->title( )->data( 'Test Page' );

            ob_start( );
            $r->final( );
            $out = ob_get_clean( );

            Assert::contains( '<title>Test Page</title>' , $out );
        },

        'full page structure' => function ( ) {
            $m = new main( );
            $r = new visuorelease( $m );

            ob_start( );
            $r->final( );
            $out = ob_get_clean( );

            Assert::contains( '<! doctype' , $out );
            Assert::contains( '<html'      , $out );
            Assert::contains( '<head'      , $out );
            Assert::contains( '<body'      , $out );
        },

        'release_min(null) — no crash' => function ( ) {
            $r = new visuorelease( new main( ) );
            $r->release_min( null );
            Assert::true( true );
        },

        'release_struct(null) — no crash' => function ( ) {
            $r = new visuorelease( new main( ) );
            $r->release_struct( null );
            Assert::true( true );
        },

        # --- settings ---

        'settings from cookie' => function ( ) {
            $_COOKIE[ 'language' ] = 'en-en';
            $s = new visuorelease_settings( );
            Assert::same( 'en-en' , $s->get( 'language' ) );
            unset( $_COOKIE[ 'language' ] );
        },

        'settings default language' => function ( ) {
            unset( $_COOKIE[ 'language' ] );
            $s = new visuorelease_settings( );
            Assert::same( 'ru' , $s->get( 'language' ) );
        },

        'settings get unknown' => function ( ) {
            $s = new visuorelease_settings( );
            Assert::null( $s->get( 'nope' ) );
        },

        'settings get falsy (regression)' => function ( ) {
            $s = new visuorelease_settings( );
            $s->set( 'language' , '0' );
            Assert::same( '0' , $s->get( 'language' ) );
        },

        'settings set chainable' => function ( ) {
            $s = new visuorelease_settings( );
            Assert::same( $s , $s->set( 'language' , 'de-de' ) );
            Assert::same( 'de-de' , $s->get( 'language' ) );
        },

        'custom settings injection' => function ( ) {
            $m      = new main( );
            $custom = new visuorelease_settings( );
            $custom->set( 'language' , 'fr-fr' );
            $r = new visuorelease( $m , $custom );
            Assert::same( $custom , $r->settings( ) );
            Assert::same( 'fr-fr' , $r->settings( )->get( 'language' ) );
        },

    ],
];