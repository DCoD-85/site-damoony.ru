<?PHP

namespace damoony;

class alliasglobal_bottom extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'bottom';
	public bool   $settings_asmenu   = false;

	# --- custom ---
	public string $copyright      = 'damoony &copy; 2026';
	public string $agreement_href = '/policy';
	public string $agreement_text = 'Пользовательское соглашение';

	# --- methods ---
	protected function scheme ( ) : void {
		$this->visuotag
			->tag( 'div' , class : 'bottom_nav' , return : false )
			->tag( 'div' , class : 'bottom' , return : false )
			->tag( 'div' , class : 'bottom_box' , return : false )
			->tag( 'div' , class : 'agreement' )
				->tag( 'a' )
					->attrs_add( 'href' , $this->agreement_href )
					->content( $this->agreement_text )
				->parent( )
			->parent( )
			->tag( 'div' , class : 'copyright' )
				->content( $this->copyright );
	}

	protected function data ( ) : void {
		$main  = $this->main;
		$lib   = $main->library;
		$url   = $lib->url( );
		$link  = $lib->link( );

		$url_base = $url->static_base;

		$this->menu->link(
			'nav' ,
			'Новости' ,
			$link->href( false , [ $url_base[ 0 ] , 'news' ] , use_all_adress_data : false )
		);
		$this->menu->link(
			'nav' ,
			'Visuo' ,
			$link->href( false , [ $url_base[ 0 ] , 'visuo' ] , use_all_adress_data : false )
		);
		$this->menu->link(
			'nav' ,
			'Football' ,
			$link->href( false , [ $url_base[ 0 ] , 'football' ] , use_all_adress_data : false )
		);
		$this->menu->link(
			'nav' ,
			'О проекте' ,
			$link->href( false , [ $url_base[ 0 ] , 'about' ] , use_all_adress_data : false )
		);
	}

	protected function release ( ) : void {
		$root = $this->visuotag->root( );

		$slot = $root->find( class : 'bottom_nav' );
		if ( $slot !== null ) {
			$slot->append( $this->menu->as_tag( 'nav' ) );
		}
	}
}