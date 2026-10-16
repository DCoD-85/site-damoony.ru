<?PHP

# ============================================================
#   Suite: render — обёртка над render__data + visuorelease
# ============================================================

return [

    'title' => 'render',

    'setup' => function ( ) {
        require_once SOURCES . '/render.php';

        if ( ! class_exists( 'fake_visuorelease' ) ) {
            class fake_visuorelease {
                public array $calls = [ ];
                public function final      ( $log = false , $debug = false ) { $this->calls[ ] = [ 'final'      , $log , $debug ]; }
                public function final_ajax ( $log = false , $debug = false ) { $this->calls[ ] = [ 'final_ajax' , $log , $debug ]; }
            }
        }
    },

    'tests' => [

        'construct without args' => function ( ) {
            $r = new render( );
            Assert::null( $r->main );
            Assert::instance( 'render__data' , $r->data( ) );
        },

        'construct with main' => function ( ) {
            $m = new main( );
            $r = new render( $m );
            Assert::same( $m , $r->main );
        },

        'set/get min' => function ( ) {
            $r = new render( );
            $r->setdata( 'min' , true );
            Assert::true( $r->getdata( 'min' ) );
            $r->setdata( 'min' , false );
            Assert::false( $r->getdata( 'min' ) );
        },

        'falsy values (regression)' => function ( ) {
            $r = new render( );
            $r->setdata( 'render_ajax' , false );
            Assert::false( $r->getdata( 'render_ajax' ) );
        },

        'unknown key' => function ( ) {
            $r = new render( );
            Assert::null( $r->getdata( 'nope' ) );
            $r->setdata( 'nope' , 123 );
            Assert::null( $r->getdata( 'nope' ) );
        },

        'init(true) sets render_ajax' => function ( ) {
            $r = new render( );
            $r->data( )->init( true );
            Assert::true( $r->getdata( 'render_ajax' ) );
        },

        'release no-ajax' => function ( ) {
            $m = new main( );
            $m->visuorelease = new fake_visuorelease( );
            $r = new render( $m );
            $r->data( )->init( false );
            $r->release( true , true );

            $last = end( $m->visuorelease->calls );
            Assert::same( 'final' , $last[ 0 ] );
            Assert::true( $last[ 1 ] );
            Assert::true( $last[ 2 ] );
        },

        'release ajax' => function ( ) {
            $m = new main( );
            $m->visuorelease = new fake_visuorelease( );
            $r = new render( $m );
            $r->data( )->init( true );
            $r->release( );

            $last = end( $m->visuorelease->calls );
            Assert::same( 'final_ajax' , $last[ 0 ] );
        },

        'release null main' => function ( ) {
            $r = new render( );
            $r->release( );
            Assert::true( true );
        },

        'release main without visuorelease' => function ( ) {
            $m = new main( );
            $m->visuorelease = null;
            $r = new render( $m );
            $r->release( );
            Assert::true( true );
        },

    ],
];