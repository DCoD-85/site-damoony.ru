<?php

/*
    ---
    ---
    ---
*/

class visuostruct_ajax {
	# vars
	public null|visuostruct $visuostruct = null;

	public bool $error = false;
	public null|visuostruct_ajax_address $address = null;
	public null|visuostruct_ajax_header $header = null;
	public null|visuostruct_ajax_top $top = null;
	public null|visuostruct_ajax_left $left = null;
	public null|visuostruct_ajax_center $center = null;
	public null|visuostruct_ajax_right $right = null;
	public null|visuostruct_ajax_bottom $bottom = null;
	# methods
	public function __construct ( visuostruct $visuostruct ) {
		$this->visuostruct = $visuostruct;
	}
	public function address ( ) {
		return $this->address = is_null( $this->address ) ? new visuostruct_ajax_address( $this ) : $this->address;
	}
	public function header ( ) {
		return $this->header = is_null( $this->header ) ? new visuostruct_ajax_header( $this ) : $this->header;
	}
	public function top ( ) {
		return is_null( $this->top ) ? $this->top = new visuostruct_ajax_top( $this ) : $this->top;
	}
	public function left ( ) {
		return is_null( $this->left ) ? $this->left = new visuostruct_ajax_left( $this ) : $this->left;
	}
	public function center ( ) {
		return is_null( $this->center ) ? $this->center = new visuostruct_ajax_center( $this ) : $this->center;
	}
	public function right ( ) {
		return is_null( $this->right ) ? $this->right = new visuostruct_ajax_right( $this ) : $this->right;
	}
	public function bottom ( ) {
		return $this->bottom = is_null( $this->bottom ) ? new visuostruct_ajax_bottom( $this ) : $this->bottom;
	}

	public function as_json ( ) {
		$structs = [ ];
		$structs[ 'error' ] = $this->error;
		$structs[ 'address' ] = $this->address ? $this->address->release( ) : null;
		$structs[ 'layers' ] = [ ];
		$structs[ 'layers' ][ 'header' ] = $this->header ? $this->header->release( ) : null;
		$structs[ 'layers' ][ 'top' ] = $this->top ? $this->top->release( ) : null;
		$structs[ 'layers' ][ 'left' ] = $this->left ? $this->left->release( ) : null;
		$structs[ 'layers' ][ 'center' ] = $this->center ? $this->center->release( ) : null;
		$structs[ 'layers' ][ 'right' ] = $this->right ? $this->right->release( ) : null;
		$structs[ 'layers' ][ 'bottom' ] = $this->bottom ? $this->bottom->release( ) : null;

		return json_encode( $structs );
	}
}

# 
class visuostruct_ajax_address {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_ajax $visuostruct_ajax;
	public null|string $address = null;
	# methods
	public function __construct ( visuostruct_ajax $visuostruct_ajax ) {
		$this->visuostruct_ajax = $visuostruct_ajax;
		$this->visuostruct = $visuostruct_ajax->visuostruct;
	}
	public function set ( null|string $address ) {
		$this->address = $address;
	}
	public function astag ( ) {
		return $this->address;
	}
	public function release ( ) {
		return $this->address;
	}
}

# 
class visuostruct_ajax_header {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_ajax $visuostruct_ajax;
	public null|visuotag $visuotag = null;
	# methods
	public function __construct ( visuostruct_ajax $visuostruct_ajax ) {
		$this->visuostruct_ajax = $visuostruct_ajax;
		$this->visuostruct = $visuostruct_ajax->visuostruct;
	}
	public function set ( null|visuotag $visuotag ) {
		$this->visuotag = $visuotag;
	}
	public function astag ( ) {
		return $this->visuotag;
	}
	public function release ( ) {
		return ! is_null( $this->visuotag ) ? $this->visuotag->root( )->release( ) : null;
	}
}

# 
class visuostruct_ajax_top {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_ajax $visuostruct_ajax;
	public null|visuotag $visuotag = null;
	# methods
	public function __construct ( visuostruct_ajax $visuostruct_ajax ) {
		$this->visuostruct_ajax = $visuostruct_ajax;
		$this->visuostruct = $visuostruct_ajax->visuostruct;
	}
	public function set ( null|visuotag $visuotag ) {
		$this->visuotag = $visuotag;
	}
	public function astag ( ) {
		return $this->visuotag;
	}
	public function release ( ) {
		return ! is_null( $this->visuotag )
			? [ 'layer'=>'dt_top' , 'html'=>$this->visuotag->root( )->release( ) ]
			: null;
	}
}

# 
class visuostruct_ajax_center {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_ajax $visuostruct_ajax;
	public null|visuotag $visuotag = null;
	# methods
	public function __construct ( visuostruct_ajax $visuostruct_ajax ) {
		$this->visuostruct_ajax = $visuostruct_ajax;
		$this->visuostruct = $visuostruct_ajax->visuostruct;
	}
	public function set ( null|visuotag $visuotag ) {
		$this->visuotag = $visuotag;
	}
	public function astag ( ) {
		return $this->visuotag;
	}
	public function release ( ) {
		return ! is_null( $this->visuotag )
			? [ 'layer'=>'dt_center' , 'html'=>$this->visuotag->root( )->release( ) ]
			: null;
	}
}

# 
class visuostruct_ajax_bottom {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_ajax $visuostruct_ajax;
	public null|visuotag $visuotag = null;
	# methods
	public function __construct ( visuostruct_ajax $visuostruct_ajax ) {
		$this->visuostruct_ajax = $visuostruct_ajax;
		$this->visuostruct = $visuostruct_ajax->visuostruct;
	}
	public function set ( null|visuotag $visuotag ) {
		$this->visuotag = $visuotag;
	}
	public function astag ( ) {
		return $this->visuotag;
	}
	public function release ( ) {
		return ! is_null( $this->visuotag )
			? [ 'layer'=>'dt_bottom' , 'html'=>$this->visuotag->root( )->release( ) ]
			: null;
	}
}

?>