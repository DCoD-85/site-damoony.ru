<?PHP

# Ячейка. Дивный режим:
#   <div class="col [cols_N] [colelsinline] [extra]" style="width: N%">
#     <div class="col-inside">…</div>
#   </div>
# HTML:
#   <td rowspan="2" colspan="3" style="width: N%">…</td>
#
# Ширина:
#   ->width( 42 )           → inline style="width: 42%"  (любое число)
#   ->col( 'cols_45' )      → class="col cols_45"        (из CSS)
#   ->col( 'my_class' )     → class="col my_class"
#
# Если задан ->col( … ) с классом — width из CSS, inline style не добавляется.
# Если задан только ->width( … ) — inline style.

class visuoel_table_cell extends visuoel_group_element {
	public function data ( ) : visuoel_table_cell__data {
		/** @var visuoel_table_cell__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_table_cell__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;
		$html_mode = $this->detect_html_mode( );

		if ( $html_mode ) {
			$vt = new visuotag( );
			$vt = $vt->tag( 'td' );
			if ( $d->colspan > 1 ) $vt->attrs_add( 'colspan' , (string) $d->colspan );
			if ( $d->rowspan > 1 ) $vt->attrs_add( 'rowspan' , (string) $d->rowspan );
			if ( $d->width > 0 )   $vt->attrs_style_add( 'width: ' . $d->width . '%;' );

			if ( $d->content !== '' ) {
				$vt->content( $d->is_html ? $d->content : htmlspecialchars( $d->content , ENT_QUOTES ) );
			}

			$this->vt_start = $vt;
			$this->vt_end   = $vt;
			return;
		}

		# дивный режим
		$class = 'col';
		if ( $d->class  !== '' ) $class .= ' ' . $d->class;
		if ( $d->inline )        $class .= ' colelsinline';

		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : $class );

		# width через inline-style только если не задан CSS-класс
		if ( $d->width > 0 && $d->class === '' ) {
			$vt->attrs_style_add( 'width: ' . $d->width . '%;' );
		}

		$inner = $vt->tag( 'div' , class : 'col-inside' );

		if ( $d->content !== '' ) {
			$inner->content( $d->is_html ? $d->content : htmlspecialchars( $d->content , ENT_QUOTES ) );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $inner;
	}

	private function detect_html_mode ( ) : bool {
		$p = $this->parent;
		while ( $p !== null ) {
			$d = $p->data( );
			if ( $d instanceof visuoel_table__data ) return $d->html_mode;
			$p = $p->parent;
		}
		return false;
	}

	# --- proxies to data (fluent) ---

	public function set ( string $text ) : static {
		$this->data( )->set( $text );
		return $this;
	}

	public function html ( string $html ) : static {
		$this->data( )->html( $html );
		return $this;
	}

	public function width ( int $w ) : static {
		$this->data( )->width( $w );
		return $this;
	}

	public function colspan ( int $c ) : static {
		$this->data( )->colspan( $c );
		return $this;
	}

	public function rowspan ( int $r ) : static {
		$this->data( )->rowspan( $r );
		return $this;
	}

	public function inline ( bool $v = true ) : static {
		$this->data( )->inline( $v );
		return $this;
	}

	public function col ( string $class ) : static {
		$this->data( )->class( $class );
		return $this;
	}
}

class visuoel_table_cell__data extends visuoel_group_element__data {
	public int    $width   = 0;
	public int    $colspan = 1;
	public int    $rowspan = 1;
	public bool   $inline  = false;
	public string $content = '';
	public bool   $is_html = false;
	public string $class   = '';

	public function width   ( int $w )         : static { $this->width   = $w ;       return $this; }
	public function colspan ( int $c )         : static { $this->colspan = $c ;       return $this; }
	public function rowspan ( int $r )         : static { $this->rowspan = $r ;       return $this; }
	public function inline  ( bool $v = true ) : static { $this->inline  = $v ;       return $this; }
	public function set     ( string $text )   : static { $this->content = $text ; $this->is_html = false ; return $this; }
	public function html    ( string $html )   : static { $this->content = $html ; $this->is_html = true  ; return $this; }
	public function class   ( string $c )      : static { $this->class   = $c ;       return $this; }
}