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
		$tabs = $ve->tabs( );

		# =====================================================
		# Таб 1: Регистрация
		# =====================================================
		$page_reg = $tabs->page( );
		$page_reg->data( )->set( 'Регистрация' );

		$form_grp = new \visuoel_form( );
		$form = $form_grp->form( );
		$page_reg->add( $form_grp );

		$form->data( )->set( 'register_form' , 'post' , '/register' , title_text : 'Регистрация' , form_jquery : true );

		# csrf — вне таблицы
		$csrf = new \visuoel_input_hidden( );
		$csrf->data( )->set( 'csrf' , 'x' );
		$form->add( $csrf );

		# таблица полей
		$table = new \visuoel_table( );
		$form->add( $table );

		$head = $table->head( )->row( );
		$head->cell( width : 42 )->set( 'Поле' );
		$head->cell( width : 43 )->set( 'Описание' );
		$head->cell( width : 15 )->set( 'Обязательно' );

		$body = $table->body( );

		# --- login ---
		$row = $body->row( );
		$f = new \visuoel_field_text( );
		$f->data( )->set( 'login2' )->label( 'Login' )->placeholder( 'username1' )->required( )->hide_label( )->hide_hint( );
		
		$f2 = new \visuoel_field_text( );
		$f2->data( )->set( 'login' )->placeholder( 'username2' )->required( )->hide_label( )->hide_hint( );
		
		$f3 = new \visuoel_field_text( );
		$f3->data( )->set( 'login3' )->placeholder( 'username3' )->required( )->hide_label( )->hide_hint( );

		$row->cell( width : 42 , inline : true )->add( $f )->add( $f2 )->set( 'asd' )->add( $f3 );
		$row->cell( width : 43 )->set( 'Латиница, цифры, от 3 до 32 символов' );
		$row->cell( width : 15 )->set( '*' );

		# --- email ---
		$row = $body->row( );
		$f = new \visuoel_field_email( );
		$f->data( )->set( 'email' )->placeholder( 'user@example.com' )->required( )->hide_label( )->hide_hint( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 43 )->set( 'Email для связи и восстановления пароля' );
		$row->cell( width : 15 )->set( '*' );

		# --- password ---
		$row = $body->row( );
		$f = new \visuoel_field_password( );
		$f->data( )->set( 'password' )->required( )->hide_label( )->hide_hint( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 43 )->set( 'Минимум 8 символов' );
		$row->cell( width : 15 )->set( '*' );

		# --- password_confirm ---
		$row = $body->row( );
		$f = new \visuoel_field_password( );
		$f->data( )->set( 'password_confirm' )->required( )->hide_label( )->hide_hint( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 43 )->set( 'Повтор пароля' );
		$row->cell( width : 15 )->set( '*' );

		# --- birth_date ---
		$row = $body->row( );
		$f = new \visuoel_field_text( );
		$f->data( )->set( 'birth_date' )->placeholder( 'дд.мм.гггг' )->hide_label( )->hide_hint( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 43 )->set( 'Формат: дд.мм.гггг' );
		$row->cell( width : 15 )->set( '' );

		# --- gender ---
		$row = $body->row( );
		$g = new \visuoel_field_radio_group( );
		$g->data( )->hide_label( )->hide_hint( );

		$r = new \visuoel_field_radio( );
		$r->data( )->set( 'gender' )->value( 'm' )->label( 'Мужской' );
		$g->add( $r );

		$r = new \visuoel_field_radio( );
		$r->data( )->set( 'gender' )->value( 'f' )->label( 'Женский' );
		$g->add( $r );

		$r = new \visuoel_field_radio( );
		$r->data( )->set( 'gender' )->value( 'x' )->label( 'Другое' );
		$g->add( $r );

		$row->cell( width : 42 )->add( $g );
		$row->cell( width : 43 )->set( 'Пол' );
		$row->cell( width : 15 )->set( '' );

		# --- city ---
		$row = $body->row( );
		$f = new \visuoel_field_select( );
		$f->data( )->set( 'city' )
			->placeholder_option( '— выберите город —' )
			->options( [
				'msk' => 'Москва' ,
				'spb' => 'Санкт-Петербург' ,
				'nsk' => 'Новосибирск' ,
				'ekb' => 'Екатеринбург' ,
				'kzn' => 'Казань' ,
			] )
			->required( )->hide_label( )->hide_hint( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 43 )->set( 'Город проживания' );
		$row->cell( width : 15 )->set( '*' );

		# --- interests ---
		$row = $body->row( );
		$g = new \visuoel_field_checkbox_group( );
		$g->data( )->hide_label( )->hide_hint( );

		$opts = [
			'sport'   => 'Спорт' ,
			'music'   => 'Музыка' ,
			'books'   => 'Книги' ,
			'movies'  => 'Кино' ,
			'travel'  => 'Путешествия' ,
			'tech'    => 'Технологии' ,
			'art'     => 'Искусство' ,
			'science' => 'Наука' ,
		];
		foreach ( $opts as $val => $lbl ) {
			$c = new \visuoel_field_checkbox( );
			$c->data( )->set( 'interests[]' )->value( $val )->label( $lbl );
			$g->add( $c );
		}

		$row->cell( width : 42 )->add( $g );
		$row->cell( width : 43 )->set( 'Направления: выберите одно или несколько' );
		$row->cell( width : 15 )->set( '' );

		# --- bio ---
		$row = $body->row( );
		$f = new \visuoel_field_textarea( );
		$f->data( )->set( 'bio' )->placeholder( 'Пара слов о вас' )->rows( 4 )->hide_label( )->hide_hint( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 43 )->set( 'О себе (необязательно)' );
		$row->cell( width : 15 )->set( '' );

		# --- agree ---
		$row = $body->row( );
		$f = new \visuoel_field_checkbox( );
		$f->data( )->set( 'agree' )->required( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 43 )->set( 'Принимаю пользовательское соглашение' );
		$row->cell( width : 15 )->set( '*' );

		# --- news ---
		$row = $body->row( );
		$f = new \visuoel_field_checkbox( );
		$f->data( )->set( 'news' )->checked( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 43 )->set( 'Подписаться на новости' );
		$row->cell( width : 15 )->set( '' );

		$bottom = $table->foot( );
		
		$row = $bottom->row( );
		$submit = new \visuoel_field_submit( );
		$submit->data( )->text( 'Регистрация' );

		$row->cell( width : 42 )->add( $submit );
		$row->cell( width : 43 )->set( '' );
		$row->cell( width : 15 )->set( '');

		# =====================================================
		# Таб 2: Авторизация
		# =====================================================
		$page_auth = $tabs->page( );
		$page_auth->data( )->set( 'Авторизация' );

		$form_grp2 = new \visuoel_form( );
		$form2 = $form_grp2->form( );
		$page_auth->add( $form_grp2 );

		$form2->data( )->set( 'login_form' , 'post' , '/login' , title_text : 'Вход' , form_jquery : true );

		$csrf2 = new \visuoel_input_hidden( );
		$csrf2->data( )->set( 'csrf' , 'x' );
		$form2->add( $csrf2 );

		$table2 = new \visuoel_table( );
		$form2->add( $table2 );

		$head2 = $table2->head( )->row( );
		$head2->cell( width : 42 )->set( 'Поле' );
		$head2->cell( width : 58 )->set( 'Описание' );

		$body2 = $table2->body( );

		$row = $body2->row( );
		$f = new \visuoel_field_text( );
		$f->data( )->set( 'login' )->placeholder( 'username или email' )->required( )->hide_label( )->hide_hint( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 58 )->set( 'Логин или email' );

		$row = $body2->row( );
		$f = new \visuoel_field_password( );
		$f->data( )->set( 'password' )->required( )->hide_label( )->hide_hint( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 58 )->set( 'Пароль' );

		$row = $body2->row( );
		$f = new \visuoel_field_checkbox( );
		$f->data( )->set( 'remember' )->label( 'Запомнить меня' )->checked( );
		$row->cell( width : 42 )->add( $f );
		$row->cell( width : 58 )->set( 'Хранить сессию' );

		$bottom = $table2->foot( );
		
		$row = $bottom->row( );
		$submit = new \visuoel_field_submit( );
		$submit->data( )->text( 'Регистрация' );

		$row->cell( width : 42 )->add( $submit );
		$row->cell( width : 43 )->set( '' );
		$row->cell( width : 15 )->set( '');
		

		# =====================================================
		# Таб 3: Памятка
		# =====================================================
		$page_memo = $tabs->page( );
		$page_memo->data( )->set( 'Памятка' );

		$memo = new visuotag( );
		$memo = $memo->tag( 'div' , class : 'memo_block' );

		$memo->tag( 'h2' )->content( 'Как зарегистрироваться' );
		$memo->tag( 'p' )->content( 'Заполните поля формы «Регистрация». Все поля с пометкой * обязательны.' );

		$memo->tag( 'h3' )->content( 'Логин' );
		$memo->tag( 'p' )->content( 'Латиница и цифры, длина от 3 до 32 символов. Логин нельзя будет изменить после регистрации.' );

		$memo->tag( 'h3' )->content( 'Пароль' );
		$memo->tag( 'p' )->content( 'Минимум 8 символов. Рекомендуем использовать буквы обоих регистров, цифры и специальные символы.' );

		$memo->tag( 'h3' )->content( 'Email' );
		$memo->tag( 'p' )->content( 'Используется для подтверждения аккаунта и восстановления пароля. Указывайте действующий адрес.' );

		$memo->tag( 'h3' )->content( 'Безопасность' );
		$memo->tag( 'p' )->content( 'Никому не сообщайте свой пароль. Администрация сайта никогда не запрашивает пароль по email или в чате.' );

		$memo->tag( 'h3' )->content( 'Нужна помощь?' );
		$memo->tag( 'p' )->content( 'Пишите на noadmin@damoony.ru — ответим в течение суток.' );

		$page_memo->add( $memo );

		# =====================================================
		# Рендер
		# =====================================================
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