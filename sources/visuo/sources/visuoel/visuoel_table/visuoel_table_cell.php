<?PHP

# Ячейка. Дивный режим:
#   <div class="col cols_30 colelsinline"><div class="col-inside">…</div></div>
# HTML:
#   <td rowspan="2" colspan="3" style="width: 30%">…</td>

class visuoel_table_cell extends visuoel_group_element {
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
		if ( $d->width > 0 ) $class .= ' cols_' . $d->width;
		if ( $d->inline )    $class .= ' colelsinline';

		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : $class );
		$inner = $vt->tag( 'div' , class : 'col-inside' );

		if ( $d->content !== '' ) {
			$inner->content( $d->is_html ? $d->content : htmlspecialchars( $d->content , ENT_QUOTES ) );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $inner;
	}

	# --- proxies to data (fluent) ---

	public function set ( string $text ) : static {
		$this->__data->set( $text );
		return $this;
	}

	public function html ( string $html ) : static {
		$this->__data->html( $html );
		return $this;
	}

	public function width ( int $w ) : static {
		$this->__data->width( $w );
		return $this;
	}

	public function colspan ( int $c ) : static {
		$this->__data->colspan( $c );
		return $this;
	}

	public function rowspan ( int $r ) : static {
		$this->__data->rowspan( $r );
		return $this;
	}

	public function inline ( bool $v = true ) : static {
		$this->__data->inline( $v );
		return $this;
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
}

class visuoel_table_cell__data extends visuoel_group_element__data {
	public int    $width   = 0;
	public int    $colspan = 1;
	public int    $rowspan = 1;
	public bool   $inline  = false;
	public string $content = '';
	public bool   $is_html = false;

	public function width   ( int $w )    : static { $this->width   = $w ; return $this; }
	public function colspan ( int $c )    : static { $this->colspan = $c ; return $this; }
	public function rowspan ( int $r )    : static { $this->rowspan = $r ; return $this; }
	public function inline  ( bool $v = true ) : static { $this->inline = $v ; return $this; }
	public function set     ( string $text )   : static { $this->content = $text ; $this->is_html = false ; return $this; }
	public function html    ( string $html )   : static { $this->content = $html ; $this->is_html = true  ; return $this; }
}