<?PHP

class visuorelease_scheme {
	### vars
	public main $main;
	
	public string $scheme_name = 'default';
	public string $schemesub_name = 'default';
	public null|visuorelease $visuorelease = null;
	public null|visuometa $visuometa = null;
	public null|visuotag $visuotag = null;
	# .. VARS
	# .. METHODS
	public function __construct( null|visuorelease $visuorelease , string $scheme_name = 'default' , string $subscheme_name = 'base' ){
		# .. adding vars ..
		$this->visuorelease = $visuorelease;
		$this->scheme_type = 'release';
		$this->scheme_name = $scheme_name;
		$this->subscheme_name = $subscheme_name;
		$this->visuometa = new visuometa( $this->visuorelease );
		$this->visuotag = new visuotag( $this->visuorelease , null , 'root' , true , true );
		$this->visuotag_final = new visuotag( $this->visuorelease , null , 'root' , true , true );
		
		# preset
		$this->preset_vm_default = new visuometa( $this->visuorelease );
		$this->preset_vm_error = new visuometa( $this->visuorelease );
		$this->preset_vt_default = new visuotag( $this->visuorelease , null , 'root' , true , true );
		$this->preset_vt_error = new visuotag( $this->visuorelease , null , 'root' , true , true );
		
		# start functions
		# $this->preset_init( [ __FILE__ , __LINE__ , __METHOD__ ] );
		# $this->init( [ __FILE__ , __LINE__ , __METHOD__ ] );
	}
	
	private function preset_init( array $fo = [ ] ){
		
		# meta
		/*
		$this->preset_vm_default->add_link( )->data( 'css' . DSA . 'main.css' );
		$this->preset_vm_error->add_link( )->data( 'css' . DSA . 'main.css' );
		$this->visuometa = $this->preset_vm_error;
		# visuotag
		# :: default
		$this->preset_vt_default->tag( '!doctype html' , false , false )
		->tag( 'html' , class: 'cl_html' )
		->tag( 'head' , class: 'cl_head' )
			->tag( 'title' )->content( 'This is title' )
			->html( )
				->tag( 'body' , class: 'cl_body' )
					->tag( 'div' , class: 'cl_struct_top' , parent: null )->struct( 'struct_top' )
					->tag( 'div' , class: 'cl_struct_left' , parent: 'body' )->struct( 'struct_left' )
					->tag( 'div' , class: 'cl_struct_right' , parent: 'body' )->struct( 'struct_right' )
					->tag( 'div' , class: 'cl_struct_bottom' , parent: 'body' )->struct( 'struct_bottom' )->root( );
		# :: error
		$this->preset_vt_error->tag( '!doctype html' , false , false )
		->tag( 'html' , class: 'cl_html' )
		->tag( 'head' , class: 'cl_head' )
			->meta( $this->visuometa )
			->html( )
				->tag( 'body' , class: 'cl_body' )
					->tag( 'div' , class: 'cl_structError_top' )->struct( 'structError_top' )->parent( )
					->tag( 'div' , class: 'cl_structError_top2' )->struct( 'structError_top2' )->parent( )
					->tag( 'div' , class: 'cl_structError_bottom' )->struct( 'structError_bottom' )->root( );
		*/
		print '<div>preset_init( );<br />' . __METHOD__ . ' <> ' . __FILE__ . ':' . __LINE__ . '</div>';
	}
	
	public function init_set( bool $init ){
		$this->init = $init;
	}
	
	public function init( array $fo = [ ] ){
		$scheme_name = $this->scheme_name;
		$visuoscheme_class_name = __CLASS__ . '_' . $scheme_name;
		if( class_exists( $visuoscheme_class_name ) && method_exists( $visuoscheme_class_name , '__construct' ) ){
		}
		else {
			die( '<div>Class [ ' . $visuoscheme_class_name . ' ] или Method не существует. [ ' . __FILE__ . ':' . __LINE__ . ' ][ ' . ( $fo ? $fo[ 2 ] . ' ' . $fo[ 0 ] . ':' . $fo[ 1 ] : '--' ) . ' ]</div>' );
		}
		
		switch( $scheme_name ){
			# case '': $this->visuoscheme = new $visuoscheme_class_name( $this->visuorelease , $scheme_name ); break;
			# default : $this->visuoscheme = new visuorelease_scheme_default( $this->visuorelease , $this->subscheme_name); break;
			# case 'error' : $this->scheme = new visuorelease_scheme_error( $this ); break;
			case 'default' : $this->scheme = new visuorelease_scheme_default( $this ); break;
			default : $this->scheme = new visuorelease_scheme_default( $this ); break;
		}
		# if( ! is_null( $this->scheme ) ) $this->scheme->struct_init( );
		
		return $this->visuorelease;
	}	
	
	public function clear( ){
		$this->scheme = null;
		$this->init = false;
	}	
	
	public function set( string $scheme_name , bool $scheme_clear = false , bool $scheme_init = true ){
		$this->scheme_name = $scheme_name;
		if( $scheme_clear === true ) $this->clear( );
		if( $scheme_init === true ) $this->init( );
		return $this;
	}
	public function meta( ){
		# return $this->scheme->visuometa;
		return $this->visuometa;
	}	
	public function visuotag( ){
		return $this->scheme->visuotag;
	}	
	public function visuostruct( ){
		
	}	
	public function release( ){
		$this->visuotag_final = $this->scheme->release( );
		$meta = $this->meta( )->append( $this->preset_vm_error );
		
		$this->visuotag_final->root( )->find( 'head' )->meta( $meta );
	}
}

