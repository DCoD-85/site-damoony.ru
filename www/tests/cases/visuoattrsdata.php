<?PHP

# ============================================================
#   Suite: visuoattrsdata
# ============================================================

return [

    'title' => 'visuoattrsdata',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoattrs/visuoattrsdata.php';
        require_once VISUO_SRC . '/visuoattrs.php';
        require_once VISUO_SRC . '/visuotag.php';
    },

    'tests' => [

        'empty' => function ( ) {
            $d = new visuoattrsdata( );
            Assert::eq( '' , $d->release( ) );
        },

        'add string no json' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'role' , 'admin' , false );
            Assert::eq( ' data-role="admin"' , $d->release( ) );
        },

        'add string json' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'role' , 'admin' , true );
            Assert::contains( 'data-role=' , $d->release( ) );
        },

        'add int json' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'count' , 5 );
            Assert::contains( 'data-count="5"' , $d->release( ) );
        },

        'add array json' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'info' , [ 'a' => 1 , 'b' => 'x' ] );
            Assert::contains( 'data-info=' , $d->release( ) );
        },

        'escaping' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'xss' , '"><script>alert(1)</script>' , false );
            Assert::notContains( '<script>' , $d->release( ) );
        },

        'get' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'role' , 'admin' , false );
            $d->add( 'info' , [ 'a' => 1 ] , true );
            Assert::same( 'admin' , $d->get( 'role' ) );
            Assert::same( [ 'a' => 1 ] , $d->get( 'info' ) );
            Assert::null( $d->get( 'nope' ) );
        },

        'has' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'role' , 'admin' );
            Assert::true( $d->has( 'role' ) );
            Assert::false( $d->has( 'nope' ) );
        },

        'remove' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'role' , 'admin' )->remove( 'role' );
            Assert::false( $d->has( 'role' ) );
        },

        'clear' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'x' , 'y' )->clear( );
            Assert::eq( '' , $d->release( ) );
        },

        'overwrite' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'x' , 'a' , false )->add( 'x' , 'b' , false );
            Assert::contains( 'data-x="b"' , $d->release( ) );
        },

        'cyrillic' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'msg' , [ 'text' => 'Привет' ] );
            Assert::contains( 'Привет' , $d->release( ) );
        },

        'owner' => function ( ) {
            $va = new visuoattrs( new visuotag( ) );
            $d  = new visuoattrsdata( $va );
            Assert::same( $va , $d->owner( ) );
        },

        'null value json' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'x' , null , true );
            Assert::contains( 'data-x="null"' , $d->release( ) );
        },

        'false zero json' => function ( ) {
            $d = new visuoattrsdata( );
            $d->add( 'a' , false , true )->add( 'b' , 0 , true );
            Assert::contains( 'data-a="false"' , $d->release( ) );
            Assert::contains( 'data-b="0"' , $d->release( ) );
        },

        'encode/decode roundtrip' => function ( ) {
            $arr = [ 'a' => 1 , 'b' => 'Привет' ];
            $enc = visuoattrsdata::encode( $arr );
            $dec = visuoattrsdata::decode( $enc );
            Assert::same( $arr , $dec );
        },

        'static encode' => function ( ) {
            Assert::same( '{"a":1}' , visuoattrsdata::encode( [ 'a' => 1 ] ) );
        },

    ],
];