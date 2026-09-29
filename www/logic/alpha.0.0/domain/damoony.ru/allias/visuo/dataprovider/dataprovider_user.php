<?PHP

namespace damoony\visuo;

class dataprovider_user extends dataprovider_base {

	# --- AUTH ---

	public function by_login ( string $login ) : array {
		$row = $this->query(
			'SELECT id, login, password_hash, email, display_name, avatar, status, created_at, last_login_at
			 FROM users WHERE login = %s LIMIT 1' ,
			[ $login ]
		)->row( );
		return $row ?: [ ];
	}

	public function by_id ( int $id ) : array {
		$row = $this->query(
			'SELECT id, login, email, display_name, avatar, status, created_at, last_login_at
			 FROM users WHERE id = %s LIMIT 1' ,
			[ $id ]
		)->row( );
		return $row ?: [ ];
	}

	public function exists ( string $login ) : bool {
		$row = $this->query(
			'SELECT 1 FROM users WHERE login = %s LIMIT 1' ,
			[ $login ]
		)->row( );
		return ! empty( $row );
	}

	public function create ( string $login , string $password_hash , string $email , string $display_name = '' ) : int {
		$this->query(
			'INSERT INTO users ( login , password_hash , email , display_name ) VALUES ( %s , %s , %s , %s )' ,
			[ $login , $password_hash , $email , $display_name ]
		)->insert_id( );
		return $this->main->db->last_id( );
	}

	public function touch_login ( int $id ) : void {
		$this->query(
			'UPDATE users SET last_login_at = NOW( ) WHERE id = %s' ,
			[ $id ]
		)->affected( );
	}

	# --- ROLES ---

	public function roles ( int $user_id ) : array {
		return $this->query(
			'SELECT r.id, r.code, r.name, r.description
			 FROM roles r
			 INNER JOIN user_roles ur ON ur.role_id = r.id
			 WHERE ur.user_id = %s' ,
			[ $user_id ]
		)->result( );
	}

	public function has_role ( int $user_id , string $role_code ) : bool {
		$row = $this->query(
			'SELECT 1 FROM user_roles ur
			 INNER JOIN roles r ON r.id = ur.role_id
			 WHERE ur.user_id = %s AND r.code = %s LIMIT 1' ,
			[ $user_id , $role_code ]
		)->row( );
		return ! empty( $row );
	}

	public function assign_role ( int $user_id , string $role_code ) : void {
		$this->query(
			'INSERT IGNORE INTO user_roles ( user_id , role_id )
			 SELECT %s , id FROM roles WHERE code = %s' ,
			[ $user_id , $role_code ]
		)->affected( );
	}

	# --- SESSIONS ---

	public function session_create ( int $user_id , string $token , string $ip , string $ua , int $ttl_seconds = 86400 * 30 ) : int {
		$this->query(
			'INSERT INTO sessions ( user_id , token , ip , user_agent , expires_at )
			 VALUES ( %s , %s , %s , %s , DATE_ADD( NOW( ) , INTERVAL %d SECOND ) )' ,
			[ $user_id , $token , $ip , $ua , $ttl_seconds ]
		)->insert_id( );
		return $this->main->db->last_id( );
	}

	public function session_find ( string $token ) : array {
		$row = $this->query(
			'SELECT s.id, s.user_id, s.expires_at, u.login
			 FROM sessions s
			 INNER JOIN users u ON u.id = s.user_id
			 WHERE s.token = %s AND s.expires_at > NOW( ) LIMIT 1' ,
			[ $token ]
		)->row( );
		return $row ?: [ ];
	}

	public function session_drop ( string $token ) : void {
		$this->query( 'DELETE FROM sessions WHERE token = %s' , [ $token ] )->affected( );
	}

	public function session_cleanup ( ) : int {
		return $this->query( 'DELETE FROM sessions WHERE expires_at <= NOW( )' )->affected( );
	}
}