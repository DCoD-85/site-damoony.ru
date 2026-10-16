<?PHP

# ============================================================
#   Suite: blacklist
# ============================================================

return [

    'title' => 'blacklist',

    'setup' => function ( ) {
        require_once SOURCES . '/blacklist.php';

        if ( ! class_exists( 'test_blacklist' ) ) {
            class test_blacklist extends blacklist {
                public array $denied  = [ ];
                public int   $allowed = 0;
                public function deny  ( string $message , int $http_code = 0 ) : void {
                    $this->denied[ ] = [ 'message' => $message , 'code' => $http_code ];
                }
                public function allow ( ) : void { $this->allowed++; }
            }
        }

        $tmp = sys_get_temp_dir( ) . DIRECTORY_SEPARATOR . 'damoony2_bl_test' . DIRECTORY_SEPARATOR;
        if ( is_dir( $tmp ) ) {
            foreach ( glob( $tmp . '*' ) as $f ) if ( is_file( $f ) ) unlink( $f );
        } else {
            mkdir( $tmp , 0777 , true );
        }
        $GLOBALS[ 'bl_tmp' ] = $tmp;
    },

    'tests' => [

        'construct' => function ( ) {
            $b = new test_blacklist( );
            Assert::null( $b->main );
            Assert::true( $b->mode );
        },

        'blacklist missing file' => function ( ) {
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
            $b = new test_blacklist( null , $GLOBALS[ 'bl_tmp' ] );
            $b->check_blacklist( );
            Assert::count( 0 , $b->denied );
        },

        'blacklist IP matched' => function ( ) {
            file_put_contents( $GLOBALS[ 'bl_tmp' ] . 'blacklist.txt' , "10.0.0.1\n10.0.0.2\n" );
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
            $b = new test_blacklist( null , $GLOBALS[ 'bl_tmp' ] );
            $b->check_blacklist( );
            Assert::count( 1 , $b->denied );
            Assert::same( 0 , $b->denied[ 0 ][ 'code' ] );
        },

        'blacklist comments skipped' => function ( ) {
            file_put_contents( $GLOBALS[ 'bl_tmp' ] . 'blacklist.txt' , "# 10.0.0.1\n10.0.0.3\n" );
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
            $b = new test_blacklist( null , $GLOBALS[ 'bl_tmp' ] );
            $b->check_blacklist( );
            Assert::count( 0 , $b->denied );
        },

        'whitelist base file, IP allowed' => function ( ) {
            file_put_contents( $GLOBALS[ 'bl_tmp' ] . 'whitelist.txt' , "10.0.0.1\n" );
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';

            $m = new main( );
            $m->main_settings->allias_real_name = '';

            $b = new test_blacklist( $m , $GLOBALS[ 'bl_tmp' ] );
            $b->check_whitelist( );
            Assert::same( 1 , $b->allowed );
            Assert::count( 0 , $b->denied );
        },

        'whitelist allias file, IP allowed' => function ( ) {
            file_put_contents( $GLOBALS[ 'bl_tmp' ] . 'whitelist_damoony.ru_login.txt' , "10.0.0.1\n" );
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';

            $m = new main( );
            $m->main_settings->allias_real_name = 'login';

            $b = new test_blacklist( $m , $GLOBALS[ 'bl_tmp' ] );
            $b->check_whitelist( );
            Assert::same( 1 , $b->allowed );
        },

        'whitelist IP not listed → deny 404' => function ( ) {
            file_put_contents( $GLOBALS[ 'bl_tmp' ] . 'whitelist_damoony.ru_login.txt' , "10.0.0.1\n" );
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.99';

            $m = new main( );
            $m->main_settings->allias_real_name = 'login';

            $b = new test_blacklist( $m , $GLOBALS[ 'bl_tmp' ] );
            $b->check_whitelist( );
            Assert::count( 1 , $b->denied );
            Assert::same( 404 , $b->denied[ 0 ][ 'code' ] );
        },

        'whitelist missing file → no block' => function ( ) {
            foreach ( glob( $GLOBALS[ 'bl_tmp' ] . 'whitelist*' ) as $f ) unlink( $f );
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';

            $m = new main( );
            $m->main_settings->allias_real_name = '';
            $b = new test_blacklist( $m , $GLOBALS[ 'bl_tmp' ] );
            $b->check_whitelist( );
            Assert::same( 0 , $b->allowed );
            Assert::count( 0 , $b->denied );
        },

        'init → blacklist' => function ( ) {
            file_put_contents( $GLOBALS[ 'bl_tmp' ] . 'blacklist.txt' , "10.0.0.1\n" );
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
            $b = new test_blacklist( null , $GLOBALS[ 'bl_tmp' ] );
            $b->mode = true;
            $b->init( );
            Assert::count( 1 , $b->denied );
        },

        'init → whitelist' => function ( ) {
            file_put_contents( $GLOBALS[ 'bl_tmp' ] . 'whitelist.txt' , "10.0.0.1\n" );
            $_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';

            $m = new main( );
            $m->main_settings->allias_real_name = '';
            $b = new test_blacklist( $m , $GLOBALS[ 'bl_tmp' ] );
            $b->mode = false;
            $b->init( );
            Assert::same( 1 , $b->allowed );
        },

        'no REMOTE_ADDR' => function ( ) {
            unset( $_SERVER[ 'REMOTE_ADDR' ] );
            $b = new test_blacklist( null , $GLOBALS[ 'bl_tmp' ] );
            $b->check_blacklist( );
            $b->check_whitelist( );
            Assert::true( true );
        },

    ],
];