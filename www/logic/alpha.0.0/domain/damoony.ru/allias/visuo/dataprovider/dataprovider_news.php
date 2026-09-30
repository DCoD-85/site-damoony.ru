<?PHP

namespace damoony\visuo;

class dataprovider_news extends dataprovider_base {

	# --- whitelist полей, разрешённых к записи ---
	private const WRITABLE = [
		'slug'         => '%s' ,
		'title'        => '%s' ,
		'excerpt'      => '%s' ,
		'content_md'   => '%s' ,
		'content_html' => '%s' ,
		'status'       => '%s' ,
		'author_id'    => '%d' ,
		'lang'         => '%s' ,
		'published_at' => '%s' ,
	];

	# --- READ ---

	public function recent ( int $limit = 10 , string $lang = 'ru-ru' ) : array {
		$limit = max( 1 , min( 100 , $limit ) );
		return $this->query(
			'SELECT id, slug, title, excerpt, content_html, author_id, lang, published_at, created_at
			 FROM news
			 WHERE status = %s AND lang = %s
			 ORDER BY published_at DESC, id DESC
			 LIMIT %d' ,
			[ 'published' , $lang , $limit ]
		)->result( );
	}

	public function by_slug ( string $slug , string $lang = 'ru-ru' ) : array {
		$row = $this->query(
			'SELECT id, slug, title, excerpt, content_md, content_html, status,
			        author_id, lang, published_at, created_at, updated_at
			 FROM news
			 WHERE slug = %s AND lang = %s
			 LIMIT 1' ,
			[ $slug , $lang ]
		)->row( );
		return $row ?: [ ];
	}

	public function by_id ( int $id ) : array {
		$row = $this->query(
			'SELECT id, slug, title, excerpt, content_md, content_html, status,
			        author_id, lang, published_at, created_at, updated_at
			 FROM news
			 WHERE id = %s
			 LIMIT 1' ,
			[ $id ]
		)->row( );
		return $row ?: [ ];
	}

	public function slug_exists ( string $slug , string $lang , int $exclude_id = 0 ) : bool {
		$row = $this->query(
			'SELECT 1 FROM news
			 WHERE slug = %s AND lang = %s AND id <> %d
			 LIMIT 1' ,
			[ $slug , $lang , $exclude_id ]
		)->row( );
		return ! empty( $row );
	}

	# --- WRITE ---

	public function create ( array $data ) : int {
		$data = $this->filter_writable( $data );

		if ( empty( $data['slug'] ) || empty( $data['title'] ) ) {
			return 0;
		}
		$data += [
			'excerpt'      => '' ,
			'content_md'   => null ,
			'content_html' => null ,
			'status'       => 'draft' ,
			'author_id'    => null ,
			'lang'         => 'ru-ru' ,
			'published_at' => null ,
		];

		$cols    = array_keys( $data );
		$holders = [ ];
		$params  = [ ];
		foreach ( $data as $col => $val ) {
			$holders[ ] = self::WRITABLE[ $col ];
			$params[ ]  = $val;
		}

		$sql = 'INSERT INTO news ( `' . implode( '` , `' , $cols ) . '` ) VALUES ( '
		     . implode( ' , ' , $holders ) . ' )';

		$this->query( $sql , $params )->insert_id( );
		return (int) $this->main->db->last_id( );
	}

	public function update ( int $id , array $data ) : void {
		$data = $this->filter_writable( $data );
		if ( empty( $data ) ) {
			return;
		}

		$sets   = [ ];
		$params = [ ];
		foreach ( $data as $col => $val ) {
			$sets[ ]   = '`' . $col . '` = ' . self::WRITABLE[ $col ];
			$params[ ] = $val;
		}
		$params[ ] = $id;

		$sql = 'UPDATE news SET ' . implode( ' , ' , $sets ) . ' WHERE id = %d';

		$this->query( $sql , $params )->affected( );
	}

	public function publish ( int $id ) : void {
		$this->query(
			'UPDATE news
			 SET status = %s, published_at = COALESCE( published_at , NOW( ) )
			 WHERE id = %s' ,
			[ 'published' , $id ]
		)->affected( );
	}

	public function archive ( int $id ) : void {
		$this->query(
			'UPDATE news SET status = %s WHERE id = %s' ,
			[ 'archived' , $id ]
		)->affected( );
	}

	public function delete ( int $id ) : void {
		$this->query( 'DELETE FROM news WHERE id = %s' , [ $id ] )->affected( );
	}

	# --- helpers ---

	private function filter_writable ( array $data ) : array {
		$out = [ ];
		foreach ( $data as $k => $v ) {
			if ( isset( self::WRITABLE[ $k ] ) ) {
				$out[ $k ] = $v;
			}
		}
		return $out;
	}
}