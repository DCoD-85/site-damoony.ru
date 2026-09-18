<?PHP
# 
# 
# 

class library_url {
	
	private null|library $library = null;
	
	public array $static_domain = [ ];
	public array $static_allias = [ ];
	public array $static_base = [ ];
	public array $static_vars = [ ];
	
	public function __construct( null|library $library ) {
		$this->library = $library;
		# init
		$this->init( );
	}
	
	public function init ( ) {
		$main = $this->library->main;

		$path_domain = $main->main_path->get_domain( );
		$path_url = $main->main_path->get_url( );
		
		
		$this->static_domain = $this->domain( false , $path_domain );
		$this->static_allias = $this->domain( true , $path_domain );
		$this->static_base = $this->url( $path_url );
		$this->static_vars = $this->vars( $path_url );
	} 

	public function about ( ){
		return '<div>This is module ' . __FILE__ . '</div>';
	}
	
	public function url ( null|string $str = null ) {
		if( is_null( $str ) ) $str = $_SERVER[ 'REQUEST_SCHEME' ] . '://' . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
		
		$returnData = [ ];
		preg_match( '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i' , $str , $returnData );
		
		return explode( '/' , substr( $returnData[ 5 ] , 1 , strlen( $returnData[ 5 ] ) - 1 ) );
	}
	
	public function urlfull ( null|string $str = null ) {
		if( is_null( $str ) ) $str = $_SERVER[ 'REQUEST_SCHEME' ] . '://' . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
		$returnData = [ ];
		preg_match( '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i' , $str , $returnData );
		
		return $returnData[ 5 ];
	}
	
	public function vars ( null|string $str = null ) {
		if( is_null( $str ) ) $str = $_SERVER[ 'REQUEST_SCHEME' ] . '://' . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
		
		$returnData = [ ];
		preg_match( '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i' , $str , $returnData );
		
		$return = [ ];
		$this->vars_correct( $return , explode( '&' , $returnData[ 7 ] ) );
		
		return $return;
	}
	
	public function vars_correct ( array &$return , array $vars ) {
		if( ! empty( $vars ) ){
			foreach( $vars as $key => $value ) {
				$cur = explode( '=' , $value );
				$cur_key = $cur[ 0 ];
				$cur_value = $cur[ 1 ];
				$return[ $cur_key ] = $cur_value;
			}
		}
	}
	
	public function domain ( bool $revert = true , null|string $path = null ) {
		if( is_null( $path ) ) $path = $_SERVER[ 'SERVER_NAME' ];
		
		$domain = explode( '.' , $path );
		if( $revert === true ){
			$domain = array_reverse( $domain );
			$domain[ 1 ] .= '.' . $domain[ 0 ];
			unset( $domain[ 0 ] );
			$domain = array_values( $domain );
		}
		
		return $domain;
	}
	
	
	public function get_return( array $var , array $whithout = [ ] , string $varname = '' , array &$return = [ ] ) {
		if( ! empty( $var ) ){
			foreach( $var as $k=>$v ){
				$add = true;
				if( $varname == '' ) $nvarname = $k;
				else $nvarname = $varname.'['.$k.']';
				
				if( ! empty( $whithout ) ){
					foreach( $whithout as $k1 ){
						if( $nvarname == $k1 ){
							$add = false;
							break;
						}
					}
				}
				
				if( $add === true ) {
					if( is_array( $v ) ) $this->get_return( $v , $whithout , $nvarname , $return );
					else $return[ $nvarname ] = $v;
				}
			}
		}
		return $return;
	}
	
}

?>