<?PHP

# ============================================================
#   Suite: language — строки локализации
# ============================================================

return [

    'title' => 'language',

    'setup' => function ( ) {
        require_once SOURCES . '/language.php';
    },

    'tests' => [

        'load' => function ( ) {
            $l = new language( new main( ) );
            $l->load( );
            Assert::same( 'ru-ru' , $l->language );
            Assert::true( is_array( $l->strings ) );
        },

        'get existing key' => function ( ) {
            $l = new language( new main( ) );
            $l->load( );
            $val = $l->get( 'NONEXISTENT_KEY_XYZ' , [ ] , 'Default value' );
            Assert::same( 'Default value' , $val );
        },

        'get missing → default + tosave' => function ( ) {
            $l = new language( new main( ) );
            $l->load( );
            $l->get( 'SOME_RANDOM_KEY_42' , [ ] , 'fallback' );
            Assert::true( array_key_exists( 'SOME_RANDOM_KEY_42' , $l->strings_tosave ) );
        },

        'get with params' => function ( ) {
            $l = new language( new main( ) );
            $l->load( );
            $val = $l->get( 'HELLO_TEST_KEY' , [ 'Вася' ] , 'Привет, %s' );
            Assert::same( 'Привет, Вася' , $val );
        },

        'savedefaults no crash' => function ( ) {
            $l = new language( new main( ) );
            $l->load( );
            $l->get( 'SAVE_TEST_KEY' , [ ] , 'value' );
            $l->savedefaults( );
            Assert::true( true );
        },

        'set_language chain' => function ( ) {
            $l = new language( new main( ) );
            Assert::same( $l , $l->set_language( 'ru-ru' ) );
        },

        'current' => function ( ) {
            $l = new language( new main( ) );
            Assert::same( 'ru-ru' , $l->current( ) );
        },

    ],
];