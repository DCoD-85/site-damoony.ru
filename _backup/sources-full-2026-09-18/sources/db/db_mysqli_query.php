<?PHP

class db_query {
	# vars
	public db_class $db_class;
	public string $query_type = '';
	public string $query_string = '';
	public array $query_values = [ ];
	public array $query_args = [ ];
	public int $query_limit_page = 0;
	public int $query_limit_step = 10;
	public int $limit_page = -1;
	public int $limit_step = -1;
	public bool $limit = true;
	public int $limitpage = 0;
	public int $limitstep = 10;
	public string $query;
	public null|mysqli_result $result = null;
	# methods
	public function __construct ( db_class $db_class ) {
		$this->db_class = $db_class;
	}

	public function query_prepare ( ) {
		// limit_step:
		//		-1 : auto (как указано в настройках)
		// 		0 : all (все записи)
		//		1-... : user step (пользовательское значение)
		// limit 0 , 10 ( 0 - 10 )
		// limit 0
		// 
		// limit_step : -1
		//		LIMIT query_limit_page , query_limit_step
		// limit_step : 0
		//		LIMIT 1
		
		/*
		if( $this->limit_step == -1 ){
			$this->query_limit_step = (int)$_GET[ 'p_max' ] ?: 10;
			$page = $this->limit_page >= 0 ? $this->limit_page : ( (int)$_GET[ 'p' ] ?: 0 );
			$this->query_limit_page = $page * $this->query_limit_step;
		} else if( $this->limit_step == 0 ) {
			$this->query_limit_step = 1;
			$this->query_limit_page = 0;
		} else {
			$this->query_limit_step = $this->limit_step;
			$page = $this->limit_page >= 0 ? $this->limit_page : ( (int)$_GET[ 'p' ] ?: 0 );
			$this->query_limit_page = $page * $this->query_limit_step;
		}

		$limit_page = $this->limit_page;
		$limit_step = $this->limit_step;
		$query_limit_page = $this->query_limit_page;
		$query_limit_step = $this->query_limit_step;
		# print '$limit_page: ' . $limit_page . '<br />';
		# print '$limit_step: ' . $limit_step . '<br />';
		# print $this->query_string . '<br />' . $limit_page . ' - ' . $limit_step . '<br />';
		if( $this->query_type == 'SELECT' ){
			print '1: ' . $query_string . '<br />';
			if ( $limit_step == 0 ) {
				if( $limit_page == - 1 ){
				} else {
					$this->query_string .= ' LIMIT %d;';
					$this->query_values[ ] = $query_limit_step;
				}
			} else {
				$this->query_string .= ' LIMIT %d , %d;';
				$this->query_values[ ] = $query_limit_page;
				$this->query_values[ ] = $query_limit_step;
			}
			print '2: ' . $query_string . '<br />';
		} else {
			$this->query_string .= ' LIMIT %d;';
			$this->query_values[ ] = 1;
		}
		*/
		
		$limit = $this->limit;
		$limitstep = $this->limitstep ?: 10;
		$limitpage = $this->limitpage ?: 0;
		if( $limit == true ){
			if( $limitpage == 0 && $limitstep == 1 ){
				$this->query_string .= ' LIMIT %d;';
				$this->query_values[ ] = $limitstep;
			} else {
				$this->query_string .= ' LIMIT %d , %d;';
				$this->query_values[ ] = $limitpage * $limitstep;
				$this->query_values[ ] = $limitstep;
			}
		}
	}
	public function query_values ( ) {
		$db = $this->db_class;

		// строка
		// аргументы
		$values = $this->query_values;
		if( ! empty( $values ) ){
			foreach( $values as $key=>$value ){
				if( gettype( $value ) == NULL ) $values[ $key ] = NULL;
				else if( gettype( $value ) == 'NULL' ) $values[ $key ] = 'NULL';
				else if( is_string( $value ) ){
					$values[ $key ] = '\'' . mysqli_real_escape_string( $db->db_link , $value ) . '\'';
				} else $values[ $key ] = mysqli_real_escape_string( $db->db_link , $value );
			}
		}
		$this->query_values = $values;
	}


	public function query_query ( ) {
		$db = $this->db_class;

		$query_string = $this->query_string;
		$query_values = $this->query_values;

		$query_query = vsprintf( $query_string , $query_values );
		

		$this->query = $query_query;
		## Выполняем запрос
		try {
			if( $this->query_type == 'SELECT' ){
				$this->result = mysqli_query( $db->db_link , $query_query );
			} else {
				mysqli_query( $db->db_link , $query_query );
				// print '> ' . mysqli_insert_id( $db->db_link ) . ' <<br />';
			}
		}
		catch( Exception $e ){
			print '<div>' . $e->getMessage() . '</div>';
		}
	}

	
	public function query ( string $string , array $values = [ ] , array $args = [ ] , bool $limit = false , int $limitpage = 0 , int $limitstep = 10 ) {
		$this->query_string = $string;
		$this->query_values = $values;
		$this->query_args = $args;
		$this->limit = $limit;
		$this->limitpage = $limitpage;
		$this->limitstep = $limitstep;
		# $this->limit_page = $limit_page;
		# $this->limit_step = $limit_step;
		

		$this->query_prepare( );
		$this->query_values( );
		$this->query_query( );
		
	}

