<?PHP

namespace damoony;

class alliasglobal_auth {
	# vars
	public \main $main;
	private bool $logged = true;
	private string $login;
	private string $password;
	# methods
	public function __construct( \main $main ) {
		$this->main = $main;
	}
	public function check ( ) {
		return $this->logged;
	}
}

?>