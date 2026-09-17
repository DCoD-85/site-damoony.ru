<?PHP

namespace damoony;

class alliasglobal_menu {
	public alliasglobal $alliasglobal;
	public \main $main;
	public \visuorelease $visuorelease;
	public \visuotag $visuotag;
	public string $menu_name;
	public array $menu_elements = [ ];
	# methods
	public function __construct ( alliasglobal $alliasglobal , string $menu_name ) {
		$this->alliasglobal = $alliasglobal;
		$this->main = $alliasglobal->main;
		$this->visuotag = new \visuotag( );

		$this->menu_name = $menu_name;
	}

	# 
	public function new ( string $name ) {
		$return = $this->menu_elements[ $name ][ ] = new alliasglobal_menu_row( $this );
		return $return;
	}

	public function clear ( string $name = '' ) {
		if( ! empty( $name ) ) unset( $this->menu_elements[ $name ] );
		else unset( $this->menu_elements );
	}

	public function as_tag ( string $name ) {
		$main = $this->main;

		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$vt = new \visuotag( );
		if( ! isset( $this->menu_elements[ $name ] ) ) return $vt;

		$elements = $this->menu_elements[ $name ];

		# print '$name: ' . $name . '<br />';
		if( ! empty( $elements ) ){

			$vt = $vt->tag( 'div' , class : $name );
			$vt->tag( 'div' , class : $name . '_fon menulink_fon' );
			$vt = $vt->tag( 'div' , class : $name . '_unfon' );

			$count = count( $elements );
			foreach( $elements as $element ){
				$type = $element->type;
				$active = $element->active;
				$text = $element->text;
				$text_link = $element->text_link;
				$selected = $element->selected;
				$ajax = $element->ajax;
				$comment = $element->comment;
				$popup = $element->popup;

				$vt_text = new \visuotag( );
				$vt_comment = new \visuotag( );
				if( $type === 'link' ){
					$vt_link = new \visuotag( );
					$vt_link = $vt_link->tag( 'a' );
					$vt_link->attrs_add( 'href' , $text_link );
					$element->targetblank ? $vt_link->attrs_add( 'target' , '_blank' ) : true;
					$vt_link->data_add( 'notajax' , true );
					
					if( $element->notajax == true ){
						$vt_link->data_add( 'notajax' , true );
					}

					$vt_link->content( $text );
					
					#
					$vt_text->append( $vt_link );
				} else if( $type === 'text' ){
					$vt_text->content( $text );
				}

				if( $comment ){
					$vt_comment->tag( 'span' , class : 'span_comment' )
						->tag( 'div' , class : 'span_comment_narrow' )->content( '' )
						->parent( )
						->tag( 'div' , class : 'span_comment_text' )->content( $comment );
				}
				$vt = $vt->tag( 'span' , class : 'menulink' . ' link_' . $type . ( $selected ? ' selected' : '' ) . ( ( $comment && $popup && $active ) ? ' link_popup' : '' ) );
				$vt->tag( 'span' , class : 'span_text' )->append( $vt_text );
				! $comment ?: $vt->append( $vt_comment );

				$vt = $vt->parent( );
				
				if( --$count != 0 ){
					$vt->tag( 'span' , class : 'menulink_between' )->content( '|' );
				}
				# $vt->tag( 'div' , class : 'menu_element' )
				# 	->tag( 'div' )
				# 	->content( $text );
			}
		}

		$vt = $vt->root( );
		return $vt;
	}
}

class alliasglobal_menu_row {
	public alliasglobal_menu $alliasglobal_menu;
	public string $type;
	public string $text;
	public null|string $text_link = null;
	public string $comment;
	public bool $popup = true;
	public bool $targetblank = true;
	
	public bool $active = true;
	public bool $selected = false;
	public bool $notajax = false; 
	public string $selected_var;
	public string $current_var;

	public function __construct ( alliasglobal_menu $alliasglobal_menu ) {
		$this->alliasglobal_menu = $alliasglobal_menu;
	}

	public function text (
		string $text ,
		string $comment = '' ,
		bool $popup = true
	) {
		$this->type = __FUNCTION__;
		$this->text = $text;
		$this->text_link = null;
		$this->comment = $comment;
		$this->popup = $popup;
	}

	public function link (
		string $text ,
		string $text_link ,
		bool $selected ,
		bool $notajax = false ,
		string $comment = '' ,
		bool $popup = true ,
		bool $targetblank = false
	) {
		$this->type = __FUNCTION__;
		$this->text = $text;
		$this->text_link = $text_link;
		$this->selected = $selected;
		$this->notajax = $notajax;
		$this->comment = $comment;
		$this->popup = $popup;
		$this->targetblank = $targetblank;
	}
}

?>