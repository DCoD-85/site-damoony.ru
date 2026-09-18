<?PHP

class visuolog {
	# vars
	private bool $settings_online = false;
	public array $rows = [ ];
	public int $rows_count = 0;
	public float $time;
	public string $html;
	# methods
	public function __construct ( bool $online = false ) {
		$this->settings_online = $online;
	}
	public function add ( string $message , string $method , string $file , string $line ) {
		
		$dr = str_replace( '/' , DS , $_SERVER[ 'DOCUMENT_ROOT' ] );
		$dr_repl = '...';
		
		$file = str_replace( $dr , $dr_repl , $file );
		$visuolog_row = new visuolog_row( $this );
		$visuolog_row->add($message , ++$this->rows_count , $method , $file , $line );
		$this->rows[ ] = $visuolog_row;
		
		if( $this->settings_online === true ) print $visuolog_row->release( );
	}
	public function as_tag ( ) {
		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : 'log' );
		if( ! empty( $this->rows ) ) {
			foreach( $this->rows as $num=>$row ) {
				$vt->append( $row->as_tag( $num ) );
			}
		} else {
			$vt->tag( 'div' , class : 'log_row_empty' )->content( 'Лог пустой. Нет Лог-записей для отображения.' );
		}
		return $vt;
	}
	
	public function release ( ) {
		$html = '';
		if( ! empty( $this->rows ) ) {
			foreach( $this->rows as $row ) {
				$tmp = $row->release( );
				$dr = $_SERVER[ 'DOCUMENT_ROOT' ];
				$dr_repl = '...';
				$html .= $tmp;
			}
		} else {
			$html = '<div>Лог пустой. Нет Лог-записей для отображения.</div>';
		}
		
		return $html;
	}
	public function final ( ) {
		if( $this->settings_online === false ) print $this->release();
		else die( '<div>Log is end</div>' );
	}
}

class visuolog_row {
	# VARS
	# private
	private visuolog $visuolog;
	# public
	public float $hrtime;
	public int $row_num;
	public string $message = '';
	public string $method = '';
	public string $file = '';
	public string $line = '';
	public DateTime $datetime;
	# METHODS
	public function __construct ( visuolog $visuolog ) {
		$this->visuolog = $visuolog;
	}
	public function add ( string $message , int $row_num , string $method , string $file , string $line ) {
		$this->hrtime = hrtime( true );
		$this->row_num = $row_num;
		$this->message = $message;
		$this->method = $method;
		$this->file = $file;
		$this->line = $line;
		$this->datetime = new DateTime( );
	}
	public function as_tag ( int $number ) {
		$vt = new visuotag( );
		
		
		if( $number > 0 ){
			$microtime_prev = $this->visuolog->rows[ $number-1 ]->hrtime;
		} else {
			global $start_hr;
			$microtime_prev = $start_hr;
		}
		$microtime_cur = $this->hrtime;
		$microtime = '+' . number_format( ( ( $microtime_cur - $microtime_prev ) / 1000000 ) , 3 );
		
		
		$vt->tag( 'div' , class : 'log_row' )
			->tag( 'div' , class : 'log_row_num' )->content( $this->row_num )->parent( )
			->tag( 'div' , class : 'log_row_message' )->content( $this->message )->parent( )
			->tag( 'div' , class : 'log_row_method' )->content( $this->method . ' ' . $this->file . ':' . $this->line )->parent( )
			->tag( 'div' , class : 'log_row_microtime' )->content( $microtime )->parent( )
			->tag( 'div' , class : 'log_row_datetime' )->content( $this->datetime->format( ' d-m-Y [ H:i:u ]' ) )->parent( )
			->root( );
		return $vt;
	}
	public function release ( ) {
		$html = '';
		return '<div style=" font-family : \'Exo 2\'; font-size : 14px ; display : grid ; grid-template-columns : 50px auto 200px 200px ; border-bottom : solid #CCC 1px ; ">
			<div style=" font-weight : bold ; padding : 3px ; text-align : right ; ">' . $this->row_num . '</div>
			<div style=" padding : 3px ; ">' . $this->message . '</div>
			<div style=" padding : 3px ; ">' . $this->method . '</div>
			<div style=" padding : 3px ; ">' . $this->datetime->format( 'H:i:u d-m-Y' ) . '</div>
		</div>';
	}
}

?>