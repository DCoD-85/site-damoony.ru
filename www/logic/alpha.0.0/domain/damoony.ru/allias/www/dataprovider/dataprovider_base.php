<?PHP

namespace damoony\www;

abstract class dataprovider_base {
	# --- refs ---
	public \main $main;
	public dataprovider $facade;

	# --- methods ---
	public function __construct ( dataprovider $facade ) {
		$this->facade = $facade;
		$this->main   = $facade->main;
	}

	protected function query ( string $db_name ) : \db_query {
		$db = $this->main->db->db( );
		$db->open( $db_name );
		return $db->query( );
	}
}