	public function select ( string $string , array $values , array $args = [ ] , bool $limit = false , int $limitpage = 0 , int $limitstep = 10 ) {
		$this->query_type = 'SELECT';

		$this->query( $string , $values , $args , $limit , $limitpage , $limitstep );
	}
	
	public function insert ( string $tablename , array $names , array $values , array $args = [ ] , bool $limit = false , int $limitpage = 0 , int $limitstep = 1 ) {
		$this->query_type = 'INSERT';
		# $names = $this->query_names( $names );
		$tablename = '`' . $tablename . '`';
		if( ! empty( $names ) ) foreach( $names as $key=>$value ) $names[ $key ] = '`' . $value . '`';

		$names = implode( ' , ' , $names );
		$args = implode( ' , ' , $args );
		$string = 'INSERT INTO ' . $tablename . ' ( ' . $names . ' )  VALUES ( ' . $args . ' )';
		$args = [ ];

		$this->query( $string , $values , $args , $limit , $limitpage , $limitstep );
	}

	public function createtable ( string $tablename , array $fields = [ ] , string $primary_key = ' PRIMARY KEY (  `id` )' , string $engine = ' ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4;' , bool $notexists = true ) {
		if( ! empty( $tablename ) ) $tablename = '`' . $tablename . '`';

		$fields_result = '';

		if( ! empty( $fields ) ){
			foreach( $fields as $value ){
				$fields_result .= $value . ' , ';
			}
		}
		$string = 'CREATE TABLE IF NOT EXISTS ' . $tablename . ' ( ' . $fields_result . ' ' . $primary_key . ' )' . $engine . '';

		$this->query( $string );
	}

	public function droptable ( string $tablename ) {
		if( ! empty( $tablename ) ) $tablename = '`' . $tablename . '`';
		
		$string = 'DROP TABLE ' . $tablename . '';

		$this->query( $string );
	}


	
	public function result ( bool $assoc = true ) {
		$db = $this->db_class;

		$result = $this->result;

		$return = [ ];
		
		## Выполняем запрос
		try {
			if( $this->query_type == 'SELECT' ){
				if( mysqli_num_rows( $result ) != false ){
					$count = 0;
					while( $result_row = mysqli_fetch_assoc( $result ) ){
						// .. Продумать систему, когда сразу вставлять как ключ ID записи (для избежания дальнейших доп. обработок)
						// if( $result_row[ 'id' ] ) $count = $result_row[ 'id' ];
						$return[ $count++ ] = $result_row;
					}
					
					# $free = mysqli_free_result( $result );
				}
				else {
					unset( $return );
				}
			} else if( $this->query_type == 'INSERT' ){
				$return = mysqli_insert_id( $this->db_class->db_link);
			}
		}
		catch( Exception $e ){
			print '<div>' . $e->getMessage() . '</div>';
		}
		return $return;
	}
	
	public function show ( ) {
		$query = $this->query ?: '';

		// .. косметические изменения
		$query = mb_ereg_replace( 'INSERT INTO' , '<i style=" font-style : normal ; font-weight : 600 ; ">INSERT INTO</i>' , $query );
		$query = mb_ereg_replace( 'UPDATE' , '<i style=" font-style : normal ; font-weight : 600 ; ">UPDATE</i>' , $query );
		$query = mb_ereg_replace( 'SELECT' , '<i style=" font-style : normal ; font-weight : 600 ; ">SELECT</i>' , $query );
		$query = mb_ereg_replace( 'FROM' , '<i style=" font-style : normal ; font-weight : 600 ; ">FROM</i>' , $query );
		$query = mb_ereg_replace( 'WHERE' , '<i style=" font-style : normal ; font-weight : 600 ; ">WHERE</i>' , $query );
		$query = mb_ereg_replace( '\sAND\s' , ' <i style="font-weight:600;">AND</i> ' , $query );
		$query = mb_ereg_replace( '\sOR\s' , ' <i style="font-weight:600;">OR</i> ' , $query );
		$query = mb_ereg_replace( 'ORDER BY' , '<i style=" font-style : normal ; font-weight : 600 ; ">ORDER BY</i>' , $query );
		$query = mb_ereg_replace( 'LIMIT' , '<i style=" font-style : normal ; font-weight : 600 ; ">LIMIT</i>' , $query );
		
		return '<div>' . $query . '</div>';
	}
	
}

?>