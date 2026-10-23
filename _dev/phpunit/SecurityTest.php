<?PHP

use PHPUnit\Framework\TestCase;

/**
 * Тесты Security.
 *
 * Требования:
 *  - bootstrap PHPUnit должен определить PROJECT_ROOT (реальный корень проекта).
 *  - Security.php подключается лениво, чтобы не тянуть Fs/Json из admin bootstrap.
 *
 * Изоляция:
 *  - setUp: бэкапит реальные config/admin.security.config и secure/admins.config,
 *           удаляет их, сбрасывает статики Security.
 *  - tearDown: восстанавливает бэкап (или удаляет, если файла до теста не было).
 *  - register_shutdown_function в setUpBeforeClass гарантирует восстановление
 *    даже при фатальном падении.
 */
class SecurityTest extends TestCase {

    /** @var array<string,string> Относительный путь => содержимое */
    private static array $backups = [ ];

    private static array $managed_paths = [
        'config/admin.security.config' ,
        'secure/admins.config' ,
    ];

    public static function setUpBeforeClass () : void {
        if ( ! defined( 'PROJECT_ROOT' ) ) {
            define( 'PROJECT_ROOT' , dirname( __DIR__ , 2 ) );
        }
        require_once PROJECT_ROOT . '/www/admin/_lib/Security.php';

        # снимаем бэкап ОДИН раз — если что-то упадёт, восстановим в shutdown
        foreach ( self::$managed_paths as $rel ) {
            $p = PROJECT_ROOT . '/' . $rel;
            if ( is_file( $p ) ) self::$backups[ $rel ] = (string) file_get_contents( $p );
        }

        register_shutdown_function( static function () : void {
            foreach ( self::$backups as $rel => $content ) {
                $p = PROJECT_ROOT . '/' . $rel;
                @mkdir( dirname( $p ) , 0777 , true );
                @file_put_contents( $p , $content );
            }
        } );
    }

    protected function setUp () : void {
        # очищаем реальные файлы — тесты работают с «чистым листом»
        foreach ( self::$managed_paths as $rel ) {
            @unlink( PROJECT_ROOT . '/' . $rel );
        }
        $this->resetStatics();
    }

    protected function tearDown () : void {
        foreach ( self::$managed_paths as $rel ) {
            $p = PROJECT_ROOT . '/' . $rel;
            @unlink( $p );
            if ( isset( self::$backups[ $rel ] ) ) {
                @mkdir( dirname( $p ) , 0777 , true );
                @file_put_contents( $p , self::$backups[ $rel ] );
            }
        }
        $this->resetStatics();
    }

    # ------------------------------------------------------------------
    # helpers
    # ------------------------------------------------------------------

    private function resetStatics () : void {
        $r = new ReflectionClass( Security::class );
        $r->setStaticPropertyValue( 'cfg' , null );
        $r->setStaticPropertyValue( 'admins' , null );
    }

