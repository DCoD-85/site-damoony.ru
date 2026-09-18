<?PHP

class visuostruct {
	# vars
	public main $main;
	public null|visuostruct_min $min = null;
	public null|visuostruct_ajax $ajax = null;
	/*
	public null|visuostruct_minerror $minerror = null;
	public null|visuostruct_minmessages $minmessages = null;
	public null|visuostruct_minrelease $minrelease = null;
	public null|visuostruct_minform $minform = null;
	public null|visuostruct_minreferer $minreferer = null;
	*/
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
	}
	public function min ( ) {
		( include_once $path = __DIR__ . DS . 'visuostruct' . DS . 'visuostruct_min.php' ) or error_file( $path , __FILE__ , __LINE__);
		return $this->min = new visuostruct_min( $this );
	}
	public function ajax ( ) {
		( include_once $path = __DIR__ . DS . 'visuostruct' . DS . 'visuostruct_ajax.php' ) or error_file( $path , __FILE__ , __LINE__);
		return $this->ajax = new visuostruct_ajax( $this );
	}
}


?>