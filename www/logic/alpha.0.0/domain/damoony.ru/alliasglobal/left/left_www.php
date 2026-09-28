<?PHP

namespace damoony;

class alliasglobal_left_www {
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
			'Цель проекта' ,
			$link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === 'overview'
		);
		$this->left->menu->link(
			'nav' ,
			'Текущие задачи' ,
			$link->href( false , [ $url_base[ 0 ] , 'tasks' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === 'tasks'
		);
		$this->left->menu->link(
			'nav' ,
			'Наши достижения' ,
			$link->href( false , [ $url_base[ 0 ] , 'results' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === 'results'
		);
		$this->left->menu->link(
			'nav' ,
			'Главные вопросы' ,
			$link->href( false , [ $url_base[ 0 ] , 'faq' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === 'faq'
		);
	}
}