class visuorelease_scheme_default {
	### VARS
	public null|visuorelease $visuorelease = null;
	public null|visuorelease_scheme $visuorelease_scheme = null;
	public null|visuometa $visuometa = null;
	public null|visuotag $visuotag = null;
	
	public null|visuometa $vm_default = null;
	public null|visuostruct $vs_default_top = null;
	public null|visuostruct $vs_default_left = null;
	public null|visuostruct $vs_default_right = null;
	public null|visuostruct $vs_default_bottom = null;
	
	### METHODS
	public function __construct( visuorelease_scheme $visuorelease_scheme ){
		$this->visuorelease = $visuorelease_scheme->visuorelease;
		$this->visuorelease_scheme = $visuorelease_scheme;
		# visuometa
		$this->vm_default = new visuometa( $this->visuorelease );
		# visuostruct
		$this->vs_default_top = new visuostruct( $this->visuorelease , null , 'root' , true );
		$this->vs_default_left = new visuostruct( $this->visuorelease , null , 'root' , true );
		$this->vs_default_right = new visuostruct( $this->visuorelease , null , 'root' , true );
		$this->vs_default_bottom = new visuostruct( $this->visuorelease , null , 'root' , true );
		
		# $this->struct_init( );
	}
	
	# public function visuostruct_set( string $varname , null|visuostruct $visuostruct ){
	# 	if( isset( $this->$varname ) || is_null( $this->$varname ) ){
	# 		$this->$varname = $visuostruct;
	# 	}
	# }
	
	# public function meta_return( ){
	# 	return $this->vm_default;
	# }
	
	public function struct_init( ){
		#visuostruct
		$this->vs_default_top
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		$this->vs_default_left
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		$this->vs_default_right
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		$this->vs_default_bottom
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
	}
	public function release( ){
		return $this->vt;
	}
}
/*
class visuorelease_scheme_error {
	### VARS
	public visuorelease $visuorelease;
	public visuorelease_scheme $visuorelease_scheme;
	public null|visuotag $visuotag = null;
	public null|visuometa $vm = null;
	public null|visuostruct $vs_error_top = null;
	public null|visuostruct $vs_error_bottom = null;
	
	### METHODS
	public function __construct( visuorelease_scheme $visuorelease_scheme ){
		$this->visuorelease = $visuorelease_scheme->visuorelease;
		$this->visuorelease_scheme = $visuorelease_scheme;
		$this->visuotag = $this->visuorelease_scheme->preset_vt_error;
		
		# visuometa
		$this->vm = $this->visuorelease_scheme->preset_vm_error;
		# visuostruct
		$this->vs_error_top = new visuostruct( $this->visuorelease , null , 'root' , true );
		$this->vs_error_bottom = new visuostruct( $this->visuorelease , null , 'root' , true );
	}
	
	public function visuostruct_set( string $varname , null|visuostruct $visuostruct ){
		if( isset( $this->$varname ) || is_null( $this->$varname ) ){
			$this->$varname = $visuostruct;
		}
	}
	
	public function meta_return( ){
		return $this->vm;
	}
	
	public function struct_init( ){
		#visuostruct
		$this->vs_error_top
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		$this->vs_error_bottom
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
	}
	public function release( ){
		# $preset_vt_error = $this->visuorelease_scheme->preset_vt_error;
		# print $preset_vt_error->releasetree( );
		# print get_class( $this->vs_error_top->release( ) );
		$this->visuotag->struct_set( 'structError_top' , $this->vs_error_top );
		$this->visuotag->struct_set( 'structError_bottom' , $this->vs_error_bottom );
		return $this->visuotag;
	}
}
*/

/*
class visuorelease_scheme_error2 {
	### VARS
	public visuorelease $visuorelease;
	public visuorelease_scheme $visuorelease_scheme;
	public null|visuostruct $vs_error_top = null;
	public null|visuostruct $vs_error_bottom = null;
	
	### METHODS
	public function __construct( visuorelease_scheme $visuorelease_scheme ){
		$this->visuorelease = $visuorelease_scheme->visuorelease;
		$this->visuorelease_scheme = $visuorelease_scheme;
		# visuostruct
		$this->vs_error_top = new visuostruct( $this->visuorelease , null , 'root' , true );
		$this->vs_error_bottom = new visuostruct( $this->visuorelease , null , 'root' , true );
	}
	
	public function visuostruct_set( string $varname , null|visuostruct $visuostruct ){
		if( isset( $this->$varname ) || is_null( $this->$varname ) ){
			$this->$varname = $visuostruct;
		}
	}
	
	public function init( ){
		# visuotag
		$vt = $this->visuorelease_scheme->vt_error;
		#visuostruct
		$vs1 = $vs1
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		$vs2 = new visuostruct( $this->visuorelease , null , 'root' , true );
		$vs2 = $vs2->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		$vt->struct_set( 'struct1' , $this->vs_base1 ?: $vs1 );
		$vt->struct_set( 'struct2' , $this->vs_base2 ?: $vs2 );
		
		$this->vt = $vt;
		# $this->vs_error_base = $vs;
	}
	public function release( ){
		return $this->vt;
	}
}
	*/
?>