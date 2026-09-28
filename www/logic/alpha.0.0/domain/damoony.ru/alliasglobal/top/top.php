<?PHP

namespace damoony;

class alliasglobal_top extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'top';
	public bool   $settings_asmenu   = true;

	# --- custom ---
	public string $sitename  = 'Damoony';
	public string $siteabout = '';

	# --- methods ---
	protected function scheme ( ) : void {
		$this->visuotag
			->tag( 'div' , class : 'top' , return : false )
			->tag( 'div' , class : 'top_logo' , return : false )
			->tag( 'div' , class : 'top_title' )
				->tag( 'div' , class : 'top_sitename' )
				->tag( 'div' , class : 'top_siteabout' )
			->parent( )
			->tag( 'div' , class : 'top_projects' )
			->parent( )
			->tag( 'div' , class : 'top_languages' );
	}

	protected function data ( ) : void {
		$main = $this->main;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		# --- languages ---
		$url_ru = $url_base; $url_ru[ 0 ] = 'ru-ru';
		$url_en = $url_base; $url_en[ 0 ] = 'en-en';

		$this->menu->link(
			'languages' ,
			'Русский' ,
			$link->href( false , $url_ru , use_all_adress_data : false ) ,
			$url_base[ 0 ] === 'ru-ru'
		);
		$this->menu->link(
			'languages' ,
			'Английский' ,
			$link->href( false , $url_en , use_all_adress_data : false ) ,
			$url_base[ 0 ] === 'en-en'
		);

		# --- projects ---
		$this->menu->link(
			'projects' ,
			'Damoony' ,
			$link->href( false , [ $url_base[ 0 ] , 'news' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === 'news'
		);
		$this->menu->link(
			'projects' ,
			'Asptour' ,
			$link->href( false , [ $url_base[ 0 ] , 'profile' ] , use_all_adress_data : false ) ,
			( $url_base[ 1 ] ?? '' ) === 'profile'
		);
	}

	protected function release ( ) : void {
		$root = $this->visuotag->root( );

		$slot = $root->find( class : 'top_sitename' );
		if ( $slot !== null ) $slot->content( $this->sitename );

		$slot = $root->find( class : 'top_siteabout' );
		if ( $slot !== null ) $slot->content( $this->siteabout );

		$slot = $root->find( class : 'top_projects' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'projects' ) );

		$slot = $root->find( class : 'top_languages' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'languages' ) );
	}
}