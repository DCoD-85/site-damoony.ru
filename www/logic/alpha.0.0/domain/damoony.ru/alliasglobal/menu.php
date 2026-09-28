<?PHP

namespace damoony;

# alliasglobal_menu — базовый класс меню.
#
# Один класс — много именованных списков внутри.
# Списки: 'projects' , 'login' , 'languages' , 'nav' , 'subnav' , …
#
# HTML-разметка единая. Дифференциация блоков — через CSS-префикс
# от menu_name ('top', 'left', 'right', 'bottom'):
#
#   <div class="menu menu_top" data-list="projects">
#     <div class="menu_list">
#       <span class="menulink link_link">…</span>
#       <span class="menulink_between">|</span>
#       …
#     </div>
#   </div>
#
# Программист имеет доступ:
#   $menu->new( 'projects' )->link( … )
#   $menu->link( 'projects' , '…' , '…' )
#   $menu->text( 'projects' , '…' )
#   $menu->between( 'projects' )
#   $menu->all( )        — все списки
#   $menu->get( 'projects' )
#   $menu->has( 'projects' )
#   $menu->count( 'projects' )
#   $menu->clear( 'projects' )  — очистить список
#   $menu->clear( )             — очистить всё
#   $menu->remove( 'projects' , 2 )
#   $menu->as_tag( 'projects' )

class alliasglobal_menu {
	# --- refs ---
	public null|alliasglobal $alliasglobal = null;
	public null|\main $main = null;

	# --- state ---
	public string $menu_name = '';
	public string $separator = '|';
	public array  $lists     = [ ];   # [ 'projects' => alliasglobal_menu_row[] , … ]

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
	public function as_tag ( string $name ) : \visuotag {
		$vt = new \visuotag( );

		if ( ! isset( $this->lists[ $name ] ) ) return $vt;

		$rows = $this->lists[ $name ];
		if ( empty( $rows ) ) return $vt;

		$wrapper = $vt->tag( 'div' , class : 'menu menu_' . $this->menu_name );
		$wrapper->data_add( 'list' , $name );

		$inner = $wrapper->tag( 'div' , class : 'menu_list' );

		$count = count( $rows );
		$i     = 0;
		foreach ( $rows as $row ) {
			$i++;
			$inner->append( $row->as_tag( ) );
			if ( $i < $count && $this->separator !== '' && $row->type !== 'between' ) {
				$inner->tag( 'span' , class : 'menulink_between' )->content( $this->separator );
			}
		}

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