<?PHP

# ============================================================
#   Suite: visuoel_table
# ============================================================

return [

    'title' => 'visuoel_table',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuoels.php';
    },

    'tests' => [

        'class_exists visuoel_table' => function ( ) {
            Assert::true( class_exists( 'visuoel_table' ) );
        },
        'class_exists visuoel_table_section' => function ( ) {
            Assert::true( class_exists( 'visuoel_table_section' ) );
        },
        'class_exists visuoel_table_row' => function ( ) {
            Assert::true( class_exists( 'visuoel_table_row' ) );
        },
        'class_exists visuoel_table_cell' => function ( ) {
            Assert::true( class_exists( 'visuoel_table_cell' ) );
        },

        'дивный режим: div.visuotable' => function ( ) {
            $ve = new visuoel( );
            $t = $ve->table( );
            $t->row( )->cell( width : 30 )->set( 'Логин' );
            $t->row( )->cell( width : 70 )->set( 'Значение' );
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<div class="visuotable">' , $h );
            Assert::contains( '<div class="ttype tbody">' , $h );
            Assert::contains( '<div class="row">' , $h );
            Assert::contains( 'class="col cols_30"' , $h );
            Assert::contains( 'Логин' , $h );
            Assert::contains( 'Значение' , $h );
        },

        'дивный режим: colelsinline' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $r  = $t->row( );
            $r->cell( width : 30 , inline : true )->set( 'A' );
            $ve->init( );
            Assert::contains( 'colelsinline' , $ve->release( ) );
        },

        'html_mode: table' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $t->html_mode( )->attr( 'border' , '1' )->attr( 'cellpadding' , '10' );
            $t->row( )->cell( )->set( 'X' );
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( '<table class="visuotable_html"' , $h );
            Assert::contains( 'border="1"' , $h );
            Assert::contains( 'cellpadding="10"' , $h );
            Assert::contains( '<tbody>' , $h );
            Assert::contains( '<tr>' , $h );
            Assert::contains( '<td>' , $h );
        },

        'html_mode: rowspan / colspan' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $t->html_mode( );
            $r1 = $t->row( );
            $r1->cell( rowspan : 2 )->set( 'V' );
            $r1->cell( )->set( 'A' );
            $r2 = $t->row( );
            $r2->cell( colspan : 2 )->set( 'B' );
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( 'rowspan="2"' , $h );
            Assert::contains( 'colspan="2"' , $h );
        },

        'head / body / foot' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $t->head( )->row( )->cell( )->set( 'H' );
            $t->body( )->row( )->cell( )->set( 'B' );
            $t->foot( )->row( )->cell( )->set( 'F' );
            $ve->init( );
            $h = $ve->release( );

            Assert::contains( 'ttype thead' , $h );
            Assert::contains( 'ttype tbody' , $h );
            Assert::contains( 'ttype tfoot' , $h );
        },

        'ячейка с вложенным field' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $c  = $t->row( )->cell( width : 100 );

            $inner = new visuoel( );
            $inner->field( )->text( )->data( )->set( 'q' )->label( 'Поиск' );
            $c->add( $inner );

            $ve->init( );
            Assert::contains( 'Поиск' , $ve->release( ) );
        },

        'width в дивном режиме ставится' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $t->row( )->cell( width : 42 )->set( 'X' );
            $ve->init( );
            Assert::contains( 'cols_42' , $ve->release( ) );
        },

        'XSS в cell->set — экранирован' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $t->row( )->cell( )->set( '<script>alert(1)</script>' );
            $ve->init( );
            Assert::notContains( '<script>' , $ve->release( ) );
        },

        'cell->html — сырой HTML' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $t->row( )->cell( )->html( '<b>B</b>' );
            $ve->init( );
            Assert::contains( '<b>B</b>' , $ve->release( ) );
        },

        'html_mode: width через style' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            $t->html_mode( );
            $t->row( )->cell( width : 30 )->set( 'X' );
            $ve->init( );
            Assert::contains( 'style="width:30%;"' , $ve->release( ) );
        },

        'singleton=false: две таблицы' => function ( ) {
            $ve = new visuoel( );
            $t1 = $ve->table( );
            $t2 = $ve->table( );
            Assert::true( $t1 !== $t2 );
        },

        'section singleton' => function ( ) {
            $ve = new visuoel( );
            $t  = $ve->table( );
            Assert::same( $t->body( ) , $t->body( ) );
            Assert::same( $t->head( ) , $t->head( ) );
        },

    ],
];