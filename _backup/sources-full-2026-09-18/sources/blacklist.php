<?PHP

class blacklist {
	# vars
	public null|main $main = null;
	public bool $blacklist = true;	// true - blacklist | false - whitelist
	# methods
	public function __construct ( null|main $main = null ) {
		$this->main = $main;
	}
	# # 
	public function init ( ) {
		
		if( $this->blacklist === true ) $this->blacklist( );
		else if( $this->blacklist === false ) $this->whitelist( );
	}
	# #
	
	public function blacklist ( ) {
		
		$ip = $_SERVER[ 'REMOTE_ADDR' ];
		$badip = [ ];
		
		$file = __DIR__ . DS . 'blacklist' . DS . 'blacklist.txt';
		if ( file_exists( $file ) ){
			$content = file_get_contents( $file ); // {Link: Документация file_get_contents https://www.php.net/manual/ru/function.file-get-contents.php}
			$badip = array_flip( explode( BR , $content ) );
			
			if( isset( $badip[ $ip ] ) ) $this->error( 'Error: badip.' );
		}
	}
	
	public function whitelist ( ) {
		$main = $this->main;
		$library = $main->library;
		$url = $library->url( );
		
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$ip = $_SERVER[ 'REMOTE_ADDR' ];
		$badip = [ ];
		
		$domain = $main->main_settings->domain_real_name;
		$allias = $main->main_settings->allias_real_name;
		$whitelist_name = 'whitelist_' . $domain . '_' . ( $allias ?: '' ) . '.txt';
	
		$file = __DIR__ . DS . 'blacklist' . DS . $whitelist_name;
		
		
		if ( file_exists( $file ) ){
			$content = file_get_contents( $file ); // {Link: Документация file_get_contents https://www.php.net/manual/ru/function.file-get-contents.php}
			$badip = array_flip( explode( BR , $content ) );
			
			if( isset( $badip[ $ip ] ) ) { $this->ok( 'is ok' ); }
			else{
				http_response_code( 404 );
				$this->error( 'Error: access denied' );
			}
		}
		
	}
	
	public function ok ( string $message ) {
		# print '<div>' . $message . '</div>';
	}
	public function error ( string $message ) {
		# $vt = new visuotag( );
		# 
		# $vt->tag( 'div' )->content( 'ERROR' );
		die( $message ?: 'Error' );
	}
	
}

# $b = new blacklist( );
# $b->init( );

?>