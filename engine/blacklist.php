<?PHP

class blacklist {
	# --- refs ---
	public null|\main $main = null;

	# --- state ---
	public bool   $mode     = true;   # true = blacklist (deny), false = whitelist (allow)
	public string $data_dir = '';     # data directory; default __DIR__/blacklist

	# --- methods ---
    public function __construct ( null|\main $main = null , null|string $data_dir = null ) {
        $this->main     = $main;
        $this->data_dir = $data_dir ?? dirname( __DIR__ ) . DS . 'secure' . DS . 'blacklist';
    }

	public function init ( ) : void {
		if ( $this->mode === true ) {
			$this->check_blacklist( );
		} else {
			$this->check_whitelist( );
		}
	}

	# --- checks ---
	public function check_blacklist ( ) : void {
		$ip = $this->remote_ip( );
		if ( $ip === null ) return;

		$file = $this->blacklist_file( );
		if ( ! is_file( $file ) ) return;

		$ips = $this->load_ips( $file );
		if ( isset( $ips[ $ip ] ) ) {
			$this->deny( 'Error: badip.' );
		}
	}

	public function check_whitelist ( ) : void {
		$ip = $this->remote_ip( );
		if ( $ip === null ) return;

		$file = $this->whitelist_file( );
		if ( ! is_file( $file ) ) return;

		$ips = $this->load_ips( $file );
		if ( isset( $ips[ $ip ] ) ) {
			$this->allow( );
		} else {
			$this->deny( 'Error: access denied' , 404 );
		}
	}

	# --- responses (overridable in tests) ---
	public function allow ( ) : void {
		# hook point, no-op by default
	}

	public function deny ( string $message , int $http_code = 0 ) : void {
		if ( $http_code > 0 ) http_response_code( $http_code );
		die( $message !== '' ? $message : 'Error' );
	}

	# --- helpers ---
	protected function remote_ip ( ) : null|string {
		$ip = $_SERVER[ 'REMOTE_ADDR' ] ?? null;
		return is_string( $ip ) && $ip !== '' ? $ip : null;
	}

	protected function blacklist_file ( ) : string {
		return $this->data_dir . DS . 'blacklist.txt';
	}

	protected function whitelist_file ( ) : string {
		$settings = $this->main?->main_settings;
		$domain   = $settings?->domain_real_name ?? '';
		$allias   = $settings?->allias_real_name ?? '';

		$name = ( is_string( $allias ) && $allias !== '' )
			? 'whitelist_' . $domain . '_' . $allias . '.txt'
			: 'whitelist.txt';

		return $this->data_dir . DS . $name;
	}

	protected function load_ips ( string $file ) : array {
		$content = file_get_contents( $file );
		if ( $content === false ) return [ ];

		$lines = preg_split( '/\r\n|\n|\r/' , $content );
		$ips   = [ ];

		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( $line === '' ) continue;
			if ( $line[ 0 ] === '#' ) continue;   # комментарий
			$ips[ $line ] = true;
		}

		return $ips;
	}
}