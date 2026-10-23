<?PHP

namespace damoony_ru\oxydog;

abstract class dataprovider_base {
	# --- refs ---
	public \main $main;
	public dataprovider $facade;

	# --- state ---
	protected string $localname = 'visuo_damoony';

	public function __construct ( dataprovider $facade ) {
		$this->facade = $facade;
		$this->main   = $facade->main;

		$domain = $this->main->main_settings->domain_real_name ?? '';
		$map    = $this->main->main_settings->domain_db ?? [];
		if ( is_array( $map ) && isset( $map[ $domain ] ) && $map[ $domain ] !== '' ) {
			$this->localname = (string) $map[ $domain ];
		}
	}

	# Открыть свою БД и вернуть query-объект.
	protected function query ( string $sql , array $params = [ ] ) : \db_query_interface {
		$this->main->db->open( $this->localname );
		return $this->main->db->query( $sql , $params );
	}
}