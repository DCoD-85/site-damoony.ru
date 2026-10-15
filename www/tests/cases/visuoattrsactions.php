<?PHP

# ============================================================
#   Suite: visuoattrsactions
# ============================================================

return [

    'title' => 'visuoattrsactions',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoattrs/visuoattrsactions.php';
        require_once VISUO_SRC . '/visuoattrs.php';
        require_once VISUO_SRC . '/visuotag.php';
    },

    'tests' => [

        'empty' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            Assert::eq( '' , $a->release( ) );
        },

        'add onclick' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'onclick' , 'alert(1)' );
            Assert::eq( ' onclick="alert(1)"' , $a->release( ) );
            Assert::true( $a->has( 'onclick' ) );
        },

        'non-event rejected (whitelist)' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'href' , 'javascript:void(0)' );
            Assert::false( $a->has( 'href' ) );
            Assert::eq( '' , $a->release( ) );
        },

        'empty value rejected' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'onclick' , '' );
            Assert::eq( '' , $a->release( ) );
        },

        'case insensitivity' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'ONCLICK' , 'x()' );
            Assert::true( $a->has( 'onclick' ) );
        },

        'remove' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'onclick' , 'a()' )->remove( 'onclick' );
            Assert::eq( '' , $a->release( ) );
        },

        'clear' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'onclick' , 'a()' )->clear( );
            Assert::eq( '' , $a->release( ) );
        },

        'shorthand onclick' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->onclick( 'go()' );
            Assert::true( $a->has( 'onclick' ) );
        },

        'shorthand onmouseover' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->onmouseover( 'hi()' );
            Assert::contains( 'onmouseover="hi()"' , $a->release( ) );
        },

        'get returns value or empty' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'onclick' , 'x()' );
            Assert::same( 'x()' , $a->get( 'onclick' ) );
            Assert::same( '' , $a->get( 'nope' ) );
        },

        'release full=false trims' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'onclick' , 'x()' );
            Assert::same( 'onclick="x()"' , $a->release( false ) );
        },

        'escaping' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->add( 'onclick' , '"><script>alert(1)</script>' );
            Assert::notContains( '<script>' , $a->release( ) );
        },

        'parent' => function ( ) {
            $vt = new visuotag( );
            $a  = new visuoattrsactions( new visuoattrs( $vt ) );
            Assert::same( $vt , $a->parent( ) );
            Assert::same( $vt , $a->tag( ) );
        },

        'all' => function ( ) {
            $a = new visuoattrsactions( new visuoattrs( new visuotag( ) ) );
            $a->onclick( 'a()' )->onmouseover( 'b()' );
            Assert::same( [ 'onclick' => 'a()' , 'onmouseover' => 'b()' ] , $a->all( ) );
        },

    ],
];