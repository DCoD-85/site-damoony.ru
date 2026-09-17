<?PHP

class library_other {
	private null|library $library = null;
	
	public function __construct( null|library $library ) {
		$this->library = $library;
	}
	
	
	/*
	// ..
	public function arr_merge( array $arrays = [ ] ) {
		$return = [ ];
		
		if( ! empty( $arrays ) ) foreach( $arrays as $array ) foreach( $array as $value ) $return[ ] = $value;
		
		return $return;
	}
	
	// .. 
	public function my_json( string|array $data ) {
		if( is_string( $data ) )
			return str_replace( '"' , '&quot;' , json_encode( $data ) );
		else if( is_array( $data ) )
			return str_replace( '"' , '&quot;' , json_encode( $data ) );
	}
	
	// ..
	public function my_rand( int $min , int $max , bool $useAll=false ) {
		$return = '';
		if( $useAll == false ){
			$return = rand( $min , $max );
		}
		else {
			$str_num = '0123456789';
			$str_eng = 'abcdefghijklmnopqrstuvwxyz';
			$str_rus = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
			
			$str = $str_num . $str_eng;
			for( $q = 0 ; $q <= $max ; $q++ ){
				$cur = $str[ rand( 0 , mb_strlen( $str ) ) ];
				if( rand( 1 , 100 ) >= 51 ){
					$cur = mb_strtoupper( $cur );
				}
				
				$return .= $cur;
			}
		}
		
		return $return;
	}
	
	public function my_returnget( $var , $whithout = '' , $varname = '' , &$return = [ ] ) {
		if( is_array( $var ) && ! empty( $var ) ){
			foreach( $var as $k=>$v ){
				if( $varname == '' ) $nvarname = $k;
				else $nvarname = $varname.'['.$k.']';
				
				if( is_array( $v ) ){
					$this->my_returnget( $v , $whithout , $nvarname , $return );
				}
				else{
					if( is_array( $whithout ) ){
						foreach( $whithout as $k1 ){
							if( $nvarname == $k1 ) return;
						}
					}
					$return[ $nvarname ] = $v;
				}
			}
		}
		return $return;
	}
	public function wordsanalizator( $text , $opts=[] ){
		
		$text = mb_strtolower( $text );
		$text = strip_tags( $text );
		
		$find = [ ',' , '.' , ':' , '(' , ')' , '!' , '-' , '<' , '>' , '`' , '/' , ];
		$replace = [ '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , ];
		$replace = [ ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ];
		$replace = [ ' , ' , ' . ' , ' : ' , ' ( ' , ' ) ' , ' ! ' , ' - ' , ' < ' , ' > ' , ' ` ' , ' / ' , ];
		$textedited = str_replace( $find , $replace , $text );
		
		$texttmp = explode( ' ' , $textedited );
		$words = [ ];
		$predlogs = [ ];
		$textwords = '';
		
		foreach( $texttmp as $k1=>$v1 ){
			$v1 = trim( $v1 );
			if( $v1 != '' && $v1 != ' ' ){
				if( mb_strlen( $v1 ) >= 2 ){
					if( empty( $words[ $v1 ] ) )
						$words[ $v1 ] = 0;
					
					$words[ $v1 ]++;
				}
				else {
					if( empty( $predlogs[ $v1 ] ) )
						$predlogs[ $v1 ] = 0;
					
					$predlogs[ $v1 ]++;
				}
			}
		}
		arsort( $words );
		arsort( $predlogs );
		if( ! empty( $words ) ){
			$textwords .= '<div style="font-weight : 700 ; margin : 0 5px 5px 0 ; padding : 10px ; background : #CCC ; border-radius : 3px ; ">Слова</div>';
			foreach( $words as $k1=>$v1 ){
				if( $k1 != '' && $k1 != ' ' ){
					$textwords .= '<span style="display :inline-block ; font-weight : 500 ; margin : 0 5px 5px 0 ; padding : px ; background : #FFF ; border : solid #DDD 0px ; border-radius : 3px ; ">
						<span style="display : inline-block ; padding : 5px ; ">'.$k1.'</span>
						<span style="display : inline-block ; padding : 5px ; background : #333 ; color : #DDD ; min-width : 15px ; text-align : center ; border-radius : 0 3px 3px 0 ; ">'.$v1.'</span>
					</span>';
				}
			}
		}
		
		if( ! empty( $words ) ){
			$textwords .= '<div style="font-weight : 700 ; margin : 0 5px 5px 0 ; padding : 10px ; background : #CCC ; border-radius : 3px ; ">Предлоги и прочее</div>';
			foreach( $predlogs as $k1=>$v1 ){
				if( $k1 != '' && $k1 != ' ' ){
					$textwords .= '<span style="display :inline-block ; font-weight : 500 ; margin : 0 5px 5px 0 ; padding : px ; background : #FFF ; border : solid #DDD 0px ; border-radius : 3px ; ">
						<span style="display : inline-block ; padding : 5px ; ">'.$k1.'</span>
						<span style="display : inline-block ; padding : 5px ; background : #333 ; color : #DDD ; min-width : 15px ; text-align : center ; border-radius : 0 3px 3px 0 ; ">'.$v1.'</span>
					</span>';
				}
			}
		}
		
		return $textwords;
	}
	
	// CAPCHA
	public function my_capcha( $session_name , $check='' ){
		if( $check == '' ){
			$num_acts = [ '-' , '+' , '*' ];
			$num_1 = rand( 0 , 10 );
			$num_2 = rand( 0 , 10 );
			$num_3 = rand( 0 , 10 );
			$num_act_num1 = rand( 0 , count( $num_acts )-1 );
			$num_act_num2 = rand( 0 , count( $num_acts )-2 );
			$num_act1 = $num_acts[ $num_act_num1 ];
			$num_act2 = $num_acts[ $num_act_num2 ];
			
			# if( $num_1 >= $num_2 ){
			# 	$n1 = $num_1;
			# 	$n2 = $num_2;
			# }
			# else {
			# 	$n1 = $num_2;
			# 	$n2 = $num_1;
			# }
			# 
			# $num_1 = $n1;
			# $num_2 = $n2;
			
			switch( $num_act_num1 ){
				case 0 : $num_result = $num_1 - $num_2; break;
				case 1 : $num_result = $num_1 + $num_2; break;
				case 2 : $num_result = $num_1 * $num_2; break;
				default : break;
			}
			
			switch( $num_act_num2 ){
				case 0 : $num_result = $num_result - $num_3; break;
				case 1 : $num_result = $num_result + $num_3; break;
				default : break;
			}
			$_SESSION[ 'antispam' ][ $session_name ] = $num_result;
			
			$string = 'Введите результат операции: <span>'.$num_1.'</span><span>'.$num_act1.'</span><span>'.$num_2.'</span><span>'.$num_act2.'</span><span>'.$num_3.'</span>';
			
			return [
				'num_1'=>$num_1 ,
				'num_2'=>$num_2 ,
				'num_3'=>$num_3 ,
				'num_act1'=>$num_act1 ,
				'num_act2'=>$num_act2 ,
				'numresult'=>$num_result ,
				'string'=>$string
			];
		}
		else {
			return $_SESSION[ 'antispam' ][ $session_name ];
		}
	}
	
	// Calendar
	
	// Формирование данных календаря
	public function calendar_data_return( $time ){		
		if( $time == false )	$timestamp = time();
		else							$timestamp = $time;
		
		// $data2 = array('day'=>date('d' , $data) , 'mon'=>date('m' , $data) , 'year'=>date('Y' , $data));
		// unset($data);
		// $data = $data2;
		// Формируем дату
		$cur_day = date( 'd' , $timestamp );
		$cur_mon = date( 'm' , $timestamp );
		$cur_year = date( 'Y' , $timestamp );
		$current_date = mktime( 0 , 0 , 0 , $cur_mon , $cur_day , $cur_year );
		// Вычисляем число дней в текущем месяце
		$dayofmonth = date( 't' , $current_date );
		// Счётчик для дней месяца
		$day_count = 1;
		
		// 1. Первая неделя
		$num = 0;
		for( $i = 0 ; $i < 7 ; $i++ ){
			// Вычисляем номер дня недели для числа
			$dayofweek = date( 'w' , mktime( 0 , 0 , 0 , date( 'm' , $current_date ) , $day_count , date( 'Y' , $current_date ) ) );
			
			// Приводим к числа к формату 1 - понедельник, ..., 6 - суббота
			$dayofweek = $dayofweek - 1;
			if( $dayofweek == -1 ) $dayofweek = 6;
			
			if( $dayofweek == $i ){
				// Если дни недели совпадают , заполняем массив $week числами месяца
				$week[$num][$i] = $day_count;
				$day_count++;
			}
			else{
				$week[$num][$i] = '';
			}
		}
		
		// 2. Последующие недели месяца
		while( true ){
			$num++;
			for( $i = 0; $i < 7; $i++ ){
				$week[$num][$i] = $day_count;
				$day_count++;
				// Если достигли конца месяца - выходим из цикла
				if( $day_count > $dayofmonth ) break;
			}
			// Если достигли конца месяца - выходим из цикла
			if( $day_count > $dayofmonth ) break;
		}
		
		return $week;
	}
	
	public function calendar_data_show( $timestamp='' , $fieldname='', $show_type='dmy' , $link_address='' ){
		$class__index					= $this->class__index;
		
		# $class_parent					= $this->class_parent;
		# $class_parentsub			= $this->class_parentsub;
		$class__user					= $class__index->class__user;
		$class__							= $class__index->class__;
		$class__db						= $class__index->class__db;
		$class__functions			= $class__index->class__functions;
		$class__form					= $class__index->class__lib->lib_form;
		

		// get && post && ...
		foreach( $class__->get as $k=>$v ) ${'get_'.$k} = $v;
		foreach( $class__->post as $k=>$v ) ${'post_'.$k} = $v;
		foreach( $class__->address as $k=>$v ) ${'address_'.$k} = $v;
		foreach( $class__->files as $k=>$v ) ${'files_'.$k} = $v;
		
		switch( $show_type ){
			// ДеньМесяцГод
			case 'dmy' :
				$show = [ 'd'=>true , 'm'=>true , 'y'=>true ];
				break;
			// Год
			case 'y' :
				$show = [ 'd'=>false , 'm'=>false , 'y'=>true ];
				break;
			default :
				break;
		}
		
		// Данные
		# $data = array('day'=>24 , 'mon'=>8 , 'year'=>2012);
		# $data_time = mktime(0 , 0 , 0 , 8 , 23 , 1985);
		
		if( $timestamp == false )	$data_time = time();
		else											$data_time = $timestamp;
		
		
		
		$mounth = [ 'Январь' , 'Февраль' , 'Март' , 'Апрель' , 'Май' , 'Июнь' , 'Июль' , 'Август' , 'Сентябрь' , 'Октябрь' , 'Ноябрь' , 'Декабрь' ];
		$header = [ 'Пн' , 'Вт' , 'Ср' , 'Чт' , 'Пт' , 'Сб' , 'Вс' ];
		
		$data = array(
			'day'=>date( 'd' , $data_time ) ,
			'mon'=>date( 'm' , $data_time ) ,
			'year'=>date( 'Y' , $data_time )
		);
		
		$return = '';
		// День
		if( $show[ 'd' ] ){
			// Достаем данные по неделям
			$week = $this->calendar_data_return( $data_time );
			
			$link = $this->ahref( [ 'time'=>'\' + this.options[this.selectedIndex].value + \'' ] , 1 , $link_address );
			$link = 'javascript : calendar( \''.$link.'\' , \''.$fieldname.'\' );';
			$return .= '<span><select onChange="'.$link.'">';
				for( $i = 0 ; $i < count( $week ); $i++ ){
					for( $j = 0 ; $j < 7 ; $j++ ){
						$selected = '';
						$class = '';
						$style = '';
						if( $j == 6 ){
							$class .= ' td_row_last';
						}
						if( $i < count( $week ) - 1 ){
						}
						else {
							$class .= ' td_row_bottom_last';
						}
						
						if( ! empty( $week[ $i ][ $j ] ) ){
							if( $data[ 'day' ] == $week[ $i ][ $j ] ){
								$selected = ' selected="selected"';
								$class .= ' td_row_selected';
							}
							else{
							}
							// Если имеем дело с субботой и воскресенья -> подсвечиваем их
							if( $j == 5 || $j == 6 ){
								$style = 'color : #F00 ;';
								$class .= " td_row_holiday";
							}
							$name = '['.$header[ $j ].'] '.$week[ $i ][ $j ];
							$value = mktime( 0 , 0 , 0 , $data[ 'mon' ] , $week[ $i ][ $j ] , $data[ 'year' ] );
							
							$return .= '<option value="'.$value.'" style="'.$style.'"'.$selected.'>'.$name.'</option>';
						}
						else {
						}
					}
				}
				$return .= '
			</select></span>';
		}
		
		
		// Месяц
		if( $show[ 'm' ] ){
			$link = $this->ahref( [ 'time'=>'\' + this.options[this.selectedIndex].value + \'' ] , 1 , $link_address );
			$link = 'javascript : calendar( \''.$link.'\' , \''.$fieldname.'\' );';
			$return .= '<span><select onChange="'.$link.'">';
				for( $q = 0 ; $q < count( $mounth ) ; $q++ ){
					$selected = ( ( (int)$data[ 'mon' ] - 1 ) == $q ? ' selected="selected"' : '' );
					$name = $mounth[ $q ];
					$value = mktime( 0 , 0 , 0 , ( $q+1 ) , 1 , $data[ 'year' ] );
					$return .= '<option value="'.$value.'"'.$selected.'>'.$name.'</option>';
				}
				$return .= '
			</select></span>';
		}
		
		// Год
		if( $show[ 'y' ] ){
			$link = $this->ahref( [ 'time'=>'\' + this.options[this.selectedIndex].value + \'' ] , 1 , $link_address );
			$link = 'javascript : calendar( \''.$link.'\' , \''.$fieldname.'\' );';
			$return .= '<span><select onChange="'.$link.'">';
				for( $q = 1970 ; $q <= date( 'Y' ) ; $q++ ){
					$selected = ( ( (int)$data[ 'year' ] ) == $q ? ' selected="selected"' : '' );
					$name = $q;
					$value = mktime( 0 , 0 , 0 , $data[ 'mon' ] , 1 , $q );
					$return .= '<option value="'.$value.'"'.$selected.'>'.$name.'</option>';
				}
				$return .= '
			</select></span>';
		}
		
		// Месяц
		$link = $this->ahref( [ 'hash'=>1 , 'fieldname'=>$fieldname , 'status'=>'start' ] , 0 , $link_address );
		$link = 'javascript : calendar( \''.$link.'\' , \''.$fieldname.'\' );';
		$return .= '<span class="span-close"><a href="javascript:;" onClick="'.$link.'">
			убрать
		</a></span>';
		
		return $return;
	}
	
	
	public function calendar_show( $timestamp='' , $url='' , $func='' , $layer_id='' , $input_name='' , $file_output='' ){
		// Данные
		# $data = array('day'=>24 , 'mon'=>8 , 'year'=>2012);
		# $data_time = mktime(0 , 0 , 0 , 8 , 23 , 1985);
		
		if( $timestamp == false )	$data_time = time();
		else									$data_time = $timestamp;
		
		
		$week = $this->calendar_data_return( $data_time );
		$mounth = array('Январь' , 'Февраль' , 'Март' , 'Апрель' , 'Май' , 'Июнь' , 'Июль' , 'Август' , 'Сентябрь' , 'Октябрь' , 'Ноябрь' , 'Декабрь');
		$header = array('Пн' , 'Вт' , 'Ср' , 'Чт' , 'Пт' , 'Сб' , 'Вс');
		
		$data = array(
			'day'=>date( 'd' , $data_time ) ,
			'mon'=>date( 'm' , $data_time ) ,
			'year'=>date( 'Y' , $data_time )
		);
			
		// 3. Выводим содержимое массива $week
		// в виде календаря
		// Выводим таблицу
		$return = '
		<style>
			.table_cal_fon {display:block; padding: 5px; background:url(\'/.style/loaders/load.gif\') #FFF center no-repeat;}
			.table_cal_fon {}
			.table_cal_fon2 {display:inline-block; width:100%;}
			.table_cal_fon2_ch {background:#FFF; width:100%;}
			.table_contur {display:block;}
			table.table_cal {background:#FFF; width:100%; border:solid #FFF 0px;}
			.table_cal tr td {margin:0; padding:0px; font-size:13px; text-align:center;}
			.table_cal select {margin:0 0px 0 0; width:100%; font-size: 13px; color:#666;}
			.table_cal .tr_month td {padding:0 0 5px 0;}
			.table_cal .tr_month td:last-child {padding-left: 5px;}
			.table_cal tr.tr_head {font-weight:;}
			# .table_cal tr.tr_head td.td_row {padding:5px; color: #999; background:#333; border-top:solid #222 1px; #FFF; border-bottom:solid #222 1px; border-left:solid #333 1px;}
			.table_cal tr.tr_head td.td_row {padding:5px; background:#F9F9F9; border-top:solid #EEE 1px; border-bottom:solid #FFF 0px; border-left:solid #F9F9F9 1px;}
			.table_cal tr.tr_head td.td_row_last {border-right: none;}
			.table_cal tr.tr_head td.td_row_holiday {color: #F66;}
			
			div.divTableDays {border: solid #CCC 1px !important;}
			.tableDays {width: 100%;}
			.table_cal tbody.tbodyDays {}
			.table_cal tr.tr_head {}
			.table_cal tr.tr_head td {}
			.table_cal tr.tr_body {}
			.table_cal tr.tr_body td {vertical-align: middle;}
			.table_cal tr.tr_body td a {}
			.table_cal tr.tr_body td a {display:block; padding:5px; color:#999; border:solid #CCC; border-width:0 1px 1px 0; border:none;}
			.table_cal tr.tr_body td a:hover {background:#EEE; color:#333; text-decoration:none;}
			.table_cal tr.tr_body td.td_row {border-top:solid #EEE 1px; border-right:solid #EEE 1px;}
			.table_cal tr.tr_body td.td_row_last {border-right:none;}
			.table_cal tr.tr_body td.td_row_bottom_last {border-bottom:solid #999 0px;}
			.table_cal tr.tr_body td.td_row_empty a {background:none;}
			.table_cal tr.tr_body td.td_row_selected {color:#FFF;}
			.table_cal tr.tr_body td.td_row_selected a {color:#FFF; background:#666 !important; border-color:#333;}
			.table_cal tr.tr_body td.td_row_selected a:hover {color:#FFF; background:#333;}
			.table_cal tr.tr_body td.td_row_holiday {background:#;}
			.table_cal tr.tr_body td.td_row_holiday a {color:#F66;}
			.table_cal tr.tr_body td.td_row_holiday a:hover {color:#F66;}
		</style>';
		
		$return .= '
			<span class="table_cal_fon"><span class="table_cal_fon2">
				<span class="table_contur">
					<table class="table_cal">
						<tr class="tr_month">
							<td colspan="4" align="left">';
								if( $func ){
									$link = ''.$func.'( \''.$layer_id.'\' , \''.$input_name.'\' , \''.($url ? $url : '').'?time=\' + this.options[this.options.selectedIndex].value ); return false;';
								}
								$return .= '
								<select onChange="'.$link.'">';
								for($q = 0 ; $q < count($mounth) ; $q++){
									$sel = (((int)$data['mon']-1) == $q ? ' selected="selected"' : '');
									
									$val = mktime(0 , 0 , 0 , ($q+1) , 1 , $data['year']);
									
									$return .= '<option value="'.$val.'"'.$sel.'>'.$mounth[$q].'</option>';
								}
								$return .= '</select>
							</td>
							<td colspan="3" align="right">';
								if($func) {
									$link = ''.$func.'( \''.$layer_id.'\'  , \''.$input_name.'\' , \''.($url ? $url : '').'?time=\' + this.options[this.options.selectedIndex].value ); return false;';
								}
								$return .= '
								<select onChange="'.$link.'">';
								for($q = 1970 ; $q <= date('Y') ; $q++){
									$sel = (((int)$data['year']) == $q ? ' selected="selected"' : '');
									
									$val = mktime(0 , 0 , 0 , $data['mon'] , 1 , $q);
									
									$return .= '<option value="'.$val.'"'.$sel.'>'.$q.'</option>';
								}
								$return .= '
								</select>
							</td>
						</tr>
							<tr class="tr_head">';
						for($i = 0; $i < count($header); $i++){
							$class = '';
							if($i == 6) $class .= ' td_row_last';
							if($i == 5 || $i == 6) $class .= " td_row_holiday";
							$return .= '<td class="td_row'.$class.'">'.$header[$i].'</td>';
						}
						$return .= '</tr>';
						$return .= '<tbody class="tbodyDays">';
						for($i = 0; $i < count($week); $i++){
							$return .= '<tr class="tr_body">';
							for($j = 0; $j < 7; $j++){
								$class = '';
								if($j == 6) $class .= ' td_row_last';
								if($i < count($week)-1){} else {$class .= ' td_row_bottom_last';}
								
								if(!empty($week[$i][$j])){
									if($data['day'] == $week[$i][$j]) $class .= ' td_row_selected';
									else $style = "";
									// Если имеем дело с субботой и воскресенья -> подсвечиваем их
									if($j == 5 || $j == 6) $class .= " td_row_holiday";
									
									$link = ($url ? $url : '').'?time='.mktime(0 , 0 , 0 , $data['mon'] , $week[$i][$j] , $data['year']);
									if($func) {$link = 'javascript:;" onClick="'.$func.'( \''.$layer_id.'\'  , \''.$input_name.'\' , \''.$link.'\'); return false;';}
									$return .= '<td class="td_row'.$class.'" style="'.$style.'" align="center"><a href="'.$link.'">'.$week[$i][$j].'</a></td>';
								}
								else {
									if($j == 5 || $j == 6) $class .= " td_row_holiday";
									$return .= '<td class="td_row td_row_empty'.$class.'"><a href="javascript:;">&nbsp;</a></td>';
								}
							}
							$return .= '</tr>';
						}
						$return .= '
						</tbody>
						<tbottom><tr class="tr_month" style="display: none;">
							<td colspan="3" style="padding-top: 5px; text-align:left;">Часов:<input type="number" min="0" max="23" class="in-text inp-wide" value="8" onChange="alert( \'input is changed!\');" /></td>
							<td colspan="4" style="padding-top: 5px; text-align:left;">Минут:<input type="number" min="0" max="59" class="in-text inp-wide" value="0" /></td>
						</tr></tbottom>
					</table>
			</span>
		</span></span>';
		
		return $return;
	}
	
	public function return_file_type( $mimeType ){
		$mimeTypes = [
			// images
			'image/png'=>[ 'image' , 'png' ] ,
			'image/jpeg'=>[ 'image' , 'jpeg' ] ,
			'image/gif'=>[ 'image' , 'gif' ] ,
			'image/bmp'=>[ 'image' , 'bmp' ] ,
			// Video
			'video/mp4'=>[ 'video' , 'mp4' ] ,
			// spreadsheets
			'application/vnd.ms-excel'=>[ 'application' , 'xl' ] ,
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>[ 'application' , 'xlsx' ] ,
			'text/csv'=>[ 'application' , 'csv' ] ,
			'text/text'=>[ 'application' , 'text' ] ,
			'application/vnd.stardivision.calc'=>[ 'application' , 'sdc' ] ,
			'application/vnd.oasis.opendocument.chart'=>[ 'application' , 'odc' ] ,
			// text files
			'application/msword'=>[ 'application' , 'doc' ] ,
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>[ 'application' , 'docx' ] ,
			'application/vnd.oasis.opendocument.text'=>[ 'application' , 'odt' ] ,
			'application/rtf'=>[ 'application' , 'rtf' ] ,
			'text/richtext'=>[ 'application' , 'rtx' ] ,
			// PDF
			'application/pdf'=>[ 'application' , 'pdf' ] ,
		];
		
		// .. return
		return [ 'mimeType'=>$mimeType , 'type'=>$mimeTypes[ $mimeType ][ 0 ] , 'format'=>$mimeTypes[ $mimeType ][ 1 ] ];
	}
	
	
	
	
	
	public function isFile( $path , $filename ){
		$filetmp = $this->dir_create( $path );
		
		$filepath			= $filetmp[ 'ds' ].$filename;
		$filepatha		= $filetmp[ 'dsa' ].$filename.'?'.rand( 1111,9999);
		
		if( ! is_file( $filepath ) ){
			return $filepath;
		}
		
		return [ 'ds'=>$filepath , 'dsa'=>$filepatha ];
	}
	
	public function isFileType( $path , $filename , $extended=true ){
		$filepath = $this->isFile( $path , $filename );
		if( $filepath != false ){
			$finfo = new finfo( FILEINFO_MIME_TYPE );
			$filetype  = $this->return_file_type( $finfo->file( $filepath[ 'ds' ] ) );
			
			if( $extended == true ){
				switch( $filetype[ 'type' ] ){
					case 'image' :
						// $return = 'Изображение<span style="display : block ; margin-top : 5px ;"><img src="'.$filepath[ 'dsa' ].'" /></span>';
						$return = '<img src="/media/files_img.png" /> Изображение';
						break;
					case 'video' :
						$return = 'Видео';
						break;
					case 'application' :
						$return = 'Файл: '.$filetype[ 'format' ];
						$return = '<img src="/media/files_'.$filetype[ 'format' ].'.png" /> Файл';
						break;
					default :
						break;
				}
			}
			else {
				switch( $filetype[ 'type' ] ){
					case 'image' :
						$return = '<img src="'.$filepath[ 'dsa' ].'" />';
						break;
					case 'video' :
						$return = '';
						break;
					case 'application' :
						$return = '';
						break;
					default :
						break;
				}
			}
		}
		else {
			if( $extended == true ) $return = 'Файл не загружен';
			else $return = '';
		}
		
		return $return;
	}
	
	public function isFileFormat( $fileinfo ){
	}
	
	public function isFileFormatSave( $fileinfo , $filepathfrom , $filepathto , $filenameto , $nWidth=800 , $only='' , $quality=100 , $nWidthImportant=false , $widthToHeight=false , $rotate=[] ){
		// .. only - определенный тип файла
		// (array) fileinfo : 
		// (array) $saveData : dir | 
		// $class__index = $this->class__index;
		
		if( $only != false && $only != $fileinfo[ 'type' ] ) return false;
		
		$filepath									= is_string( $filepathto ) ? $this->dir_create( $filepathto )[ 'ds' ] : '';
		
		// ..
		switch( $fileinfo[ 'type' ] ){
			case 'image' :
				$fileappointment		= 'imagemain';
				$filename						= $filenameto;
				$file									= $this->image_resize( $filepathfrom , $filepath , $filename , 215 , 'jpeg' , $quality , true , false , $rotate );
				// .. загружаем оригинальный файл
				
				$filename_original	= 'original_'.$filenameto;
				$file_original				= $this->image_resize( $filepathfrom , $filepath , $filename_original , false , 'jpeg' , $quality , false , false , $rotate );
				break;
			case 'video' :
				$fileappointment		= 'videoside';
				break;
			case 'application' :
				$fileappointment		= 'applicationdoc';
				$filename						= $filenameto.'.'.$fileinfo[ 'format' ];
				$file									= $this->file_rename( $filepathfrom , $filepath , $filename );
				break;
			default :
				return false;
		}
		
		$id = false;
		return [ 'id'=>$id , 'filepath'=>$file[ 'filepath' ] , 'filename'=>$file[ 'filename' ] , 'filetype'=>$fileinfo[ 'type' ] , 'fileappointment'=>$fileappointment , 'fileformat'=>$fileinfo[ 'format' ] , 'mimetype'=>$fileinfo[ 'mimetype' ] ];
	}
	
	// .. file
	public function file_rename( $path_tmp , $filepath , $filename ){
		$error = false;
		$return = [ ];
		if( rename( $path_tmp , $filepath.$filename ) ){
		}
		
		return [ 'error'=>$error , 'filepath'=>$filepath , 'filename'=>$filename ];
	}
	
	public function files_delete( $filepath , $fileid ){
		$filepath = $this->dir_create( $filepath )[ 'ds' ];
		$return = [ 'info'=>$filepath.$fileid ];
		$tmp = [ 'jpeg' , 'png' , 'gif' , 'bmp' , 'mp4' , 'xl' , 'xlsx' , 'csv' , 'text' , 'sdc' , 'odc' , 'doc' , 'docx' , 'odt' , 'rtf' , 'rtx' , 'pdf' ];
		
		foreach( $tmp as $k=>$v ){
			$df = $filepath.$fileid.'.'.$v;
			$dfo = $filepath.'original_'.$fileid.'.'.$v;
			$dfm = $filepath.'min_'.$fileid.'.'.$v;
			
			if( is_file( $df ) ) unlink( $df ) ? $return[ 'ok' ][ ] = $df : $return[ 'error' ][ ] = $df;
			if( is_file( $dfo ) ) unlink( $dfo ) ? $return[ 'ok' ][ ] = $dfo : $return[ 'error' ][ ] = $dfo;
			if( is_file( $dfm ) ) unlink( $dfo ) ? $return[ 'ok' ][ ] = $dfm : $return[ 'error' ][ ] = $dfm;
		}
		
		return $return;
	}
	
	// ..
	public function allias_rename( $str ){
		// $rus = [ 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я' ];
		// $translit = [ 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'i', '', '', 'yu', 'ya' ];
		
		$trans = [
			'а'=>'a' ,
			'б'=>'b' ,
			'в'=>'v' ,
			'г'=>'g' ,
			'д'=>'d' ,
			'е'=>'e' ,
			'ё'=>'e' ,
			'ж'=>'zg' ,
			'з'=>'z' ,
			'и'=>'i' ,
			'й'=>'i' ,
			'к'=>'k' ,
			'л'=>'l' ,
			'м'=>'m' ,
			'н'=>'n' ,
			'о'=>'o' ,
			'п'=>'p' ,
			'р'=>'r' ,
			'с'=>'s' ,
			'т'=>'t' ,
			'у'=>'u' ,
			'ф'=>'f' ,
			'х'=>'ch' ,
			'ц'=>'ts' ,
			'ч'=>'ch' ,
			'ш'=>'sh' ,
			'щ'=>'sch' ,
			'ъ'=>'' ,
			'ы'=>'i' ,
			'ь'=>'' ,
			'э'=>'e' ,
			'ю'=>'yu' ,
			'я'=>'ya' ,
			'/'=>'-' ,
		];
		
		$newstr = '';
		$str = mb_strtolower( $str );
		for( $q = 0 ; $q < mb_strlen( $str ) ; $q++ ){
			$letter = mb_substr($str , $q , 1 );
			
			if( isset( $trans[ $letter ] ) ) $newstr .= $trans[ $letter ];
			else if( $letter == ' ' ) $newstr .= '-';
			else $newstr .= $letter;
		}
		
		return $newstr;
	}
	
	
	// .. image
	public function image_resize( $path , $nPath , $nFilename , $nWidth=false , $nType=false , $quality='100' , $nWidthImportant=false , $widthToHeight=false , $alpha=false , $rotate=[] ){
		// .. path -
		// .. nPath
		$class__index = $this->class__index;
		
		// .. создаём исходное изображение на основе исходного файла и опеределяем его размеры
		
		// .. Определяем исходный формат по MIME-информации, предоставленной функцией getimagesize, и выбираем соответствующую формату imagecreatefrom-функцию.
		if( is_file( $path ) ){
			$size = getimagesize( $path );
			$format = mb_strtolower( substr( $size[ 'mime' ] , strpos( $size[ 'mime' ] , '/' ) +1 ) );
			
			// .. если не указан новый тип файла
			if( $nType == false ) $nType = $format;
			
			
			
			$bOk = true;
			switch( $format ){
				case 'jpeg': case 'jpg': case 'jpe': $src = imagecreatefromjpeg( $path ); break;
				case 'png': $src = imagecreatefrompng( $path ); break;
				case 'gif': $src = imagecreatefromgif( $path ); break;
				default: $bOk = false; break;
			}
			if( is_array( $rotate ) && ! empty( $rotate ) ){
				$keys = array_keys( $rotate );
				$action = $keys[ 0 ];
				$degrees = $rotate[ $action ];
				
				switch( $action ){
					case 'maxwidth' :
						# switch( $format ){
						# 	case 'jpeg': case 'jpg': case 'jpe': $src = imagecreatefromjpeg( $path ); break;
						# 	case 'png': $src = imagecreatefrompng( $path ); break;
						# 	case 'gif': $src = imagecreatefromgif( $path ); break;
						# 	default: $bOk = false; break;
						# }
						
						$w_src = imagesx( $src ); 
						$h_src = imagesy( $src );
						if( $w_src < $h_src ){
							$src = imagerotate( $src , -$degrees , 0 );
							
							# switch( mb_strtolower( $format ) ){
							# 	case 'jpg' :
							# 	case 'jpeg' : $bOk = imagejpeg( $dest , $nPath.$nFilename.$nType , $quality ); break;
							# 	case 'gif' : $bOk = imagegif( $dest ,  $nPath.$nFilename.$nType , $quality ); break;
							# 	case 'png' : $bOk = imagepng( $dest ,  $nPath.$nFilename.$nType , ( ( $quality / 100 ) - 1 ) ); break;
							# 	default : $bOk = false; break;
							# }
						}
						
						break;
					default :
						break;
				}
			}
			
			
			
			if( $bOk == true ){
				if( $widthToHeight == true ){
					// Ширина и высота исходной картинки
					$w_src = imagesx( $src ); 
					$h_src = imagesy( $src );
					
					if( $w_src < $h_src ){
						$h_src = $w_src;
					}
					else if( $w_src > $h_src ){
						$w_src = $h_src;
					}
					
					if( $nWidth != false && $nWidthImportant == true ){
						if( $w_src > $nWidth ){
							$ratio = $w_src/$nWidth; 
							$w_dest = round( $w_src/$ratio ); 
							$h_dest = round( $h_src/$ratio ); 
						}
						else if( $w_src < $nWidth ){
							$ratio = $nWidth/$w_src;
							$w_dest = round( $w_src*$ratio );
							$h_dest = round( $h_src*$ratio );
						}
						else {
							$w_dest = $w_src;
							$h_dest = $h_src;
						}
					}
					else{
						$w_dest = $w_src;
						$h_dest = $h_src;
					}
					$pH = 0;
					$pW = 0;
				}
				else {
					// Ширина и высота исходной картинки
					$w_src = imagesx( $src );
					$h_src = imagesy( $src );
					
					// Выводи изображение непосредственно на экран
					// header("Content-type: image/jpeg");
					
					// вычисление пропорций
					// Ширина и высота будущей картинки
					if( $nWidth != false && $nWidthImportant == true ){
						if( $w_src > $nWidth ){
							$ratio = $w_src/$nWidth; 
							$w_dest = round( $w_src/$ratio ); 
							$h_dest = round( $h_src/$ratio ); 
						}
						else if( $w_src < $nWidth ){
							$ratio = $nWidth/$w_src;
							$w_dest = round( $w_src*$ratio );
							$h_dest = round( $h_src*$ratio );
						}
						else {
							$w_dest = $w_src;
							$h_dest = $h_src;
						}
					}
					else {
						$w_dest = $w_src;
						$h_dest = $h_src;
					}
					$pH = 0;
					$pW = 0;
				}
				
				// создаём пустую картинку, важно именно truecolor!, иначе будем иметь 8-битный результат 
				$widthToHeight ? $dest = imagecreatetruecolor( $w_dest , $w_dest ) : $dest = imagecreatetruecolor( $w_dest , $h_dest );
				
				// .. прозрачная картинка
				if( $alpha == true ){
					$transparent = imagecolorallocatealpha( $dest , 0 , 0 , 0 , 127 );
					imagefill( $dest , 0 , 0 , $transparent );
					imagesavealpha( $dest , true ); // save alphablending setting (important);
				}
				
				$spec_redraw = '';
				$spec_redraw == 'simple'
					? imagecopyresized( $dest , $src, $pH , $pW ,  0 , 0 , $w_dest , $h_dest , $w_src , $h_src )
					: imagecopyresampled( $dest , $src , $pW , $pH ,  0 , 0 , $w_dest , $h_dest , $w_src , $h_src );
				
				$nType = '.'.$nType;
				
				switch( mb_strtolower( $format ) ){
					case 'gif' : $bOk = imagegif( $dest ,  $nPath.$nFilename.$nType ); break;
					case 'jpg' :
					case 'jpeg' : $bOk = imagejpeg( $dest , $nPath.$nFilename.$nType , $quality ); break;
					case 'png' : $bOk = imagepng( $dest ,  $nPath.$nFilename.$nType , ( $quality / 100 ) - 1 ); break;
					default : $bOk = false; break;
				}
				
				// Освобождаем память
				imagedestroy( $src );
				imagedestroy( $dest );
				
				// Выводи изображение непосредственно на экран
				// header("Content-type: image/png");
				// Изображение создано
				
				$returnFilepath = $nPath;
				$returnFilename = $nFilename.$nType;
			}
			else {
				$error = 'Не изображение.';
			}
		}
		else {
			$error = 'Не указан файл для изменения.';
		}
		
		
		// .. Отладочная информация ..
		$data = '<div>
			<div>$path: '.$path.'</div>
			<div>$nPath: '.$nPath.'</div>
			<div>$nWidth: '.$nWidth.'</div>
			<div>$quality: '.$quality.'</div>
			<div>$nWidthImportant: '.$nWidthImportant.'</div>
			<div>$widthToHeight: '.$widthToHeight.'</div>
		</div>' ;
		// $_SESSION[ 'messages' ][ ] = [ 'status'=>'console' , 'data'=>$data ];
		
		return [ 'error'=>$error , 'filepath'=>$returnFilepath , 'filename'=>$returnFilename ];
	}
	
	
	
	// .. image
	public function image_resize22( $path , $nPath , $nWidth , $nType='' , $quality='100' , $nWidthImportant=false , $widthToHeight=false ){
		$class__index = $this->class__index;
		$src = [ ];
		
		// .. создаём исходное изображение на основе исходного файла и опеределяем его размеры
		
		// .. Определяем исходный формат по MIME-информации, предоставленной функцией getimagesize, и выбираем соответствующую формату imagecreatefrom-функцию.
		if( is_file( $path ) ){
			$size = getimagesize( $path );
			$format = strtolower( substr( $size[ 'mime' ] , strpos( $size[ 'mime' ] , '/' ) +1 ) );
			
			$format_original = $format;
			$bOk = true;
			switch( mb_strtolower( $format ) ){
				case 'jpeg': case 'jpg': case 'jpe': $src[ 'f' ] = imagecreatefromjpeg( $path ); break;		#.. JPEG
				case 'png': $src[ 'f' ] = imagecreatefrompng( $path ); break;												# .. PNG
				case 'gif': $src[ 'f' ] = imagecreatefromgif( $path ); break;																		# .. GIF
				default: $bOk = false; break;																																# .. else
			}
			
			if( $bOk == true ){
				if( $widthToHeight ){
					// Ширина и высота исходной картинки
					// $src = [ 'f'=>$src[ 'f' ] , 'w'=>imagesx( $src[ 'f' ] ) , 'h'=>imagesy( $src[ 'f' ] ) ];
					$src[ 'w' ] = imagesx( $src[ 'f' ] );
					$src[ 'h' ] = imagesy( $src[ 'f' ] );
					
					if( $src[ 'w' ] < $src[ 'h' ] ) $src[ 'h' ] = $src[ 'w' ];
					else if( $src[ 'w' ] > $src[ 'h' ] ) $src[ 'w' ] = $src[ 'h' ];
					
					if( $nWidth != false && $nWidthImportant == true ){
						if( $src[ 'w' ] > $nWidth ){
							$ratio = $src[ 'w' ]/$nWidth;
							$dest = [ 'w'=>round( $src[ 'w' ]/$ratio ) , 'h'=>round( $src[ 'h' ]/$ratio ) ];
						}
						else if( $src[ 'w' ] < $nWidth ){
							$ratio = $nWidth/$src[ 'w' ];
							$dest = [ 'w'=>round( $src[ 'w' ]*$ratio ) , 'h'=>round( $src[ 'h' ]*$ratio ) ];
						}
						else $dest = [ 'w'=>round( $src[ 'w' ] ) , 'h'=>round( $src[ 'h' ] ) ];
					}
					else $dest = [ 'w'=>round( $src[ 'w' ] ) , 'h'=>round( $src[ 'h' ] ) ];
					
					$pH = 0;
					$pW = 0;
				}
				else {
					// Ширина и высота исходной картинки
					$src[ 'w' ] = imagesx( $src[ 'f' ] );
					$src[ 'h' ] = imagesy( $src[ 'f' ] );
					
					// Выводи изображение непосредственно на экран
					// header("Content-type: image/jpeg");
					
					// вычисление пропорций
					// Ширина и высота будущей картинки
					if( $nWidth != false && $nWidthImportant == true ){
						if( $src[ 'w' ] > $nWidth ){
							$ratio = $src[ 'w' ]/$nWidth; 
							$dest = [ 'w'=>round( $src[ 'w' ]/$ratio ) , 'h'=>round( $src[ 'h' ]/$ratio ) ];
						}
						else if( $src[ 'w' ] < $nWidth ){
							$ratio = $nWidth/$src[ 'w' ];
							$dest = [ 'w'=>round( $src[ 'w' ]*$ratio ) , 'h'=>round( $src[ 'h' ]*$ratio ) ];
						}
						else $dest = [ 'w'=>round( $src[ 'w' ] ) , 'h'=>round( $src[ 'h' ] ) ];
					}
					else $dest = [ 'w'=>round( $src[ 'w' ] ) , 'h'=>round( $src[ 'h' ] ) ];
					
					$pH = 0;
					$pW = 0;
				}
				
				// создаём пустую картинку, важно именно truecolor!, иначе будем иметь 8-битный результат 
				$widthToHeight
					? $dest[ 'f' ] = imagecreatetruecolor( $dest[ 'w' ] , $dest[ 'w' ] )
					: $dest = imagecreatetruecolor( $dest[ 'w' ] , $dest[ 'h' ] );
				
				define( '__IMG_SPEC_REDRAW' , 'simple' );
				__IMG_SPEC_REDRAW == 'simple'
					? imagecopyresized( $dest[ 'f' ] , $src[ 'f' ], $pH , $pW ,  0 , 0 , $dest[ 'w' ] , $dest[ 'h' ] , $src[ 'w' ] , $src[ 'h' ] )
					: imagecopyresampled( $dest[ 'f' ] , $src[ 'f' ] , $pW , $pH ,  0 , 0 , $dest[ 'w' ] , $dest[ 'h' ] , $src[ 'w' ] , $src[ 'h' ] );
				
				// Расширение изображения
				if( $nType == false )	$nType = '';
				else								$nType = '.' . $nType;
				
				$bOk = false;
				switch( mb_strtolower( $format ) ){
					case 'gif' : $bOk = imagegif( $dest[ 'f' ] ,  $nPath.$nType ); break;
					case 'jpg' :
					case 'jpeg' : $bOk = imagejpeg( $dest[ 'f' ] ,  $nPath.$nType , $quality ); break;
					case 'png' : $bOk = imagepng( $dest[ 'f' ] ,  $nPath.$nType , ( $quality / 100 ) - 1 ); break;
					default : $bOk = false; break;
				}
				
				// Освобождаем память
				imagedestroy( $src[ 'f' ] );
				imagedestroy( $dest[ 'f' ] );
				
				// Выводи изображение непосредственно на экран
				// header("Content-type: image/png");
				// Изображение создано
				$return = $nPath.$nType;
				
				$returnFilename = explode( '\\' , $return );
				$returnFilename = $returnFilename[ count( $returnFilename )-1 ];
			}
			else {
				$error = 'Не изображение.';
			}
		}
		else {
			$error = 'Не указан файл для изменения.';
		}
		
		return [ 'error'=>$error , 'filepath'=>$return , 'filename'=>$returnFilename ];
	}
		*/
}

?>