<?PHP

namespace damoony;

class alliasglobal_left_www {
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
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		switch( $url_base[ 0 ] ){
			case 'index' :
				$method = 'data_' . $url_base[ 0 ];
				
				if( method_exists( $this , $method ) ) $this->{$method}( );
				break;
			default :
				# $left->menu_add_data( false , 1 , 'statisticts' , [ 'ru'=>'Статистика' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
				break;
		}
	}
	
	public function data_index ( ) {
		$main = $this->main;
		$left = $this->left;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		# $left->menu_add_data( null , true , 1 , '' , [ 'ru'=>'Форма авторизации' , 'en'=>'Statistict' ] , [ 'ru'=>'Показать форму авторизации' , 'en'=>'Show form auth' ] , $link->href( false , [ $url_base[ 0 ] , '' ] , use_all_adress_data : false ) );
		
		$left->menu_add_data( null , true , 1 , 'statisticts' , [ 'ru'=>'Цель проекта' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( null , true , 1 , 'statisticts' , [ 'ru'=>'Текущие задачи' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( null ,  true , 1 , 'statisticts' , [ 'ru'=>'Наши достижения' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( null , true , 1 , 'statisticts' , [ 'ru'=>'Главные вопросы' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		
	}
	
	public function data_statistics ( ) {
		$main = $this->main;
		$left = $this->left;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Цель проекта' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Текущие задачи' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Наши достижения' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Главные вопросы' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		
	}
	
	public function data_sdk ( ) {
		$main = $this->main;
		$left = $this->left;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Описание' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Установка' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Настройка' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Использование' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		$left->menu_add_data( true , 1 , 'statisticts' , [ 'ru'=>'Классы и функции' , 'en'=>'Statistict' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
		
	}
}

?>