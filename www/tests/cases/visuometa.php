<?PHP

# ============================================================
#   Suite: visuometa
# ============================================================

return [

    'title' => 'visuometa',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuotag.php';
        require_once VISUO_SRC . '/visuolink.php';
        require_once VISUO_SRC . '/visuometa.php';
    },

    'tests' => [

        'construct without args' => function ( ) {
            $m = new visuometa( );
            Assert::null( $m->visuorelease( ) );
            Assert::count( 0 , $m->elements );
        },

        'title factory' => function ( ) {
            $m = new visuometa( );
            $t = $m->title( );
            Assert::instance( 'visuometa_title' , $t );
            Assert::count( 1 , $m->elements );
            Assert::same( $t , $m->elements[ 0 ] );
        },

        'title data + as_tag' => function ( ) {
            $m = new visuometa( );
            $t = $m->title( )->data( 'My Page' );
            $vt = $t->as_tag( );
            Assert::eq( 'title' , $vt->tagname );
            Assert::eq( '<title>My Page</title>' , $vt->release( ) );
        },

        'title back' => function ( ) {
            $m = new visuometa( );
            $t = $m->title( );
            Assert::same( $m , $t->back( ) );
        },

        'link factory' => function ( ) {
            $m = new visuometa( );
            $l = $m->link( );
            Assert::instance( 'visuolink' , $l );
            Assert::count( 1 , $m->elements );
        },

        'link css' => function ( ) {
            $m = new visuometa( );
            $m->link( )->css( '/main.css' );
            $out = $m->as_tag( )->release( );
            Assert::contains( 'href="/main.css"' , $out );
        },

        'link js' => function ( ) {
            $m = new visuometa( );
            $m->link( )->js( '/main.js' );
            $out = $m->as_tag( )->release( );
            Assert::contains( 'src="/main.js"' , $out );
        },

        'link favicon' => function ( ) {
            $m = new visuometa( );
            $m->link( )->favicon( '/favicon.ico' );
            $out = $m->as_tag( )->release( );
            Assert::contains( 'rel="icon"' , $out );
        },

        'multiple links' => function ( ) {
            $m = new visuometa( );
            $m->link( )->css( '/a.css' );
            $m->link( )->css( '/b.css' );
            $m->link( )->js( '/a.js' );
            Assert::count( 3 , $m->elements );
        },

        'title + links' => function ( ) {
            $m = new visuometa( );
            $m->title( )->data( 'T' );
            $m->link( )->css( '/x.css' );
            $out = $m->as_tag( )->release( );
            Assert::contains( '<title>T</title>' , $out );
            Assert::contains( '/x.css' , $out );
        },

        'clear' => function ( ) {
            $m = new visuometa( );
            $m->title( )->data( 'X' );
            $m->link( )->css( '/a.css' );
            $m->clear( );
            Assert::count( 0 , $m->elements );
            Assert::eq( '' , $m->as_tag( )->release( ) );
        },

        'clear chainable' => function ( ) {
            $m = new visuometa( );
            Assert::same( $m , $m->clear( ) );
        },

        'as_tag idempotent' => function ( ) {
            $m = new visuometa( );
            $m->title( )->data( 'X' );
            $vt1 = $m->as_tag( );
            $vt2 = $m->as_tag( );
            Assert::true( $vt1 !== $vt2 );
            Assert::same( $vt1->release( ) , $vt2->release( ) );
        },

        'as_tag empty' => function ( ) {
            $m = new visuometa( );
            Assert::eq( '' , $m->as_tag( )->release( ) );
        },

        'integration with visuotag::meta()' => function ( ) {
            $root = new visuotag( );
            $head = $root->tag( 'head' );
            $m    = new visuometa( );
            $m->title( )->data( 'Integrated' );
            $head->meta( $m );
            Assert::contains( '<title>Integrated</title>' , $root->release( ) );
        },

        'visuotag accessor' => function ( ) {
            $m = new visuometa( );
            Assert::instance( 'visuotag' , $m->visuotag( ) );
        },

        'visuotag_set' => function ( ) {
            $m  = new visuometa( );
            $vt = new visuotag( );
            Assert::same( $m , $m->visuotag_set( $vt ) );
            Assert::same( $vt , $m->visuotag( ) );
        },

        'title empty content' => function ( ) {
            $m = new visuometa( );
            $vt = $m->title( )->as_tag( );
            Assert::eq( '<title></title>' , $vt->release( ) );
        },

    ],
];