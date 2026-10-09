<?PHP

namespace <domain_ns>\www;

class dataprovider_test extends dataprovider_base {

	public function ping ( ) : string {
		$row = $this->query( 'SELECT VERSION() AS v' )->row( );
		return (string) ( $row[ 'v' ] ?? 'unknown' );
	}

	public function now ( ) : string {
		$row = $this->query( 'SELECT NOW() AS n' )->row( );
		return (string) ( $row[ 'n' ] ?? '' );
	}
}