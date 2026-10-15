<?PHP

# ============================================================
#   Suite: visuoattrs
#   Контейнер атрибутов + субклассы.
# ============================================================

return [

    'title' => 'visuoattrs',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoattrs/visuoattrsclass.php';
        require_once VISUO_SRC . '/visuoattrs/visuoattrsstyle.php';
        require_once VISUO_SRC . '/visuoattrs/visuoattrsactions.php';
        require_once VISUO_SRC . '/visuoattrs/visuoattrsdata.php';
        require_once VISUO_SRC . '/visuoattrs.php';
        require_once VISUO_SRC . '/visuotag.php';
    },

    'tests' => [

        'empty' => function ( ) {
            $vt = new visuotag( );
            $a  = new visuoattrs( $vt );
            Assert::eq( '' , $a->release( ) );
            Assert::same( $vt , $a->visuotag( ) );
        },

        'generic' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'id' , 'main' )->add( 'href' , '/index.php' );
            Assert::contains( 'id="main"' , $a->release( ) );
            Assert::contains( 'href="/index.php"' , $a->release( ) );
            Assert::same( 'main' , $a->get( 'id' ) );
            Assert::true( $a->has( 'href' ) );
        },

        'add_empty flag' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'data-x' , '' );
            $a->add( 'data-y' , '' , true );
            Assert::false( $a->has( 'data-x' ) );
            Assert::true( $a->has( 'data-y' ) );
        },

        'falsy value regression' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'value' , '0' );
            Assert::same( '0' , $a->get( 'value' ) );
            Assert::null( $a->get( 'nope' ) );
        },

        'remove' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'value' , 'x' )->remove( 'value' );
            Assert::false( $a->has( 'value' ) );
        },

        'class' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->class_add( 'container dark' )->class_add( 'rounded' );
            Assert::contains( 'class="container dark rounded"' , $a->release( ) );
            Assert::true( $a->class( )->has( 'dark' ) );
        },

        'class_remove' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->class_add( 'container dark' )->class_remove( 'dark' );
            Assert::same( [ 'container' ] , $a->class( )->all( ) );
        },

        'style' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->style_add( 'color:red; font-size:14px' );
            Assert::contains( 'style="color:red;font-size:14px;"' , $a->release( ) );
            Assert::same( 'red' , $a->style( )->get( 'color' ) );
        },

        'actions' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->actions_add( 'onclick' , 'alert(1)' )->actions_add( 'onmouseover' , 'hi()' );
            $out = $a->release( );
            Assert::contains( 'onclick="alert(1)"' , $out );
            Assert::contains( 'onmouseover="hi()"' , $out );
            Assert::true( $a->actions( )->has( 'onclick' ) );
        },

        'release order' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'id' , 'x' )->class_add( 'cls' )
                ->style_add( 'color:red' )->actions_add( 'onclick' , 'y()' );
            $out = $a->release( );
            $pos_class = strpos( $out , 'class=' );
            $pos_style = strpos( $out , 'style=' );
            $pos_act   = strpos( $out , 'onclick=' );
            $pos_id    = strpos( $out , 'id=' );
            Assert::true( $pos_class < $pos_style );
            Assert::true( $pos_style < $pos_act );
            Assert::true( $pos_act < $pos_id );
        },

        'attributes_release' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'id' , 'x' )->class_add( 'cls' );
            Assert::same( ' id="x"' , $a->attributes_release( ) );
        },

        'escaping generic' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'xss' , '"><script>alert(1)</script>' );
            Assert::notContains( '<script>' , $a->release( ) );
            Assert::contains( '&lt;script&gt;' , $a->release( ) );
        },

        'clear' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'id' , 'x' )->class_add( 'y' );
            $a->clear( );
            Assert::eq( '' , $a->release( ) );
        },

        'sub-class identity' => function ( ) {
            $vt = new visuotag( );
            $a  = new visuoattrs( $vt );
            Assert::same( $a->class( ) , $a->class( ) );
            Assert::same( $vt , $a->class( )->parent( ) );
        },

        'chainable' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->add( 'id' , 'x' )->class_add( 'y' )->style_add( 'z:1' )->actions_add( 'onclick' , 'q()' );
            Assert::contains( 'id="x"' , $a->release( ) );
            Assert::contains( 'class="y"' , $a->release( ) );
        },

        'data через attrs' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->data_add( 'role' , 'admin' , false );
            $a->data_add( 'info' , [ 'x' => 1 ] );
            $out = $a->release( );
            Assert::contains( 'data-role="admin"' , $out );
            Assert::contains( 'data-info=' , $out );
            Assert::same( 'admin' , $a->data( )->get( 'role' ) );
        },

        'data cleared by clear()' => function ( ) {
            $a = new visuoattrs( new visuotag( ) );
            $a->data_add( 'role' , 'admin' , false )->class_add( 'y' );
            $a->clear( );
            Assert::eq( '' , $a->release( ) );
            Assert::false( $a->data( )->has( 'role' ) );
        },

    ],
];