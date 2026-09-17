<?PHP

namespace damoony;

class alliasglobal_center {
	# vars
	public \main $main;
	public \visuorelease $visuorelease;
	
	public \visuotag $tmp;
	public \visuotag $visuotag;
	# methods
	public function __construct ( \main $main ) {
		$this->main = $main;
		
		$this->visuotag = new \visuotag( );
		
		$this->settings_lang = $main->main_settings->get( 'language' );		
	}

	public function visuotag ( ) {
		return $this->visuotag;
	}
	
	public function as_tag ( ) {
		return $this->visuotag;
	}
	
	public function release ( ) {
		
	}
}

?>