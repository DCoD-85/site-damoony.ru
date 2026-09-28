<?PHP

namespace damoony;

class alliasglobal_left extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'left';
	public bool $settings_asmenu = true;

	# --- methods ---
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal_menu = new alliasglobal_menu( $alliasglobal , 'left' );
		parent::__construct( $alliasglobal );
	}

	protected function scheme ( ) : void {
		$this->visuotag
			->tag( 'div' , class : 'menu' , return : false )
			->tag( 'div' , class : 'menuactions' );
	}

	protected function data ( ) : void {
		$main = $this->main;

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
		if ( $this->alliasglobal_menu === null ) return;

		$root = $this->visuotag->root( );

		$slot_menu = $root->find( class : 'menu' );
		if ( $slot_menu !== null ) {
			$slot_menu->append( $this->alliasglobal_menu->as_tag( 'menuleft' ) );
		}

		$slot_actions = $root->find( class : 'menuactions' );
		if ( $slot_actions !== null ) {
			$slot_actions->append( $this->alliasglobal_menu->as_tag( 'menuactionsleft' ) );
		}
	}
}