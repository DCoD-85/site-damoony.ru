<?PHP

# ============================================================
#   Suite: visuoel_button
# ============================================================

return [

    'title' => 'visuoel_button',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoels.php';
    },

    'tests' => [

        'class_exists visuoel_button' => function ( ) {
            Assert::true( class_exists( 'visuoel_button' ) );
        },
        'class_exists visuoel_button_submit' => function ( ) {
            Assert::true( class_exists( 'visuoel_button_submit' ) );
        },
        'class_exists visuoel_button_reset' => function ( ) {
            Assert::true( class_exists( 'visuoel_button_reset' ) );
        },
        'class_exists visuoel_button_button' => function ( ) {
            Assert::true( class_exists( 'visuoel_button_button' ) );
        },

        'visuoel_button extends visuoel_group' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_button' , 'visuoel_group' ) );
        },
        'visuoel_button_submit extends button__element' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_button_submit' , 'visuoel_button__element' ) );
        },

        '->button() returns visuoel_button' => function ( ) {
            $r = new visuoel( );
            $g = $r->button( );
            Assert::instance( 'visuoel_button' , $g );
            Assert::same( $r , $g->parent );
        },
        '->submit() returns visuoel_button_submit' => function ( ) {
            $r = new visuoel( );
            $g = $r->button( );
            $s = $g->submit( );
            Assert::instance( 'visuoel_button_submit' , $s );
            Assert::same( $g , $s->parent );
        },
        '->reset() returns visuoel_button_reset' => function ( ) {
            $g = ( new visuoel( ) )->button( );
            Assert::instance( 'visuoel_button_reset' , $g->reset( ) );
        },
        '->button() returns visuoel_button_button' => function ( ) {
            $g = ( new visuoel( ) )->button( );
            Assert::instance( 'visuoel_button_button' , $g->button( ) );
        },

        'multi: три кнопки — три элемента' => function ( ) {
            $g = ( new visuoel( ) )->button( );
            $g->submit( );
            $g->reset( );
            $g->button( );
            Assert::count( 3 , $g->elements );
        },

        'data->set() текст кнопки' => function ( ) {
            $s = ( new visuoel( ) )->button( )->submit( );
            $s->data( )->set( 'Отправить' );
            Assert::same( 'Отправить' , $s->data( )->text );
        },

        'name / value / css_class' => function ( ) {
            $b = ( new visuoel( ) )->button( )->button( );
            $b->data( )->name( 'action' )->value( 'x' )->css_class( 'btn_primary' );
            Assert::same( 'action' , $b->data( )->name );
            Assert::same( 'x' , $b->data( )->value );
            Assert::same( 'btn_primary' , $b->data( )->css_class );
        },

        'release содержит три типа кнопок' => function ( ) {
            $r = new visuoel( );
            $g = $r->button( );
            $g->submit( )->data( )->set( 'Send' );
            $g->reset( )->data( )->set( 'Reset' );
            $g->button( )->data( )->set( 'Go' );
            $r->init( );
            $html = $r->release( );

            Assert::contains( 'type="submit"' , $html );
            Assert::contains( 'type="reset"' , $html );
            Assert::contains( 'type="button"' , $html );
            Assert::contains( 'class="button button_submit"' , $html );
            Assert::contains( 'class="button button_reset"' , $html );
            Assert::contains( 'class="button button_button"' , $html );
            Assert::contains( '>Send</button>' , $html );
            Assert::contains( '>Reset</button>' , $html );
            Assert::contains( '>Go</button>' , $html );
        },

        'disabled' => function ( ) {
            $r = new visuoel( );
            $s = $r->button( )->submit( );
            $s->data( )->set( 'X' )->disabled( );
            $r->init( );
            Assert::contains( 'disabled="disabled"' , $r->release( ) );
        },

        'name / value в HTML' => function ( ) {
            $r = new visuoel( );
            $b = $r->button( )->button( );
            $b->data( )->set( 'Go' )->name( 'action' )->value( 'x' );
            $r->init( );
            $html = $r->release( );
            Assert::contains( 'name="action"' , $html );
            Assert::contains( 'value="x"' , $html );
        },

        'XSS в тексте кнопки — экранирован' => function ( ) {
            $r = new visuoel( );
            $s = $r->button( )->submit( );
            $s->data( )->set( '<script>alert(1)</script>' );
            $r->init( );
            $html = $r->release( );
            Assert::notContains( '<script>' , $html );
            Assert::contains( '&lt;script&gt;' , $html );
        },

    ],
];