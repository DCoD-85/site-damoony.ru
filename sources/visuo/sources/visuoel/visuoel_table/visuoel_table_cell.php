<?PHP

# Ячейка. Дивный режим:
#   <div class="col [cols_N] [colelsinline] [extra]" style="width: N%">
#     <div class="col-inside">
#       <div class="col_block">
#         <!-- сюда пришиваются дети в порядке вызовов add/set/html -->
#       </div>
#     </div>
#   </div>
# HTML:
#   <td rowspan="2" colspan="3" style="width: N%">…</td>
#
# Порядок рендера внутри col_block = порядок вызовов в цепочке:
#   ->add(A)->set('text')->add(B)   → A, field_block, B
#   ->set('text')->add(A)->add(B)   → field_block, A, B
#
# Голый текст (через set/html) оборачивается в visuoel_field_block —
# структура обёртки живёт в самом visuoel_field_block, не здесь.

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

		# дивный режим — только скелет
		$class = 'col';
		if ( $d->width > 0 )     $class .= ' cols_' . $d->width;
		if ( $d->class  !== '' ) $class .= ' ' . $d->class;
		if ( $d->inline )        $class .= ' colelsinline';

		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : $class );

		if ( $d->width > 0 ) {
			$vt->attrs_style_add( 'width: ' . $d->width . '%;' );
		}

		$inner = $vt->tag( 'div' , class : 'col-inside' )
			->tag( 'div' , class : 'col_block' );

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

	# --- content block ---

	private function ensure_content_block ( ) : void {
		$d = $this->data( );

		if ( $d->content === '' ) return;

		if ( $d->content_block === null ) {
			$block = new visuoel_field_block( );
			$this->add( $block );
			$d->content_block = $block;
		}

		$d->content_block->data( )->content = $d->content;
		$d->content_block->data( )->is_html = $d->is_html;
	}

	# --- proxies to data (fluent) ---

	public function set ( string $text ) : static {
		$this->data( )->set( $text );
		$this->ensure_content_block( );
		return $this;
	}

	public function html ( string $html ) : static {
		$this->data( )->html( $html );
		$this->ensure_content_block( );
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

	public null|visuoel_field_block $content_block = null;

	public function width   ( int $w )         : static { $this->width   = $w ;       return $this; }
	public function colspan ( int $c )         : static { $this->colspan = $c ;       return $this; }
	public function rowspan ( int $r )         : static { $this->rowspan = $r ;       return $this; }
	public function inline  ( bool $v = true ) : static { $this->inline  = $v ;       return $this; }
	public function set     ( string $text )   : static { $this->content = $text ; $this->is_html = false ; return $this; }
	public function html    ( string $html )   : static { $this->content = $html ; $this->is_html = true  ; return $this; }
	public function class   ( string $c )      : static { $this->class   = $c ;       return $this; }
}