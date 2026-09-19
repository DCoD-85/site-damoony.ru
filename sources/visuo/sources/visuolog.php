<?PHP

class visuolog {
	# --- state ---
	private bool $settings_online = false;
	public array $rows = [ ];
	public int $rows_count = 0;
	public float $time;
	public string $html;

	# --- methods ---
	public function __construct ( bool $online = false ) {
		$this->settings_online = $online;
	}

	public function add ( string $message , string $method , string $file , string $line ) : void {
		if( isset( $_SERVER[ 'DOCUMENT_ROOT' ] ) && $_SERVER[ 'DOCUMENT_ROOT' ] !== '' ) {
			$dr = str_replace( '/' , DS , $_SERVER[ 'DOCUMENT_ROOT' ] );
			$file = str_replace( $dr , '...' , $file );
		}

		$visuolog_row = new visuolog_row( $this );
		$visuolog_row->add( $message , ++$this->rows_count , $method , $file , $line );
		$this->rows[ ] = $visuolog_row;

		if( $this->settings_online === true ) {
			print $visuolog_row->release( );
		}
	}

	public function as_tag ( ) : visuotag {
		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : 'log' );

		if( empty( $this->rows ) ) {
			$vt->tag( 'div' , class : 'log_row_empty' )->content( 'Лог пустой. Нет лог-записей для отображения.' );
			return $vt;
		}

		foreach( $this->rows as $num => $row ) {
			$vt->append( $row->as_tag( $num ) );
		}

		return $vt;
	}

	public function release ( ) : string {
		if( empty( $this->rows ) ) {
			return '<div>Лог пустой. Нет лог-записей для отображения.</div>';
		}

		$html = '';
		foreach( $this->rows as $row ) {
			$html .= $row->release( );
		}

		return $html;
	}

	public function final ( ) : void {
		if( $this->settings_online === false ) {
			print $this->release( );
		} else {
			die( '<div>Log is end</div>' );
		}
	}
}

class visuolog_row {
	# --- refs ---
	private visuolog $visuolog;

	# --- state ---
	public float $hrtime = 0.0;
	public int $row_num = 0;
	public string $message = '';
	public string $method = '';
	public string $file = '';
	public string $line = '';
	public null|DateTime $datetime = null;

	# --- methods ---
	public function __construct ( visuolog $visuolog ) {
		$this->visuolog = $visuolog;
	}

	public function add ( string $message , int $row_num , string $method , string $file , string $line ) : void {
		$this->hrtime   = hrtime( true );
		$this->row_num  = $row_num;
		$this->message  = $message;
		$this->method   = $method;
		$this->file     = $file;
		$this->line     = $line;
		$this->datetime = new DateTime( );
	}

	public function as_tag ( int $number ) : visuotag {
		$vt = new visuotag( );

		$microtime_cur = $this->hrtime;
		if( $number > 0 && isset( $this->visuolog->rows[ $number - 1 ] ) ) {
			$microtime_prev = $this->visuolog->rows[ $number - 1 ]->hrtime;
		} else {
			$microtime_prev = $GLOBALS[ 'start_hr' ] ?? $microtime_cur;
		}
		$microtime = '+' . number_format( ( ( $microtime_cur - $microtime_prev ) / 1000000 ) , 3 );

		$datetime = $this->datetime !== null ? $this->datetime->format( ' d-m-Y [ H:i:u ]' ) : '';

		$vt->tag( 'div' , class : 'log_row' )
			->tag( 'div' , class : 'log_row_num' )->content( $this->row_num )->parent( )
			->tag( 'div' , class : 'log_row_message' )->content( $this->message )->parent( )
			->tag( 'div' , class : 'log_row_method' )->content( $this->method . ' ' . $this->file . ':' . $this->line )->parent( )
			->tag( 'div' , class : 'log_row_microtime' )->content( $microtime )->parent( )
			->tag( 'div' , class : 'log_row_datetime' )->content( $datetime )->parent( )
			->root( );

		return $vt;
	}

	public function release ( ) : string {
		$datetime = $this->datetime !== null ? $this->datetime->format( 'H:i:u d-m-Y' ) : '';

		return '<div style=" font-family : \'Exo 2\'; font-size : 14px ; display : grid ; grid-template-columns : 50px auto 200px 200px ; border-bottom : solid #CCC 1px ; ">
			<div style=" font-weight : bold ; padding : 3px ; text-align : right ; ">' . $this->row_num . '</div>
			<div style=" padding : 3px ; ">' . $this->message . '</div>
			<div style=" padding : 3px ; ">' . $this->method . '</div>
			<div style=" padding : 3px ; ">' . $datetime . '</div>
		</div>';
	}
}