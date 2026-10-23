<?PHP

namespace damoony_ru;

class alliasglobal_center_oxydog {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_center $center;

	public function __construct ( alliasglobal_center $center ) {
		$this->main    = $center->main;
		$this->center = $center;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'center_oxydog.data.php';
		if ( is_file( $file ) ) include $file;
	}
}