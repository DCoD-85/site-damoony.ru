<?PHP

namespace idoam_ru;

class alliasglobal_top extends alliasblock {
	public string $alliasglobal_name = 'top';

	# --- custom ---
	public string $top_sitename  = 'idoam.ru';
	public string $top_siteabout = '&nbsp;';

	# --- data ---
	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'top.data.php';
		if ( is_file( $file ) ) include $file;
	}

	# ===============================================================
	#   СТРУКТУРА
	# ===============================================================

	protected function make_tophead ( ) : \visuotag {
		$menu = $this->menu;

		$vt   = new \visuotag( );
		$head = $vt->tag( 'div' , class : 'tophead' );

		$head->tag( 'div' , class : 'topheaddomain' );
		$head->tag( 'div' , class : 'topheadauth'   );
		$head->tag( 'div' , class : 'topheadskin'   );
		$head->tag( 'div' , class : 'topheadlang'   );

		return $vt->root( );
	}

	protected function make_toppro ( ) : \visuotag {
		$vt  = new \visuotag( );
		$pro = $vt->tag( 'div' , class : 'toppro' );

		$pro->tag( 'div' , class : 'topproname' )
			->content( $this->top_sitename );

		$logo = $pro->tag( 'div' , class : 'topprologo' );
		$logo->tag( 'div' , class : 'topprologopro' );
		$logo->tag( 'div' , class : 'toppronav' );

		return $vt->root( );
	}

	protected function make_toplinks ( ) : \visuotag {
		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'toplinks' );
		return $vt->root( );
	}
}