<?PHP

class language_load {
	# vars
	public main $main;
	public language $language;
	# methods
	public function __construct ( language $language ) {
		$this->main = $language->main;
		$this->language = $language;
	}

	public function init ( ) {
		$language = $this->main->main_settings->get( 'language' );
		$path_lang = __DIR__ . DS . 'langdata' . DS . $language . '.inc';

		$this->parser( $path_lang );
	}

	public function parser ( string $path_lang ) {
		$main = $this->main;
		$file = $main->library->file;
	
		$read = trim( $file->read( $path_lang ) );

		$read = explode( PHP_EOL , $read );
		# убираем комментарии
		foreach( $read as $key=>$value ) if( $value[ 0 ] == '#' ) unset( $read[ $key ] );

		$read = implode( PHP_EOL , $read );
		# $php_eol = PHP_EOL;
		$read = mb_ereg_replace( PHP_EOL . '[\t]{1,}' , '/$1/' , $read );
		
		$read = explode( PHP_EOL , $read );

		$newread = [ ];
		foreach( $read as $string ){
			$values = explode( '/$1/' , $string );
			$key = $values[ 0 ];
			if( count( $values ) > 1 ){
				$value = $values;
				unset( $value[ 0 ] );
				$value = implode( PHP_EOL , $value );
			} else $value = $values[ 1 ];

			$key = trim( $key );
			$value = trim( $value );
			
			$read[ $key ] = $value;
			$newread[ $key] = $value;
		}

		$this->language->strings = $newread;
		# return $read;
	}
}

?>