<?PHP

# ------------------------------------------------------------
#  visuostruct_min — контейнер для JSON-ответа (AJAX "min")
# ------------------------------------------------------------

class visuostruct_min {
	# --- refs ---
	public null|visuostruct $visuostruct = null;

	# --- state ---
	public null|visuostruct_min_error    $error    = null;
	public null|visuostruct_min_messages $messages = null;
	public null|visuostruct_min_release  $release  = null;
	public null|visuostruct_min_form     $form     = null;
	public null|visuostruct_min_referer  $referer  = null;

	# --- methods ---
	public function __construct ( null|visuostruct $visuostruct = null ) {
		$this->visuostruct = $visuostruct;
	}

	public function error ( ) : visuostruct_min_error {
		return $this->error ??= new visuostruct_min_error( $this );
	}

	public function messages ( ) : visuostruct_min_messages {
		return $this->messages ??= new visuostruct_min_messages( $this );
	}

	public function release ( ) : visuostruct_min_release {
		return $this->release ??= new visuostruct_min_release( $this );
	}

	public function form ( ) : visuostruct_min_form {
		return $this->form ??= new visuostruct_min_form( $this );
	}

	public function referer ( ) : visuostruct_min_referer {
		return $this->referer ??= new visuostruct_min_referer( $this );
	}
	
	public function return_type ( ) : string {
		return 'json';
	}

	# ------------------------------------------------------------
	#  AS JSON
	# ------------------------------------------------------------
	public function as_json ( ) : string {
		$data = [
			'error'    => $this->error?->release( )    ?? false ,
			'messages' => $this->messages?->release( ) ?? ''    ,
			'release'  => $this->release?->release( )  ?? null  ,
			'form'     => $this->form?->release( )     ?? null  ,
			'referer'  => $this->referer?->release( )  ?? null  ,
		];

		$json = json_encode( $data , JSON_UNESCAPED_UNICODE );
		return $json === false ? '{}' : $json;
	}
}

# ------------------------------------------------------------
#  Base для всех min-элементов
# ------------------------------------------------------------

abstract class visuostruct_min_element {
	# --- refs ---
	public null|visuostruct     $visuostruct     = null;
	public null|visuostruct_min $visuostruct_min = null;

	# --- methods ---
	public function __construct ( visuostruct_min $visuostruct_min ) {
		$this->visuostruct_min = $visuostruct_min;
		$this->visuostruct     = $visuostruct_min->visuostruct;
	}
}

# ------------------------------------------------------------
#  error — bool
# ------------------------------------------------------------

class visuostruct_min_error extends visuostruct_min_element {
	# --- state ---
	public bool $error = false;

	public function set ( bool $error = false ) : void {
		$this->error = $error;
	}

	public function release ( ) : bool {
		return $this->error;
	}
}

# ------------------------------------------------------------
#  messages — HTML строка (visuotag)
# ------------------------------------------------------------

class visuostruct_min_messages extends visuostruct_min_element {
	# --- state ---
	public null|visuotag $visuotag = null;

	public function set ( null|visuotag $visuotag = null ) : void {
		$this->visuotag = $visuotag;
	}

	public function release ( ) : string {
		if ( $this->visuotag === null ) return '';
		return $this->visuotag->root( )->release( );
	}
}

# ------------------------------------------------------------
#  release — html + layer + popup флаги
# ------------------------------------------------------------

class visuostruct_min_release extends visuostruct_min_element {
	# --- state ---
	public null|visuotag $html = null;
	public string        $layer = '';
	public bool          $popup = false;
	public bool          $popup_back = false;
	public bool          $popup_navigate = false;

	public function set ( null|visuotag $html = null ,
	                      string $layer = '' ,
	                      bool $popup = false ,
	                      bool $popup_back = false ,
	                      bool $popup_navigate = false ) : void {
		$this->html           = $html;
		$this->layer          = $layer;
		$this->popup          = $popup;
		$this->popup_back     = $popup_back;
		$this->popup_navigate = $popup_navigate;
	}

	public function release ( ) : array {
		$html_str = '';

		if ( $this->html !== null ) {
			$root = $this->html->root( );
			$root->prepare( );
			$html_str = $root->release( );
		}

		return [
			'html'           => $html_str ,
			'layer'          => $this->layer ,
			'popup'          => $this->popup ,
			'popup_back'     => $this->popup_back ,
			'popup_navigate' => $this->popup_navigate ,
		];
	}
}

# ------------------------------------------------------------
#  form — reset / redirect
# ------------------------------------------------------------

class visuostruct_min_form extends visuostruct_min_element {
	# --- state ---
	public bool         $reset         = false;
	public null|string  $redirect      = null;
	public null|int     $redirect_time = null;

	public function set ( bool $reset = false ,
	                      null|string $redirect = null ,
	                      null|int $redirect_time = null ) : void {
		$this->reset         = $reset;
		$this->redirect      = $redirect;
		$this->redirect_time = $redirect_time;
	}

	public function release ( ) : array {
		return [
			'reset'         => $this->reset ,
			'redirect'      => $this->redirect ,
			'redirect_time' => $this->redirect_time ,
		];
	}
}

# ------------------------------------------------------------
#  referer — URL откуда пришли
# ------------------------------------------------------------

class visuostruct_min_referer extends visuostruct_min_element {
	# --- state ---
	public null|string $referer = null;

	public function set ( null|string $referer = null ) : void {
		$this->referer = $referer;
	}

	public function release ( ) : null|string {
		if ( $this->referer !== null ) return $this->referer;
		$http = $_SERVER[ 'HTTP_REFERER' ] ?? null;
		return is_string( $http ) ? $http : null;
	}
}

?>