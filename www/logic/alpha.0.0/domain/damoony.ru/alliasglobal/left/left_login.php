<?PHP

namespace damoony;

class alliasglobal_left_login {
	# vars
	public \main $main;
	public alliasglobal_left $left;
	# methods
	public function __construct ( alliasglobal_left $left ) {
		$this->main = $left->main;
		$this->left = $left;
		
		$this->algoritm( );
	}
	
	public function algoritm ( ) {
		$main = $this->main;
		$left = $this->left;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		switch( $url_base[ 1 ] ){
			case 'index' :
				$method = 'data_' . $url_base[ 0 ];
				
				if( method_exists( $this , $method ) ) $this->{$method}( );
				break;
			case 'login' :
			case 'registration' :
				
				$method = 'data_' . $url_base[ 0 ];
				
				
				if( method_exists( $this , $method ) ) $this->{$method}( );
				break;
			default :
				# $left->menu_add_data( 'statisticts' , false , 1 , 'statisticts' , [ 'ru'=>'Статистика' , 'en'=>'Statistict' ] , [ 'ru'=>'wad wa dwa dwad awd aw dwad' , 'en'=>'' ] , $link->href( true , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : true ) );
				# $left->menu_edit( 'statisticts' , [ 'active'=>true , 'title'=>[ 'en'=>'this is statistics menu' ] , 'comment'=>[ 'en'=>'ных требований к защите п' ] ] );
				
				break;
		}
	}
	
	public function data_index ( ) {
		$main = $this->main;
		$left = $this->left;
		
		$lang = $main->language;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$left_target = $lang->get( 'LEFT_TARGET' , [ ] , 'Цель проекта' );
		$left_target_comment = $lang->get( 'LEFT_TARGET_COMMENT' , [ ] , '' );

		$left->menu_add_data( null , true , 1 , 'statisticts' , $left_target , $left_target_comment , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		
	}
	
	
	public function data_login ( ) {
		$main = $this->main;
		$left = $this->left;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$left->menu_add_data( null , true , 1 , '' , [ 'ru'=>'Форма авторизации' , 'en'=>'Statistict' ] , [ 'ru'=>'Показать форму авторизации' , 'en'=>'Show form auth' ] , $link->href( false , [ $url_base[ 0 ] , '' ] , use_all_adress_data : false ) );
		$left->menu_add_data( null , true , 1 , 'rememberpassword' , [ 'ru'=>'Вспомнить пароль' , 'en'=>'Statistict' ] , [ 'ru'=>'Показать форму восстановления пароля' , 'en'=>'Show form regs' ] , $link->href( false , [ $url_base[ 0 ] , 'rememberpassword' ] , use_all_adress_data : false ) );
		
	}
	
	
	public function data_registration ( ) {
		$main = $this->main;
		$left = $this->left;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$left->menu_add_data( null , true , 1 , '' , [ 'ru'=>'Форма регистрации' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , '' ] , use_all_adress_data : false ) );
		
	}
}

?>