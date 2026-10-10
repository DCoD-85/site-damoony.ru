<?PHP

namespace idoam_ru;

class alliasglobal_debug extends alliasblock {
	public string $alliasglobal_name = 'debug';

	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'debug.data.php';
		if ( is_file( $file ) ) include $file;
	}
}