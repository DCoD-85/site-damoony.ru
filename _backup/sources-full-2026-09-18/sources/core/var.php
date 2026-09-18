<?PHP

class core_var {
	# vars
	public main $main;
	public generate_css $css;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
		# ( include_once $path = 'generate' . DS . 'generate_css.php' ) or error_file( $path , __FILE__ , __LINE__ );

		# $this->css = new generate_css( $this );
	}

	public function check ( mixed $var , string $var_type , int $var_min , int $var_max ) {
		$vars = $this->main->library->vars;
		$check = $vars->check( $var , $var_type , $var_min , $var_max );
		if( $check == $var_type ) {
			return true;
		} else {
			return false;
		}
	}

	public function error ( string $message ) {
		$messages = $this->main->messages;
		$messages->add( 'error' , 'Заполните поля правильно: ' . $message , 'Заполните поле правильно: <b>' . $message . '</b>' );
	}
}

?>