<?PHP

class language_save {
	# vars
	public main $main;
	public language $language;
	# methods
	public function __construct ( language $language ) {
		$this->main = $language->main;
		$this->language = $language;
		
	} 
}

?>