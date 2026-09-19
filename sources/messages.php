<?PHP

class messages {
	# --- refs ---
	public null|\main $main = null;
	
	# --- state ---
	public array $messages = [ ];
	
	# --- allowed status types (match CSS: .message_<status>) ---
	private const STATUSES = [ 'success' , 'warning' , 'error' , 'console' ];
	
	# --- methods ---
	public function __construct ( null|\main $main = null ) {
		$this->main = $main;
	}
	
	public function count ( ) : int {
		return count( $this->messages );
	}
	
	public function has ( ) : bool {
		return ! empty( $this->messages );
	}
	
	public function add ( string $status , string $title , string $text ) : void {
		# whitelist - unknown status falls back to 'console'
		if ( ! in_array( $status , self::STATUSES , true ) ) {
			$status = 'console';
		}
		
		$this->messages[ ] = [
			'status' => $status ,
			'title'  => $title ,
			'text'   => $text ,
		];
	}
	
	public function clear ( ) : void {
		$this->messages = [ ];
	}
	
	# --- render as visuotag ---
	public function as_tag ( ) : \visuotag {
		$main = $this->main;
		$vt = new \visuotag( );
		
		if ( empty( $this->messages ) ) {
			return $vt;
		}
		
		$skin = $main !== null
			? $main->main_settings->skin( 'skin' )
			: 'default';
		
		$image_close = DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'icons' . DSA . 'min' . DSA . 'close.png';
		$image_full  = DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'icons' . DSA . 'min' . DSA . 'full.png';
		
		foreach ( $this->messages as $value ) {
			$status = $value[ 'status' ];
			$text   = $value[ 'text' ];
			
			$vt->tag( 'div' , class : 'message message_' . $status . ' new' )
				->tag( 'div' , class : 'container' )
					->tag( 'div' , class : 'divInside' )
						->tag( 'div' , class : 'time' )->content( date( 'H:i d/m/Y' , time( ) ) )
						->parent( )
						->tag( 'div' , class : 'messagebody' )
							->tag( 'div' , class : 'textbox' )->content( $text )
							->parent( )
						->parent( )
					->parent( )
					->tag( 'div' , class : 'buttons' )
						->tag( 'div' , class : 'button close' )
							->attrs_add( 'alt' , 'Закрыть' )
							->attrs_add( 'title' , 'Закрыть' )
							->tag( 'img' , false )->attrs_add( 'src' , $image_close )
							->parent( )
						->parent( )
						->tag( 'div' , class : 'button full' )
							->attrs_add( 'alt' , 'Раскрыть' )
							->attrs_add( 'title' , 'Раскрыть' )
							->tag( 'img' , false )->attrs_add( 'src' , $image_full );
		}
		
		return $vt;
	}
	
	# --- render as html string (for AJAX) ---
	public function release ( ) : string {
		if ( ! $this->has( ) ) return '';
		return $this->as_tag( )->root( )->release( );
	}
}