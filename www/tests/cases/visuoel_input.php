<?PHP

# ============================================================
#   Suite: visuoel_input — группа input
# ============================================================

return [

    'title' => 'visuoel_input',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoels.php';
    },

    'tests' => [

        'class_exists visuoel_input' => function ( ) {
            Assert::true( class_exists( 'visuoel_input' ) );
        },
        'class_exists visuoel_input_text' => function ( ) {
            Assert::true( class_exists( 'visuoel_input_text' ) );
        },
        'class_exists visuoel_input_hidden' => function ( ) {
            Assert::true( class_exists( 'visuoel_input_hidden' ) );
        },
        'class_exists visuoel_input_password' => function ( ) {
            Assert::true( class_exists( 'visuoel_input_password' ) );
        },
        'class_exists visuoel_input_submit' => function ( ) {
            Assert::true( class_exists( 'visuoel_input_submit' ) );
        },
        'visuoel_input extends visuoel_group' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_input' , 'visuoel_group' ) );
        },
        'visuoel_input_text extends input__element' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_input_text' , 'visuoel_input__element' ) );
        },

        '->input() returns visuoel_input' => function ( ) {
            $r = new visuoel( );
            $g = $r->input( );
            Assert::instance( 'visuoel_input' , $g );
            Assert::same( 'input' , $g->css_class( ) );
        },
        '->text() returns visuoel_input_text' => function ( ) {
            $g = ( new visuoel( ) )->input( );
            $t = $g->text( );
            Assert::instance( 'visuoel_input_text' , $t );
            Assert::same( $g , $t->parent );
        },
        'data класс text' => function ( ) {
            $t = ( new visuoel( ) )->input( )->text( );
            Assert::instance( 'visuoel_input_text__data' , $t->data( ) );
        },
        'input() singleton' => function ( ) {
            $r = new visuoel( );
            $g = $r->input( );
            Assert::same( $g , $r->input( ) );
        },
        'multi: два text — два элемента' => function ( ) {
            $g = ( new visuoel( ) )->input( );
            $g->text( );
            $g->text( );
            Assert::count( 2 , $g->elements );
        },

        'data->set(name, value, placeholder)' => function ( ) {
            $t = new visuoel_input_text( );
            $t->data( )->set( 'login' , 'admin' , 'Email' );
            Assert::same( 'login' , $t->data( )->name );
            Assert::same( 'admin' , $t->data( )->value );
            Assert::same( 'Email' , $t->data( )->placeholder );
        },

        'required / readonly / disabled' => function ( ) {
            $t = new visuoel_input_text( );
            $t->data( )->required( )->readonly( )->disabled( );
            Assert::true( $t->data( )->required );
            Assert::true( $t->data( )->readonly );
            Assert::true( $t->data( )->disabled );
        },

        'init vt_start/vt_end' => function ( ) {
            $r = new visuoel( );
            $t = $r->input( )->text( );
            $r->init( );
            Assert::instance( 'visuotag' , $t->vt_start );
            Assert::same( $t->vt_start , $t->vt_end );
        },

        'HTML содержит &lt;input type="text"&gt;' => function ( ) {
            $r = new visuoel( );
            $t = $r->input( )->text( );
            $t->data( )->set( 'login' )->placeholder( 'Email' );
            $r->init( );
            $html = $r->release( );
            Assert::contains( 'type="text"' , $html );
            Assert::contains( 'name="login"' , $html );
            Assert::contains( 'placeholder="Email"' , $html );
        },

        'HTML содержит class="input"' => function ( ) {
            $r = new visuoel( );
            $r->input( )->text( );
            $r->init( );
            Assert::contains( 'class="input"' , $r->release( ) );
        },

        'полный сценарий: форма логина' => function ( ) {
            $ve = new visuoel( );
            $form = $ve->form( )->form( );
            $form->data( )->set( 'login' , 'post' , '/auth' , title_text : 'Вход' );

            $csrf = new visuoel( );
            $csrf->input( )->hidden( )->data( )->set( 'csrf' , 'abc123' );
            $login = new visuoel( );
            $login->input( )->text( )->data( )->set( 'login' )->placeholder( 'Email' )->required( );
            $pass = new visuoel( );
            $pass->input( )->password( )->data( )->set( 'password' )->required( );
            $submit = new visuoel( );
            $submit->input( )->submit( )->data( )->set( 'submit' )->value( 'Войти' );

            $form->data( )->add( $csrf )->add( $login )->add( $pass )->add( $submit );

            $ve->init( );
            $html = $ve->release( );

            Assert::contains( '<form' , $html );
            Assert::contains( 'action="/auth"' , $html );
            Assert::contains( 'type="hidden"' , $html );
            Assert::contains( 'name="csrf"' , $html );
            Assert::contains( 'type="text"' , $html );
            Assert::contains( 'type="password"' , $html );
            Assert::contains( 'type="submit"' , $html );
            Assert::contains( 'value="Войти"' , $html );
            Assert::contains( 'placeholder="Email"' , $html );
            Assert::contains( 'required' , $html );
        },

        'XSS в placeholder — экранирован' => function ( ) {
            $r = new visuoel( );
            $t = $r->input( )->text( );
            $t->data( )->set( 'x' )->placeholder( '"><script>alert(1)</script>' );
            $r->init( );
            $html = $r->release( );
            Assert::notContains( '<script>' , $html );
        },

    ],
];