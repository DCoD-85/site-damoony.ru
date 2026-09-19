<?PHP

class visuotag {
	# --- refs ---
	public null|visuorelease $visuorelease = null;
	public null|visuotag     $visuotag     = null;

	# --- state ---
	public string $tagname    = '';
	public bool   $tag_closed = true;
	public bool   $root       = false;

	# --- attrs ---
	public visuoattrs $attrs;

	# --- children ---
	public array $elements = [ ];   # [ visuotag | string , ... ]

	# --- hooks ---
	private null|visuostruct  $visuostruct      = null;
	private null|string       $visuostruct_name = null;
	private null|visuoelement $visuoelement     = null;

	### methods
	public function __construct ( null|visuorelease $visuorelease = null ,
	                              null|visuotag     $visuotag     = null ,
	                              string            $tagname      = '' ,
	                              bool              $tag_closed   = true ) {
		$this->visuorelease = $visuorelease;
		$this->visuotag     = $visuotag;
		$this->tagname      = $visuotag === null ? 'root' : $tagname;
		$this->tag_closed   = $tag_closed;
		$this->root         = $visuotag === null;

		$this->attrs = new visuoattrs( $this );
	}

	public function visuorelease ( null|visuorelease $visuorelease ) : visuotag {
		$this->visuorelease = $visuorelease;
		return $this;
	}

	public function tag ( string               $tagname ,
	                      bool                 $tag_closed    = true ,
	                      string               $id            = '' ,
	                      string               $style         = '' ,
	                      string               $class         = '' ,
	                      null|visuostruct     $visuostruct   = null ,
	                      null|visuoelement    $visuoelement  = null ,
	                      null|bool|string|visuotag $parent     = null ,
	                      bool                 $return        = true ,
	                      bool                 $return_parent = false ) : visuotag {
		# log
		if ( $this->visuorelease ) {
			$this->visuorelease->visuolog->add( 'Создаем новый тег: ' . $tagname , __METHOD__ , __FILE__ , __LINE__ );
		}

		# resolve target parent
		if ( is_string( $parent ) && $parent !== '' ) {
			$target = $this->parent( $parent );
		} elseif ( $parent instanceof visuotag ) {
			$target = $parent;
		} else {
			$target = $this;
		}

		# create child
		$newtag = new visuotag( $this->visuorelease , $this , $tagname , $tag_closed );

		if ( $id    !== '' ) $newtag->attrs_add( 'id'    , $id );
		if ( $class !== '' ) $newtag->attrs_class_add( $class );
		if ( $style !== '' ) $newtag->attrs_style_add( $style );

		$newtag->visuostruct  = $visuostruct;
		$newtag->visuoelement = $visuoelement;

		$target->elements[ ] = $newtag;

		return $return ? $newtag : $target;
	}

	public function structv ( string $visuostruct_name , null|visuostruct $visuostruct = null ) {
		$newtag = $this->tag( 'struct' , true , true , class: 'cl_struct' );
		$newtag->visuostruct_name = $visuostruct_name;
		$newtag->visuostruct = $visuostruct;

		$return_new_tag = true;

		return $return_new_tag == true ? $newtag : $this;
	}

	public function element ( string $visuoelement_name , null|visuoelement $visuoelement = null , bool $init = false ) {
		$newtag = $this->tag( 'element' , true , class: 'cl_element' );
		# $newtag->visuoelement_name = $visuoelement_name;

		$visuoelement = $visuoelement ?: new visuoelement( $this->visuorelease , null );
		$visuoelement->name = $visuoelement_name;
		if( $init === true ) $visuoelement->init( );
		$newtag->visuoelement = $visuoelement;

		$return_new_tag = false;

		return $return_new_tag == true ? $newtag : $this;
	}

	public function struct_set ( string $visuostruct_name , visuostruct $visuostruct ) {
		$return = [ ];
		$vt = new visuotag( $this->visuorelease , null , 'root' , true );
		$this->root( )->findstruct( $visuostruct_name , $return , $vt );

		$vt->visuostruct = $visuostruct;
		return $this;
	}

	public function meta ( visuometa $visuometa ){
		$this->append( $visuometa->as_tag( ) );
		return $this;
	}

	public function clear ( ) : visuotag {
		$this->elements = [ ];
		return $this;
	}

