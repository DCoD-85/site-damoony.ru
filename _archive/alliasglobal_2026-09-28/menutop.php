<?PHP

namespace damoony;

class alliasglobal_menutop {
	# --- refs ---
	public null|\main $main = null;

	# --- state ---
	public array  $rows      = [ ];
	public string $separator = '|';

	# --- methods ---
	public function __construct ( null|\main $main = null , string $separator = '|' ) {
		$this->main      = $main;
		$this->separator = $separator;
	}

	public function link (
		string $text ,
		string $href ,
		bool   $selected    = false ,
		bool   $notajax     = false ,
		string $comment     = '' ,
		bool   $popup       = true ,
		bool   $targetblank = false
	) : alliasglobal_menutop_row {
		$row = new alliasglobal_menutop_row( );
		$row->link( $text , $href , $selected , $notajax , $comment , $popup , $targetblank );
		$this->rows[ ] = $row;
		return $row;
	}

	public function text (
		string $text ,
		string $comment = '' ,
		bool   $popup   = true
	) : alliasglobal_menutop_row {
		$row = new alliasglobal_menutop_row( );
		$row->text( $text , $comment , $popup );
		$this->rows[ ] = $row;
		return $row;
	}

	public function between ( string $separator = '|' ) : alliasglobal_menutop_row {
		$row = new alliasglobal_menutop_row( );
		$row->between( $separator );
		$this->rows[ ] = $row;
		return $row;
	}

	public function add ( alliasglobal_menutop_row $row ) : alliasglobal_menutop_row {
		$this->rows[ ] = $row;
		return $row;
	}

	public function remove ( alliasglobal_menutop_row $row ) : bool {
		foreach ( $this->rows as $i => $r ) {
			if ( $r === $row ) {
				unset( $this->rows[ $i ] );
				$this->rows = array_values( $this->rows );
				return true;
			}
		}
		return false;
	}

	public function clear ( ) : static {
		$this->rows = [ ];
		return $this;
	}

	public function as_tag ( ) : \visuotag {
		$vt = new \visuotag( );

		$count = count( $this->rows );
		if ( $count === 0 ) return $vt;

		$i = 0;
		foreach ( $this->rows as $row ) {
			$i++;
			$vt->append( $row->as_tag( ) );
			if ( $i < $count && $this->separator !== '' && $row->type !== 'between' ) {
				$vt->tag( 'span' , class : 'menulink_between' )->content( $this->separator );
			}
		}

		return $vt;
	}
}