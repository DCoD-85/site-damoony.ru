<?PHP

# ============================================================
#   Suite: visuostruct_min — JSON для AJAX min
# ============================================================

return [

    'title' => 'visuostruct_min',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuotag.php';
        require_once VISUO_SRC . '/visuostruct.php';
    },

    'tests' => [

        'construct' => function ( ) {
            $s   = new visuostruct( );
            $min = $s->min( );
            Assert::same( $s , $min->visuostruct );
            Assert::null( $min->error );
        },

        'error() returns visuostruct_min_error' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $e   = $min->error( );
            Assert::instance( 'visuostruct_min_error' , $e );
            Assert::same( $e , $min->error );
            Assert::same( $min , $e->visuostruct_min );
        },

        'error singleton' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            Assert::same( $min->error( ) , $min->error( ) );
        },

        'error set / release' => function ( ) {
            $e = ( new visuostruct( ) )->min( )->error( );
            $e->set( true );
            Assert::true( $e->release( ) );
            $e->set( false );
            Assert::false( $e->release( ) );
        },

        'messages() empty release — no fatal' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $msg = $min->messages( );
            Assert::instance( 'visuostruct_min_messages' , $msg );
            Assert::eq( '' , $msg->release( ) );
        },

        'messages set + release' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $msg = $min->messages( );
            $vt  = new visuotag( );
            $vt->tag( 'div' )->content( 'hello' );
            $msg->set( $vt );
            Assert::contains( 'hello' , $msg->release( ) );
        },

        'release() empty — no fatal' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $rel = $min->release( );
            Assert::instance( 'visuostruct_min_release' , $rel );
            $data = $rel->release( );
            Assert::eq( '' , $data[ 'html' ] );
            Assert::eq( '' , $data[ 'layer' ] );
            Assert::false( $data[ 'popup' ] );
        },

        'release set + release' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $rel = $min->release( );
            $vt  = new visuotag( );
            $vt->tag( 'div' )->content( 'content' );
            $rel->set( $vt , 'dt_center' , true , true , false );
            $data = $rel->release( );

            Assert::contains( 'content' , $data[ 'html' ] );
            Assert::same( 'dt_center' , $data[ 'layer' ] );
            Assert::true( $data[ 'popup' ] );
            Assert::true( $data[ 'popup_back' ] );
            Assert::false( $data[ 'popup_navigate' ] );
        },

        'form empty' => function ( ) {
            $min  = ( new visuostruct( ) )->min( );
            $form = $min->form( );
            Assert::instance( 'visuostruct_min_form' , $form );
            $data = $form->release( );
            Assert::false( $data[ 'reset' ] );
            Assert::null( $data[ 'redirect' ] );
        },

        'form set' => function ( ) {
            $form = ( new visuostruct( ) )->min( )->form( );
            $form->set( reset : true , redirect : '/next' , redirect_time : 5 );
            $data = $form->release( );
            Assert::true( $data[ 'reset' ] );
            Assert::same( '/next' , $data[ 'redirect' ] );
            Assert::same( 5 , $data[ 'redirect_time' ] );
        },

        'referer empty' => function ( ) {
            unset( $_SERVER[ 'HTTP_REFERER' ] );
            $ref = ( new visuostruct( ) )->min( )->referer( );
            Assert::instance( 'visuostruct_min_referer' , $ref );
            Assert::null( $ref->release( ) );
        },

        'referer from HTTP_REFERER' => function ( ) {
            $_SERVER[ 'HTTP_REFERER' ] = 'https://example.com/prev';
            $min = ( new visuostruct( ) )->min( );
            Assert::same( 'https://example.com/prev' , $min->referer( )->release( ) );
        },

        'referer set' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $min->referer( )->set( '/custom' );
            Assert::same( '/custom' , $min->referer( )->release( ) );
        },

        'referer falsy "0" (regression)' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $min->referer( )->set( '0' );
            Assert::same( '0' , $min->referer( )->release( ) );
        },

        'referer set null → server fallback' => function ( ) {
            $_SERVER[ 'HTTP_REFERER' ] = 'https://from-server.example';
            $min = ( new visuostruct( ) )->min( );
            $min->referer( )->set( null );
            Assert::same( 'https://from-server.example' , $min->referer( )->release( ) );
        },

        'all factories identity' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            Assert::same( $min->error( )    , $min->error( ) );
            Assert::same( $min->messages( ) , $min->messages( ) );
            Assert::same( $min->release( )  , $min->release( ) );
            Assert::same( $min->form( )     , $min->form( ) );
            Assert::same( $min->referer( )  , $min->referer( ) );
        },

        'all elements inherit base' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            Assert::instance( 'visuostruct_min_element' , $min->error( ) );
            Assert::instance( 'visuostruct_min_element' , $min->messages( ) );
            Assert::instance( 'visuostruct_min_element' , $min->release( ) );
            Assert::instance( 'visuostruct_min_element' , $min->form( ) );
            Assert::instance( 'visuostruct_min_element' , $min->referer( ) );
        },

        'construct without visuostruct' => function ( ) {
            $min = new visuostruct_min( );
            Assert::null( $min->visuostruct );
            Assert::null( $min->error( )->visuostruct );
        },

        'messages release idempotent' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $vt  = new visuotag( );
            $vt->tag( 'p' )->content( 'x' );
            $min->messages( )->set( $vt );
            Assert::same( $min->messages( )->release( ) , $min->messages( )->release( ) );
        },

        'release idempotent' => function ( ) {
            $min = ( new visuostruct( ) )->min( );
            $vt  = new visuotag( );
            $vt->tag( 'p' )->content( 'x' );
            $min->release( )->set( $vt , 'dt_test' );
            $r1 = $min->release( )->release( );
            $r2 = $min->release( )->release( );
            Assert::same( $r1[ 'html' ] , $r2[ 'html' ] );
        },

    ],
];