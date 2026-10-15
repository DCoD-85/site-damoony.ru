<?PHP

# ============================================================
#   Suite: visuolink
# ============================================================

return [

    'title' => 'visuolink',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuotag.php';
        require_once VISUO_SRC . '/visuometa.php';
        require_once VISUO_SRC . '/visuolink.php';
    },

    'tests' => [

        'construct' => function ( ) {
            $meta = new visuometa( );
            $l    = new visuolink( $meta );
            Assert::same( $meta , $l->visuometa );
            Assert::same( '' , $l->type );
        },

        'favicon factory' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $f = $l->favicon( '/favicon.ico' );
            Assert::instance( 'visuolink_favicon' , $f );
            Assert::same( 'favicon' , $l->type );
            Assert::same( $f , $l->favicon );
        },

        'favicon as_tag' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->favicon( '/favicon.ico' );
            $out = $l->as_tag( )->release( );
            Assert::contains( 'rel="icon"' , $out );
            Assert::contains( 'href="/favicon.ico"' , $out );
            Assert::contains( 'type="image/x-icon"' , $out );
        },

        'css factory' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $c = $l->css( '/main.css' );
            Assert::instance( 'visuolink_css' , $c );
            Assert::same( 'css' , $l->type );
        },

        'css as_tag' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->css( '/main.css' );
            $out = $l->as_tag( )->release( );
            Assert::contains( 'href="/main.css"' , $out );
            Assert::contains( 'type="text/css"' , $out );
            Assert::contains( 'rel="stylesheet"' , $out );
        },

        'css без cache-buster по умолчанию' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->css( 'https://cdn.plyr.io/3.8.4/plyr.css' );
            $out = $l->as_tag( )->release( );
            Assert::notContains( '?' , $out );
        },

        'js factory' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $j = $l->js( '/main.js' );
            Assert::instance( 'visuolink_js' , $j );
            Assert::same( 'js' , $l->type );
        },

        'js as_tag' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->js( '/main.js' );
            $out = $l->as_tag( )->release( );
            Assert::contains( '<script' , $out );
            Assert::contains( 'src="/main.js"' , $out );
            Assert::contains( 'type="text/javascript"' , $out );
        },

        'as_tag без выбора' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            Assert::eq( '' , $l->as_tag( )->release( ) );
        },

        'type nonexistent — guard' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->type = 'nonexistent';
            Assert::eq( '' , $l->as_tag( )->release( ) );
        },

        'favicon defaults' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $f = $l->favicon( '/x.ico' );
            Assert::same( 'icon' , $f->d_rel );
            Assert::same( 'image/x-icon' , $f->d_type );
            Assert::same( '/x.ico' , $f->d_href );
        },

        'css idempotent' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->css( '/a.css' );
            Assert::same( $l->as_tag( )->release( ) , $l->as_tag( )->release( ) );
        },

        'разные подклассы' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $f = $l->favicon( '/fav.ico' );
            $c = $l->css( '/s.css' );
            $j = $l->js( '/s.js' );
            Assert::same( $f , $l->favicon );
            Assert::same( $c , $l->css );
            Assert::same( $j , $l->js );
            Assert::same( 'js' , $l->type );
        },

        'css rand=true' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->css( '/main.css' , true );
            $out = $l->as_tag( )->release( );
            Assert::contains( '?' , $out );
        },

        'js rand=true' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->js( '/main.js' , true );
            $out = $l->as_tag( )->release( );
            Assert::contains( '?' , $out );
        },

        'rand с существующим query' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->css( '/main.css?v=1' , true );
            $out = $l->as_tag( )->release( );
            Assert::contains( '?v=1&amp;' , $out );
        },

        'rand + якорь' => function ( ) {
            $l = new visuolink( new visuometa( ) );
            $l->favicon( '/favicon.ico#v1' , true );
            $out = $l->as_tag( )->release( );
            Assert::contains( '#v1"' , $out );
            Assert::true( preg_match( '/\?\d+#v1/' , $out ) === 1 );
        },

        'resolve_rand' => function ( ) {
            Assert::false( visuolink::resolve_rand( null ) );
            Assert::true( visuolink::resolve_rand( true ) );
            Assert::false( visuolink::resolve_rand( false ) );
        },

    ],
];