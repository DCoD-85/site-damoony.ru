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
		# --- hooks ---
	private null|visuostruct  $visuostruct      = null;
	private null|string       $visuostruct_name = null;
	private null|visuoel      $visuoel          = null;

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

		$target->elements[ ] = $newtag;

		return $return ? $newtag : $target;
	}

	# ------------------------------------------------------------
	#  Hooks: struct / element / meta
	# ------------------------------------------------------------

	# Создать узел-заглушку для структуры (visuostruct).
	# Возвращает новый узел — дальнейшая настройка через ->visuostruct_name.
	public function structv ( string $visuostruct_name , null|visuostruct $visuostruct = null ) : visuotag {
		$newtag = $this->tag( 'struct' , true , class: 'cl_struct' );
		$newtag->visuostruct_name = $visuostruct_name;
		$newtag->visuostruct      = $visuostruct;
		return $newtag;
	}
	# Создать узел-заглушку для visuoel (новый UI-движок).
	# Возвращает $this — цепочка продолжается на текущем узле.
	# Плейсхолдер в release не оборачивается: содержимое — вывод $visuoel->release().
	public function el ( null|visuoel $visuoel = null , bool $init = false ) : visuotag {
		$newtag = $this->tag( 'el' , true , class: 'cl_el' );

		$visuoel = $visuoel ?: new visuoel( );
		if ( $init === true ) $visuoel->init( );
		$newtag->visuoel = $visuoel;

		return $this;
	}

	# Добавить meta как поддерево
	public function meta ( visuometa $visuometa ) : visuotag {
		$this->append( $visuometa->as_tag( ) );
		return $this;
	}

	# Найти структуру по имени и привязать visuostruct
	public function struct_set ( string $visuostruct_name , visuostruct $visuostruct ) : visuotag {
		$return = [ ];
		$vt = new visuotag( $this->visuorelease , null , 'root' , true );
		$this->root( )->findstruct( $visuostruct_name , $return , $vt );

		$vt->visuostruct = $visuostruct;
		return $this;
	}

	# Рекурсивный поиск struct-узлов по имени.
	# Возвращает через $return массив найденных узлов и через $vt — последний найденный.
	public function findstruct ( string $structname , array &$return = [] , null|visuotag &$vt = null ) : void {
		# log
		if ( $this->visuorelease ) {
			$this->visuorelease->visuolog->add( 'Поиск структуры: ' . $structname , __METHOD__ , __FILE__ , __LINE__ );
		}

		foreach ( $this->elements as $v ) {
			if ( ! is_object( $v ) ) continue;

			# именованный struct: совпало — сохраняем
			if ( $v->visuostruct_name !== null && $v->visuostruct_name !== '' ) {
				if ( $v->visuostruct_name === $structname ) {
					$return[ ] = $v;
					$vt = $v;
				}
				continue;
			}

			# без имени — идём глубже
			$v->findstruct( $structname , $return , $vt );
		}
	}

	# ------------------------------------------------------------
	#  Tree / children
	# ------------------------------------------------------------

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

	# ------------------------------------------------------------
	#  Attrs
	# ------------------------------------------------------------

	public function attrs ( ) : visuoattrs {
		return $this->attrs;
	}
	public function attrs_add ( string $name , string $value , bool $add_empty = false ) : visuotag {
		$this->attrs( )->add( $name , $value , $add_empty );
		return $this;
	}
	public function attrs_remove ( string $name ) : visuotag {
		$this->attrs( )->remove( $name );
		return $this;
	}
	public function attrs_class_add ( string $value ) : visuotag {
		$this->attrs( )->class_add( $value );
		return $this;
	}
	public function attrs_class_remove ( string $value ) : visuotag {
		$this->attrs( )->class_remove( $value );
		return $this;
	}
	public function attrs_style_add ( string $value ) : visuotag {
		$this->attrs( )->style_add( $value );
		return $this;
	}
	public function attrs_style_remove ( string $value ) : visuotag {
		$this->attrs( )->style_remove( $value );
		return $this;
	}
	public function attrs_actions_add ( string $action , string $value ) : visuotag {
		$this->attrs( )->actions_add( $action , $value );
		return $this;
	}
	public function attrs_actions_remove ( string $action ) : visuotag {
		$this->attrs( )->actions_remove( $action );
		return $this;
	}

	# ------------------------------------------------------------
	#  Data
	# ------------------------------------------------------------

	public function data ( ) : visuoattrsdata {
		return $this->attrs( )->data( );
	}
	public function data_add ( string $data_name , null|bool|string|int|array $data_value , bool $data_tojson = true ) : visuotag {
		$this->attrs( )->data_add( $data_name , $data_value , $data_tojson );
		return $this;
	}
	public function data_remove ( string $data_name ) : visuotag {
		$this->attrs( )->data_remove( $data_name );
		return $this;
	}

	# ------------------------------------------------------------
	#  Shortcuts
	# ------------------------------------------------------------

	public function id ( string $value ) : visuotag {
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

	public function tree ( ) : void {
		# return $this->root( )->releasetree( true );
	}

	# ------------------------------------------------------------
	#  Release
	# ------------------------------------------------------------

	public function release ( int $cicle = 0 ) : string {
		# log
		if ( $this->visuorelease ) {
			$this->visuorelease->visuolog->add( 'Реализация тегов: ' . $cicle , __METHOD__ , __FILE__ , __LINE__ );
		}

		$html       = '';
		$root       = $this->root;
		$tagname    = $this->tagname;
		$tag_closed = $this->tag_closed;
		$elements   = $this->elements;

		# attrs (включая class, style, actions, data-*)
		$html_attrs = $this->attrs( )->release( );

		if ( $tag_closed === true ) {
			# children first
			foreach ( $elements as $v ) {
				$html .= is_object( $v ) ? $v->release( $cicle + 1 ) : $v;
			}

			# wrap:
			# - root — не оборачивается
			# - всё остальное — оборачивается в <tag ...>...</tag>
			if ( $root === false && $this->visuoel === null ) {
				$html = '<' . $tagname . $html_attrs . '>' . $html . '</' . $tagname . '>';
			}
		} else {
			# self-closing
			$html = '<' . $tagname . $html_attrs . ' />';
		}

		return $html;
	}

	public function final ( ) : string {
		return $this->root( )->release( );
	}

	public function prepare ( int $count = 0 ) : void {
		# log
		if ( $this->visuorelease ) {
			$this->visuorelease->visuolog->add( '' , __METHOD__ , __FILE__ , __LINE__ );
		}

		# hook by node type
		if ( $this->visuostruct !== null ) {
			$this->prepare_visuostruct( );
		} elseif ( $this->visuoel !== null ) {
			$this->prepare_visuoel( );
		}

		# recurse
		foreach ( $this->elements as $el ) {
			if ( is_object( $el ) ) {
				$el->prepare( $count );
			} else {
				$this->prepare_visuotag( );
			}
		}
	}

	# prepare [ ... visuotag | visuostruct | visuoel ]
	private function prepare_visuotag ( ) : void {
	}

	private function prepare_visuostruct ( ) : void {
	}

	private function prepare_visuoel ( ) : void {
		if ( ! $this->visuoel->init ) {
			$this->visuoel->init( );
		}
		if ( $this->visuoel->vt_start !== null ) {
			$this->visuoel->vt_start->visuotag = $this;
			$this->elements[ ] = $this->visuoel->vt_start;
		}
	}

	public function releasetree ( bool $tree = true , int $count = 0 ) : string {
		# log
		if ( $this->visuorelease ) {
			$this->visuorelease->visuolog->add( '' , __METHOD__ , __FILE__ , __LINE__ );
		}

		$html       = '';
		$root       = $this->root;
		$tagname    = $this->tagname;
		$tag_closed = $this->tag_closed;
		$elements   = $this->elements;

		# attrs (разложены по секциям — для debug-панели)
		$html_attrs   = $this->attrs( )->attributes_release( );
		$html_class   = $this->attrs( )->class( )->release( false );
		$html_style   = $this->attrs( )->style( )->release( false );
		$html_actions = $this->attrs( )->actions( )->release( false );
		$html_data    = $this->attrs( )->data( )->release( );

		$tmp_tag_before = '... ';
		$tag_before     = str_repeat( $tmp_tag_before , $count );

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
		$html .= '</div>';

		foreach ( $elements as $v ) {
			if ( is_object( $v ) ) {
				if ( $v->visuostruct_name === null || $v->visuostruct_name === '' ) {
					$html .= $v->releasetree( $tree , $count + 1 );
				} elseif ( $v->visuostruct !== null ) {
					# skip — struct-узлы не отображаются отдельно
				} else {
					$html .= $tag_before . $tmp_tag_before . '[ ' . $v->tagname . ( $v->tag_closed ? '' : ' /' ) . ' ][ struct: ' . $v->visuostruct_name . ' ]' . '<br />';
				}
			} else {
				$html .= '<div class="debugtree_row_content">' . $tag_before . $tmp_tag_before . '[ content ]' . '</div>';
			}
		}

		return $html;
	}
}

?>