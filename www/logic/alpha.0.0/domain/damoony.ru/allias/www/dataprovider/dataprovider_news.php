<?PHP

namespace damoony\www;

class dataprovider_news extends dataprovider_base {

	public function recent ( int $limit = 5 ) : array {
		$q = $this->query( 'www_damoony' );
		$q->select(
			'SELECT id, title, created_at FROM news
			 WHERE status = %s ORDER BY created_at DESC LIMIT %s' ,
			[ 'published' , $limit ] ,
			limit : true , limitstep : $limit
		);
		return $q->result( );
	}

	public function by_id ( int $id ) : array {
		$q = $this->query( 'www_damoony' );
		$q->select(
			'SELECT id, title, content, created_at FROM news
			 WHERE id = %s LIMIT 1' ,
			[ $id ] ,
			limit : true , limitstep : 1
		);
		return $q->result( )[ 0 ] ?? [ ];
	}

	public function exists ( array $params ) : bool {
		$id = (int) ( $params[ 'id' ] ?? 0 );
		if ( $id <= 0 ) return false;

		$q = $this->query( 'www_damoony' );
		$q->select(
			'SELECT 1 FROM news WHERE id = %s LIMIT 1' ,
			[ $id ] ,
			limit : true , limitstep : 1
		);
		return ! empty( $q->result( ) );
	}
}