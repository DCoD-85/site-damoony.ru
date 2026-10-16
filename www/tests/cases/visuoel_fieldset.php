<?PHP

# ============================================================
#   Suite: visuoel_fieldset
# ============================================================

return [

    'title' => 'visuoel_fieldset',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoels.php';
    },

    'tests' => [

        'class_exists visuoel_form_fieldset' => function ( ) {
            Assert::true( class_exists( 'visuoel_form_fieldset' ) );
        },

        'fieldset базовый рендер' => function ( ) {
            $ve = new visuoel( );
            $form = $ve->form( )->form( );
            $form->data( )->set( 'reg' , 'post' , '/reg' );

            $fs = $form->fieldset( );
            $fs->data( )->set( 'Личные данные' );

            $inner = new visuoel( );
            $inner->field( )->text( )->data( )->set( 'login' )->label( 'Логин' )->required( );
            $fs->add( $inner );

            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<fieldset class="form_fieldset">' , $h );
            Assert::contains( '<legend>Личные данные</legend>' , $h );
            Assert::contains( '<div class="field field_text">' , $h );
            Assert::contains( 'for="field_login"' , $h );
            Assert::contains( '>Логин</label>' , $h );
            Assert::contains( '</fieldset>' , $h );
        },

        'legend пустой — не рисуется' => function ( ) {
            $ve = new visuoel( );
            $form = $ve->form( )->form( );
            $form->data( )->set( 'f' , 'post' );
            $fs = $form->fieldset( );
            $ve->init( );
            Assert::notContains( '<legend>' , $ve->release( ) );
        },

        'fieldset extends group_element' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_form_fieldset' , 'visuoel_group_element' ) );
        },

        'fieldset__data extends group_element__data' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_form_fieldset__data' , 'visuoel_group_element__data' ) );
        },

        'XSS в legend — экранирован' => function ( ) {
            $ve = new visuoel( );
            $form = $ve->form( )->form( );
            $form->data( )->set( 'f' , 'post' );
            $fs = $form->fieldset( );
            $fs->data( )->set( '<script>alert(1)</script>' );
            $ve->init( );
            $h = $ve->release( );
            Assert::notContains( '<script>' , $h );
            Assert::contains( '&lt;script&gt;' , $h );
        },

    ],
];