<?PHP

# ============================================================
#   Suite: visuostruct — агрегатор html/min/ajax
# ============================================================

return [

    'title' => 'visuostruct',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuotag.php';
        require_once VISUO_SRC . '/visuostruct.php';
    },

    'tests' => [

        'construct without args' => function ( ) {
            $s = new visuostruct( );
            Assert::null( $s->main );
            Assert::null( $s->min );
            Assert::null( $s->ajax );
            Assert::null( $s->html );
        },

        'construct with main' => function ( ) {
            $m = new main( );
            $s = new visuostruct( $m );
            Assert::same( $m , $s->main );
        },

        'min() returns visuostruct_min' => function ( ) {
            $s = new visuostruct( );
            $min = $s->min( );
            Assert::instance( 'visuostruct_min' , $min );
            Assert::same( $min , $s->min );
        },

        'min() singleton' => function ( ) {
            $s = new visuostruct( );
            Assert::same( $s->min( ) , $s->min( ) );
        },

        'ajax() returns visuostruct_ajax' => function ( ) {
            $s = new visuostruct( );
            $ajax = $s->ajax( );
            Assert::instance( 'visuostruct_ajax' , $ajax );
            Assert::same( $ajax , $s->ajax );
        },

        'ajax() singleton' => function ( ) {
            $s = new visuostruct( );
            Assert::same( $s->ajax( ) , $s->ajax( ) );
        },

        'html() returns visuostruct_html' => function ( ) {
            $s = new visuostruct( );
            $html = $s->html( );
            Assert::instance( 'visuostruct_html' , $html );
            Assert::same( $html , $s->html );
        },

        'min / ajax independent' => function ( ) {
            $s = new visuostruct( );
            $min = $s->min( );
            $ajax = $s->ajax( );
            Assert::true( $min !== $ajax );
            Assert::true( $s->min !== null && $s->ajax !== null );
        },

        'min backref' => function ( ) {
            $s = new visuostruct( );
            Assert::same( $s , $s->min( )->visuostruct );
        },

        'ajax backref' => function ( ) {
            $s = new visuostruct( );
            Assert::same( $s , $s->ajax( )->visuostruct );
        },

        'html backref' => function ( ) {
            $s = new visuostruct( );
            Assert::same( $s , $s->html( )->visuostruct );
        },

        'current(): приоритет min > ajax > html' => function ( ) {
            $s = new visuostruct( );
            Assert::null( $s->current( ) );

            $s->html( );
            Assert::same( $s->html , $s->current( ) );

            $s->ajax( );
            Assert::same( $s->ajax , $s->current( ) );

            $s->min( );
            Assert::same( $s->min , $s->current( ) );
        },

        # --- html sub-struct ---

        'html return_type' => function ( ) {
            $s = new visuostruct( );
            Assert::same( 'html' , $s->html( )->return_type( ) );
        },

        'html set() возвращает $this' => function ( ) {
            $s = new visuostruct( );
            $h = $s->html( );
            $vt = new visuotag( );
            Assert::same( $h , $h->set( top : $vt ) );
        },

        'html set() устанавливает свойства' => function ( ) {
            $s = new visuostruct( );
            $h = $s->html( );
            $vt = new visuotag( );
            $h->set( top : $vt );
            Assert::same( $vt , $h->top );
        },

        'html set(null) не перезаписывает' => function ( ) {
            $s = new visuostruct( );
            $h = $s->html( );
            $vt = new visuotag( );
            $h->set( top : $vt );
            $h->set( top : null );
            Assert::same( $vt , $h->top );
        },

        'html off() устанавливает false' => function ( ) {
            $s = new visuostruct( );
            $h = $s->html( );
            $h->off( 'top' );
            Assert::false( $h->top );
        },

        'html off() неизвестный блок — no-op' => function ( ) {
            $s = new visuostruct( );
            $h = $s->html( );
            $h->off( 'nonexistent' );
            Assert::true( true );
        },

    ],
];