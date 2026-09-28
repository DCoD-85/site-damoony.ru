<?PHP

namespace damoony;

class alliasglobal_center {
	# --- refs ---
	public \main $main;
	public \visuorelease $visuorelease;

	# --- state ---
	public \visuotag $tmp;
	public \visuotag $visuotag;
	public string $settings_lang = '';

	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main          = $main;
		$this->visuorelease  = $main->visuorelease;
		$this->visuotag      = new \visuotag( );
		$this->tmp           = new \visuotag( );
		$this->settings_lang = $main->main_settings->get( 'language' );
	}

	public function visuotag ( ) { return $this->visuotag; }
	public function as_tag   ( ) { return $this->visuotag; }
	public function release  ( ) { }
}