<?PHP

class visuorelease_settings {
	# --- refs ---
	public null|\visuorelease $visuorelease = null;

	# --- state ---
	private string $language = 'ru';

	# --- methods ---
	public function __construct ( null|\visuorelease $visuorelease = null ) {
		$this->visuorelease = $visuorelease;

		$cookie_lang = $_COOKIE[ 'language' ] ?? null;
		if ( is_string( $cookie_lang ) && $cookie_lang !== '' ) {
			$this->language = $cookie_lang;
		}
	}

	public function get ( string $varname ) : mixed {
		return property_exists( $this , $varname )
			? $this->{$varname}
			: null;
	}

	public function set ( string $varname , mixed $value ) : visuorelease_settings {
		if ( property_exists( $this , $varname ) ) {
			$this->{$varname} = $value;
		}
		return $this;
	}
}