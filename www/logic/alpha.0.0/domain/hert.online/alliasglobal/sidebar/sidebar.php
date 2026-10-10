<?PHP

namespace hert_online;

class alliasglobal_sidebar extends alliasblock {
	public string $alliasglobal_name = 'sidebar';

	# --- data ---
	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'sidebar.data.php';
		if ( is_file( $file ) ) include $file;
	}
}