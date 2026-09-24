<?PHP

namespace damoony\login;

use visuoel;
use visuotag;

class algoritm_lang_registration
	extends \algoritm_ {
		
	# vars
	# public algoritm $algoritm;
	public null|\algoritmdata_ $algoritmdata = null;
	public visuotag $vt;
	public array $args = [ ];
	# methods
	public function __construct( \main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritm = $main->algoritm;
		$this->algoritmdata = new \algoritmdata_( );
	}
	
	
	public function args ( array $args ) {
		$this->args = $args;
	}
	public function algoritm ( ) {
		$_SESSION[ 'login' ][ 'redirect' ] = $_SERVER[ 'HTTP_REFERER' ];
	}

	public function release ( ) {
		$main = $this->main;
		if( $main->user->is_guest( ) ){
			$this->algoritmdata->set( );
		} else {
			$this->algoritmdata->set( );
		}

		$available = [ '' ];
		$auth = false;
		$search = true;
		$default = $this->main->library->url->static_base[ 2 ] == '' ? true : false;

		$this->algoritmdata->set( $available , $auth , $search , $default );

		# release
		$this->release_( $this );
	}
	
	public function default ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		$lang = $main->language;

		$is_ajax = $main->render->getdata( 'render_ajax' );
		$is_guest = $main->user->is_guest( );

		if( $is_ajax && $_POST ){
			return $this->ajax( );
		} else {
			if( $is_guest ) return $this->default_guest( );
			else return $this->default_logged( );
		}
	}

	public function ajax ( ) {
		$main = $this->algoritm->main;
		$is_guest = $main->user->is_guest( );
		if( $is_guest ) return $this->ajax_guest( );
		else return $this->ajax_logged( );
	}

	public function ajax_guest ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$var = $main->library->vars;
		$library = $main->library;
		$vars = $library->vars;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$top = $main->alliasglobal->top;
		$center = $main->alliasglobal->center;
		$bottom = $main->alliasglobal->bottom;
		
		$headers = $main->alliasglobal->headers;
		$scheme = $main->alliasscheme;
		
		# struct
		$struct = $main->visuostruct->ajax( );
		
		# struct : init
		$struct_address = $struct->address( );
		$struct_top = $struct->top( );
		$struct_center = $struct->center( );
		$struct_bottom = $struct->bottom( );
		
		$vt_top = new visuotag( );
		$vt_top = $top->as_tag( );
		$vt_center = new visuotag( );
		$vt_center->tag( 'div' )->content( 'login' );
		$vt_bottom = new visuotag( );
		$vt_bottom = $bottom->as_tag( );

		# struct : set
		$struct_address->set( $link->href( true , true ) );
		$struct_top->set( $top->as_tag( ) );
		$struct_center->set( $vt_center );
		$struct_bottom->set( $bottom->as_tag( ) );
		
		$visuorelease->release_struct( $struct );
	}

	public function ajax_logged ( ) {

	}

		public function default_guest ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;

		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$left = $main->alliasglobal->left;
		$center = $main->alliasglobal->center;
		$right = $main->alliasglobal->right;


		$ve = new visuoel( );

		$form = $ve->form( )->form( );
		
		
		

		$form->data( )->set( 'register_form' , 'post' , '/register' , title_text : 'Регистрация' , form_jquery : true );

		# csrf — прямо в форму, вне таблицы
		$csrf = new \visuoel_input_hidden( );
		$csrf->data( )->set( 'csrf' , 'x' );
		$form->data( )->add( $csrf );

		# --- таблица полей ---
		$table = new \visuoel_table( );
		$form->data( )->add( $table );

		$fdsa = new visuotag( );
		$fdsa->tag( 'div' , class : 'headdd' )->tag( 'span' )->content( 'Поле' );
		# шапка
		$head = $table->head( )->row( );
		# $head->cell( width : 42 )->set( 'Поле' );
		$head->cell( width : 42 )->add( $fdsa );
		$head->cell( width : 43 )->set( 'Описание' );
		$head->cell( width : 15 )->set( 'Обязательно' );

		$body = $table->body( );

		# --- login ---
		$row = $body->row( );
		$inp = new \visuoel_input_text( );
		$inp->data( )->set( 'login' )->placeholder( 'username' )->required( );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Латиница, цифры, от 3 до 32 символов' );
		$row->cell( width : 15 )->set( '*' );

		# --- email ---
		$row = $body->row( );
		$inp = new \visuoel_input_email( );
		$inp->data( )->set( 'email' )->placeholder( 'user@example.com' )->required( );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Email для связи и восстановления пароля' );
		$row->cell( width : 15 )->set( '*' );

		# --- password ---
		$row = $body->row( );
		$inp = new \visuoel_input_password( );
		$inp->data( )->set( 'password' )->required( );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Минимум 8 символов' );
		$row->cell( width : 15 )->set( '*' );
		
		# --- password_confirm ---
		$row = $body->row( );
		$inp = new \visuoel_input_password( );
		$inp->data( )->set( 'password_confirm' )->required( );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Повтор пароля' );
		$row->cell( width : 15 )->set( '*' );

		# --- birth_date ---
		$row = $body->row( );
		$inp = new \visuoel_input_text( );
		$inp->data( )->set( 'birth_date' )->placeholder( 'дд.мм.гггг' );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Формат: дд.мм.гггг' );
		$row->cell( width : 15 )->set( '' );

		# --- gender ---
		$row = $body->row( );
		$inp = new \visuoel_input_radio_group( );
		$inp->data( )->set( 'gender' )->options( [
			'm' => 'Мужской' ,
			'f' => 'Женский' ,
			'x' => 'Другое' ,
		] );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Пол' );
		$row->cell( width : 15 )->set( '' );

		# --- city ---
		$row = $body->row( );
		$inp = new \visuoel_input_select( );
		$inp->data( )->set( 'city' )
			->placeholder_option( '— выберите город —' )
			->options( [
				'msk' => 'Москва' ,
				'spb' => 'Санкт-Петербург' ,
				'nsk' => 'Новосибирск' ,
				'ekb' => 'Екатеринбург' ,
				'kzn' => 'Казань' ,
			] )
			->required( );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Город проживания' );
		$row->cell( width : 15 )->set( '*' );

		# --- interests ---
		$row = $body->row( );
		$inp = new \visuoel_input_checkbox_group( );
		$inp->data( )->set( 'interests' )->options( [
			'sport'   => 'Спорт' ,
			'music'   => 'Музыка' ,
			'books'   => 'Книги' ,
			'movies'  => 'Кино' ,
			'travel'  => 'Путешествия' ,
			'tech'    => 'Технологии' ,
			'art'     => 'Искусство' ,
			'science' => 'Наука' ,
		] );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Направления: выберите одно или несколько' );
		$row->cell( width : 15 )->set( '' );

		# --- bio ---
		$row = $body->row( );
		$inp = new \visuoel_input_textarea( );
		$inp->data( )->set( 'bio' )->placeholder( 'Пара слов о вас' )->rows( 4 );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'О себе (необязательно)' );
		$row->cell( width : 15 )->set( '' );

		# --- agree ---
		$row = $body->row( );
		$inp = new \visuoel_input_checkbox( );
		$inp->data( )->set( 'agree' )->required( );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Принимаю пользовательское соглашение' );
		$row->cell( width : 15 )->set( '*' );

		# --- news ---
		$row = $body->row( );
		$inp = new \visuoel_input_checkbox( );
		$inp->data( )->set( 'news' )->checked( );
		$row->cell( width : 42 )->add( $inp );
		$row->cell( width : 43 )->set( 'Подписаться на новости' );
		$row->cell( width : 15 )->set( '' );

		# --- submit ---
		$submit = new \visuoel_button_submit( );
		$submit->data( )->set( 'Зарегистрироваться' );
		$form->data( )->add( $submit );

		# --- обёртка ---
		$vt_center = new visuotag( );
		$vt_center->tag( 'div' , class : 'register_block' )->el( $ve );

		$center->visuotag( )->append( $vt_center );

		return true;
	}

	public function default_logged ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$left = $main->alliasglobal->left;
		$center = $main->alliasglobal->center;
		$right = $main->alliasglobal->right;

		$vt_center = new visuotag( );
		$vt_center->tag( 'div' , style : 'font-size : 20px ; margin : 100px auto ; text-align : center ; ' )->content( 'Вы уже авторизованы' );

		$center->visuotag( )->append( $vt_center );

		return true;
	}
	
}

?>