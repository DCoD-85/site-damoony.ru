<?PHP

namespace damoony;

class alliasglobal_right_login {
	# --- refs ---
	public \main $main;
	public alliasglobal_right $right;

	# --- methods ---
	public function __construct ( alliasglobal_right $right ) {
		$this->main  = $right->main;
		$this->right = $right;
		$this->data( );
	}

	public function data ( ) : void {
		# пусто — наполнение right для login появится позже
	}
}