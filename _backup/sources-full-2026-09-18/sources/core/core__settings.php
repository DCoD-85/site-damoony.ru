<?PHP

# ! about : 
# ! author : 
# ! create : 
# ! modify : 

class core__settings {
	# vars
	public main $main;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
	}
	public function core ( ) {
		return $this->main->core;
	}
}