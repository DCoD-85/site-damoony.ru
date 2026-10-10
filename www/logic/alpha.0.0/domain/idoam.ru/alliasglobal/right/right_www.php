<?PHP

namespace idoam_ru;

class alliasglobal_right_www {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_right $right;

	public function __construct ( alliasglobal_right $right ) {
		$this->main  = $right->main;
		$this->right = $right;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'right_www.data.php';
		if ( is_file( $file ) ) include $file;
	}
}