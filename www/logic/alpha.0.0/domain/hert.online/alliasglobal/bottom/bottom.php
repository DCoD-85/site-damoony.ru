<?PHP

namespace hert_online;

class alliasglobal_bottom extends alliasblock {
	public string $alliasglobal_name = 'bottom';

	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'bottom.data.php';
		if ( is_file( $file ) ) include $file;
	}

	# ===============================================================
	#   СТРУКТУРА: многопанельная навигация
	# ===============================================================

	private function make_bottomnav ( ) : \visuotag {
		$main = $this->main;
		$link = $main->library->link( );
		$url  = $main->library->url( );
		$url_allias = $url->static_allias;

		$vt  = new \visuotag( );
		$nav = $vt->tag( 'div' , class : 'bottom_nav' );

		# --- панель: домен ---
		$panel = $nav->tag( 'div' , class : 'bottom_nav_panel' );
		$panel->tag( 'div' , class : 'bottom_nav_project' )
			->tag( 'div' , class : 'title' )->content( 'hert.online' )
			->parent( )
			->tag( 'div' , class : 'links' )
				->tag( 'a' )
					->attrs_add( 'href' , '/' )
					->content( 'Главная' );

		# --- панель: копирайт ---
		$nav->tag( 'div' , class : 'bottom_nav_copyright' )
			->content( '© ' . date( 'Y' ) . ' hert.online' );

		return $vt->root( );
	}
}