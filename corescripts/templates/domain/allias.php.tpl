<?PHP

namespace <domain_ns>;


class allias {
	# vars
	public \main $main;
	public alliasinfo $alliasinfo;
	
	# methods
	public function __construct ( \main $main ) {
		$this->main = $main;
		
		$this->alliasinfo = new alliasinfo( $this );
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$allias_accepted = $this->main->main_settings->allias_accepted( );
		$this->alliasinfo->set( 'name' , $allias_accepted );
		( $allias_accepted ) or error( true , true , __FILE__ , __LINE__ );
		
		( include_once $path = __DIR__ . DS . 'allias' . DS . $allias_accepted . DS . 'include.php' ) or error_file( $path , __FILE__ , __LINE__ );
	}
}


class alliasinfo {
	# vars
	public allias $allias;
	public mixed $name = null;
	public mixed $subname = null;
	public array $history = [ ];
	# methods
	public function __construct ( allias $allias ) {
		$this->allias = $allias;
	}
	public function set ( string $var , mixed $value , bool $add_as_array = false ) {
		$add_as_array === true ? $this->{$var}[ ] = $value : $this->{$var} = $value;
		
		$this->history[ $var ][ ] = $value;
	}
	public function ashtml ( ) {
		
	}
	public function astag ( ) {
		$vt = new \visuotag( );
		
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
					
						$vt->tag( 'div' )->content( '- - - ' . $name1 . ' = ' . $value2 );
					}
				}
			} else {
				$type = gettype( $value );
				if( $type === 'array' ) {
					$vt = $vt->tag( 'div' )->content( $name . ' = ' . $value );
					foreach( $value as $array_value ){
						$vt->tag( 'div' )->content( $array_value );
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