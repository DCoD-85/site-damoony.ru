<?PHP

namespace damoony;

class alliasglobal_left {
	# vars
	public alliasglobal $alliasglobal;
	public \main $main;
	public \visuorelease $visuorelease;
	public \visuotag $visuotag;
	public array $vts = [ ]; // visuotags
	public array $vtd = [ ]; // visuotags data
	public null|\visuotag $visuotag_title = null;
	public string $alliasglobal_name = 'left';
	public string $allias_accepted = '';
	public bool $settings_on = false;
	public bool $settings_asmenu = true;	// отображать как меню: [ true - меню | false - vt ]
	public alliasglobal_menu $alliasglobal_menu;
	# methods
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal = $alliasglobal;
		$this->main = $alliasglobal->main;
		$this->visuorelease = $this->main->visuorelease;
		$this->visuotag = new \visuotag( $this->visuorelease );
		$this->allias_accepted = $this->main->main_settings->allias_accepted( );

		$this->alliasglobal_menu = new alliasglobal_menu( $this->alliasglobal , $this->alliasglobal_name );

		$this->init( );
	}
	
	private function init ( ) {
		$localname = $this->alliasglobal_name;
		$alliaslocal_on = $this->main->alliaslocal->$localname->settings_on;
		
		if( $alliaslocal_on === true ){
			$this->visuotag->clear( )->append( $this->main->alliaslocal->$localname->visuotag );
		} else {
			$this->alliasglobal_menu->clear( );
			$this->algoritm( );
		}
	}

	public function algoritm ( ) {
		$this->scheme( );
		$this->data ( );
		$this->release ( );
	}
	
	public function scheme ( ) {
		$object = $this;
		
		$visuotag = $object->visuotag;
		$visuotag->tag( 'div' , class: 'menu' , return : false )
			->tag( 'div' , class : 'menuactions' );
		
		
		$object->vts[ 'root' ] = $visuotag->root( );
		$root = $object->vts[ 'root' ];
		$object->vts[ 'menu' ] = $root->find( class : 'menu' );
		$object->vts[ 'menuactions' ] = $root->find( class : 'menuactions' );
		
	}

	public function data ( ) {
		$main = $this->main;
		$lang = $main->language;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		# data__init
		$this->data__init( );

		$domain = $main->main_settings->domain_accepted( false );
		$accept = $main->main_settings->allias_accepted( );
		
		$data = [ ];
		if( ! is_null( $accept ) ){
			$classglobal_path = __DIR__ . DS;
			$classglobal_path .= $this->alliasglobal_name . '_' . $accept . '.php';
			$classglobal_name = '\\' . $domain . '\\alliasglobal_' . $this->alliasglobal_name . '_' . $accept;

			if( include_once $path = $classglobal_path ){
				if( class_exists( $classglobal_name ) ){
					$classglobal_class = new $classglobal_name( $this );
				}
			} else {
			}
		}
	}

	public function data__init ( ) {
		$main = $this->main;
		
		$lang = $main->language;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$menu = $this->alliasglobal_menu;

		# $top_login_profile = $lang->get( 'TOP_LOGIN_PROFILE' , [ $main->user->profile->data[ 'login' ] ] , '%s' );
		# $menu->new( 'languages' )->link( $title_languages_rus , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_base_rus , use_all_adress_data : false ) , $url_base[ 0 ] === 'ru-ru' );
	}
	// 
	public function release ( ) {
		$main = $this->main;
		$alliasglobal = $this->alliasglobal;
		
		$lang = $main->language;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$ahref_home = $link->href( [ $url_allias[ 0 ] ] , [ ] );
		

		# $this->vts[ 'toplogo_links_top_skins' ] = $this->vts[ 'toplogo_links_top_skins' ]
		# 	->tag( 'div' , class : 'box' )->tag( 'div' , class : 'data' );
		# $this->vts[ 'toplogo_links_top_languages' ] = $this->vts[ 'toplogo_links_top_languages' ]
		# 	->tag( 'div' , class : 'box' );
		
		$vts = $this->vts;
		$vtd = [ ];
		$vtd[ 'menu' ] = $this->alliasglobal_menu->as_tag( 'menuleft' );
		$vtd[ 'menuactions' ] = $this->alliasglobal_menu->as_tag( 'menuactionsleft' );
		
		# die( $vtd[ 'toplogo_links_navigate' ]->release( ) );
		$alliasglobal->append( $this , 'menu' , $vts , $vtd );
		$alliasglobal->append( $this , 'menuactions' , $vts , $vtd );
	}
	
	public function reload ( ) {
		# example from algoritm script:
		// $alliasglobal = $this->algoritm->main->alliasglobal;
		// $alliasglobal->top->alliasglobal_menu->clear( 'navigate' );
		// $alliasglobal->top->alliasglobal_menu->new( 'navigate' )->link( 'test1' , 'test' , true );
		// $alliasglobal->top->alliasglobal_menu->new( 'navigate' )->link( 'test1' , 'test' , true );
		// $alliasglobal->top->alliasglobal_menu->new( 'navigate' )->link( 'test1' , 'test' , true );
		// $alliasglobal->top->reload( );


		$this->visuotag->clear( );
		
		$this->scheme( );
		$this->release( );
	}
	
	public function as_tag ( ) {
		return $this->visuotag->root( );
	}
	
	
	public function asmenu ( bool $asmenu ) {
		$this->settings_asmenu = $asmenu;
		if( $asmenu === false ){
			$this->visuotag->clear( );
		}
	}
	public function visuotag ( ) {
		return $this->visuotag;
	}
}

?>