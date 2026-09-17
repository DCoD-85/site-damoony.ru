<?PHP

class algoritmdata_ {
	# vars
	public array $available = [ ];
	public bool $auth = false;
	public bool $search = false;
	public bool $default = false;
	# methods
	public function __construct ( ) {
	}
	public function set ( array $available = [ ] ,
											bool $auth = false ,
											bool $search = false ,
											bool $default = false ) {
		$this->available = $available;
		$this->auth = $auth;
		$this->search = $search;
		$this->default = $default;
	}

	public function get_available ( ) {
		return $this->available;
	}
	public function get_auth ( ) {
		return $this->auth;
	}

	public function get_search ( ) {
		return $this->search;
	}

	public function get_default ( ) {
		return $this->default;
	}
}

?>