<?PHP

# ---------------------------------------------------------------
#   include.php — visuo
#   Вызывается из allias::__construct (движок).
#   $this — объект \damoony\allias.
#   Задача: константы, каркас, подмена $main->user.
# ---------------------------------------------------------------

# --- константы путей ---
# __DIR__ = .../allias/visuo/
# dirname(__DIR__, 6) = .../www/
if ( ! defined( 'VISUO_MEDIA_DOWNLOADS' ) ) {
	define( 'VISUO_MEDIA_DOWNLOADS' , dirname( __DIR__ , 6 ) . DS . 'media' . DS . 'downloads' . DS );
}
if ( ! defined( 'VISUO_MEDIA_IMG' ) ) {
	define( 'VISUO_MEDIA_IMG' , dirname( __DIR__ , 6 ) . DS . 'media' . DS . 'visuo' . DS . 'img' . DS );
}

# --- каркас ---
( include_once $path = __DIR__ . DS . 'helpers.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'user.php'    ) or error_file( $path , __FILE__ , __LINE__ );

# --- подмена $main->user на visuo-версию ---
if ( isset( $this ) && $this instanceof \damoony\allias ) {
	$main = $this->main;
	if ( $main instanceof \main && ! ( $main->user instanceof \damoony\visuo\user ) ) {
		$main->user = new \damoony\visuo\user( $main );
		$main->user->boot( );
	}
}

( include_once $path = __DIR__ . DS . 'algoritm' . DS . 'algoritm.php' ) or error_file( $path , __FILE__ , __LINE__ );

?>