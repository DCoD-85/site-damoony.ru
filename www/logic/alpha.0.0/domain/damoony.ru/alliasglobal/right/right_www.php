<?PHP

namespace damoony;

class alliasglobal_right_www {
	# --- refs ---
	public \main $main;
	public alliasglobal_right $right;

	# --- methods ---
	public function __construct ( alliasglobal_right $right ) {
		$this->main  = $right->main;
		$this->right = $right;
		$this->data( );
	}

	public function data ( ) : void {
		$lib  = $this->main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base = $url->static_base;
		
		$this->right->menu->link(
			'nav' ,
			'Статистика' ,
			$link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === 'overview'
		);
	}
}