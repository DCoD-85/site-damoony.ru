<?PHP

class visuorelease_settings {
	# vars
	public visuorelease $visuorelease;
	private bool $scheme_use = true;
	private string $scheme_name = 'default';
	private string $language = '';
	# methods
	public function __construct ( visuorelease $visuorelease ) {
		$this->visuorelease = $visuorelease;
		if( $_COOKIE[ 'language' ] ) $this->language = $_COOKIE[ 'language' ] ?: 'ru';
	}
	public function set ( bool $scheme_use = false , string $shceme_name = 'default' ) {
		#
		$this->scheme_use = $scheme_use;
		$this->scheme_name = $shceme_name;		
	}
	public function get ( string $varname ) {
		return $this->{$varname}?:null;
	}
}

?>