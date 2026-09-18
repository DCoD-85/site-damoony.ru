<?PHP

class vardump {
	# vars
	public null|main $main = null;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
	}
	
	public function vardump ( mixed $var ,
														null|string $varname = null ,
														bool $debug = false ,
														bool $headers = true ,
														int $count = 0 ,
														null|visuotag $visuotag = null ) {
		$return = '';
		
		if( ! class_exists( 'visuotag' ) ) error( 'Отсутствует класс Visuotag' , 'Отсутствует класс Visuotag' , __FILE__ , __LINE__ );
		
		if( $count == 0 ){
			$visuotag = new visuotag( );
			$visuotag = $visuotag->tag( 'div' , class : 'my_vardump' )
				->tag( 'div' , class : 'div_title' )->content( 'adea' )
				->parent( )
				->tag( 'div' , class : 'div_body' );
		}
				
		switch( mb_strtolower( gettype( $var ) ) ){
			case 'object' :
				# $title = strtoupper( gettype( $var ) );
				# $title_count = ' : ' . get_class( $var ); // ':' . count( $var ).'';
				$title_var = ! is_null( $varname ) ? $varname : strtoupper( gettype( $var ) );
				$title_type = strtoupper( gettype( $var ) );
				$title_count = ':' . get_class( $var ) . '';
				
				$visuotag = $visuotag
					->tag( 'div' , class : 'my_vardump_row' )
						->tag( 'span' , class : 'my_vardump_row_type' )
							->attrs_actions_add( 'onclick' , '$(this).parent( ).find( \'div\' ).eq( 0 ).stop( 1 ).toggle( 200 ); return false;' )
						->content( $title_type . $title_count )
						->parent( )->tag( 'span' , class : 'my_vardump_row_name' )->content( $title_var )
						->parent( )->tag( 'span' , class : 'my_vardump_row_start' )->content( '[' )
						->parent( )->tag( 'div' , class : 'my_vardump_row_box' );
						
				foreach( $var as $key=>$value ) {
					$name = $key;
					if( is_object( $value ) ){
						$value2 = $value;
					} else {
						$value2 = $value;
					}
					$this->vardump( $value2 , $name , $debug , $headers , ++$count , $visuotag );
						$count--;
					}
				
				$visuotag = $visuotag
					->parent( )->tag( 'span' , class : 'my_vardump_row_end' )->content( ']' )
					->parent( )->tag( 'div' , class : 'clear' )
					->parent( );
				break;
			case 'array' :
				$title_var = ! is_null( $varname ) ? $varname : strtoupper( gettype( $var ) );
				$title_type = strtoupper( gettype( $var ) );
				$title_count = ':' . count( $var ) . '';
				
				$visuotag = $visuotag
					->tag( 'div' , class : 'my_vardump_row' )
						->tag( 'span' , class : 'my_vardump_row_type' )
							->attrs_actions_add( 'onclick' , '$(this).parent( ).find( \'div\' ).eq( 0 ).stop( 1 ).toggle( 200 ); return false;' )
						->content( $title_type . $title_count )
						->parent( )->tag( 'span' , class : 'my_vardump_row_name' )->content( $title_var )
						->parent( )->tag( 'span' , class : 'my_vardump_row_start' )->content( '[' )
						->parent( )->tag( 'div' , class : 'my_vardump_row_box' );
				
				
				if( ! empty( $var ) ) {
					foreach( $var as $key=>$value ) {
						$name = $key;
						if( is_object( $var ) ){
							$value = $var->{$key};
						}
						$this->vardump( $value , $name , $debug , $headers , ++$count , $visuotag );
						$count--;
					}
				}
				$visuotag = $visuotag
					->parent( )->tag( 'span' , class : 'my_vardump_row_end' )->content( ']' )
					->parent( )->tag( 'div' , class : 'clear' )
					->parent( );
				break;
			# case 'null' :
			# 	break;
			default :
				$title_var = ! is_null( $varname ) ? $varname : $var;
				$title_val = ' = ' . $var;
				$title = '';
				$title_type = strtoupper( gettype( $var ) );
				$title_len = ':' . strlen( $var );
				if( is_null( $var ) ){
					$title_val = ' = NULL';
					$title_len = '';
				} else if( is_bool( $var ) ) {
					$title_val = ' = ' . ( $var == true ? 'true' : 'false' );
					$title_len = '';
				} else {
				}
				
				
				$title_strlen = ( is_string( $var ) ? ':' . mb_strlen( $var ) : '' );
				$visuotag->tag( 'div' , class : 'my_vardump_row' )
					->tag( 'span' , class : 'my_vardump_row_type' )->content( $title_type . $title_len )
					->parent( )->tag( 'span' , class : 'my_vardump_row_name' )->content( $title_var )
					->parent( )->tag( 'span' , class : 'my_vardump_row_val' )->content( $title_val )
					->parent( )->tag( 'span' , class : 'clear' );
				break;
		}
		
		return $visuotag;
	}
	
	public function my_vardump ( $var , string $varname = '' , bool $debug = false , bool $headers = true , int $count = 0 ) {
		$return = '';
		
		if( ! empty( $varname ) || $debug != false ){
			$return .= '<div class="my_vardump">
			<div class="div_title">
				<span class="div_title_actions"><span onClick="text = $( this ).parents( \'.messagebody\' ).find( \'.messagetext\' ); test = navigator.clipboard.writeText( text.text( ) ); if( test ){alert( \'Данные скопированы вбуфер обмена\' );} return false;"><a href="javascript:;">Копировать</a></span></span>
				<span class="div_title_varname"><span style=" font-weight : 500 ; color : #69C ; ">'.$varname.'</b>';
				if( is_array( $var ) ) $return .= '<span class="div_title_type">// array: '.count( $var ).'</span>';
				else if( is_object( $var ) ) $return .= '<span class="div_title_type">// object</span>';
				
				$return .= '</span></span>
			</div><div class="messagetext">';
		}
		else if( $count == 0 ){
			$return .= '<div class="my_vardump">';
		}
		else {
		}
		
		if( is_array( $var ) ){
			if( ! empty( $var ) ){
				foreach( $var as $k=>$v ){
					$var_type = gettype( $v );
					
					if( is_array( $v ) ){
						$title = strtoupper( gettype( $v ) ).':<b>'.count( $v ).'</b>';
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body" onClick="$(this).parent( ).find( \'div\' ).eq( 0 ).stop( 1 ).toggle( 200 ); return false;">'.$title.' </span> <b>'.$k.'</b> [
							<div title="'.$title.'"
								class="my_vardump_row_box"
								onMouseOut="$( this ).css({ \'border-color\' : \'#CCCCCC\' });"
								onMouseOver="$( this ).css({ \'border-color\' : \'#336699\' });">';
						$return .= $this->my_vardump( $v , 0 , $debug , $headers , ++$count );
						$count--;
						$return .= '</div>
							]
						</div><div class="clear">
						</div>';
					}
					else if(  is_object( $v ) ){
						$title = strtoupper( gettype( $v ) ).':<b>'.count( $v ).'</b>';
						$return .= '<div class="my_vardump_row" onClick="$(this).next().toggle( 200 ); return false;">
							<span class="my_vardump_row_body">'.$title.' </span> <b>'.$k.'</b> [
						</div><div title="'.$title.'" class="my_vardump_row_box" onMouseOut="$( this ).css({ \'border-color\' : \'#CCCCCC\' });" onMouseOver="$( this ).css({ \'border-color\' : \'#336699\' });">';
						# $return .= $this->my_vardump( $var->{$k} , 0 );
						$return .= '</div><div>
							]
						</div><div class="clear">
						</div>';
					}
					else if( is_string( $v ) ){
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$k.'</b> = <span style="color : #693 ; ">'.htmlspecialchars( $v ).'</span>
						</div><div class="clear">
						</div>';
					}
					else if( is_int( $v ) ){
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$k.'</b> = <span style="color : #693 ; ">'. $v .'</span>
						</div><div class="clear">
						</div>';
					}
					else if( is_bool( $v ) ){
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$k.'</b> = <span style="color : #693 ; ">'. $v .'</span>
						</div><div class="clear">
						</div>';
					}
					else if( is_null( $v ) ) {
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b></b>' : '' ) . '</span> <b>'.$k.'</b> = <span style="color : #693 ; ">NULL</span>
						</div><div class="clear">
						</div>';
					}
				}
			}
			else {
				$var_type = gettype( $var );
				$return .= '<div class="my_vardump_row">
					<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>0</b>' : '' ) . '</span> Пустой массив
				</div><div class="clear">
				</div>';
			}
		}
		else if( is_object( $var ) ){
			// if( ! empty( $var ) ){
				foreach( $var as $k=>$v ){
					$v = $var->{$k};
					$var_type = gettype( $v );
					
					if( is_array( $v ) ){
						$title = strtoupper( gettype( $v ) ).':<b>'.count( $v ).'</b>';
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body" onClick="$(this).parent( ).find( \'div\' ).eq( 0 ).toggle( 200 ); return false;">'.$title.' </span> <b>'.$k.'</b> [
							<div title="'.$title.'" class="my_vardump_row_box" onMouseOut="$( this ).css({ \'border-color\' : \'#CCCCCC\' });" onMouseOver="$( this ).css({ \'border-color\' : \'#336699\' });">';
						$return .= $this->my_vardump( $v , 0 , $debug , $headers , ++$count );
						$count--;
						$return .= '</div>
							]
						</div><div class="clear">
						</div>';
					}
					else if(  is_object( $v ) ){
						
						$title = strtoupper( gettype( $v ) ).'</b>';
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body" onClick="$(this).parent( ).find( \'div\' ).eq( 0 ).toggle( 200 ); return false;">'.$title.' </span> <b>'.$k.'</b> [
							<div title="'.$title.'" class="my_vardump_row_box" onMouseOut="$( this ).css({ \'border-color\' : \'#CCCCCC\' });" onMouseOver="$( this ).css({ \'border-color\' : \'#336699\' });">';
							$return .= $this->my_vardump( $v , 0 , $debug , $headers , ++$count );
							$count--;
						
						$return .= '</div>
							]
						</div><div class="clear">
						</div>';
					}
					else if( is_string( $v ) ){
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$k.'</b> = <span style="color : #693 ; ">'.htmlspecialchars( $v ).'</span>
						</div><div class="clear">
						</div>';
					}
					else if( is_int( $v ) ){
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$k.'</b> = <span style="color : #693 ; ">'. $v .'</span>
						</div><div class="clear">
						</div>';
					}
					else if( is_bool( $v ) ){
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$k.'</b> = <span style="color : #693 ; ">'. $v .'</span>
						</div><div class="clear">
						</div>';
					}
					else if( is_null( $v ) ){
						$return .= '<div class="my_vardump_row">
							<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$k.'</b> = <span style="color : #693 ; ">NULL</span>
						</div><div class="clear">
						</div>';
					}
					else {
					}
				}
			// }
		}
		else {
			$v = $var;
			$var_type = gettype( $v );
			
			if( is_string( $v ) ){
				$return .= '<div class="my_vardump_row">
					<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$v.'</b> = <span style="color : #693 ; ">'.htmlspecialchars( $v ).'</span>
				</div><div class="clear">
				</div>';
			}
			else if( is_int( $v ) ){
				$return .= '<div class="my_vardump_row">
					<span class="my_vardump_row_body">
						' . ( $headers == true
							 ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>'
							  : ''
							) . '</span> <b>'.$v.'</b> = <span style="color : #693 ; ">'. $v .'</span>
				</div><div class="clear">
				</div>';
			}
			else if( is_bool( $v ) ){
				$return .= '<div class="my_vardump_row">
					<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$v.'</b> = <span style="color : #693 ; ">'. $v .'</span>
				</div><div class="clear">
				</div>';
			}
			else if( is_null( $v ) ){
				$return .= '<div class="my_vardump_row">
					<span class="my_vardump_row_body">' . ( $headers == true ? strtoupper( $var_type ).':<b>'.mb_strlen( $v ).'</b>' : '' ) . '</span> <b>'.$v.'</b> = <span style="color : #693 ; ">'. $v .'</span>
				</div><div class="clear">
				</div>';
			}
			else {
				$return .= 'Переменная пустая.';
			}
		}
		
		if( ! empty( $varname ) ){
			$return .= '</div></div>';
		}
		else if( $count == 0 ){
			$return .= '</div>';
		}
		else {
		}
		
		return $return;
	}
}

?>