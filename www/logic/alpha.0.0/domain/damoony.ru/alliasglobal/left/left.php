<?PHP

namespace damoony;

class alliasglobal_left extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'left';
	public bool   $settings_asmenu   = false;

	# --- methods ---
	protected function scheme ( ) : void {
		$this->visuotag
			->tag( 'div' , class : 'menu' , return : false )
			->tag( 'div' , class : 'menuactions' );
	}

	protected function data ( ) : void {
		$main   = $this->main;
		$domain = $main->main_settings->domain_accepted( false );
		$accept = $main->main_settings->allias_accepted( );

		if ( is_null( $accept ) ) return;

		$path  = __DIR__ . DS . $this->alliasglobal_name . '_' . $accept . '.php';
		$class = '\\' . $domain . '\\alliasglobal_' . $this->alliasglobal_name . '_' . $accept;

		if ( include_once $path ) {
			if ( class_exists( $class ) ) {
				new $class( $this );
			}
		}
	}

	protected function release ( ) : void {
		$root = $this->visuotag->root( );

		$slot = $root->find( class : 'menu' );
		if ( $slot !== null ) {
			$slot->append( $this->menu->as_tag( 'nav' ) );
		}

		$slot = $root->find( class : 'menuactions' );
		if ( $slot !== null ) {
			$slot->append( $this->menu->as_tag( 'actions' ) );
		}
	}
}