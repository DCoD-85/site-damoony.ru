<?PHP

namespace damoony\visuo;

class dataprovider_files extends dataprovider_base {

	# --- READ ---

	public function all_active ( ) : array {
		return $this->query(
			'SELECT id, slug, title, description, filename, filesize, mime_type, version, downloads, created_at
			 FROM files
			 WHERE status = %s
			 ORDER BY id ASC' ,
			[ 'active' ]
		)->result( );
	}

	public function by_slug ( string $slug ) : array {
		$row = $this->query(
			'SELECT id, slug, title, description, filename, filesize, mime_type, version, downloads, status, created_at
			 FROM files
			 WHERE slug = %s
			 LIMIT 1' ,
			[ $slug ]
		)->row( );
		return $row ?: [ ];
	}

	public function by_id ( int $id ) : array {
		$row = $this->query(
			'SELECT id, slug, title, description, filename, filesize, mime_type, version, downloads, status, created_at
			 FROM files
			 WHERE id = %s
			 LIMIT 1' ,
			[ $id ]
		)->row( );
		return $row ?: [ ];
	}

	# --- WRITE ---

	public function create ( array $data ) : int {
		$slug     = (string) ( $data['slug']     ?? '' );
		$title    = (string) ( $data['title']    ?? '' );
		$filename = (string) ( $data['filename'] ?? '' );
		if ( $slug === '' || $title === '' || $filename === '' ) {
			return 0;
		}

		$this->query(
			'INSERT INTO files
			     ( slug , title , description , filename , filesize , mime_type , version , status )
			 VALUES ( %s , %s , %s , %s , %d , %s , %s , %s )' ,
			[
				$slug ,
				$title ,
				(string) ( $data['description'] ?? '' ) ,
				$filename ,
				(int)    ( $data['filesize']    ?? 0 ) ,
				(string) ( $data['mime_type']   ?? 'application/octet-stream' ) ,
				(string) ( $data['version']     ?? '' ) ,
				(string) ( $data['status']      ?? 'active' ) ,
			]
		)->insert_id( );
		return (int) $this->main->db->last_id( );
	}

	public function update_filesize ( int $id , int $filesize ) : void {
		$this->query(
			'UPDATE files SET filesize = %d WHERE id = %s' ,
			[ $filesize , $id ]
		)->affected( );
	}

	public function set_status ( int $id , string $status ) : void {
		if ( ! in_array( $status , [ 'active' , 'hidden' ] , true ) ) {
			return;
		}
		$this->query(
			'UPDATE files SET status = %s WHERE id = %s' ,
			[ $status , $id ]
		)->affected( );
	}

	public function delete ( int $id ) : void {
		$this->query( 'DELETE FROM files WHERE id = %s' , [ $id ] )->affected( );
	}

	# --- DOWNLOADS ---

	public function increment_downloads ( int $file_id , ?int $user_id , string $ip ) : void {
		// журнал
		$this->query(
			'INSERT INTO file_downloads ( file_id , user_id , ip )
			 VALUES ( %s , %s , %s )' ,
			[ $file_id , $user_id , $ip ]
		)->insert_id( );

		// денормализованный счётчик
		$this->query(
			'UPDATE files SET downloads = downloads + 1 WHERE id = %s' ,
			[ $file_id ]
		)->affected( );
	}

	public function downloads_stats ( int $file_id , int $days = 30 ) : array {
		$days = max( 1 , min( 365 , $days ) );
		return $this->query(
			'SELECT DATE( created_at ) AS d , COUNT( * ) AS c
			 FROM file_downloads
			 WHERE file_id = %s AND created_at >= DATE_SUB( NOW( ) , INTERVAL %d DAY )
			 GROUP BY DATE( created_at )
			 ORDER BY d ASC' ,
			[ $file_id , $days ]
		)->result( );
	}

	public function downloads_total ( int $file_id ) : int {
		$row = $this->query(
			'SELECT COUNT( * ) AS c FROM file_downloads WHERE file_id = %s' ,
			[ $file_id ]
		)->row( );
		return (int) ( $row['c'] ?? 0 );
	}
}