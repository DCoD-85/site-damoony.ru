<?PHP

namespace damoony\www;

class algoritm_lang_index
	extends \algoritm_ {
	# vars
	# public algoritm $algoritm;
	public null|\algoritmdata_ $algoritmdata = null;
	public \visuotag $vt;
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
		
		$vt_top = new \visuotag( );
		$vt_top = $top->as_tag( );
		$vt_center = new \visuotag( );
		$vt_center->tag( 'div' )->content( 'login' );
		$vt_bottom = new \visuotag( );
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
		
		$ve = new \visuoel( );

		$p = $ve->primer( );
		$p->text( )->data( )->set( 'Damoony' );
		$p->text( )->data( )->set( 'Самописный PHP-движок. Модульная архитектура, тонкий switchboard, собственный UI-слой.' );

		$p = $ve->primer( );
		$p->text( )->data( )->set( 'Visuo' );
		$p->text( )->data( )->set( 'UI-подсистема Damoony: сборка страниц из объектов вместо ручного HTML.' );
		$p->text( )->data( )->set( 'Три уровня: visuoel → группа → элемент. Каждый — со своей обёрткой и данными.' );
		$p->text( )->data( )->set( 'Рендер через visuotag: родитель инициализируется раньше детей, дети попадают в его vt_end.' );

		$p = $ve->primer( );
		$p->text( )->data( )->set( 'Состояние visuo' );
		$p->text( )->data( )->set( 'База: visuoel, visuoel_group, visuoel_group_element — vt_start / vt_end, add / remove / clear, singleton-флаг.' );
		$p->text( )->data( )->set( 'Семейства: form (singleton), primer (multi), а также visuotag, visuolog, visuostruct, visuolink, visuometa.' );
		$p->text( )->data( )->set( 'Интеграция: visuotag::el( $visuoel ) — точка стыковки нового движка со старым рендером.' );

		$p = $ve->primer( );
		$p->text( )->data( )->set( 'В разработке' );
		$p->text( )->data( )->set( 'Семейство input: text, hidden, password, submit, checkbox, radio, select, textarea.' );
		$p->text( )->data( )->set( 'Семейства table, block, link. Постепенная миграция visuoelement → visuoel.' );

		$p = $ve->primer( );
		$p->text( )->data( )->set( 'Стек' );
		$p->text( )->data( )->set( 'PHP 8.3+ · без фреймворков · собственный switchboard · модульная логика по доменам и аллиасам.' );

		$vt_center = new \visuotag( );
		$vt_center->tag( 'div' , class : 'welcome_block' )->el( $ve );

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

		$vt_center = new \visuotag( );
		$vt_center->tag( 'div' , style : 'font-size : 20px ; margin : 100px auto ; text-align : center ; ' )->content( 'Вы уже авторизованы' );

		$center->visuotag( )->append( $vt_center );

		return true;
	}
	
}

?>