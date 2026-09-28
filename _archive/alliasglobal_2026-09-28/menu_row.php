<?PHP

namespace damoony;

# alliasglobal_menu_row — одна строка меню.
#
# Типы:
#   link    — <span class="menulink link_link [selected] [link_popup]">
#               <span class="span_text"><a href="...">Текст</a></span>
#               [<span class="span_comment">Комментарий</span>]
#             </span>
#   text    — <span class="menulink link_text [selected]">
#               <span class="span_text">Текст</span>
#             </span>
#   between — <span class="menulink_between">|</span>
#
# Поля строки — публичные. Программист может менять их после создания.

class alliasglobal_menu_row {
	# --- state ---
	public string $type        = 'link';   # link | text | between
	public string $text        = '';
	public string $href        = '';
	public bool   $selected    = false;
	public bool   $notajax     = false;
	public string $comment     = '';
	public bool   $popup       = true;
	public bool   $targetblank = false;
	public string $separator   = '|';

	# ---------------------------------------------------------------
	#   FILLERS
	# ---------------------------------------------------------------

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

	public function text (
		string $text ,
		string $comment = '' ,
		bool   $popup   = true
	) : static {
		$this->type    = 'text';
		$this->text    = $text;
		$this->comment = $comment;
		$this->popup   = $popup;
		return $this;
	}

	public function between ( string $separator = '|' ) : static {
		$this->type      = 'between';
		$this->separator = $separator;
		return $this;
	}

	# ---------------------------------------------------------------
	#   RELEASE
	# ---------------------------------------------------------------

	public function as_tag ( ) : \visuotag {
		$vt = new \visuotag( );

		# --- between ---
		if ( $this->type === 'between' ) {
			$vt->tag( 'span' , class : 'menulink_between' )->content( $this->separator );
			return $vt;
		}

		# --- classes ---
		$class = 'menulink link_' . $this->type;
		if ( $this->selected ) $class .= ' selected';
		if ( $this->comment && $this->popup ) $class .= ' link_popup';

		$span = $vt->tag( 'span' , class : $class );

		# --- text ---
		$span_text = $span->tag( 'span' , class : 'span_text' );

		if ( $this->type === 'link' ) {
			$a = $span_text->tag( 'a' );
			$a->attrs_add( 'href' , $this->href );
			if ( $this->targetblank ) $a->attrs_add( 'target' , '_blank' );
			if ( $this->notajax )     $a->data_add( 'notajax' , true );
			$a->content( htmlspecialchars( $this->text , ENT_QUOTES ) );
		} else {
			# text
			$span_text->content( htmlspecialchars( $this->text , ENT_QUOTES ) );
		}

		# --- comment ---
		if ( $this->comment !== '' ) {
			$comment_box = $span->tag( 'span' , class : 'span_comment' );
			$comment_box->tag( 'div' , class : 'span_comment_narrow' )->content( '' );
			$comment_box->tag( 'div' , class : 'span_comment_text' )->content( $this->comment );
		}

		return $vt;
	}
}