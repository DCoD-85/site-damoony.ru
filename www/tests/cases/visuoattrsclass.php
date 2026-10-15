<?PHP

# ============================================================
#   Suite: visuoattrsclass
# ============================================================

return [

    'title' => 'visuoattrsclass',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoattrs/visuoattrsclass.php';
        require_once VISUO_SRC . '/visuoattrs.php';
        require_once VISUO_SRC . '/visuotag.php';
    },

    'tests' => [

        'empty' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            Assert::eq( '' , $c->release( ) );
            Assert::eq( '' , $c->release( false ) );
        },

        'add one' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'container' );
            Assert::eq( ' class="container"' , $c->release( ) );
            Assert::eq( 'container' , $c->release( false ) );
        },

        'add many' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'container' )->add( 'dark  rounded' );
            Assert::same( [ 'container' , 'dark' , 'rounded' ] , $c->all( ) );
        },

        'dedup' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'a' )->add( 'b' )->add( 'a' );
            Assert::count( 2 , $c->all( ) );
        },

        'has' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'dark' );
            Assert::true( $c->has( 'dark' ) );
            Assert::false( $c->has( 'nope' ) );
        },

        'get' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'dark' );
            Assert::same( 'dark' , $c->get( 'dark' ) );
            Assert::same( '' , $c->get( 'nope' ) );
        },

        'remove' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'container dark rounded' )->remove( 'dark rounded' );
            Assert::same( [ 'container' ] , $c->all( ) );
        },

        'clear' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'x' )->clear( );
            Assert::eq( '' , $c->release( ) );
        },

        'whitespace trim' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( '   spaced   classes   ' );
            Assert::same( [ 'spaced' , 'classes' ] , $c->all( ) );
        },

        'chainable' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'a' )->add( 'b' )->remove( 'a' );
            Assert::same( [ 'b' ] , $c->all( ) );
        },

        'parent' => function ( ) {
            $vt = new visuotag( );
            $c  = new visuoattrsclass( new visuoattrs( $vt ) );
            Assert::same( $vt , $c->parent( ) );
        },

        'order preserved' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'z' )->add( 'a' )->add( 'm' );
            Assert::same( [ 'z' , 'a' , 'm' ] , $c->all( ) );
        },

        'escaping' => function ( ) {
            $c = new visuoattrsclass( new visuoattrs( new visuotag( ) ) );
            $c->add( 'evil"><script>alert(1)</script>' );
            Assert::notContains( '<script>' , $c->release( ) );
        },

    ],
];