	public function insert ( visuotag $vt_start , visuotag $vt_end ) : visuotag {
		$own = $this->elements;
		$this->elements = [ ];

		foreach ( $vt_start->elements as $el ) {
			$this->elements[ ] = $el;
		}
		foreach ( $own as $el ) {
			$vt_end->elements[ ] = $el;
		}

		return $this;
	}

	# Вставить в начало списка элементов
	public function prepend ( visuotag $vt ) : visuotag {
		$root = $vt->root( );
		$this->elements = array_merge( $root->elements , $this->elements );
		$this->rebind_children( );
		return $this;
	}

	# Вставить в конец списка элементов
	public function append ( visuotag $vt , bool $root = true ) : visuotag {
		$root_vt = $vt->root( );
		if ( ! empty( $root_vt->elements ) ) {
			$this->elements = array_merge( $this->elements , $root_vt->elements );
			$this->rebind_children( );
		}
		return $this;
	}

	# Вставить до объекта
	public function before ( visuotag $vt ) : visuotag {
		# TODO
		return $this;
	}

	# Вставить после объекта
	public function after ( visuotag $vt ) : visuotag {
		# TODO
		return $this;
	}

	# Пересобрать ссылки visuotag у детей на $this
	private function rebind_children ( ) : void {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuotag ) $el->visuotag = $this;
		}
	}

	# attrs
	public function attrs ( ) {
		return $this->attrs;
	}
	public function attrs_add ( string $name , string $value ) {
		$this->attrs( )->add( $name , $value );
		return $this;
	}
	public function attrs_remove ( string $name ) {
		$this->attrs( )->remove( $name );
		return $this;
	}
	public function attrs_class_add ( string $value ) {
		$this->attrs( )->class_add($value );
		return $this;
	}
	public function attrs_class_remove ( string $value ) {
		$this->attrs( )->class_remove( $value );
		return $this;
	}
	public function attrs_style_add ( string $value ) {
		$this->attrs( )->style_add($value );
		return $this;
	}
	public function attrs_style_remove ( string $value ) {
		$this->attrs( )->style_remove( $value );
		return $this;
	}
	public function attrs_actions_add ( string $action , string $value , bool $json = false , bool $new = true ) {
		$this->attrs( )->actions_add( $action , $value , $json );
		return $this;
	}
	public function attrs_actions_remove ( string $action ) {
		$this->attrs( )->actions_remove( $action );
		return $this;
	}

	# data
	public function data( ) {
		return $this->attrs( )->data( );
	}

	public function data_add ( string $data_name , null|bool|string|int|array $data_value , bool $data_tojson = true ) {
		$this->attrs( )->data_add( $data_name , $data_value , $data_tojson );
		return $this;
	}

	public function id ( string $value ){
		return $this->attrs_add( 'id' , $value );
	}

	public function content ( string $html ) : visuotag {
		$this->elements[ ] = $html;
		return $this;
	}

	public function next ( ) : mixed {
		return $this->elements[ 0 ] ?? $this;
	}

	# ------------------------------------------------------------
	#  Navigation
	# ------------------------------------------------------------

	public function parent ( string $tagname = '' , int $eq = 0 ) : visuotag {
		if ( $eq < 0 ) return $this;
		if ( $this->root === true ) return $this;

		$parent = $this->visuotag;
		if ( $parent === null ) return $this;

		$feq = 0;

		if ( $tagname !== '' ) {
			while ( $parent !== null ) {
				if ( $parent->tagname === $tagname ) {
					if ( $feq === $eq ) return $parent;
					$feq++;
				}
				if ( $parent->visuotag === null ) return $parent;
				$parent = $parent->visuotag;
			}
		} else {
			while ( $parent !== null ) {
				if ( $feq === $eq ) return $parent;
				if ( $parent->visuotag === null ) return $parent;
				$parent = $parent->visuotag;
				$feq++;
			}
		}

		return $this;
	}

	public function root  ( ) : visuotag { return $this->parent( 'root'  ); }
	public function html  ( ) : visuotag { return $this->parent( 'html'  ); }
	public function head  ( ) : visuotag { return $this->parent( 'head'  ); }
	public function title ( ) : visuotag { return $this->parent( 'title' ); }
	public function body  ( ) : visuotag { return $this->parent( 'body'  ); }

	public function div  ( int $eq = 0 ) : visuotag { return $this->parent( 'div'  , $eq ); }
	public function span ( int $eq = 0 ) : visuotag { return $this->parent( 'span' , $eq ); }

	# ------------------------------------------------------------
	#  Find
	# ------------------------------------------------------------

	public function find ( null|string $tagname = null ,
	                       null|visuotag $visuotag = null ,
	                       int $eq = 0 ,
	                       int $fec = 0 ,
	                       int $cicle = 0 ,
	                       null|string $class = null ,
	                       null|string $id = null ) : null|visuotag {
		# log
		if ( $this->visuorelease && $cicle === 0 ) {
			$this->visuorelease->visuolog->add( 'Поиск тега: ' . $tagname , __METHOD__ , __FILE__ , __LINE__ );
		}

		if ( $visuotag !== null ) return $visuotag;

		# match by tag / class / id
		if ( $tagname === null ) {
			if ( $class !== null ) {
				if ( $this->attrs( )->class( )->get( $class ) === $class ) return $this;
			} elseif ( $id !== null ) {
				$is_id = $this->attrs( )->get( 'id' );
				if ( $is_id !== null && $is_id === $id ) return $this;
			}
		} else {
			if ( $this->tagname === $tagname ) {
				if ( $class !== null ) {
					if ( $this->attrs( )->class( )->get( $class ) === $class ) return $this;
				} else {
					return $this;
				}
			}
		}

		# depth-first search
		foreach ( $this->elements as $v ) {
			if ( ! is_object( $v ) ) continue;
			$visuotag = $v->find( $tagname , $visuotag , $eq , $fec , $cicle + 1 , $class , $id );
			if ( $visuotag !== null ) return $visuotag;
		}

		return $visuotag;
	}

	# find tag (from root, descending)
	public function _html ( ) : null|visuotag { return $this->root( )->find( 'html' ); }
	public function _body ( ) : null|visuotag { return $this->root( )->find( 'body' ); }

	# поиск структуры
	# TODO: relies on $v->getstruct() which does not exist yet;
	#       used only by struct_set() which has no external callers.
	#       Revisit when refactoring visuostruct (stage 5).
	public function findstruct ( string $structname , array &$return = [] , null|visuotag &$vt = null ){
		# log
		if( $this->visuorelease ) $this->visuorelease->visuolog->add( 'Поиск структуры: ' . $structname , __METHOD__ , __FILE__ , __LINE__ );
		#
		if( ! is_object( $this ) ) return false;
		$elements = $this->elements;
		if( ! empty( $elements ) ){
			foreach( $elements as $k=>$v ){
				if( is_object( $v ) ){
					if( $v->visuostruct_name == '' ){
						$v->getstruct( $structname , $return , $vt );
					}
					else if( $v->visuostruct_name != '' ){
						if( $v->visuostruct_name == $structname ){
							$return[ ] = $v;
							$vt = $v;
						}
					}
				}
			}
		}
	}

	public function tree( ) {
		# return $this->root( )->releasetree( true );
	}

	# ------------------------------------------------------------
	#  Release
	# ------------------------------------------------------------

	public function release ( int $cicle = 0 ){
		# log
		if( $this->visuorelease ) $this->visuorelease->visuolog->add( 'Реализация тегов: ' . $cicle , __METHOD__ , __FILE__ , __LINE__ );
		#
		$html = '';
		#
		$root = $this->root;
		$tagname = $this->tagname;
		$tag_closed = $this->tag_closed;
		$elements = $this->elements;
		# attrs & data
		$html_attrs = $this->attrs( )->release( );

		$tag_open = '<';
		$tag_close = '>';

		if( $tag_closed  === true ){
			foreach( $elements as $k=>$v ){
				if( is_object( $v ) ){
					$html .= $v->release( ++$cicle );
				} else {
					$html .= $v;
				}
			}

			if( $root === true ){
			}
			else {
				if( $this->visuostruct !== null ){
					$html = $tag_open . $tagname . $html_attrs . $tag_close . $html . $tag_open . '/' . $tagname . $tag_close;
				}
				else if( $this->visuoelement !== null ) {
				} else {
					$html = $tag_open . $tagname . $html_attrs . $tag_close . $html . $tag_open . '/' . $tagname . $tag_close;
				}
			}
		}
		else {
			$html =$tag_open . $tagname . $html_attrs . ' /' . $tag_close;
		}

		return $html;
	}

	public function final ( ){
		return $this->root( )->release( );
	}

	public function prepare ( int $count = 0 ) {
		# log
		if( $this->visuorelease ) $this->visuorelease->visuolog->add( '' , __METHOD__ , __FILE__ , __LINE__ );
		#

		# attrs & data
		if( $this->visuostruct !== null ) $this->prepare_visuostruct( );
		else if( $this->visuoelement !== null ) $this->prepare_visuoelement( );

		$elements = $this->elements;

		foreach( $elements as $el ) {
			if( is_object( $el ) ) {
				$el->prepare( ++$count );
				--$count;
			}
			else {
				$this->prepare_visuotag( );
			}
		}
	}
	# prepare [ ... visuotag | visuostruct | visuoelement ]
	private function prepare_visuotag ( ) {
	}
	private function prepare_visuostruct ( ) {
	}
	private function prepare_visuoelement ( ) {
		if( $this->visuoelement->init == false ){
			$this->visuoelement->init( );
			if( $this->visuoelement->visuotag_start !== null ){
				$this->visuoelement->release( $this );
			}
		}
	}

	public function releasetree( bool $tree = true , int $count = 0 ){
		# log
		if( $this->visuorelease ) $this->visuorelease->visuolog->add( '' , __METHOD__ , __FILE__ , __LINE__ );
		#
		$html = '';

		#
		$root = $this->root;
		$tagname = $this->tagname;
		$tag_closed = $this->tag_closed;
		$elements = $this->elements;
		# attrs & data
		$html_attrs = $this->attrs( )->attributes_release( );
		$html_class = $this->attrs( )->class( )->release( false );
		$html_style = $this->attrs( )->style( )->release( false );
		$html_actions = $this->attrs( )->actions( )->release( false );
		$html_data = $this->attrs( )->data( )->release( );

		$tmp_tag_before = '... ';

		$tag_before = '';
		for( $q = 0 ; $q < $count ; $q++ ) $tag_before .= $tmp_tag_before;

		$html .= '<div class="debugtree_row">';
		$html .= $tag_before;
		$html .= '[ ' . $tagname . ( $tag_closed ? '' : ' /' ) . ' ]';
		$html .= $this->root ? '[ <b>root</b> ]' : '';
		$html .= '[ visuotag (parent): ' . ( $this->visuotag !== null ? $this->visuotag->tagname : 'NULL' ) . ' ]';
		$html .= '[ classname: ' . get_class( $this ) . ' ]';
		$html .= '[ attrs: ' . $html_attrs . ' ]';
		$html .= '[ class: ' . $html_class . ' ]';
		$html .= '[ style: ' . $html_style . ' ]';
		$html .= '[ actions: ' . $html_actions . ' ]';
		$html .= '[ data: ' . $html_data . ' ]';
		$html .= '[ visuostruct: ' . (int)( $this->visuostruct ? true : false ) . ' ]';
		$html .= '[ visuoelement: ' . (int)( $this->visuoelement ? true : false ) . ' ]';
		$html .= '</div>';

		foreach( $elements as $k=>$v ){
			if( is_object( $v ) ){
				if( $v->visuostruct_name == '' ){
					$html .= $v->releasetree( $tree , ++$count );
					$count--;
				} else if( $v->visuostruct !== null ){
				} else if( $v->visuostruct_name !== null ){
					$html .= $tag_before . $tmp_tag_before . '[ ' . $v->tagname . ( $v->tag_closed ? '' : ' /' ) . ' ][ struct: ' . $v->visuostruct_name . ' ][ type: ' . $v->type . ' ][ ' . $v->name . ' ][ ' . ( $v->visuostruct !== null ? get_class( $v->visuostruct ) : 'NULL' ) . ' ]' . '<br />';
				}
			} else {
				$html .= '<div class="debugtree_row_content">' . $tag_before . $tmp_tag_before . '[ content ]' . '</div>';
			}
		}

		return $html;
	}
}

?>