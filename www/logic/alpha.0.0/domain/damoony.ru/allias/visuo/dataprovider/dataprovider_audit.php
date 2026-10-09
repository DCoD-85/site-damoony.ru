<?PHP

namespace damoony_ru\visuo;

class dataprovider_audit extends dataprovider_base {

	public function log ( ?int $user_id , string $action , string $entity_type = '' , ?int $entity_id = null , array $payload = [ ] , string $ip = '' ) : int {
		$payload_json = empty( $payload ) ? null : json_encode( $payload , JSON_UNESCAPED_UNICODE );

		$this->query(
			'INSERT INTO audit_log ( user_id , action , entity_type , entity_id , payload_json , ip )
			 VALUES ( %s , %s , %s , %s , %s , %s )' ,
			[ $user_id , $action , $entity_type , $entity_id , $payload_json , $ip ]
		)->insert_id( );
		return $this->main->db->last_id( );
	}

	public function recent ( int $limit = 50 ) : array {
		return $this->query(
			'SELECT a.id, a.user_id, a.action, a.entity_type, a.entity_id, a.payload_json, a.ip, a.created_at, u.login
			 FROM audit_log a
			 LEFT JOIN users u ON u.id = a.user_id
			 ORDER BY a.created_at DESC LIMIT %d' ,
			[ $limit ]
		)->result( );
	}

	public function by_entity ( string $entity_type , int $entity_id , int $limit = 100 ) : array {
		return $this->query(
			'SELECT id, user_id, action, payload_json, ip, created_at
			 FROM audit_log
			 WHERE entity_type = %s AND entity_id = %s
			 ORDER BY created_at DESC LIMIT %d' ,
			[ $entity_type , $entity_id , $limit ]
		)->result( );
	}

	public function by_user ( int $user_id , int $limit = 100 ) : array {
		return $this->query(
			'SELECT id, action, entity_type, entity_id, payload_json, ip, created_at
			 FROM audit_log
			 WHERE user_id = %s
			 ORDER BY created_at DESC LIMIT %d' ,
			[ $user_id , $limit ]
		)->result( );
	}
}