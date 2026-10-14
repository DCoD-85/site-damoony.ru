<?PHP

namespace hert_online\test;

abstract class dataprovider_base {
	# --- refs ---
	public \main $main;
	public dataprovider $facade;

	# --- state ---
	protected string $localname = 'test_hert_online';

	# --- methods ---
	public function __construct ( dataprovider $facade ) {
		$this->facade = $facade;
		$this->main   = $facade->main;
	}

	protected function query ( string $sql , array $params = [ ] ) : \db_query_interface {
		$this->main->db->open( $this->localname );
		return $this->main->db->query( $sql , $params );
	}
}