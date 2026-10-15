<?PHP

# ============================================================
#   Suite: visuoattrsstyle
# ============================================================

return [

    'title' => 'visuoattrsstyle',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoattrs/visuoattrsstyle.php';
        require_once VISUO_SRC . '/visuoattrs.php';
        require_once VISUO_SRC . '/visuotag.php';
    },

    'tests' => [

        'empty' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            Assert::eq( '' , $s->release( ) );
            Assert::eq( '' , $s->release( false ) );
        },

        'add one' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'color:red' );
            Assert::eq( ' style="color:red;"' , $s->release( ) );
        },

        'add many' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'color:red' )->add( 'font-size:14px; padding: 5px' );
            Assert::same( [ 'color' , 'font-size' , 'padding' ] , array_keys( $s->all( ) ) );
        },

        'overwrite' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'color:red' )->add( 'color:blue' );
            Assert::same( 'blue' , $s->get( 'color' ) );
        },

        'has / get' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'color:red' );
            Assert::true( $s->has( 'color' ) );
            Assert::false( $s->has( 'nope' ) );
            Assert::same( 'red' , $s->get( 'color' ) );
            Assert::same( '' , $s->get( 'nope' ) );
        },

        'remove' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'color:red; padding:5px' )->remove( 'color padding' );
            Assert::false( $s->has( 'color' ) );
            Assert::false( $s->has( 'padding' ) );
        },

        'clear' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'color:red' )->clear( );
            Assert::eq( '' , $s->release( ) );
        },

        'url with colon' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'background:url(http://example.com/img.png)' );
            Assert::same( 'url(http://example.com/img.png)' , $s->get( 'background' ) );
        },

        'custom separators' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'color=red|font-size=14px' , '|' , '=' );
            Assert::same( [ 'color' => 'red' , 'font-size' => '14px' ] , $s->all( ) );
        },

        'skip empty' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( ';;color:red;;; font-size:14px ;;' );
            Assert::count( 2 , $s->all( ) );
        },

        'whitespace trim' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( '  color  :   red  ;  padding:5px  ' );
            Assert::same( 'red' , $s->get( 'color' ) );
            Assert::same( '5px' , $s->get( 'padding' ) );
        },

        'chainable' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'a:1' )->add( 'b:2' )->remove( 'a' );
            Assert::same( [ 'b' => '2' ] , $s->all( ) );
        },

        'parent' => function ( ) {
            $vt = new visuotag( );
            $s  = new visuoattrsstyle( new visuoattrs( $vt ) );
            Assert::same( $vt , $s->parent( ) );
        },

        'escaping' => function ( ) {
            $s = new visuoattrsstyle( new visuoattrs( new visuotag( ) ) );
            $s->add( 'x:"><script>alert(1)</script>' );
            Assert::notContains( '<script>' , $s->release( ) );
        },

    ],
];