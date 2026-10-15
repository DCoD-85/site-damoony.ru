<?PHP

# ============================================================
#   Suite: visuotag
#   Базовый билдер дерева + find($eq) + attrs + releasetree.
# ============================================================

return [

    'title' => 'visuotag',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoattrs/visuoattrsclass.php';
        require_once VISUO_SRC . '/visuoattrs/visuoattrsstyle.php';
        require_once VISUO_SRC . '/visuoattrs/visuoattrsactions.php';
        require_once VISUO_SRC . '/visuoattrs/visuoattrsdata.php';
        require_once VISUO_SRC . '/visuoattrs.php';
        require_once VISUO_SRC . '/visuotag.php';

        # stub для stage-5 hooks
        if ( ! class_exists( 'visuostruct' ) ) {
            class visuostruct { }
        }
        if ( ! class_exists( 'visuometa' ) ) {
            class visuometa {
                public function as_tag ( ) : visuotag {
                    $vt = new visuotag( );
                    $vt->tag( 'title' )->content( 'stub' );
                    return $vt;
                }
            }
        }
    },

    'tests' => [

        # ============================================================
        #  Baseline
        # ============================================================

        'empty root' => function ( ) {
            $r = new visuotag( );
            Assert::eq( 'root' , $r->tagname );
            Assert::true( $r->root );
            Assert::true( $r->tag_closed );
            Assert::null( $r->visuotag );
            Assert::count( 0 , $r->elements );
        },

        'root release empty' => function ( ) {
            $r = new visuotag( );
            Assert::eq( '' , $r->release( ) );
        },

        'tag child' => function ( ) {
            $r = new visuotag( );
            $div = $r->tag( 'div' );
            Assert::eq( 'div' , $div->tagname );
            Assert::false( $div->root );
            Assert::same( $r , $div->visuotag );
            Assert::count( 1 , $r->elements );
        },

        'content' => function ( ) {
            $r = new visuotag( );
            $div = $r->tag( 'div' )->content( 'hello' );
            Assert::count( 1 , $div->elements );
            Assert::same( 'hello' , $div->elements[ 0 ] );
        },

        'release tree' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->content( 'hello' );
            Assert::eq( '<div>hello</div>' , $r->release( ) );
        },

        'self-closing' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'img' , false )->attrs_add( 'src' , '/test.png' );
            Assert::eq( '<img src="/test.png" />' , $r->release( ) );
        },

        'nested' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->tag( 'span' )->content( 'x' );
            Assert::eq( '<div><span>x</span></div>' , $r->release( ) );
        },

        'parent navigation' => function ( ) {
            $r = new visuotag( );
            $d = $r->tag( 'div' );
            $s = $d->tag( 'span' );
            Assert::same( $d , $s->parent( ) );
            Assert::same( $r , $d->parent( ) );
            Assert::same( $r , $s->root( ) );
        },

        'parent with tagname' => function ( ) {
            $r = new visuotag( );
            $d = $r->tag( 'div' );
            $s = $d->tag( 'span' );
            $p = $s->tag( 'p' );
            Assert::same( $d , $p->parent( 'div' ) );
            Assert::same( $d , $p->div( ) );
        },

        'append' => function ( ) {
            $r1 = new visuotag( );
            $r1->tag( 'a' );
            $r2 = new visuotag( );
            $r2->tag( 'b' )->tag( 'c' );
            $r1->append( $r2 );
            Assert::count( 2 , $r1->elements );
            Assert::eq( '<a></a><b><c></c></b>' , $r1->release( ) );
        },

        'prepend' => function ( ) {
            $r1 = new visuotag( );
            $r1->tag( 'a' );
            $r2 = new visuotag( );
            $r2->tag( 'b' );
            $r1->prepend( $r2 );
            Assert::same( 'b' , $r1->elements[ 0 ]->tagname );
            Assert::same( 'a' , $r1->elements[ 1 ]->tagname );
        },

        'clear' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'a' )->tag( 'b' );
            $r->clear( );
            Assert::count( 0 , $r->elements );
        },

        'attrs_add' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->attrs_add( 'id' , 'main' );
            Assert::eq( '<div id="main"></div>' , $r->release( ) );
        },

        'attrs_class_add' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->attrs_class_add( 'box' );
            Assert::eq( '<div class="box"></div>' , $r->release( ) );
        },

        'data_add' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->data_add( 'ajax' , true );
            Assert::eq( '<div data-ajax="true"></div>' , $r->release( ) );
        },

        'id' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->id( 'foo' );
            Assert::eq( '<div id="foo"></div>' , $r->release( ) );
        },

        'next' => function ( ) {
            $r = new visuotag( );
            $r->content( 'first' );
            Assert::same( 'first' , $r->next( ) );
        },

        'tag with named params' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' , id : 'x' , class : 'y' , style : 'color:red' );
            $out = $r->release( );
            Assert::contains( 'id="x"' , $out );
            Assert::contains( 'class="y"' , $out );
            Assert::contains( 'style="color:red;"' , $out );
        },

        'final' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->content( 'x' );
            Assert::eq( '<div>x</div>' , $r->final( ) );
        },

        # ============================================================
        #  Stage 2 — find
        # ============================================================

        'find deep' => function ( ) {
            $r = new visuotag( );
            $a = $r->tag( 'div' );
            $b = $a->tag( 'section' );
            $c = $b->tag( 'span' );
            Assert::same( $c , $r->find( 'span' ) );
        },

        'find by class' => function ( ) {
            $r = new visuotag( );
            $a = $r->tag( 'div' );
            $a->attrs_class_add( 'box' );
            $r->tag( 'div' );
            Assert::same( $a , $r->find( class : 'box' ) );
        },

        'find tag + class' => function ( ) {
            $r = new visuotag( );
            $a = $r->tag( 'div' );
            $b = $r->tag( 'div' );
            $b->attrs_class_add( 'x' );
            Assert::same( $b , $r->find( 'div' , class : 'x' ) );
        },

        'find no match' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' );
            Assert::null( $r->find( 'span' ) );
        },

        'find by id' => function ( ) {
            $r = new visuotag( );
            $a = $r->tag( 'div' );
            $a->id( 'first' );
            $b = $r->tag( 'div' );
            $b->id( 'second' );
            Assert::same( $b , $r->find( id : 'second' ) );
        },

        'find eq=1 (second div)' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' );
            $d2 = $r->tag( 'div' );
            Assert::same( $d2 , $r->find( 'div' , null , null , 1 ) );
        },

        'find eq=2 (third div)' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' );
            $r->tag( 'div' );
            $d3 = $r->tag( 'div' );
            Assert::same( $d3 , $r->find( 'div' , null , null , 2 ) );
        },

        'parent eq' => function ( ) {
            $r = new visuotag( );
            $d1 = $r->tag( 'div' );
            $d2 = $d1->tag( 'div' );
            $s  = $d2->tag( 'span' );
            Assert::same( $d2 , $s->div( 0 ) );
            Assert::same( $d1 , $s->div( 1 ) );
        },

        'parent eq negative' => function ( ) {
            $r = new visuotag( );
            $d = $r->tag( 'div' );
            Assert::same( $d , $d->parent( 'div' , -1 ) );
        },

        'find missing in deep tree' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->tag( 'section' )->tag( 'article' );
            Assert::null( $r->find( 'aside' ) );
        },

        'find first match' => function ( ) {
            $r = new visuotag( );
            $first = $r->tag( 'span' );
            $r->tag( 'span' );
            Assert::same( $first , $r->find( 'span' ) );
        },

        '_body when missing' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' );
            Assert::null( $r->_body( ) );
            Assert::null( $r->_html( ) );
        },

        # ============================================================
        #  Stage 3 — attrs
        # ============================================================

        'attrs() type' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' );
            Assert::instance( 'visuoattrs' , $r->elements[ 0 ]->attrs( ) );
        },

        'attrs_add chain' => function ( ) {
            $r = new visuotag( );
            $v = $r->tag( 'div' )->attrs_add( 'id' , 'x' )->attrs_add( 'data-x' , 'y' );
            Assert::instance( 'visuotag' , $v );
            Assert::eq( '<div id="x" data-x="y"></div>' , $r->release( ) );
        },

        'attrs_remove' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->attrs_add( 'id' , 'x' )->attrs_remove( 'id' );
            Assert::eq( '<div></div>' , $r->release( ) );
        },

        'class add/remove' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->attrs_class_add( 'a b' )->attrs_class_remove( 'a' );
            Assert::eq( '<div class="b"></div>' , $r->release( ) );
        },

        'style add/remove' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->attrs_style_add( 'color:red;font-size:14px' )->attrs_style_remove( 'color' );
            Assert::eq( '<div style="font-size:14px;"></div>' , $r->release( ) );
        },

        'actions add/remove' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->attrs_actions_add( 'onclick' , 'a()' )->attrs_actions_add( 'onmouseover' , 'b()' )->attrs_actions_remove( 'onclick' );
            Assert::contains( 'onmouseover="b()"' , $r->release( ) );
            Assert::notContains( 'onclick' , $r->release( ) );
        },

        'data add/remove' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->data_add( 'ajax' , true )->data_add( 'role' , 'admin' , false )->data_remove( 'ajax' );
            Assert::eq( '<div data-role="admin"></div>' , $r->release( ) );
        },

        'data() type' => function ( ) {
            $r = new visuotag( );
            $d = $r->tag( 'div' )->data( );
            Assert::instance( 'visuoattrsdata' , $d );
        },

        'full combination' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'a' )->id( 'link' )->attrs_class_add( 'btn' )
                ->attrs_style_add( 'color:blue' )->attrs_actions_add( 'onclick' , 'go()' )
                ->data_add( 'role' , 'nav' , false );
            $out = $r->release( );
            Assert::contains( 'class="btn"' , $out );
            Assert::contains( 'style="color:blue;"' , $out );
            Assert::contains( 'onclick="go()"' , $out );
            Assert::contains( 'id="link"' , $out );
            Assert::contains( 'data-role="nav"' , $out );
        },

        # ============================================================
        #  Stage 4 — release
        # ============================================================

        'release — mixed content' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->content( 'a' )->tag( 'span' )->content( 'b' );
            Assert::eq( '<div>a<span>b</span></div>' , $r->release( ) );
        },

        'release — deep nesting' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->tag( 'section' )->tag( 'article' )->tag( 'p' )->content( 'deep' );
            Assert::eq( '<div><section><article><p>deep</p></article></section></div>' , $r->release( ) );
        },

        'release — attrs everywhere' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->id( 'a' )->tag( 'span' )->attrs_class_add( 'inner' )->content( 'x' );
            Assert::eq( '<div id="a"><span class="inner">x</span></div>' , $r->release( ) );
        },

        'final from nested' => function ( ) {
            $r = new visuotag( );
            $inner = $r->tag( 'div' )->tag( 'span' );
            $inner->content( 'x' );
            Assert::eq( '<div><span>x</span></div>' , $inner->final( ) );
        },

        'release — self-closing + data' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'input' , false )->attrs_add( 'type' , 'text' )->data_add( 'role' , 'name' , false );
            Assert::eq( '<input type="text" data-role="name" />' , $r->release( ) );
        },

        'prepare — empty' => function ( ) {
            $r = new visuotag( );
            $r->prepare( );
            Assert::true( true );
        },

        'prepare — tree' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->tag( 'span' )->content( 'x' );
            $r->prepare( );
            Assert::true( true );
        },

        'releasetree — simple' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->id( 'a' )->content( 'x' );
            $tree = $r->releasetree( );
            Assert::contains( 'debugtree_row' , $tree );
            Assert::contains( '[ content ]' , $tree );
        },

        'releasetree — classname' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' );
            $tree = $r->releasetree( );
            Assert::contains( 'classname: visuotag' , $tree );
        },

        'tree() no crash' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' );
            $r->tree( );
            Assert::true( true );
        },

        'release — empty content' => function ( ) {
            $r = new visuotag( );
            $r->tag( 'div' )->content( '' );
            Assert::eq( '<div></div>' , $r->release( ) );
        },

        # ============================================================
        #  Stage 5 — structv / findstruct
        # ============================================================

        'structv — clean tag' => function ( ) {
            $r = new visuotag( );
            $st = $r->structv( 'main' );
            Assert::eq( 'struct' , $st->tagname );
            Assert::true( $st->attrs( )->class( )->has( 'cl_struct' ) );
            Assert::false( $st->attrs( )->has( 'id' ) );
            Assert::eq( '<struct class="cl_struct"></struct>' , $r->release( ) );
        },

        'structv — returns child' => function ( ) {
            $r = new visuotag( );
            $st = $r->structv( 'main' );
            Assert::same( $st , $r->elements[ 0 ] );
        },

		'el — creates cl_el' => function ( ) {
            $r = new visuotag( );
            $r->el( );
            Assert::count( 1 , $r->elements );
            Assert::eq( 'el' , $r->elements[ 0 ]->tagname );
            Assert::true( $r->elements[ 0 ]->attrs( )->class( )->has( 'cl_el' ) );
        },

        'el — returns $this' => function ( ) {
            $r = new visuotag( );
            $ret = $r->el( );
            Assert::same( $r , $ret );
        },

        'meta — append' => function ( ) {
            $r = new visuotag( );
            $m = new visuometa( );
            $r->meta( $m );
            Assert::count( 1 , $r->elements );
            Assert::contains( '<title>' , $r->release( ) );
        },

        'findstruct — named' => function ( ) {
            $r = new visuotag( );
            $r->structv( 'alpha' );
            $st2 = $r->structv( 'beta' );
            $found = [ ];
            $ref = null;
            $r->findstruct( 'beta' , $found , $ref );
            Assert::count( 1 , $found );
            Assert::same( $st2 , $found[ 0 ] );
            Assert::same( $st2 , $ref );
        },

        'findstruct — deep' => function ( ) {
            $r = new visuotag( );
            $outer = $r->tag( 'div' );
            $st = $outer->structv( 'deep' );
            $found = [ ];
            $ref = null;
            $r->findstruct( 'deep' , $found , $ref );
            Assert::count( 1 , $found );
            Assert::same( $st , $found[ 0 ] );
        },

        'findstruct — no match' => function ( ) {
            $r = new visuotag( );
            $r->structv( 'alpha' );
            $found = [ ];
            $ref = null;
            $r->findstruct( 'gamma' , $found , $ref );
            Assert::count( 0 , $found );
            Assert::null( $ref );
        },

    ],
];