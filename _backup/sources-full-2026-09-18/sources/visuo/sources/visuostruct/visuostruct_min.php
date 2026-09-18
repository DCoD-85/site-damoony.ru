<?PHP

/*
    ---
    ---
    ---
*/

class visuostruct_min {
	# vars
	public null|visuostruct $visuostruct = null;

	public null|visuostruct_min_error $error = null;
	public null|visuostruct_min_messages $messages = null;
	public null|visuostruct_min_release $release = null;
	public null|visuostruct_min_form $form = null;
	public null|visuostruct_min_referer $referer = null;
	# methods
	public function __construct ( visuostruct $visuostruct ) {
		$this->visuostruct = $visuostruct;
	}
	public function error ( ) {
		return $this->error = is_null( $this->error ) ? new visuostruct_min_error( $this ) : $this->error;
	}
	public function messages ( ) {
		return $this->messages = is_null( $this->messages ) ? new visuostruct_min_messages( $this ) : $this->messages;
	}
	public function release ( ) {
		return $this->release = is_null( $this->release ) ? new visuostruct_min_release( $this ) : $this->release;
	}
	public function form ( ) {
		return $this->form = is_null( $this->form ) ? new visuostruct_min_form( $this ) : $this->form;
	}
	public function referer ( ) {
		return $this->referer = is_null( $this->referer ) ? new visuostruct_min_referer( $this ) : $this->referer;
	}
}


class visuostruct_min_error {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_min $visuostruct_min;
	public bool $error = false;
	# methods
	public function __construct ( visuostruct_min $visuostruct_min ) {
		$this->visuostruct_min = $visuostruct_min;
		$this->visuostruct = $visuostruct_min->visuostruct;
	}
	public function set ( bool $error = false ) {
		$this->error = $error;
	}
	public function release ( ) {
		return $this->error;
	}
}

class visuostruct_min_messages {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_min $visuostruct_min;
	public null|visuotag $visuotag = null;
	private string $string = '';
	# methods
	public function __construct ( visuostruct_min $visuostruct_min ) {
		$this->visuostruct_min = $visuostruct_min;
		$this->visuostruct = $visuostruct_min->visuostruct;
	}
	public function set ( null|visuotag $visuotag = null ) {
		$this->visuotag = $visuotag;
	}
	public function release ( ) {
		return $this->visuotag->root( )->release( );
	}
}

class visuostruct_min_release {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_min $visuostruct_min;
	public visuotag $html;
	public string $layer = '';
	public bool $popup = false;
	public bool $popup_back = false;
	public bool $popup_navigate = false;
	# methods
	public function __construct ( visuostruct_min $visuostruct_min ) {
		$this->visuostruct_min = $visuostruct_min;
		$this->visuostruct = $visuostruct_min->visuostruct;
	}
	public function set ( visuotag $html , string $layer = '' , bool $popup = false , bool $popup_back = false , bool $popup_navigate = false ) {
		$this->html = $html;
		$this->layer = $layer;
		$this->popup = $popup;
		$this->popup_back = $popup_back;
		$this->popup_navigate = $popup_navigate;
	}
	public function release ( ) {
		$html = $this->html->root( );
		$html->prepare( );
		$html = $html->release( );
		# return
		return [
			'html'=>$html ,
			'layer'=>$this->layer ,
			'popup'=>$this->popup ,
			'popup_back'=>$this->popup_back ,
			'popup_navigate'=>$this->popup_navigate
		];
	}
}

class visuostruct_min_form {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_min $visuostruct_min;
	public null|visuotag $visuotag = null;
	public bool $reset = false;
	public null|string $redirect = null;
	public null|int $redirect_time = null;
	 # methods
	public function __construct ( visuostruct_min $visuostruct_min ) {
		$this->visuostruct_min = $visuostruct_min;
		$this->visuostruct = $visuostruct_min->visuostruct;
	}
	public function set ( bool $reset = false , null|string $redirect = null , null|int $redirect_time = null ) {
		$this->reset = $reset;
		$this->redirect = $redirect;
		$this->redirect_time = $redirect_time;
	}
	public function release ( ) {
		return [ 'reset'=>$this->reset , 'redirect'=>$this->redirect , 'redirect_time'=>$this->redirect_time ];
	}
}

class visuostruct_min_referer {
	# vars
	public visuostruct $visuostruct;
	public visuostruct_min $visuostruct_min;
	public null|visuotag $visuotag = null;
	public null|string $referer = null;
	 # methods
	public function __construct ( visuostruct_min $visuostruct_min ) {
		$this->visuostruct_min = $visuostruct_min;
		$this->visuostruct = $visuostruct_min->visuostruct;
	}
	public function set ( null|string $referer = null ) {
		$this->referer = $referer;
	}
	public function release ( ) {
		return $this->referer ?: $_SERVER[ 'HTTP_REFERER' ];
	}
}


# min...
/*
class visuostruct_minerror {
	# vars
	public visuostruct $visuostruct;
	public bool $error = false;
	# methods
	public function __construct ( visuostruct $visuostruct ) {
		$this->visuostruct = $visuostruct;
	}
	public function set ( bool $error = false ) {
		$this->error = $error;
	}
	public function release ( ) {
		return $this->error;
	}
}

class visuostruct_minmessages {
	# vars
	public visuostruct $visuostruct;
	public null|visuotag $visuotag = null;
	private string $string = '';
	# methods
	public function __construct ( visuostruct $visuostruct ) {
		$this->visuostruct = $visuostruct;
	}
	public function set ( null|visuotag $visuotag = null ) {
		$this->visuotag = $visuotag;
	}
	public function release ( ) {
		return $this->visuotag->root( )->release( );
	}
}

class visuostruct_minrelease {
	# vars
	public visuostruct $visuostruct;
	public visuotag $html;
	public string $layer = '';
	public bool $popup = false;
	# methods
	public function __construct ( visuostruct $visuostruct ) {
		$this->visuostruct = $visuostruct;
	}
	public function set ( visuotag $html , string $layer , bool $popup ) {
		$this->html = $html;
		$this->layer = $layer;
		$this->popup = $popup;
	}
	public function release ( ) {
		return [ 'html'=>$this->html->root( )->release( ) , 'layer'=>$this->layer , 'popup'=>$this->popup ];
	}
}

class visuostruct_minform {
	# vars
	public visuostruct $visuostruct;
	public null|visuotag $visuotag = null;
	public bool $reset = false;
	public null|string $redirect = null;
	public null|int $redirect_time = null;
	 # methods
	public function __construct ( visuostruct $visuostruct ) {
		$this->visuostruct = $visuostruct;
	}
	public function set ( bool $reset = false , null|string $redirect = null , null|int $redirect_time = null ) {
		$this->reset = $reset;
		$this->redirect = $redirect;
		$this->redirect_time = $redirect_time;
	}
	public function release ( ) {
		return [ 'reset'=>$this->reset , 'redirect'=>$this->redirect , 'redirect_time'=>$this->redirect_time ];
	}
}

class visuostruct_minreferer {
	# vars
	public visuostruct $visuostruct;
	public null|visuotag $visuotag = null;
	public null|string $referer = null;
	 # methods
	public function __construct ( visuostruct $visuostruct ) {
		$this->visuostruct = $visuostruct;
	}
	public function set ( string $referer = null ) {
		$this->referer = $referer;
	}
	public function release ( ) {
		return $this->referer;
	}
}
*/

?>