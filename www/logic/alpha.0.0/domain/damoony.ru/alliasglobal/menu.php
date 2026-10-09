<?PHP

namespace damoony_ru;

class alliasglobal_menu {
	# --- refs ---
	public null|alliasglobal $alliasglobal = null;
	public null|\main $main = null;

	# --- state ---
	public string $menu_name = '';
	public string $separator = '|';
	public array  $lists     = [ ];

	# --- methods ---
	public function __construct (
		null|alliasglobal $alliasglobal = null ,
		string $menu_name = '' ,
		string $separator = '|'
	) {
		$this->alliasglobal = $alliasglobal;
		$this->main         = $alliasglobal?->main;
		$this->menu_name    = $menu_name;
		$this->separator    = $separator;
	}

	# --- fillers ---
	public function new ( string $name ) : alliasglobal_menu_row {
		$row = new alliasglobal_menu_row( );
		$this->lists[ $name ][ ] = $row;
		return $row;
	}

	public function link (
		string $name ,
		string $text ,
		string $href ,
		bool   $selected    = false ,
		bool   $notajax     = false ,
		string $comment     = '' ,
		bool   $popup       = true ,
		bool   $targetblank = false
	) : alliasglobal_menu_row {
		$row = $this->new( $name );
		$row->link( $text , $href , $selected , $notajax , $comment , $popup , $targetblank );
		return $row;
	}

	public function head ( string $name , string $text ) : alliasglobal_menu_row {
		$row = $this->new( $name );
		$row->head( $text );
		return $row;
	}
	
	public function text (
		string $name ,
		string $text ,
		string $comment = '' ,
		bool   $popup   = true
	) : alliasglobal_menu_row {
		$row = $this->new( $name );
		$row->text( $text , $comment , $popup );
		return $row;
	}

	public function between ( string $name , string $separator = '|' ) : alliasglobal_menu_row {
		$row = $this->new( $name );
		$row->between( $separator );
		return $row;
	}

	public function vt ( string $name , \visuotag $vt ) : alliasglobal_menu_row {
		$row = $this->new( $name );
		$row->vt( $vt );
		return $row;
	}

	# --- inspection ---
	public function all   ( )                : array { return $this->lists; }
	public function get   ( string $name )   : array { return $this->lists[ $name ] ?? [ ]; }
	public function has   ( string $name )   : bool  { return isset( $this->lists[ $name ] ); }
	public function count ( string $name )   : int   { return count( $this->lists[ $name ] ?? [ ] ); }
	public function names ( )                : array { return array_keys( $this->lists ); }

	# --- edit ---
	public function clear ( string $name = '' ) : static {
		if ( $name !== '' ) unset( $this->lists[ $name ] );
		else                $this->lists = [ ];
		return $this;
	}

	public function remove ( string $name , int $index ) : bool {
		if ( ! isset( $this->lists[ $name ][ $index ] ) ) return false;
		array_splice( $this->lists[ $name ] , $index , 1 );
		return true;
	}

	# --- release ---

	# плоский набор menulink + menulink_between
	public function as_tag ( string $name ) : \visuotag {
		$vt = new \visuotag( );

		if ( ! isset( $this->lists[ $name ] ) ) return $vt;

		$rows = $this->lists[ $name ];
		if ( empty( $rows ) ) return $vt;

		$count = count( $rows );
		$i     = 0;

		foreach ( $rows as $row ) {
			$i++;
			$vt->append( $row->as_tag( ) );

			if ( $i < $count && $this->separator !== '' && $row->type !== 'between' && $row->type !== 'vt' ) {
				$vt->tag( 'span' , class : 'menulink_between' )->content( $this->separator );
			}
		}

		return $vt->root( );
	}

	# плоский набор — алиас
	public function as_link ( string $name ) : \visuotag {
		return $this->as_tag( $name );
	}

	# обёрнутый список menulist menulist_{name}
	public function as_list ( string $name ) : \visuotag {
		$inner = $this->as_tag( $name );

		if ( $inner->elements === [ ] ) return $inner;

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'menulist menulist_' . $name )
			->append( $inner );

		return $vt->root( );
	}

	public function as_tag_all ( ) : array {
		$out = [ ];
		foreach ( array_keys( $this->lists ) as $name ) {
			$out[ $name ] = $this->as_tag( $name );
		}
		return $out;
	}
}