    private function writeJson ( string $rel , array $data ) : void {
        $p = PROJECT_ROOT . '/' . $rel;
        @mkdir( dirname( $p ) , 0777 , true );
        file_put_contents( $p , json_encode( $data , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        $this->resetStatics();
    }

    private function seedUser ( string $login , string $pass = 'secret' , array $devices = [] ) : array {
        $admins = [ 'users' => [ $login => [
            'password_hash' => password_hash( $pass , PASSWORD_DEFAULT ) ,
            'role' => 'admin' ,
            'devices' => $devices ,
        ] ] , 'invites' => [] ];
        $this->writeJson( 'secure/admins.config' , $admins );
        return $admins;
    }

    private function makeDevice ( string $canvas , string $webgl = 'wg' , string $system = 'sys' ) : array {
        return [
            'id' => 'dev_' . substr( md5( $canvas . $webgl . $system ) , 0 , 8 ) ,
            'canvas' => $canvas , 'webgl' => $webgl , 'system' => $system ,
            'ua' => 'UA' , 'last_ip' => '127.0.0.1' , 'last_seen' => time() - 3600 ,
            'label' => 'Test' ,
        ];
    }

    private function patchInvite ( string $token , array $patch ) : void {
        $admins = Security::get_data();
        $admins['invites'][ $token ] = array_merge( $admins['invites'][ $token ] ?? [] , $patch );
        $this->writeJson( 'secure/admins.config' , $admins );
    }

    # ------------------------------------------------------------------
    # is_whitelisted
    # ------------------------------------------------------------------

    public function test_is_whitelisted_whitelist_mode_localhost () : void {
        $this->assertTrue( Security::is_whitelisted( '127.0.0.1' ) );
        $this->assertTrue( Security::is_whitelisted( '::1' ) );
    }

    public function test_is_whitelisted_whitelist_mode_denies_foreign_ip () : void {
        $this->assertFalse( Security::is_whitelisted( '8.8.8.8' ) );
    }

    public function test_is_whitelisted_whitelist_mode_custom_ip () : void {
        $this->writeJson( 'config/admin.security.config' , [
            'mode' => 'whitelist' ,
            'ip_whitelist' => [ '10.0.0.1' ] ,
        ] );
        $this->assertTrue( Security::is_whitelisted( '10.0.0.1' ) );
        $this->assertFalse( Security::is_whitelisted( '8.8.8.8' ) );
    }

    public function test_is_whitelisted_blacklist_mode_allows_all () : void {
        $this->writeJson( 'config/admin.security.config' , [ 'mode' => 'blacklist' ] );
        $this->assertTrue( Security::is_whitelisted( '8.8.8.8' ) );
        $this->assertTrue( Security::is_whitelisted( '127.0.0.1' ) );
    }

    # ------------------------------------------------------------------
    # requires_fingerprint
    # ------------------------------------------------------------------

    public function test_requires_fingerprint_default_true () : void {
        $this->assertTrue( Security::requires_fingerprint() );
    }

    public function test_requires_fingerprint_from_config () : void {
        $this->writeJson( 'config/admin.security.config' , [ 'require_fingerprint' => false ] );
        $this->assertFalse( Security::requires_fingerprint() );
    }

    # ------------------------------------------------------------------
    # find_device
    # ------------------------------------------------------------------

    public function test_find_device_empty_canvas_returns_null () : void {
        $this->assertNull( Security::find_device( [ 'canvas' => '' , 'webgl' => 'x' , 'system' => 'y' ] ) );
    }

    public function test_find_device_no_match_returns_null () : void {
        $dev = $this->makeDevice( 'c1' , 'w1' , 's1' );
        $this->seedUser( 'admin' , 'secret' , [ $dev ] );
        $this->assertNull( Security::find_device( [ 'canvas' => 'c2' , 'webgl' => 'w2' , 'system' => 's2' ] ) );
    }

    public function test_find_device_match_canvas_and_system () : void {
        $dev = $this->makeDevice( 'c1' , 'w1' , 's1' );
        $this->seedUser( 'admin' , 'secret' , [ $dev ] );
        $found = Security::find_device( [ 'canvas' => 'c1' , 'webgl' => 'w-diff' , 'system' => 's1' ] );
        $this->assertNotNull( $found );
        $this->assertSame( 'admin' , $found['login'] );
    }

    public function test_find_device_requires_canvas_among_matches () : void {
        $dev = $this->makeDevice( 'c1' , 'w1' , 's1' );
        $this->seedUser( 'admin' , 'secret' , [ $dev ] );
        # webgl+system совпали (2), но canvas другой — не совпадение
        $found = Security::find_device( [ 'canvas' => 'c-diff' , 'webgl' => 'w1' , 'system' => 's1' ] );
        $this->assertNull( $found );
    }

    # ------------------------------------------------------------------
    # invites
    # ------------------------------------------------------------------

    public function test_create_invite_basic () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'admin' );
        $this->assertSame( 48 , strlen( $token ) ); // 24 bytes hex
        $admins = Security::get_data();
        $this->assertArrayHasKey( $token , $admins['invites'] );
        $this->assertSame( 'admin' , $admins['invites'][ $token ]['role'] );
        $this->assertFalse( $admins['invites'][ $token ]['used'] );
    }

