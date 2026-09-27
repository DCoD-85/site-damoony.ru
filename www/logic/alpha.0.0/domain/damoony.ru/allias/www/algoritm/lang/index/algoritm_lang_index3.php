<?PHP

namespace damoony\www;

class algoritm_lang_index
	extends \algoritm_ {

	public function release ( ) {
		$default = $this->main->library->url->static_base[ 2 ] == '' ? true : false;

		$this->algoritmdata->set( [ '' ] , false , true , $default );

		$this->release_( $this );
	}

	public function default ( ) {
		return $this->main->user->is_guest( )
			? $this->default_guest( )
			: $this->default_logged( );
	}

	# ---------------------------------------------------------------
	#   GUEST
	# ---------------------------------------------------------------
	public function default_guest ( ) {
		$main = $this->main;

		$link = $main->library->link( );
		$url = $main->library->url( );
		$url_domain = $url->static_domain;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$center = $main->alliasglobal->center;

		$href_reg  = $link->href( $url_domain , [ $url_base[ 0 ] , 'form' ] , $url_vars );
		$href_auth = $link->href( $url_domain , [ $url_base[ 0 ] , 'login' ] , $url_vars );

		$ve = new \visuoel( );

		# --- 1. HERO: block/html ---
		$hero = new \visuoel_block_html( );
		$hero->data( )->set(
			'<section class="welcome_hero">'
			. '<h1 class="welcome_title">Damoony</h1>'
			. '<p class="welcome_lead">Самописный PHP-движок с модульной архитектурой, тонким switchboard и собственным UI-слоем.</p>'
			. '<div class="welcome_actions">'
				. '<a class="button button_submit" href="' . $href_reg . '">Регистрация</a>'
				. '<a class="button button_button" href="' . $href_auth . '">Вход</a>'
			. '</div>'
			. '</section>'
		);
		$ve->add( $hero );

		# --- 2. TABLE: visuoel_table ---
		$tbl = new \visuoel_table( );
		$ve->add( $tbl );

		$head = $tbl->head( )->row( );
		$head->cell( width : 20 )->set( 'Модуль' );
		$head->cell( width : 20 )->set( 'Состояние' );
		$head->cell( width : 60 )->set( 'Назначение' );

		$body = $tbl->body( );

		$rows = [
			[ 'Ядро'   , 'stable' , 'Модульный switchboard, автозагрузка sources/* по порядку.' ] ,
			[ 'DOM'    , 'stable' , 'Мультидоменность и аллиасы через secure/main_settings.config.' ] ,
			[ 'Логика' , 'stable' , 'Маршрутизация по домену и аллиасу: алгоритмы + подалгоритмы.' ] ,
			[ 'Visuo'  , 'dev'    , 'UI-подсистема: visuoel → группа → элемент, рендер через visuotag.' ] ,
			[ 'Input'  , 'dev'    , 'field/input: text, hidden, password, checkbox, radio, select, file.' ] ,
			[ 'Table'  , 'dev'    , 'visuoel_table: зоны head/body/foot, cell с width/colspan/rowspan.' ] ,
			[ 'Почта'  , 'plan'   , 'Внешний SMTP-релей (Яндекс 360 / Brevo) вместо локального MTA.' ] ,
			[ 'SSL'    , 'stable' , 'Wildcard *.damoony.ru через AlphaSSL, автопродление в reg.ru.' ] ,
		];

		foreach ( $rows as $r ) {
			$row = $body->row( );
			$row->cell( width : 20 )->set( $r[ 0 ] );
			$row->cell( width : 20 )->set( $r[ 1 ] );
			$row->cell( width : 60 )->set( $r[ 2 ] );
		}

		# --- 3. STACK: block/html ---
		$stack = new \visuoel_block_html( );
		$stack->data( )->set(
			'<section class="welcome_stack">'
			. '<h2>Стек</h2>'
			. '<p>PHP 8.3+ · без фреймворков · собственный switchboard · модульная логика по доменам и аллиасам.</p>'
			. '</section>'
		);
		$ve->add( $stack );

		# --- wrap into page ---
		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'welcome_page' )->el( $ve );

		$center->visuotag( )->append( $vt );

		return true;
	}

	# ---------------------------------------------------------------
	#   LOGGED
	# ---------------------------------------------------------------
	public function default_logged ( ) {
		$main = $this->main;
		$center = $main->alliasglobal->center;

		$ve = new \visuoel( );

		$b = new \visuoel_block_html( );
		$b->data( )->set(
			'<section class="welcome_hero welcome_dev">'
			. '<h1 class="welcome_title">Раздел в разработке</h1>'
			. '<p class="welcome_lead">Мы работаем над этим. Заходите позже — скоро здесь появится что-то интересное.</p>'
			. '</section>'
		);
		$ve->add( $b );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'welcome_page welcome_page_logged' )->el( $ve );

		$center->visuotag( )->append( $vt );

		return true;
	}
}

?>