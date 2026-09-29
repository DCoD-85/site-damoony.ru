<?PHP

namespace damoony;

# Фасад домена.
# Держит фасады всех аллиасов, у которых есть dataprovider.
#
# Использование:
#   $main->dataprovider->current->…  ← текущий аллиас (ярлык)
#   $main->dataprovider->visuo->…    ← явно
#   $main->dataprovider->login->…    ← чужой аллиас

class dataprovider {
	# --- refs ---
	public \main $main;

	# --- allias providers ---
	public null|\damoony\www\dataprovider   $www   = null;
	public null|\damoony\login\dataprovider $login = null;
	public null|\damoony\visuo\dataprovider $visuo = null;

	# --- current ---
	public mixed $current = null;

	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->autoload( );
		$this->set_current( );
	}

	private function autoload ( ) : void {
		$allias_dir = __DIR__ . DS . 'allias';

		if ( ! is_dir( $allias_dir ) ) return;

		foreach ( glob( $allias_dir . DS . '*' , GLOB_ONLYDIR ) as $dir ) {
			$allias_name = basename( $dir );
			$file        = $dir . DS . 'dataprovider' . DS . 'dataprovider.php';

			if ( ! is_file( $file ) ) continue;

			( include_once $file ) or error_file( $file , __FILE__ , __LINE__ );

			$class = '\\damoony\\' . $allias_name . '\\dataprovider';
			if ( ! class_exists( $class ) ) continue;

			$this->{$allias_name} = new $class( $this->main );
		}
	}

	private function set_current ( ) : void {
		$current = $this->main->main_settings->allias_accepted( );
		if ( $current === null || $current === '' ) return;
		if ( ! isset( $this->{$current} ) ) return;

		$this->current = $this->{$current};
	}
}