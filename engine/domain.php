<?PHP

class domain {
	# vars
	public main $main;
	public domaininfo $domaininfo;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
		$this->domaininfo = new domaininfo( $this );
		$this->init( );
	}
	public function init ( ) {
		$main = $this->main;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$path = $url_allias[ 0 ];
		
		$domain_accepted = $this->main->main_settings->domain_accepted( );
		$this->domaininfo->set( 'name' , $domain_accepted );
		( $domain_accepted ) or error( true , true , __FILE__ , __LINE__ );
		
		$logic_ver = $this->main->main_settings->get( 'logic' );
		( include_once $path = dirname(__DIR__) . DS . 'app' . DS . $logic_ver . DS . 'domain' . DS . $domain_accepted . DS . 'allias.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = dirname(__DIR__) . DS . 'app' . DS . $logic_ver . DS . 'domain' . DS . $domain_accepted . DS . 'alliasglobal.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = dirname(__DIR__) . DS . 'app' . DS . $logic_ver . DS . 'domain' . DS . $domain_accepted . DS . 'alliasscheme.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = dirname(__DIR__) . DS . 'app' . DS . $logic_ver . DS . 'domain' . DS . $domain_accepted . DS . 'dataprovider.php' ) or error_file( $path , __FILE__ , __LINE__ );
	}
}

class domaininfo {
	# vars
	public domain $domain;
	public mixed $name = null;
	public mixed $subname = null;
	public array $history = [ ];
	# methods
	public function __construct ( domain $domain ) {
		$this->domain = $domain;
	}
	public function set ( string $var , mixed $value , bool $add_as_array = false ) {
		$add_as_array === true ? $this->{$var}[ ] = $value : $this->{$var} = $value;
		
		$this->history[ $var ][ ] = $value;
	}
	public function ashtml ( ) {
		
	}
	public function astag ( ) {
		$vt = new visuotag( );
		
		$a = [ 'name'=>$this->name ,
					'subname'=>$this->subname ,
					'history'=>$this->history ];
		
		foreach( $a as $name=>$value ){
			$vt_old = $vt;
			
			if( $name == 'history' ){
				$vt = $vt->tag( 'div' );
				$vt->tag( 'div' )->content( 'history' );
				$vt3 = $vt;
				foreach( $value as $name1=>$value1 ) {
					foreach( $value1 as $name2=>$value2 ) {
						if( is_array( $value2 ) ){
							$vt3 = $vt;
							$vt = $vt->tag( 'div' )->content( '- - - ' . $name1 . ' ' )->content( ' (array) ' );
							foreach( $value2 as $value3 ){
								$vt->content( ' [ ' . $value3 . ' ] ' );
							}
							$vt = $vt3;
						} else {
							$vt->tag( 'div' )->content( '- - - ' . $name1 . ' = ' . $value2 );
						}
					}
				}
			} else {
				$type = gettype( $value );
				if( $type === 'array' ) {
					$vt = $vt->tag( 'div' )->content( $name . ' (' . $type . ') ' );
					foreach( $value as $array_value ){
						# print gettype( $array_value ) . '<br />';
						$vt->tag( 'span' )->content( ' [ ' . $array_value . ' ] ' );
					}
				} else {
					$vt = $vt->tag( 'div' )->content( $name . ' = ' . $value );
				}
			}
			
			$vt = $vt_old;
		}
		
		return $vt;
	}
}

?>