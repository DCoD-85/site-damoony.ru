<?PHP

namespace idoam_ru\www;

abstract class dataprovider_base {
	# --- refs ---
	public \main $main;
	public dataprovider $facade;

	# --- state ---
	protected string $localname = 'www_idoam_ru';

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