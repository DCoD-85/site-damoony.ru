<?PHP

namespace damoony;

class alliasglobal_center extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'center';
	public bool   $settings_asmenu   = false;

	# --- custom ---
	public \visuotag $tmp;

	# --- methods ---
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->tmp = new \visuotag( );
		parent::__construct( $alliasglobal );
	}

	protected function scheme  ( ) : void { }
	protected function data    ( ) : void { }
	protected function release ( ) : void { }
}