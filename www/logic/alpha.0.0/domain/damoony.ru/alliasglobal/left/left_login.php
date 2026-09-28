<?PHP

namespace damoony;

class alliasglobal_left_login {
	# --- refs ---
	public \main $main;
	public alliasglobal_left $left;

	# --- methods ---
	public function __construct ( alliasglobal_left $left ) {
		$this->main = $left->main;
		$this->left = $left;
		$this->data( );
	}

	public function data ( ) : void {
		$lib  = $this->main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base = $url->static_base;

		$this->left->menu->link(
			'nav' ,
			'Форма авторизации' ,
			$link->href( false , [ $url_base[ 0 ] , '' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === ''
		);
		$this->left->menu->link(
			'nav' ,
			'Вспомнить пароль' ,
			$link->href( false , [ $url_base[ 0 ] , 'rememberpassword' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === 'rememberpassword'
		);
	}
}