<?PHP

class visuoscheme_release {
	# vars
	public null|visuorelease $visuorelease = null;
	public string $scheme_type = '';
	public string $scheme_name = '';
	public string $subscheme_name = '';
	public null|visuoscheme_release_default|visuoscheme_release_error $visuoscheme = null;
	# functions
	public function __construct( null|visuorelease $visuorelease , string $scheme_name='default' , string $subscheme_name='base' ){
		$this->visuorelease = $visuorelease;
		$this->scheme_type = 'release';
		$this->scheme_name = $scheme_name;
		$this->subscheme_name = $subscheme_name;
	}
	
	public function set( string $scheme_name , bool $scheme_clear = true , bool $scheme_init = true ){
		$this->scheme_name = $scheme_name;
		if( $scheme_clear === true ) $this->clear( );
		if( $scheme_init === true ) $this->init( );
		return $this;
	}
	
	public function clear( ){
		
	}
	
	public function init( ){
		$scheme_name = $this->scheme_name;
		$visuoscheme_class_name = __CLASS__ . '_' . $scheme_name;
		
		switch( $scheme_name ){
			# case '': $this->visuoscheme = new $visuoscheme_class_name( $this->visuorelease , $scheme_name ); break;
			case 'error' : $this->visuoscheme = new visuoscheme_release_error( $this->visuorelease , $this->subscheme_name); break;
			default : $this->visuoscheme = new visuoscheme_release_default( $this->visuorelease , $this->subscheme_name); break;
		}
		
		$this->visuorelease->visuotag = $this->visuoscheme->release_init( );
		
		return $this->visuorelease;
	}
	
	public function list( ){
		$return_list = [ ];
		$list = get_object_vars( $this->visuoscheme );
		
		$pattern_utf8 = '/visuotag_([A-Za-z1-9]{1,})/u';
		
		foreach( $list as $k=>$v ){
			$text = $k;
			$result = [ ];
			preg_match_all( $pattern_utf8 , $text , $result , PREG_SET_ORDER );
			if( ! empty( $result ) ){
				$return_list[ ] = $result[ 0 ][ 1 ];
			}
		}
		return $return_list;
	}
	
	public function get( string $name ): visuotag{
		$this->visuoscheme->release_init( );
		return $this->visuoscheme->get( $name );
	}
}

class visuoscheme_release_default {
	# vars
	public null|visuorelease $visuorelease = null;
	public string $visuostruct_scheme_name = 'base';
	public visuotag $visuotag_login;
	public visuotag $visuotag_base;
	public visuotag $visuotag_page;
	
	public null|visuostruct $login_visuostruct_top = null;
	public null|visuostruct $login_visuostruct_left = null;
	public null|visuostruct $login_visuostruct_right = null;
	public null|visuostruct $login_visuostruct_bottom = null;
	# methods
	public function __construct( null | visuorelease $visuorelease = null , string $visuostruct_scheme_name = 'base' ){
		$this->visuorelease = $visuorelease;
		$this->visuostruct_scheme_name = $visuostruct_scheme_name;
	}
	
	public function struct_init( ){
		
		$vs = new visuostruct( null , null , 'root' , true );
		$vs = $vs
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		$this->login_visuostruct_top == null ? $this->login_visuostruct_top = $vs : true;
		$this->login_visuostruct_left == null ? $this->login_visuostruct_left = $vs : true;
		$this->login_visuostruct_right == null ? $this->login_visuostruct_right = $vs : true;
		$this->login_visuostruct_bottom == null ? $this->login_visuostruct_bottom = $vs : true;
	}
	
	public function struct_set( string $varname , visuostruct $visuostruct ){
		if( isset( $this->$varname ) ) $this->$varname = $visuostruct;
		return $this;
	}
	
	public function release_init( string $return_visuotag_name = 'base' ){
		
		$this->struct_init( );
		
		$visuorelease = $this->visuorelease;
		$meta = $visuorelease->meta();
		$meta_visuotag = $meta->as_tag( );
		$vt = new visuotag( null , null , 'root' , true , true );
		$vt = $vt->tag( '!DOCTYPE html' , false , false )
			->tag( 'html' )->id( 'htmlid' )
			->tag( 'head' )->append( $meta_visuotag )
			->tag( 'body' )
				->tag( 'block' )->tag( 'div' )->struct( 'top' , $this->login_visuostruct_top )
				->body( )->tag( 'block' )->tag( 'div' )->struct( 'left' , $this->login_visuostruct_left )
				->body( )->tag( 'block' )->tag( 'div' )->struct( 'right' , $this->login_visuostruct_right )
				->body( )->tag( 'block' )->tag( 'div' )->struct( 'bottom' , $this->login_visuostruct_bottom );
		
		# set vars
		$this->visuotag_login = $vt;
		$this->visuotag_base = $vt;
		$this->visuotag_page = $vt;
		
		return $this->visuotag_base;
	}
	public function get( string $name='base' ): visuotag {
		$var_name = 'visuotag_' . $name;
		if( ! isset( $this->{$var_name} ) ) return new visuotag( null , null , 'root' , true , true );
		
		return $this->$var_name;
	}
}

class visuoscheme_release_default_top
	extends visuoscheme_release_default {
	public function release( ){
		$visuorelease = $this->visuorelease;
		
		
		$vr = $this->visuorelease;
		
		$vs = new visuostruct( $vr , null , 'root' , true );
		# $vs->structdata( $structdata_add );
		
		$vs = $vs->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		return $vs->root( );
	}
}

class visuoscheme_release_error {
	# vars
	public null|visuorelease $visuorelease = null;
	public string $visuostruct_scheme_name = 'base';
	public visuotag $visuotag_login;
	public visuotag $visuotag_base;
	public visuotag $visuotag_page;
	
	public null|visuostruct $login_visuostruct_top = null;
	public null|visuostruct $login_visuostruct_left = null;
	public null|visuostruct $login_visuostruct_right = null;
	public null|visuostruct $login_visuostruct_bottom = null;
	# methods
	public function __construct( null | visuorelease $visuorelease = null , string $visuostruct_scheme_name = 'base' ){
		$this->visuorelease = $visuorelease;
		$this->visuostruct_scheme_name = $visuostruct_scheme_name;
	}
	
	public function struct_init( ){
	}
	
	public function struct_set( string $varname , visuostruct $visuostruct ){
		if( isset( $this->$varname ) ) $this->$varname = $visuostruct;
		return $this;
	}
	
	public function release_init( string $return_visuotag_name = 'base' ){
		
		$this->struct_init( );
		
		$visuorelease = $this->visuorelease;
		$meta = $visuorelease->meta();
		$meta_visuotag = $meta->as_tag( );
		$vt = new visuotag( null , null , 'root' , true , true );
		$vt = $vt->tag( '!DOCTYPE html' , false , false )
			->tag( 'html' )->id( 'htmlid' )
			->tag( 'head' )->append( $meta_visuotag )
			->html( )->tag( 'body' );
		
		# set vars
		$this->visuotag_login = $vt;
		$this->visuotag_base = $vt;
		$this->visuotag_page = $vt;
		
		return $this->visuotag_base;
	}
	public function get( string $name='base' ): visuotag {
		$var_name = 'visuotag_' . $name;
		if( ! isset( $this->{$var_name} ) ) return new visuotag( null , null , 'root' , true , true );
		
		return $this->$var_name;
	}
	
}

?>