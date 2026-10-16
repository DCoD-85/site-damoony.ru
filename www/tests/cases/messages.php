<?PHP

# ============================================================
#   Suite: messages — стек UI-сообщений
# ============================================================

return [

    'title' => 'messages',

    'setup' => function ( ) {
        require_once VISUO_SRC . '/visuotag.php';
        require_once VISUO_SRC . '/visuometa.php';
        require_once VISUO_SRC . '/visuolink.php';
        require_once SOURCES   . '/messages.php';
    },

    'tests' => [

        'empty state' => function ( ) {
            $m = new messages( new main( ) );
            Assert::same( 0 , $m->count( ) );
            Assert::false( $m->has( ) );
            Assert::eq( '' , $m->release( ) );
        },

        'add 3' => function ( ) {
            $m = new messages( new main( ) );
            $m->add( 'error'   , 'Ошибка'         , 'Плохо' );
            $m->add( 'success' , 'Успех'          , 'Хорошо' );
            $m->add( 'warning' , 'Предупреждение' , 'Проверь' );
            Assert::same( 3 , $m->count( ) );
            Assert::true( $m->has( ) );
        },

        'release содержит статусы' => function ( ) {
            $m = new messages( new main( ) );
            $m->add( 'error'   , '' , 'E' );
            $m->add( 'success' , '' , 'S' );
            $m->add( 'warning' , '' , 'W' );
            $html = $m->release( );
            Assert::contains( 'message_error'   , $html );
            Assert::contains( 'message_success' , $html );
            Assert::contains( 'message_warning' , $html );
        },

        'unknown status → console' => function ( ) {
            $m = new messages( new main( ) );
            $m->add( 'hacker_status' , 'X' , 'Y' );
            Assert::contains( 'message_console' , $m->release( ) );
        },

        'clear' => function ( ) {
            $m = new messages( new main( ) );
            $m->add( 'error' , 'X' , 'Y' );
            $m->clear( );
            Assert::same( 0 , $m->count( ) );
            Assert::eq( '' , $m->release( ) );
        },

    ],
];