<?PHP

namespace damoony;

class alliasglobal_menu_row {
	# --- state ---
	public string $type        = 'link';   # link | text | head | between | vt
	public string $text        = '';
	public string $href        = '';
	public bool   $selected    = false;
	public bool   $notajax     = false;
	public string $comment     = '';
	public bool   $popup       = true;
	public bool   $targetblank = false;
	public string $separator   = '|';
	public string $icon        = '';
	public null|\visuotag $vt  = null;

	# --- fillers ---
	public function link (
		string $text ,
		string $href ,
		bool   $selected    = false ,
		bool   $notajax     = false ,
		string $comment     = '' ,
		bool   $popup       = true ,
		bool   $targetblank = false
	) : static {
		$this->type        = 'link';
		$this->text        = $text;
		$this->href        = $href;
		$this->selected    = $selected;
		$this->notajax     = $notajax;
		$this->comment     = $comment;
		$this->popup       = $popup;
		$this->targetblank = $targetblank;
		return $this;
	}

	public function text ( string $text ) : static {
		$this->type = 'text';
		$this->text = $text;
		return $this;
	}

	public function head ( string $text ) : static {
		$this->type = 'head';
		$this->text = $text;
		return $this;
	}

	public function between ( string $separator = '|' ) : static {
		$this->type      = 'between';
		$this->separator = $separator;
		return $this;
	}

	public function vt ( \visuotag $vt ) : static {
		$this->type = 'vt';
		$this->vt   = $vt;
		return $this;
	}

	public function icon ( string $icon ) : static {
		$this->icon = $icon;
		return $this;
	}

	# --- release ---
	public function as_tag ( ) : \visuotag {
		# произвольный visuotag — как есть
		if ( $this->type === 'vt' && $this->vt !== null ) {
			return $this->vt;
		}

		$vt = new \visuotag( );

		# разделитель
		if ( $this->type === 'between' ) {
			$vt->tag( 'span' , class : 'menubetween' )->content( $this->separator );
			return $vt;
		}

		# заголовок
		if ( $this->type === 'head' ) {
			$vt->tag( 'span' , class : 'menuhead' )
				->content( htmlspecialchars( $this->text , ENT_QUOTES ) );
			return $vt;
		}

		# текст
		if ( $this->type === 'text' ) {
			$vt->tag( 'span' , class : 'menutext' )
				->content( htmlspecialchars( $this->text , ENT_QUOTES ) );
			return $vt;
		}

		# --- ссылка ---
		$has_popup = $this->comment !== '' && $this->popup;

		# --- ссылка: внешний тег всегда span.menulink ---
		$class = 'menulink';
		if ( $this->selected )                $class .= ' selected';
		if ( $this->comment && $this->popup ) $class .= ' menulink_popup';
		if ( $this->icon !== '' )             $class .= ' menulink_icon menulink_icon_' . $this->icon;

		$span = $vt->tag( 'span' , class : $class );
		$a    = $span->tag( 'a' );

		$a->attrs_add( 'href' , $this->href );
		if ( $this->targetblank ) $a->attrs_add( 'target' , '_blank' );
		if ( $this->notajax )     $a->data_add( 'notajax' , true );

		if ( $this->icon !== '' && $this->text !== '' ) {
			$a->attrs_add( 'title' , $this->text );
			$a->content( '' );
		} else {
			$a->content( htmlspecialchars( $this->text , ENT_QUOTES ) );
		}

		if ( $this->comment !== '' && $this->popup ) {
			$comment = $span->tag( 'span' , class : 'menucomment' );
			$comment->tag( 'div' , class : 'menucomment_narrow' )->content( '' );
			$comment->tag( 'div' , class : 'menucomment_text'   )->content( $this->comment );
		}

		return $vt;
	}
}