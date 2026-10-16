<?PHP

# ============================================================
#   Suite: visuostruct_ajax — JSON для AJAX-ответа
# ============================================================

return [

    'title' => 'visuostruct_ajax',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuotag.php';
        require_once VISUO_SRC . '/visuostruct.php';
    },

    'tests' => [

        'construct' => function ( ) {
            $s    = new visuostruct( );
            $ajax = new visuostruct_ajax( $s );
            Assert::same( $s , $ajax->visuostruct );
            Assert::false( $ajax->error );
            Assert::null( $ajax->address );
            Assert::null( $ajax->header );
            Assert::null( $ajax->top );
        },

        'address factory + backref' => function ( ) {
            $s    = new visuostruct( );
            $ajax = new visuostruct_ajax( $s );
            $a    = $ajax->address( );
            Assert::instance( 'visuostruct_ajax_address' , $a );
            Assert::same( $a , $ajax->address );
            Assert::same( $ajax , $a->visuostruct_ajax );
            Assert::same( $s , $a->visuostruct );
        },

        'address set / release' => function ( ) {
            $a = ( new visuostruct_ajax( ) )->address( );
            $a->set( 'https://example.com/page' );
            Assert::same( 'https://example.com/page' , $a->release( ) );
        },

        'header set + as_json' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $vt   = new visuotag( );
            $vt->tag( 'title' )->content( 'Header Title' );
            $ajax->header( )->set( $vt );
            $data = json_decode( $ajax->as_json( ) , true );
            Assert::contains( 'Header Title' , (string) $data[ 'layers' ][ 'header' ] );
        },

        'top layer' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $vt   = new visuotag( );
            $vt->tag( 'div' )->content( 'top' );
            $ajax->top( )->set( $vt );
            $data = json_decode( $ajax->as_json( ) , true );
            Assert::same( 'dt_top' , $data[ 'layers' ][ 'top' ][ 'layer' ] );
            Assert::contains( 'top' , $data[ 'layers' ][ 'top' ][ 'html' ] );
        },

        'left layer' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $vt   = new visuotag( );
            $vt->tag( 'div' )->content( 'L' );
            $ajax->left( )->set( $vt );
            $data = json_decode( $ajax->as_json( ) , true );
            Assert::same( 'dt_left' , $data[ 'layers' ][ 'left' ][ 'layer' ] );
        },

        'right layer' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $ajax->right( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'R' ) );
            $data = json_decode( $ajax->as_json( ) , true );
            Assert::same( 'dt_right' , $data[ 'layers' ][ 'right' ][ 'layer' ] );
        },

        'center layer' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $ajax->center( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'C' ) );
            $data = json_decode( $ajax->as_json( ) , true );
            Assert::same( 'dt_center' , $data[ 'layers' ][ 'center' ][ 'layer' ] );
        },

        'bottom layer' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $ajax->bottom( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'B' ) );
            $data = json_decode( $ajax->as_json( ) , true );
            Assert::same( 'dt_bottom' , $data[ 'layers' ][ 'bottom' ][ 'layer' ] );
        },

        'all layers at once' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $ajax->header( )->set( ( new visuotag( ) )->tag( 'title' )->content( 'H' ) );
            $ajax->top( )->set(    ( new visuotag( ) )->tag( 'div' )->content( 'T' ) );
            $ajax->left( )->set(   ( new visuotag( ) )->tag( 'div' )->content( 'L' ) );
            $ajax->center( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'C' ) );
            $ajax->right( )->set(  ( new visuotag( ) )->tag( 'div' )->content( 'R' ) );
            $ajax->bottom( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'B' ) );

            $data = json_decode( $ajax->as_json( ) , true );
            Assert::true( $data[ 'layers' ][ 'header' ] !== null );
            Assert::true( $data[ 'layers' ][ 'left'   ] !== null );
            Assert::true( $data[ 'layers' ][ 'right'  ] !== null );
        },

        'error flag' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $ajax->error = true;
            $data = json_decode( $ajax->as_json( ) , true );
            Assert::true( $data[ 'error' ] );
        },

        'layer without set → null release' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $ajax->top( );
            $data = json_decode( $ajax->as_json( ) , true );
            Assert::null( $data[ 'layers' ][ 'top' ] );
        },

        'layer release без set — no fatal' => function ( ) {
            $top = ( new visuostruct_ajax( ) )->top( );
            Assert::null( $top->release( ) );
        },

        'layer singleton' => function ( ) {
            $ajax = new visuostruct_ajax( );
            Assert::same( $ajax->top( ) , $ajax->top( ) );
        },

        'all layers separate objects' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $all  = [ $ajax->top( ) , $ajax->left( ) , $ajax->center( ) , $ajax->right( ) , $ajax->bottom( ) ];
            $ids  = array_map( 'spl_object_id' , $all );
            Assert::same( count( $ids ) , count( array_unique( $ids ) ) );
        },

        'layer instanceof base' => function ( ) {
            $ajax = new visuostruct_ajax( );
            Assert::instance( 'visuostruct_ajax_layer'   , $ajax->top( ) );
            Assert::instance( 'visuostruct_ajax_layer'   , $ajax->left( ) );
            Assert::instance( 'visuostruct_ajax_layer'   , $ajax->right( ) );
            Assert::instance( 'visuostruct_ajax_element' , $ajax->top( ) );
        },

        'address instanceof base' => function ( ) {
            $ajax = new visuostruct_ajax( );
            Assert::instance( 'visuostruct_ajax_element' , $ajax->address( ) );
            Assert::instance( 'visuostruct_ajax_element' , $ajax->header( ) );
        },

        'json structure' => function ( ) {
            $data = json_decode( ( new visuostruct_ajax( ) )->as_json( ) , true );
            Assert::true( array_key_exists( 'error'   , $data ) );
            Assert::true( array_key_exists( 'address' , $data ) );
            Assert::true( array_key_exists( 'layers'  , $data ) );
            Assert::count( 6 , $data[ 'layers' ] );
            Assert::same( [ 'header' , 'top' , 'left' , 'center' , 'right' , 'bottom' ] , array_keys( $data[ 'layers' ] ) );
        },

        'JSON_UNESCAPED_UNICODE' => function ( ) {
            $ajax = new visuostruct_ajax( );
            $vt   = new visuotag( );
            $vt->tag( 'div' )->content( 'Привет' );
            $ajax->center( )->set( $vt );
            Assert::contains( 'Привет' , $ajax->as_json( ) );
        },

        'json empty valid' => function ( ) {
            $json = ( new visuostruct_ajax( ) )->as_json( );
            Assert::true( json_decode( $json ) !== null );
            Assert::true( $json !== '{}' );
        },

        'address backref chain' => function ( ) {
            $vs   = new visuostruct( );
            $ajax = new visuostruct_ajax( $vs );
            $addr = $ajax->address( );
            Assert::same( $vs   , $addr->visuostruct );
            Assert::same( $ajax , $addr->visuostruct_ajax );
        },

        'construct without visuostruct' => function ( ) {
            $ajax = new visuostruct_ajax( );
            Assert::null( $ajax->visuostruct );
            Assert::null( $ajax->address( )->visuostruct );
        },

    ],
];