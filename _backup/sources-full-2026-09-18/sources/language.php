<?PHP

class language {
    # vars
    public main $main;
	private null|language_load $load = null;
	private null|language_save $save = null;
	public array $strings = [ ];

	public string $language = '';
	public array $data = [ ];
    public array $datatosave = [ ];
    # methods
    public function __construct ( main $main ) {
        $this->main = $main;

		$path_load = __DIR__ . DS . 'language' . DS . 'load.php';
		$path_save = __DIR__ . DS . 'language' . DS . 'save.php';

		( include_once $path = $path_load ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $path_save ) or error_file( $path , __FILE__ , __LINE__ );

		$this->load = new language_load( $this );
		$this->save = new language_save( $this );
    }
    
	public function load ( ) {
		return $this->load;
	}
	public function save ( ) {
		return $this->save;
	}

	public function get ( string $stringname , array $data , string $default_string = '' ) {
		$prepare = '';

		$this->strings_tosave[ $stringname ] = $default_string;

		if( ! empty( $this->strings[ $stringname ] ) ){
			return vsprintf( $this->strings[ $stringname ] , $data );
		}
		return vsprintf( $default_string , $data );
	}

	public function savedefaults ( ) {
		$main = $this->main;

		$strings = $this->strings_tosave;

		$file = $main->library->file;
		$filesave_path = __DIR__ . DS . 'language' . DS . 'save.txt.inc';
		$savedata = '';
		if( ! empty( $strings ) ){
			foreach( $strings as $key=>$value ){
				$savedata .= $key . '
	' . $value . '
';
			}
			$file->write( $filesave_path , $savedata );
		}


		return $savedata;
	}
}

?>