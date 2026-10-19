<?PHP

namespace damoony_ru\visuo;

class dataprovider_doc extends dataprovider_base {

	# --- SECTIONS ---

	public function section_by_slug ( string $slug ) : array {
		$row = $this->query(
			'SELECT id, parent_id, slug, title, description, sort, status
			 FROM doc_sections WHERE slug = %s LIMIT 1' ,
			[ $slug ]
		)->row( );
		return $row ?: [ ];
	}

	public function section_by_id ( int $id ) : array {
		$row = $this->query(
			'SELECT id, parent_id, slug, title, description, sort, status
			 FROM doc_sections WHERE id = %s LIMIT 1' ,
			[ $id ]
		)->row( );
		return $row ?: [ ];
	}

	public function sections_tree ( bool $only_published = true ) : array {
		$where = $only_published ? "WHERE status = 'published'" : '';
		return $this->query(
			'SELECT id, parent_id, slug, title, description, sort
			 FROM doc_sections ' . $where . '
			 ORDER BY parent_id ASC, sort ASC, title ASC'
		)->result( );
	}

	public function sections_children ( int $parent_id ) : array {
		return $this->query(
			'SELECT id, slug, title, sort FROM doc_sections
			 WHERE parent_id = %s AND status = %s
			 ORDER BY sort ASC, title ASC' ,
			[ $parent_id , 'published' ]
		)->result( );
	}

	# --- ARTICLES ---

	public function article_by_slug ( string $slug ) : array {
		$row = $this->query(
			'SELECT a.id, a.section_id, a.slug, a.title, a.excerpt,
			        a.content_md, a.content_html, a.status, a.author_id,
			        a.created_at, a.updated_at, a.published_at,
			        s.slug AS section_slug, s.title AS section_title
			 FROM doc_articles a
			 LEFT JOIN doc_sections s ON s.id = a.section_id
			 WHERE a.slug = %s LIMIT 1' ,
			[ $slug ]
		)->row( );
		return $row ?: [ ];
	}

	public function article_by_id ( int $id ) : array {
		$row = $this->query(
			'SELECT id, section_id, slug, title, excerpt, content_md, content_html,
			        status, author_id, created_at, updated_at, published_at
			 FROM doc_articles WHERE id = %s LIMIT 1' ,
			[ $id ]
		)->row( );
		return $row ?: [ ];
	}

	public function articles_by_section ( int $section_id , bool $only_published = true ) : array {
		$extra = $only_published ? " AND status = 'published'" : '';
		return $this->query(
			'SELECT id, slug, title, excerpt, sort, updated_at, published_at
			 FROM doc_articles
			 WHERE section_id = %s' . $extra . '
			 ORDER BY sort ASC, title ASC' ,
			[ $section_id ]
		)->result( );
	}

	public function articles_recent ( int $limit = 10 ) : array {
		return $this->query(
			'SELECT id, slug, title, excerpt, published_at
			 FROM doc_articles
			 WHERE status = %s
			 ORDER BY published_at DESC LIMIT %d' ,
			[ 'published' , $limit ]
		)->result( );
	}

	public function article_create ( array $data ) : int {
		$this->query(
			'INSERT INTO doc_articles ( section_id , slug , title , excerpt , content_md , content_html , status , author_id )
			 VALUES ( %s , %s , %s , %s , %s , %s , %s , %s )' ,
			[
				$data[ 'section_id' ] ?? null ,
				$data[ 'slug' ] ?? '' ,
				$data[ 'title' ] ?? '' ,
				$data[ 'excerpt' ] ?? '' ,
				$data[ 'content_md' ] ?? '' ,
				$data[ 'content_html' ] ?? '' ,
				$data[ 'status' ] ?? 'draft' ,
				$data[ 'author_id' ] ?? null ,
			]
		)->insert_id( );
		return $this->main->db->last_id( );
	}

	public function article_update ( int $id , array $data ) : void {
		$fields = [ ];
		$values = [ ];
		foreach ( [ 'section_id' , 'slug' , 'title' , 'excerpt' , 'content_md' , 'content_html' , 'status' ] as $key ) {
			if ( ! array_key_exists( $key , $data ) ) continue;
			$fields[ ] = '`' . $key . '` = %s';
			$values[ ] = $data[ $key ];
		}
		if ( empty( $fields ) ) return;

		$values[ ] = $id;
		$this->query(
			'UPDATE doc_articles SET ' . implode( ' , ' , $fields ) . ' WHERE id = %s' ,
			$values
		)->affected( );
	}

	public function article_publish ( int $id ) : void {
		$this->query(
			'UPDATE doc_articles SET status = %s , published_at = NOW( ) WHERE id = %s' ,
			[ 'published' , $id ]
		)->affected( );
	}

	# --- VERSIONS ---

	public function versions ( int $article_id ) : array {
		return $this->query(
			'SELECT v.id, v.title, v.author_id, v.comment, v.created_at, u.login AS author_login
			 FROM doc_versions v
			 LEFT JOIN users u ON u.id = v.author_id
			 WHERE v.article_id = %s
			 ORDER BY v.created_at DESC' ,
			[ $article_id ]
		)->result( );
	}

	public function version_create ( int $article_id , array $data ) : int {
		$this->query(
			'INSERT INTO doc_versions ( article_id , title , content_md , content_html , author_id , comment )
			 VALUES ( %s , %s , %s , %s , %s , %s )' ,
			[
				$article_id ,
				$data[ 'title' ] ?? '' ,
				$data[ 'content_md' ] ?? '' ,
				$data[ 'content_html' ] ?? '' ,
				$data[ 'author_id' ] ?? null ,
				$data[ 'comment' ] ?? '' ,
			]
		)->insert_id( );
		return $this->main->db->last_id( );
	}

	public function version_by_id ( int $version_id ) : array {
		$row = $this->query(
			'SELECT id, article_id, title, content_md, content_html, author_id, comment, created_at
			 FROM doc_versions WHERE id = %s LIMIT 1' ,
			[ $version_id ]
		)->row( );
		return $row ?: [ ];
	}

	# --- SEARCH ---

	public function search ( string $query , int $limit = 20 ) : array {
		return $this->query(
			'SELECT id, slug, title, excerpt,
			        MATCH( title , content_md ) AGAINST ( %s IN NATURAL LANGUAGE MODE ) AS score
			 FROM doc_articles
			 WHERE status = %s AND MATCH( title , content_md ) AGAINST ( %s IN NATURAL LANGUAGE MODE )
			 ORDER BY score DESC LIMIT %d' ,
			[ $query , 'published' , $query , $limit ]
		)->result( );
	}
}