<?PHP
class visuoelement_table_table__data {
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_table $visuoelement_group = null;
	public null|visuoelement_table_table $visuoelement_group_element = null;
	public string $form_name = 'form';
	public string $form_method = 'post';
	public string $form_action = '';	# url
	public string $form_enctype = '';	# default: 'application/x-www-form-urlencoded' | files: 'multipart/form-data' | text/plain
	public string $form_autocomplete = 'off';	# off | on
	public string $form_target = '_self';	# default: 'application/x-www-form-urlencoded' | files: 'multipart/form-data' | text/plain
	public array $ttype = [ 'thead' , 'tbody' , 'tfoot' ];
	public array $ttypedata = [ ];
	public array $colwidth = [ ];
	public array $colelsinline = [ ];
	public array $header = [ ];
	public array $colspan = [ ];
	public array $rowspan = [ ];
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_table_table $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
		$this->clear( );
	}
	public function set_visuoelement ( ) {
		
	}
	public function set ( string $form_name ,
											string $form_method = 'POST' ,
											string $form_action = '' ,
											string $form_enctype = 'application/x-www-form-urlencoded' ,
											null|visuoattrs $form_attrs = null ,
											null|visuodata $form_data = null ) {
		
		###
		$form_method =  strtolower( $form_method );
		$this->form_name = $form_name;
		$this->form_method = $form_method;
		$this->form_action = $form_action;
		
		if( $form_method == 'get' ) $this->form_enctype = 'get/html';
		else if( $form_method == 'post' ) $this->form_enctype = 'application/x-www-form-urlencoded';
		else $this->form_enctype = $form_enctype;
		
		$this->form_attrs = $form_attrs !== null ? $form_attrs : new visuoattrs( );
		$this->form_data = $form_data !== null ? $form_data : new visuodata( );
		
		#
		if( $this->visuoelement !== null ){
			# $this->visuoelement->visuoelement_group->clear( );
			$this->visuoelement->visuoelement_group->init( );
		}
	}
	public function get ( string $var , $null = false ) {
		return $this->$var ?: ( $null ? : '' );
	}
	
	public function clear ( ) {
		$ttypes = $this->ttype;
		$_data_ = [ 'ttypedata' , 'colwidth' , 'colspan' , 'rowspan' , 'colelsinline' ];
		$ttypes_clear = [ ];
		foreach( $ttypes as $ttype ) $ttype_clear[ $ttype ] = [ ];
		foreach( $_data_ as $value ) $this->{$value} = $ttype_clear;
		
		return $this;
	}
	
	public function add_thead ( mixed $data ,
															array $colspan = [ ] ,
															array $rowspan = [ ] ,
															array $colwidth = [ ] ,
															array $colelsinline = [ ] ,
															bool $header = false ,
															int $row = -1 ) {
		if( $row < 0 ) $row = count( $this->ttypedata[ 'thead' ] );
		$this->ttypedata[ 'thead' ][ $row ] = $data;
		$this->colspan[ 'thead' ][ $row ] = $colspan;
		$this->rowspan[ 'thead' ][ $row ] = $rowspan;
		$this->colwidth[ 'thead' ][ $row ] = $colwidth;
		$this->colelsinline[ 'thead' ][ $row ] = $colelsinline;
		$this->header[ 'thead' ][ $row ] = $header;
		
		return $this;
	}
	public function add_tbody ( mixed $data ,
															array $colspan = [ ] ,
															array $rowspan = [ ] ,
															array $colwidth = [ ] ,
															array $colelsinline = [ ] ,
															bool $header = false ,
															int $row = -1 ) {
		if( $row < 0 ) $row = count( $this->ttypedata[ 'tbody' ] );
		$this->ttypedata[ 'tbody' ][ $row ] = $data;
		$this->colspan[ 'tbody' ][ $row ] = $colspan;
		$this->rowspan[ 'tbody' ][ $row ] = $rowspan;
		$this->colwidth[ 'tbody' ][ $row ] = $colwidth;
		$this->colelsinline[ 'tbody' ][ $row ] = $colelsinline;
		$this->header[ 'tbody' ][ $row ] = $header;
		
		return $this;
	}
	public function add_tfoot ( mixed $data ,
															array $colspan = [ ] ,
															array $rowspan = [ ] ,
															array $colwidth = [ ] ,
															array $colelsinline = [ ] ,
															bool $header = false ,
															int $row = -1 ) {
		if( $row < 0 ) $row = count( $this->ttypedata[ 'tfoot' ] );
		$this->ttypedata[ 'tfoot' ][ $row ] = $data;
		$this->colspan[ 'tfoot' ][ $row ] = $colspan;
		$this->rowspan[ 'tfoot' ][ $row ] = $rowspan;
		$this->colwidth[ 'tfoot' ][ $row ] = $colwidth;
		$this->colelsinline[ 'tfoot' ][ $row ] = $colelsinline;
		$this->header[ 'tfoot' ][ $row ] = $header;
		
		return $this;
	}
	
	public function init ( ) {
		$this->visuoelement->visuoelement_group->init( );
	}
}


