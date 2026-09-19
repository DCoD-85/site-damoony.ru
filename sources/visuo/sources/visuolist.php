<?PHP

class visuolist {
	# vars
	public array $vars = [ ];
	# methods
	public function __construct ( array $vars ) {
		$this->vars = $vars;
	}
	public function check ( string $var_need ) {
		if( ! empty( $this->vars ) ) {
			foreach( $this->vars as $v ) {
				if( $v === $var_need ) return true;
			}
		}
		return false;
	}
}

?>