<?PHP

class core_generate {
	# vars
	public main $main;
	public generate_css $css;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
		# ( include_once $path = 'generate' . DS . 'generate_css.php' ) or error_file( $path , __FILE__ , __LINE__ );

		# $this->css = new generate_css( $this );
	}
}

?>