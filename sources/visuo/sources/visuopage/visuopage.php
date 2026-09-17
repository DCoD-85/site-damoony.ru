<?PHP
# файл: 
# описание:
# 

class visuopage {
	# vars
	public null|visuorelease $visuorelease = null;
	public null|visuopage $visuopage = null;
	public null|visuopagedata $visuopagedata = null;
	public null|object $mainclass = null;
	public null|object|index $scriptclass = null;
	# data
	public function __construct(
		null|visuorelease $visuorelease
		, null|visuopage $visuopage = null
		, null|visuopagedata $visuopagedata = null
		, null|object $mainclass = null
	){
		$this->visuorelease = $visuorelease;
		$this->visuopage = $visuopage;
		$this->visuopagedata = $visuopagedata ?: new visuopagedata( $this );
		$this->mainclass = $mainclass;
	}
	public function visuopagedata( ){
		return $this->visuopagedata;
	}
	public function loadpage( ){
		if( $this->visuopagedata === null ) die( 'ERROR: ' . __METHOD__ . ' >< ' . __FILE__ . ':' . __LINE__  );
		$vpd = $this->visuopagedata;
		$scriptpath = $vpd->scriptpath;
		$classname = $vpd->classname;
		
		include_array( [ __FILE__ , __LINE__ ] , [ $scriptpath ] , [ $classname ] );
		$this->scriptclass = new $classname( $this );
	}
}

class visuopagedata {
	public visuopage $visuopage;
	public string $scriptpath;
	public string $scriptname;
	public string $classname;
	public string $pagename;
	public null|string $commentary;
	
	public function __construct( visuopage $visuopage ){
		$this->visuopage = $visuopage;
	}
	public function add(
		string $scriptpath = DS ,
		string $scriptname = '' ,
		string $classname = '' ,
		string $pagename = '' , 
		null|string $commentary = null
	){
		$this->scriptpath = $scriptpath;
		$this->scriptname = $scriptname;
		$this->classname = $classname;
		$this->pagename = $pagename;
		$this->commentary = $commentary;
	}
}

?>