class visuoelement_table_table {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_table $visuoelement_group;
	public visuoelement_table_table__data $visuoelement_group_element__data;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement_table $visuoelement_group , null|visuoelement_table_table__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_table_table__data( $this );
		else
			$this->visuoelement_group_element__data = $visuoelement_group_element__data;
		
		# $this->init( );
	}
	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function group ( ) {
		return $this->visuoelement_group;
	}
	public function data ( ) {
		return $this->visuoelement_group_element__data;
	}
	
	public function clear ( ) {
		unset( $this->visuotag_start , $this->visuotag_end , $this->visuoelement->visuotag_start , $this->visuoelement->visuotag_end );
	}
	public function init ( ) {		
		$this->clear( );
		$vt_start = new visuotag( );
		$vt_end = new visuotag( );
		
		$type_div = false;
		$type_table = $type_div ? 'div' : 'table';
		$type_thead = $type_div ? 'div' : 'thead';
		$type_tbody = $type_div ? 'div' : 'tbody';
		$type_tfoot = $type_div ? 'div' : 'tfoot';
		$type_tr = $type_div ? 'div' : 'tr';
		$type_td = $type_div ? 'div' : 'td';
		
		$vt = new visuotag( );
		$vt = $vt->tag($type_table , class : 'visuotable' );
		
		$el_data = $this->visuoelement_group_element__data;
		$ttypes = $this->visuoelement_group_element__data->ttype;
		if( ! empty( $ttypes ) ){
			foreach( $ttypes as $ttype ){
				$_ttypedata_ = 'ttypedata';
				$_colspan_ = 'colspan';
				$_rowspan_ = 'rowspan';
				$_colwidth_ = 'colwidth';
				$_colelsinline_ = 'colelsinline';
				$_header_ = 'header';
				# $style
				$rows = $this->visuoelement_group_element__data->{$_ttypedata_}[ $ttype ];
				
				if( empty( $rows ) ){
				} else {
					$vt = $vt->tag( ${'type_'.$ttype} , class : 'ttype ' . $ttype )->data_add( 'div' , $ttype , false );
					foreach( $rows as $row_count=>$row ){
						$vt = $vt->tag( $type_tr , class : 'row' );
						
						$header = $this->visuoelement_group_element__data->{$_header_}[$ttype ][ $row_count ];

						! ( isset( $header ) && $header === true ) ?: $vt->attrs_class_add( 'header' );
						
						$cols = $row;
						if( ! empty( $cols ) ){
							foreach( $cols as $col_count=>$col ){
								
								$colspan = $this->visuoelement_group_element__data->{$_colspan_}[ $ttype ][ $row_count ][ $col_count ];
								$rowspan = $this->visuoelement_group_element__data->{$_rowspan_}[ $ttype ][ $row_count ][ $col_count ];
								$colwidth = $this->visuoelement_group_element__data->{$_colwidth_}[$ttype ][ $row_count ][ $col_count ];
								$colelsinline = $this->visuoelement_group_element__data->{$_colelsinline_}[$ttype ][ $row_count ][ $col_count ];
								
								$vt = $vt->tag( $type_td , class : 'col asdas' );
								
								
								! ( isset( $colspan ) && $colspan > 1 ) ?: $vt->attrs_add( 'colspan' , $colspan );
								! ( isset( $rowspan ) && $rowspan > 1 ) ?: $vt->attrs_add( 'rowspan' , $rowspan );
								! ( isset( $colwidth ) ) ?: $vt->attrs_style_add( 'width : ' . $colwidth );
								! ( isset( $colelsinline ) && $colelsinline === true ) ?: $vt->attrs_class_add( 'colelsinline' );
								! ( isset( $colelsinline ) && $colelsinline === true ) ?: $vt->data_add( 'colelsinline' , $colelsinline );

								$vt = $vt->tag( 'div' , class : 'col-inside' );
								
								$elements = is_array( $col ) ? $col : [ $col ];
								
								foreach( $elements as $el ){
									### print gettype( $el ) . '<br />';
									
									if( is_object( $el ) ){
										
										## print '-- ' . get_class( $el ) . '<br />';
										
										if( get_class( $el ) === 'visuotag' ){
											$el_block = new visuoelement( $this->visuoelement->visuorelease );
											$el_block->block( )->text( );
											$el_block->get_block_text__data( )->set( $el->root( )->release( ) );
											$vt->element( 'element' , $el_block );

											# # # $vt->append( $el->root( ) );
										} else if( get_class( $el ) === 'visuoelement' ){
											$vt->element( 'inside' , $el );
										} else {
											# $el_block = new visuoelement( $this->visuoelement->visuorelease );
											# $el_block->block( )->text( );
											# $el_block->set( $el );
											# $vt->element( 'element' , $el_block );
										}
									} else {
										$el_block = new visuoelement( $this->visuoelement->visuorelease );
										$el_block->table( )->blocktext( );
										$el_block->get_table_blocktext__data( )->set( $el );
										$vt->element( 'element' , $el_block );
									}
									
								}

								$vt = $vt->parent( );
								$vt = $vt->parent( );
							}
							$vt = $vt->parent( );
						}
					}
					$vt = $vt->parent( );
				}
			}
		}
		$vt = $vt->root( );
		
		# die( $vt->releasetree( ) );
		$vt_start = $vt;
		$vt_end = $vt;
		
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
	}
}

?>