<?PHP

class db_class {
	# vars
	public null|db $db;
	public bool|null|mysqli $db_link = false;
	public null|db_config_row $db_config_row = null;
	# # class db_class_query
	public null|db_query $query = null;
	public array $query_log = [ ];
	# methods
	public function __construct ( null|db $db ) {
		$this->db = $db;
	}
	
	public function open ( string $localname = '' , bool $reconnect = false ) {
		if( $this->db_config_row === null ) $this->db_config_row = $this->db->get( $localname );
		// Елси указано не переподключаться к базе данных
		if( $reconnect === false ){
			if( ! empty( $this->db_link ) && $this->db_config_row->localname == $localname ) return ;
		} else {
			# if( $this->db_link ) $this->close( );
		}
		$this->db_config_row = $this->db->get( $localname );
		
		$db = $this->db_config_row;
		if( isset( $db ) ){
			if( ! $this->db_link = mysqli_connect( $db->dbhost , $db->username , $db->password , $db->dbname ) )
				die( 'DB error [DB host: <b>' . $db->dbhost . '</b>]' );
			else {
			}
		} else {
			die( 'Указанная База Данных не существует.' );
		}
		
		mysqli_query( $this->db_link , 'SET NAMES "utf8mb4"' );
		mysqli_query( $this->db_link , 'SET collation_connection = "UTF8mb4_unicode_ci"' );
		mysqli_query( $this->db_link , 'SET collation_server = "UTF8mb4_unicode_ci"' );
	}
	
	public function close( ){
		return $this->db_link = mysqli_close( $this->db_link );
	}

	public function status ( ) {
		$return = gettype( $this->db_link ) == 'object' ? get_class( $this->db_link ) : 'NULL';
		return $return;
	}
	
	public function query ( ) {// string $query , array $args = [ ] , bool $limit_all = false ){
		if( ! is_null( $this->query ) ) $this->query_log[ ] = $this->query;

		$this->query = new db_query( $this );
		
		return $this->query;
		
		/*
		$result = false;
		
		$query_query = $query;
		$query_args = $args;
		$query_args_new = [ ];
		
		if( ! empty( $query_args ) ) {
			foreach( $query_args as $k=>$v ){
				// opt : 
				unset( $opt );
				if( is_array( $v ) ){
					$value = $v[ 0 ];
					$opt = $v[ 1 ];
				}
				else {
					$value = $v;
				}
				if( ! isset( $opt ) ) $opt = 1;
				
			
				// NULL
				if( gettype( $value ) == 'NULL' ) $query_args_new[ $k ] = 'NULL';
				else if( gettype( $value ) == NULL ) $query_args_new[ $k ] = NULL;
				// String
				else if( gettype( $value ) == 'string' ){
					$query_args_new[ $k ] = mysqli_real_escape_string( $this->db_link , $value );
					
					if( $opt == 1 ) $query_args_new[ $k ] = '\'' . $query_args_new[ $k ] . '\'';
				}
				// Integer
				else if( is_integer( $value ) ) $query_args_new[ $k ] = mysqli_real_escape_string( $this->db_link , $value );
				// Double
				else if( is_double( $value ) ) $query_args_new[ $k ] = mysqli_real_escape_string( $this->db_link , $value );
				// Else
				else $query_args_new[ $k ] = mysqli_real_escape_string( $this->db_link , $v );
			}
			$query_query = vsprintf( $query_query , $query_args_new );
		}
		
		
		
		// Записываем в логи последний запрос
		$this->log_query_all[ ] = $query_query;
		$this->query_count++;
		
		$query_exist = $query_query;
		
		# if( $query_show == true ){
			$title = is_string( $query_show ) ? $query_show : 'Подготовленный запрос';
			
			// .. косметические изменения
			$query_exist = mb_ereg_replace( 'UPDATE' , '<i style=" font-style : normal ; font-weight : 600 ; ">UPDATE</i>' , $query_exist );
			$query_exist = mb_ereg_replace( 'SELECT' , '<i style=" font-style : normal ; font-weight : 600 ; ">SELECT</i>' , $query_exist );
			$query_exist = mb_ereg_replace( 'FROM' , '<i style=" font-style : normal ; font-weight : 600 ; ">FROM</i>' , $query_exist );
			$query_exist = mb_ereg_replace( 'WHERE' , '<i style=" font-style : normal ; font-weight : 600 ; ">WHERE</i>' , $query_exist );
			$query_exist = mb_ereg_replace( '\sAND\s' , ' <i style="font-weight:600;">AND</i> ' , $query_exist );
			$query_exist = mb_ereg_replace( '\sOR\s' , ' <i style="font-weight:600;">OR</i> ' , $query_exist );
			$query_exist = mb_ereg_replace( 'ORDER BY' , '<i style=" font-style : normal ; font-weight : 600 ; ">ORDER BY</i>' , $query_exist );
			$query_exist = mb_ereg_replace( 'LIMIT' , '<i style=" font-style : normal ; font-weight : 600 ; ">LIMIT</i>' , $query_exist );
			
			$query_show =  '<div class="class_db showquery">
				<div style="color :#666;">
					mysql query: <i style=" font-style : normal ; font-weight : 300 ; color : #369 ; ">'.$title.'</i>
				</div><div style="">
					'.$query_exist.'
				</div>
			</div>';
			
			
			# if( is_array( $popup ) ){
			# 	# $popup[ ] = $class__index->class__release->messages( 'console' , $show );
			# }
			# else {
			# 	print $show;
			# }
		# }
		
		## Выполняем запрос
		try {
			$result = mysqli_query( $this->db_link , $query_query );
		}
		catch( Exception $e ){
			# $class__index->debug->debugtmp( $this , $query_query , [ __FILE__, __LINE__, __METHOD__ ] );
			# $class__index->debug->debugtmp( $this , 'Caught exception: ' . $e->getMessage() , [ __FILE__, __LINE__, __METHOD__ ] );
		}

		return $result;
		*/

	}
	

	public function result ( bool $assoc = true ){
		# $this->query->result( $results , $assoc );
		return $this->query->result( $assoc );
	}
	public function show ( ) {
		return $this->query->show( );
	}

	public function log ( ) {
		$return = 'Empty';

		$log = $this->query_log;
		$log[ ] = $this->query;

		$count = 1;
		if( ! empty( $log ) ){
			$return = '';
			foreach( $log as $key=>$query ){
				$return .= 'query ' . $count++ . ' : ' . $query->show( ) . '<br />';
			}
		}
		return $return;
	}
}

?>