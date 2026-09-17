<?PHP
# файл: 
# описание:
# 

class visuopage_data {
	# vars
	public null|visuorelease $visuorelease = null;
	public null|visuopage $visuopage = null;
	public string $href = '';
	public string $pagename = '';
	public null|string $commentary = null;
	# methods
	public function __construct( null|visuorelease $visuorelease = null , null|visuopage $visuopage = null ){
	}
	
	public function add(
		string $href ,
		string $pagename , 
		null|string $commentary = null
	){
		$this->href = $href;
		$this->pagename = $pagename;
		$this->commentary = $commentary;
	}
}

?>