<?PHP

# ============================================================
#   Suite: visuoel_form — группа form + form_form
# ============================================================

return [

    'title' => 'visuoel_form',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoels.php';
    },

    'tests' => [

        'class_exists visuoel_form' => function ( ) {
            Assert::true( class_exists( 'visuoel_form' ) );
        },
        'class_exists visuoel_form_form' => function ( ) {
            Assert::true( class_exists( 'visuoel_form_form' ) );
        },
        'visuoel_form extends visuoel_group' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_form' , 'visuoel_group' ) );
        },
        'visuoel_form_form extends group_element' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_form_form' , 'visuoel_group_element' ) );
        },
        'visuoel_form__data extends group__data' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_form__data' , 'visuoel_group__data' ) );
        },
        'form_form__data extends group_element__data' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_form_form__data' , 'visuoel_group_element__data' ) );
        },

        '->form() returns visuoel_form' => function ( ) {
            $r = new visuoel( );
            $g = $r->form( );
            Assert::instance( 'visuoel_form' , $g );
            Assert::same( $r , $g->parent );
            Assert::same( 'blockform' , $g->css_class( ) );
        },
        '->form() returns visuoel_form_form' => function ( ) {
            $r = new visuoel( );
            $g = $r->form( );
            $e = $g->form( );
            Assert::instance( 'visuoel_form_form' , $e );
            Assert::same( $g , $e->parent );
        },

        'form singleton' => function ( ) {
            $r = new visuoel( );
            $g = $r->form( );
            Assert::same( $g , $r->form( ) );
            Assert::same( $g->form( ) , $g->form( ) );
        },

        'form_form->data() тип' => function ( ) {
            $r = new visuoel( );
            $e = $r->form( )->form( );
            Assert::instance( 'visuoel_form_form__data' , $e->data( ) );
            Assert::same( $e , $e->data( )->node );
        },

        'get_form_form__data() === data()' => function ( ) {
            $r = new visuoel( );
            $g = $r->form( );
            $e = $g->form( );
            Assert::same( $e->data( ) , $g->get_form_form__data( ) );
        },

        'init создаёт vt_start / vt_end' => function ( ) {
            $r = new visuoel( );
            $g = $r->form( );
            $e = $g->form( );
            $r->init( );
            Assert::instance( 'visuotag' , $r->vt_start );
            Assert::instance( 'visuotag' , $g->vt_start );
            Assert::instance( 'visuotag' , $e->vt_start );
        },

        'form_form: vt_start !== vt_end' => function ( ) {
            $r = new visuoel( );
            $e = $r->form( )->form( );
            $r->init( );
            Assert::true( $e->vt_start !== $e->vt_end );
        },

        'release() returns non-empty string' => function ( ) {
            $r = new visuoel( );
            $r->form( )->form( )->data( )->set( 'reg' , 'post' , '/reg' );
            $r->init( );
            $html = $r->release( );
            Assert::true( is_string( $html ) && strlen( $html ) > 0 );
        },

        'HTML содержит blockform' => function ( ) {
            $r = new visuoel( );
            $r->form( )->form( )->data( )->set( 'reg' , 'post' , '/reg' );
            $r->init( );
            Assert::contains( 'class="blockform"' , $r->release( ) );
        },

        'HTML содержит form и form_body' => function ( ) {
            $r = new visuoel( );
            $r->form( )->form( )->data( )->set( 'reg' , 'post' , '/reg' );
            $r->init( );
            $html = $r->release( );
            Assert::contains( '<form' , $html );
            Assert::contains( 'class="form_body"' , $html );
        },

        'show() === release()' => function ( ) {
            $r = new visuoel( );
            $r->form( )->form( )->data( )->set( 'x' , 'post' );
            Assert::same( $r->show( ) , $r->release( ) );
        },

        'data->set() заполняет поля' => function ( ) {
            $r = new visuoel( );
            $e = $r->form( )->form( );
            $d = $e->data( );
            $d->set( 'testform' , 'post' , '/submit' , title_text : 'Форма' , form_jquery : true );
            Assert::same( 'testform' , $d->form_name );
            Assert::same( 'post' , $d->form_method );
            Assert::same( '/submit' , $d->form_action );
            Assert::same( 'Форма' , $d->title_text );
            Assert::true( $d->form_jquery );
        },

        'data->set(file) → POST + multipart' => function ( ) {
            $e = ( new visuoel( ) )->form( )->form( );
            $d = $e->data( );
            $d->set( 'upload' , 'file' );
            Assert::same( 'POST' , $d->form_method );
            Assert::same( 'multipart/form-data' , $d->form_enctype );
        },

        'HTML содержит action, name, method' => function ( ) {
            $r = new visuoel( );
            $e = $r->form( )->form( );
            $e->data( )->set( 'testform' , 'post' , '/submit' , title_text : 'T' , form_jquery : true );
            $r->init( );
            $html = $r->release( );
            Assert::contains( 'action="/submit"' , $html );
            Assert::contains( 'name="testform"' , $html );
            Assert::contains( 'method="post"' , $html );
            Assert::contains( 'data-form-jquery' , $html );
        },

        'HTML содержит form_title' => function ( ) {
            $r = new visuoel( );
            $e = $r->form( )->form( );
            $e->data( )->set( 'f' , 'post' , title_text : 'Форма авторизации' );
            $r->init( );
            Assert::contains( 'class="form_title"' , $r->release( ) );
        },

        'add(visuotag) → children в form_body' => function ( ) {
            $r = new visuoel( );
            $e = $r->form( )->form( );
            $e->data( )->set( 'f' , 'post' , '/x' )->title_text = '';

            $child = new visuotag( );
            $child->tag( 'p' )->content( 'hello' );
            $e->data( )->add( $child );

            $r->init( );
            $html = $r->release( );

            $pos_body  = strpos( $html , 'class="form_body"' );
            $pos_child = strpos( $html , 'hello' );
            Assert::contains( 'hello' , $html );
            Assert::true( $pos_body !== false && $pos_child > $pos_body );
        },

        'XSS в title_text — экранирован' => function ( ) {
            $r = new visuoel( );
            $e = $r->form( )->form( );
            $e->data( )->set( 'f' , 'post' , title_text : '<script>x()</script>' );
            $r->init( );
            $html = $r->release( );
            Assert::notContains( '<script>x()</script>' , $html );
            Assert::contains( '&lt;script&gt;' , $html );
        },

    ],
];