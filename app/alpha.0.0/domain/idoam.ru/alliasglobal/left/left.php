<?PHP

namespace idoam_ru;

class alliasglobal_left extends alliasblock {
	public string $alliasglobal_name = 'left';

	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'left.data.php';
		if ( is_file( $file ) ) include $file;
	}
}