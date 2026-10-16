<?PHP

# ============================================================
#   Suite: visuoel — база UI-элементов
# ============================================================

return [

    'title' => 'visuoel',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoels.php';
    },

    'tests' => [

        'class_exists visuoel' => function ( ) {
            Assert::true( class_exists( 'visuoel' ) );
        },
        'class_exists visuoel_group' => function ( ) {
            Assert::true( class_exists( 'visuoel_group' ) );
        },
        'class_exists visuoel_group_element' => function ( ) {
            Assert::true( class_exists( 'visuoel_group_element' ) );
        },
        'class_exists visuoel__data' => function ( ) {
            Assert::true( class_exists( 'visuoel__data' ) );
        },
        'class_exists visuoel_group__data' => function ( ) {
            Assert::true( class_exists( 'visuoel_group__data' ) );
        },
        'class_exists visuoel_group_element__data' => function ( ) {
            Assert::true( class_exists( 'visuoel_group_element__data' ) );
        },

        'visuoel_group extends visuoel' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_group' , 'visuoel' ) );
        },
        'visuoel_group_element extends visuoel' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_group_element' , 'visuoel' ) );
        },
        'visuoel_group__data extends visuoel__data' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_group__data' , 'visuoel__data' ) );
        },
        'visuoel_group_element__data extends visuoel__data' => function ( ) {
            Assert::true( is_subclass_of( 'visuoel_group_element__data' , 'visuoel__data' ) );
        },

        'root->parent === null' => function ( ) {
            $r = new visuoel( );
            Assert::null( $r->parent );
        },
        'root->visuoel === root' => function ( ) {
            $r = new visuoel( );
            Assert::same( $r , $r->visuoel );
        },
        'root->is_root() === true' => function ( ) {
            $r = new visuoel( );
            Assert::true( $r->is_root( ) );
        },
        'root->css_class() === visuoel' => function ( ) {
            $r = new visuoel( );
            Assert::same( 'visuoel' , $r->css_class( ) );
        },
        'root->data() instanceof visuoel__data' => function ( ) {
            $r = new visuoel( );
            Assert::instance( 'visuoel__data' , $r->data( ) );
        },
        'root->data()->node === root' => function ( ) {
            $r = new visuoel( );
            Assert::same( $r , $r->data( )->node );
        },

        'group->parent === root' => function ( ) {
            $r = new visuoel( );
            $g = new visuoel_group( $r );
            Assert::same( $r , $g->parent );
        },
        'group->visuoel === root' => function ( ) {
            $r = new visuoel( );
            $g = new visuoel_group( $r );
            Assert::same( $r , $g->visuoel );
        },
        'group->is_root() === false' => function ( ) {
            $r = new visuoel( );
            $g = new visuoel_group( $r );
            Assert::false( $g->is_root( ) );
        },
        'group->css_class() === group' => function ( ) {
            $g = new visuoel_group( new visuoel( ) );
            Assert::same( 'group' , $g->css_class( ) );
        },
        'group->data() instanceof visuoel_group__data' => function ( ) {
            $g = new visuoel_group( new visuoel( ) );
            Assert::instance( 'visuoel_group__data' , $g->data( ) );
        },

        'element->parent === group' => function ( ) {
            $r = new visuoel( );
            $g = new visuoel_group( $r );
            $e = new visuoel_group_element( $g );
            Assert::same( $g , $e->parent );
        },
        'element->visuoel === root' => function ( ) {
            $r = new visuoel( );
            $g = new visuoel_group( $r );
            $e = new visuoel_group_element( $g );
            Assert::same( $r , $e->visuoel );
        },
        'element->css_class() === element' => function ( ) {
            $e = new visuoel_group_element( new visuoel( ) );
            Assert::same( 'element' , $e->css_class( ) );
        },
        'element->data() instanceof visuoel_group_element__data' => function ( ) {
            $e = new visuoel_group_element( new visuoel( ) );
            Assert::instance( 'visuoel_group_element__data' , $e->data( ) );
        },
        'element->data()->readonly === false' => function ( ) {
            $e = new visuoel_group_element( new visuoel( ) );
            Assert::false( $e->data( )->readonly );
        },
        'element->data()->disabled === false' => function ( ) {
            $e = new visuoel_group_element( new visuoel( ) );
            Assert::false( $e->data( )->disabled );
        },

        'init() sets flag' => function ( ) {
            $r = new visuoel( );
            $r->init( );
            Assert::true( $r->init );
        },

        'custom __data attached' => function ( ) {
            $g = new visuoel_group( new visuoel( ) );
            $e = new visuoel_group_element( $g );
            $custom = new visuoel_group_element__data( $e );
            $custom->readonly = true;
            $e2 = new visuoel_group_element( $g , $custom );
            Assert::same( $custom , $e2->data( ) );
            Assert::true( $e2->data( )->readonly );
        },

        'tag() === div' => function ( ) {
            $r = new visuoel( );
            Assert::same( 'div' , $r->tag( ) );
        },

        'esc() escapes' => function ( ) {
            $r = new visuoel( );
            $d = $r->data( );
            Assert::same( '&lt;script&gt;' , $d->esc( '<script>' ) );
            Assert::same( '&quot;' , $d->esc( '"' ) );
        },

    ],
];