    public function test_create_invite_bind_only_requires_target () : void {
        $this->seedUser( 'admin' );
        $this->expectException( RuntimeException::class );
        Security::create_invite( 'bind_only' , null , null );
    }

    public function test_create_invite_bind_only_target_must_exist () : void {
        $this->seedUser( 'admin' );
        $this->expectException( RuntimeException::class );
        Security::create_invite( 'bind_only' , null , 'nobody' );
    }

    public function test_create_invite_bind_only_ok () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'bind_only' , null , 'admin' );
        $inv = Security::get_data()['invites'][ $token ];
        $this->assertSame( 'bind_only' , $inv['role'] );
        $this->assertSame( 'admin' , $inv['target_user'] );
    }

    public function test_validate_invite_ok () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'admin' );
        $this->assertNotNull( Security::validate_invite( $token ) );
    }

    public function test_validate_invite_expired () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'admin' );
        $this->patchInvite( $token , [ 'expires_at' => time() - 10 ] );
        $this->assertNull( Security::validate_invite( $token ) );
    }

    public function test_validate_invite_used () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'admin' );
        Security::create_user_from_invite( $token , 'newuser' , 'pw' , 'admin' ,
            [ 'canvas' => 'c' , 'webgl' => 'w' , 'system' => 's' ] , 'UA' , '127.0.0.1' );
        $this->assertNull( Security::validate_invite( $token ) );
    }

    public function test_validate_invite_bind_only_after_target_deleted () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'bind_only' , null , 'admin' );
        # удаляем target-юзера, но инвайт оставляем
        $this->writeJson( 'secure/admins.config' , [ 'users' => [] , 'invites' => [ $token => [
            'role' => 'bind_only' , 'target_user' => 'admin' , 'created_at' => time() ,
            'expires_at' => time() + 3600 , 'used' => false ,
        ] ] ] );
        $this->assertNull( Security::validate_invite( $token ) );
    }

    public function test_revoke_invite () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'admin' );
        Security::revoke_invite( $token );
        $this->assertNull( Security::validate_invite( $token ) );
    }

    # ------------------------------------------------------------------
    # create_user_from_invite / bind_device_from_invite
    # ------------------------------------------------------------------

    public function test_create_user_from_invite_creates_device () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'admin' );
        $fp = [ 'canvas' => 'c' , 'webgl' => 'w' , 'system' => 's' ];
        $this->assertTrue( Security::create_user_from_invite( $token , 'newuser' , 'pw' , 'admin' , $fp , 'UA' , '1.2.3.4' ) );
        $admins = Security::get_data();
        $this->assertArrayHasKey( 'newuser' , $admins['users'] );
        $this->assertTrue( password_verify( 'pw' , $admins['users']['newuser']['password_hash'] ) );
        $this->assertCount( 1 , $admins['users']['newuser']['devices'] );
        $this->assertSame( 'c' , $admins['users']['newuser']['devices'][0]['canvas'] );
    }

    public function test_create_user_from_invite_duplicate_login_fails () : void {
        $this->seedUser( 'admin' );
        $token = Security::create_invite( 'admin' );
        $fp = [ 'canvas' => 'c' , 'webgl' => 'w' , 'system' => 's' ];
        $this->assertFalse( Security::create_user_from_invite( $token , 'admin' , 'pw' , 'admin' , $fp , 'UA' , '1.2.3.4' ) );
    }

    public function test_bind_device_from_invite_ok () : void {
        $this->seedUser( 'admin' , 'secret' , [] );
        $token = Security::create_invite( 'bind_only' , null , 'admin' );
        $fp = [ 'canvas' => 'c' , 'webgl' => 'w' , 'system' => 's' ];
        $this->assertTrue( Security::bind_device_from_invite( $token , 'secret' , $fp , 'UA' , '1.2.3.4' ) );
        $admins = Security::get_data();
        $this->assertCount( 1 , $admins['users']['admin']['devices'] );
    }

    public function test_bind_device_from_invite_wrong_password () : void {
        $this->seedUser( 'admin' , 'secret' , [] );
        $token = Security::create_invite( 'bind_only' , null , 'admin' );
        $fp = [ 'canvas' => 'c' , 'webgl' => 'w' , 'system' => 's' ];
        $this->assertFalse( Security::bind_device_from_invite( $token , 'WRONG' , $fp , 'UA' , '1.2.3.4' ) );
    }

    public function test_bind_device_from_invite_rejects_wrong_role () : void {
        $this->seedUser( 'admin' , 'secret' , [] );
        $token = Security::create_invite( 'admin' ); // role=admin, не bind_only
        $fp = [ 'canvas' => 'c' , 'webgl' => 'w' , 'system' => 's' ];
        $this->assertFalse( Security::bind_device_from_invite( $token , 'secret' , $fp , 'UA' , '1.2.3.4' ) );
    }

    # ------------------------------------------------------------------
    # reset_user_devices
    # ------------------------------------------------------------------

    public function test_reset_user_devices_ok () : void {
        $dev = $this->makeDevice( 'c' , 'w' , 's' );
        $this->seedUser( 'admin' , 'secret' , [ $dev ] );
        $this->assertTrue( Security::reset_user_devices( 'admin' , 'someone-else' ) );
        $this->assertCount( 0 , Security::get_data()['users']['admin']['devices'] );
    }

    public function test_reset_user_devices_self_guard () : void {
        $dev = $this->makeDevice( 'c' , 'w' , 's' );
        $this->seedUser( 'admin' , 'secret' , [ $dev ] );
        $this->assertFalse( Security::reset_user_devices( 'admin' , 'admin' ) );
        # устройства не тронуты
        $this->assertCount( 1 , Security::get_data()['users']['admin']['devices'] );
    }

    # ------------------------------------------------------------------
    # touch_device / add_device
    # ------------------------------------------------------------------

    public function test_touch_device_updates_last_seen () : void {
        $dev = $this->makeDevice( 'c' , 'w' , 's' );
        $old = $dev['last_seen'];
        $this->seedUser( 'admin' , 'secret' , [ $dev ] );
        Security::touch_device( 'admin' , $dev['id'] , '5.5.5.5' );
        $after = Security::get_data()['users']['admin']['devices'][0];
        $this->assertGreaterThan( $old , $after['last_seen'] );
        $this->assertSame( '5.5.5.5' , $after['last_ip'] );
    }

    public function test_touch_device_skips_if_recent () : void {
        $dev = $this->makeDevice( 'c' , 'w' , 's' );
        $dev['last_seen'] = time() - 60; // свежий (<5 мин)
        $this->seedUser( 'admin' , 'secret' , [ $dev ] );
        $before = $dev['last_seen'];
        Security::touch_device( 'admin' , $dev['id'] , '5.5.5.5' );
        $after = Security::get_data()['users']['admin']['devices'][0]['last_seen'];
        $this->assertSame( $before , $after );
    }

    public function test_add_device () : void {
        $this->seedUser( 'admin' , 'secret' , [] );
        $id = Security::add_device( 'admin' ,
            [ 'canvas' => 'c' , 'webgl' => 'w' , 'system' => 's' ] , 'UA' , '1.2.3.4' );
        $this->assertStringStartsWith( 'dev_' , $id );
        $this->assertCount( 1 , Security::get_data()['users']['admin']['devices'] );
    }

    public function test_add_device_unknown_user_throws () : void {
        $this->seedUser( 'admin' , 'secret' , [] );
        $this->expectException( RuntimeException::class );
        Security::add_device( 'nobody' ,
            [ 'canvas' => 'c' , 'webgl' => 'w' , 'system' => 's' ] , 'UA' , '1.2.3.4' );
    }
}