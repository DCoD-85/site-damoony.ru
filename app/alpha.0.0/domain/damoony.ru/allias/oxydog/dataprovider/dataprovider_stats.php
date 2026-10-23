<?PHP

namespace damoony_ru\oxydog;

class dataprovider_stats extends dataprovider_base {

	public function view_log ( int $article_id , ?int $user_id , string $ip , string $ua ) : int {
		$this->query(
			'INSERT INTO doc_views ( article_id , user_id , ip , user_agent )
			 VALUES ( %s , %s , %s , %s )' ,
			[ $article_id , $user_id , $ip , mb_substr( $ua , 0 , 255 ) ]
		)->insert_id( );
		return $this->main->db->last_id( );
	}

	public function views_count ( int $article_id ) : int {
		$row = $this->query(
			'SELECT COUNT(*) AS c FROM doc_views WHERE article_id = %s' ,
			[ $article_id ]
		)->row( );
		return (int) ( $row[ 'c' ] ?? 0 );
	}

	public function views_total ( ) : int {
		$row = $this->query( 'SELECT COUNT(*) AS c FROM doc_views' )->row( );
		return (int) ( $row[ 'c' ] ?? 0 );
	}

	public function views_recent ( int $days = 7 ) : int {
		$row = $this->query(
			'SELECT COUNT(*) AS c FROM doc_views WHERE created_at >= DATE_SUB( NOW( ) , INTERVAL %d DAY )' ,
			[ $days ]
		)->row( );
		return (int) ( $row[ 'c' ] ?? 0 );
	}

	public function top_articles ( int $limit = 10 , int $days = 30 ) : array {
		return $this->query(
			'SELECT a.id, a.slug, a.title, COUNT(v.id) AS views
			 FROM doc_articles a
			 LEFT JOIN doc_views v ON v.article_id = a.id
			      AND v.created_at >= DATE_SUB( NOW( ) , INTERVAL %d DAY )
			 WHERE a.status = %s
			 GROUP BY a.id
			 ORDER BY views DESC LIMIT %d' ,
			[ $days , 'published' , $limit ]
		)->result( );
	}
}