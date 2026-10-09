/*
	.. javascript
	.. Formblock
	..
*/

// .. gl vars
var gl_td_id = '';

jsFormblockSelectChange = function( obj , action ){
	var t = $( obj );
	var sel = t.parents( 'div' ).next( 'select' );
	var opts = sel.find( 'option' );
	switch( action ){
		case 'select' :
			opts.prop( 'selected' , 'selected' );
			sel.trigger( 'change' );
			break;
		case 'unselect' :
			opts.prop( 'selected' , false );
			sel.trigger( 'change' );
			break;
		case 'changeselect' :
			// opts_sel = sel.find( 'option:is(selected)' );
			// opts_unsel = sel.find( 'option:not(selected)' );
			opts_sel = sel.find( 'option:selected' );
			opts_unsel = sel.find( ':not(option:selected)' );
			opts_sel.prop( 'selected' , false );
			opts_unsel.prop( 'selected' , true );
			break;
		default :
			break;
	}
	
	sel.focus( );
}
// .. functions
jsFormblockListCheck = function( obj , checkname , mainname ){
	obj = $(obj );
	obj_name = obj.prop( 'name' );
	
	form = obj.parents( 'form' );
	checkboxes = form.find( 'input[name*="' + checkname + '[]"]' );
	all_checked = true;
	checkboxes.each( function( i , elem ){
		elem_checked = $(elem).prop( 'checked' );
		if( elem_checked == false ) all_checked = false;
	} );
	
	// console.log( 'all_checked: ' + all_checked );
	
	if( obj_name == mainname ){
		is_checked = obj.is( ':checked' );
		checkboxes.prop( 'checked' , is_checked ); 
	}
	else {
		// console.log( 'main checked : ' + all_checked );
		checkbox_main = form.find( 'input[name*="' + mainname + '"]' );
		checkbox_main.prop( 'checked' , all_checked );
	}
}


// .. Устанавливаем фокус на элементе
var popup_count = 1;
var popup_interval = [ ];
jsFormblockInputselect = function( obj , url ){
	var t = $( obj );
	console.log( 'Select input!' );
	eltextselect = t.parents( '.el-textselect' );
	count = eltextselect.data( 'count' );
	if( ! count ) eltextselect.data( { 'count' : popup_count++ } );
	input = t.find( 'input[type=text]' );
	popup = t.parents( '.el-textselect' ).find( '.popup' );
	
	// .. удаляем таймаут
	clearTimeout( popup_interval[ count ] );
	
	if( input.length == 0 ){
		// t.append( inp );
		// input = t.find( 'input' );
		// input.focus( );
		// popup.show( );
	}
	else {
		input.focus( );
		// popup.show( );
	}
	return false;
}

jsFormblockInputselectAction = function( obj , action ){
	var t = $( obj );
	
	console.log( 'Select Action!' );
	
	eltextselect = t.parents( '.el-textselect' );
	url = eltextselect.data( 'url' );
	if( ! url ){
		// alert( 'Error!' );
	}
	count = eltextselect.data( 'count' );
	if( ! count ) eltextselect.data( { 'count' : popup_count++ } );
	input = t.find( 'input[type=text]' );
	popup = eltextselect.find( '.popup' );
	
	switch( action ){
		case 'keyup' :
			// t.css( { 'width' : 'auto' } ).attr( { 'size' : t.val().length } );
			jsFormblockInputselectAction_load( obj , url );
			break;
		case 'focus' :
			popup.show( );
			// jsFormblockInputselectAction_load( obj , url );
			break;
		case 'blur' :
			popup_interval[ count ] = setTimeout( jsFormblockInputselectAction_hidepopup , 100 , popup );
			// popup.hide( );
			break;
		default :
			break;
	}
	
	return false;
}

// .. устанавливаем кнопку
jsFormblockInputselect_setval = function( obj ){
	var data = Array( );
	var t = $( obj );
	var id = t.data( 'id' );
	var text = t.data( 'val' );
	
	// .. textselect
	eltextselect = t.parents( '.el-textselect' );
	
	data[ 'toid' ] = id;
	data[ 'tourl' ] = eltextselect.data( 'tourl' );
	data[ 'tolayerid' ] = eltextselect.data( 'tolayerid' );
	
	if( data[ 'tourl' ] && data[ 'tolayerid' ] ){
		jsFormblockInputselect_setval_other( eltextselect , data );
		// $( '#' + data[ 'tolayerid' ] ).append( '<tr><td>NEW DATA</td><td>22</td></tr>' );
	}
	else {
		div = eltextselect.find( '.div' );
		popupindiv = eltextselect.find( '.popup .indiv' );
		name = eltextselect.data( 'name' );
		inputtextselect = eltextselect.find( 'input[type=text]' );
		inputtextselect.before( '<div class="button" onClick="jsFormblockInputselectAction_remove( this );"><input type="hidden" name="' + name + '" value="' + id + '" />' + text + '</div>' );
		// jsFormblockInputselectAction( t , 'keyup' , );
		t.remove( );
		console.log( '"' + popupindiv.html( ) + '"' );
		if( popupindiv.html( ) == '' ) popupindiv.append( '<div class="empty">Нет результатов поиска</div>' );
	}
	return false;
}
// .. загружаем данные и добавляем их в LayerId
jsFormblockInputselect_setval_other = function( eltextselect , data ){
	var d_start = new Date( ).getTime( );
	
	var layerErrorMessages = $( '#tB_messages' );
	var dataJSON = '';
	var error = '';
	var result = '';
	var popup = false;
	var countError = 0;
	
	// var t = $( obj );
	// eltextselect = t.parents( '.el-textselect' );
	// popup = eltextselect.find( '.popup' );
	// popup_result = popup.find( '.indiv' );
	// buttons = eltextselect.find( 'input[type=hidden]' );
	
	
	toId = data[ 'toid' ];
	toUrl = data[ 'tourl' ];
	toLayerId = data[ 'tolayerid' ];
	console.log( toUrl + '?id=' + toId );
	
	data_loaded = '';
	url = toUrl;
	
	jQuery.ajax( {
		url : url ,
		async : true ,
		type : 'POST' ,
		data : data_loaded ,
		beforeSend : function( ){
			console.log( 'fastedit prepare :: START' );
			// .. Ставим флаг, что в поле  уже происходит загрузка данных
			// t.data( 'loading' , true );
		} ,
		complete : function( msg , status ){
			// .. парсим данные из JSON в массив
			dataJSON = JSON.parse( dataJSON );
			// .. Алгоритм
			// var col = t.parents( '.blockform_cols' );
			
			error = dataJSON[ 'error' ];
			returnData = dataJSON[ 'returnData' ];
			if( error ){
				for( $q = 0 ; $q < error.length ; $q++ ){
					layerErrorMessages.append( error[ $q ] );
					countError++;
				}
				mesagges_show( );
				t.data( 'loading' , false );
			}
			
			if( returnData ){
				$( '#' + toLayerId ).append( returnData );
				/*
				col.html( '' ).append( dataJSON[ 'result' ] );
				var inp = col.find( 'input , select , textarea' );
				// inp.on( 'focus' );
				inp.focus( );
				*/
			}
			
			
			$( document ).ready(function() {
				// tinymce_start( );
			});
			console.log( 'fastedit prepare :: END' );
			
			// ..
			d_end = new Date( ).getTime( );
			d_ = ( d_end - d_start ) / 1000;
			d_all_ += d_;
			d_.toFixed ( 2 );
			d_all_.toFixed ( 2 );
			
			$( '#err_updated' ).html( ' Подгрузка доп контента за ' + d_ + ' сек. (общее время подгрузки: ' + d_all_ + ')' );
		} ,
		success : function( dataLoaded ){
			// ..
			if( ! dataLoaded ) return false;
			else dataJSON = dataLoaded;
		} ,
		error : function( jqXHR, textStatus, errorThrown ){
			console.log( 'Prepare : ERROR' );
		}
	});
}

// .. удаляем кнопку (объект кнопка)
jsFormblockInputselectAction_remove = function( obj ){
	var t = $( obj );
	val = t.text( ); // .. значение
	eltextselect = t.parents( '.el-textselect' );
	input = eltextselect.find( 'input[type=text]' );
	input.val( val ).focus( );
	// popup = eltextselect.find( '.popup' );
	// popup.show( );
	// jsFormblockInputselectAction( input , 'keyup' );
	
	t.remove( );
	// buttons = textselect.find( '.div' ).find( 'input[type=button]' );
	// if( buttons.length == 0 ){
	// 	def = '<div class="empty">Выберите</div>';
	// 	def += textselect.find( '.div' ).html( );
	// 	textselect.find( '.div' ).html( def );
	// 	input = textselect.find( '.textselect' );
	// 	input.val( t.val( ) );
	// }
	
	return false;
}

jsFormblockInputselectAction_load = function( obj , url ){
	var d_start = new Date( ).getTime( );
	
	var layerErrorMessages = $( '#tB_messages' );
	var dataJSON = '';
	var error = '';
	var result = '';
	var popup = false;
	var countError = 0;
	
	var t = $( obj );
	eltextselect = t.parents( '.el-textselect' );
	popup = eltextselect.find( '.popup' );
	popup_result = popup.find( '.indiv' );
	buttons = eltextselect.find( 'input[type=hidden]' );
	
	querydata = 'searchtext=' + t.val( );
	buttons.each( function( i , elem ){
		querydata += '&' + elem[ 'name' ] + '=' + elem[ 'value' ];
	} );
	
	// console.log( 'url: ' + url + ' | querydata: ' + querydata  );
	
	jQuery.ajax( {
		url : url ,
		async : true ,
		type : 'POST' ,
		data : querydata ,
		beforeSend : function( ){
			// .. Ставим флаг, что в поле  уже происходит загрузка данных
			t.data( 'loading' , true );
		} ,
		complete : function( msg , status ){
			// .. парсим данные из JSON в массив
			if( ! dataJSON ) return false;
			
			dataJSON = JSON.parse( dataJSON );
			// .. Алгоритм
			
		
			error = dataJSON[ 'error' ];
			returnData = dataJSON[ 'returnData' ];
			if( error ){
				for( $q = 0 ; $q < error.length ; $q++ ){
					layerErrorMessages.append( error[ $q ] );
					countError++;
				}
				mesagges_show( );
				t.data( 'loading' , false );
			}
			
			if( returnData ){
				popup_result.html( returnData );
			}
			
			
			$( document ).ready(function() {
				// tinymce_start( );
			});
			// console.log( 'fastedit prepare :: END' );
			
			// ..
			d_end = new Date( ).getTime( );
			d_ = ( d_end - d_start ) / 1000;
			d_all_ += d_;
			d_.toFixed ( 2 );
			d_all_.toFixed ( 2 );
			
			$( '#err_updated' ).html( ' Подгрузка доп контента за ' + d_ + ' сек. (общее время подгрузки: ' + d_all_ + ')' );
		} ,
		success : function( dataLoaded ){
			// console.log( 'dataLoaded: ' + dataLoaded );
			// ..
			if( ! dataLoaded ) return false;
			else dataJSON = dataLoaded;
		} ,
		error : function( jqXHR, textStatus, errorThrown ){
			console.log( 'Prepare : ERROR' );
		}
	});
}

jsFormblockInputselectAction_hidepopup = function( obj ){
	var t = $(obj ).parents( '.el-textselect' );
	input = t.find( 'input.textselect' );
	// input.remove( );
	popup = t.find( '.popup' );
	popup.hide( );
}


// ..
// ..
jsFormblockFastlist = function( obj ){
	t = $( obj );
	// .. vars
	var data = {};
	var dataJSON = '';
	var returnError = '';
	var returnData = '';
	var countMessages = 0;
	
	var data_fastedit = t.data( 'prepare' );
	if( data_fastedit ){
		jsFormblock_layers( t , info , layers={} );
		jsFormblock_form( t , info , form={} , data={} , data_fastedit );
		alert( data.data_prepare );
		loaddata = jsFormblock_formdata_loaddata( data.data_prepare );
		jsFormblock_formdata( t , info , form , data , loaddata );
	}
	else
		return false;
	
	
	// .. 
	jQuery.ajax({
		async : form.async ,
		url : form.action ,
		// dataType : form.dataType ,
		type : form.method ,
		data : form.data ,
		beforeSend : function( ){
			alert( this.data );
		} ,
		complete : function( msg , status ){
			if( ! dataJSON ) return false;
			
			returnError = dataJSON.returnError;
			returnData = dataJSON.returnData;
			
			mesagges_show( returnError , true );
			
			/*
			$( '#' + data.resultToLayer ).html( returnData.html );
			layers.tbload.removeClass( 'active' );
			layers.tbload_result.html( '' );
			
			if( returnData.is_updated == true ){
				window[ data.functionUpdate ].update( data.resultToLayer );
				
				if( typeof( data.functionOtherUpdate ) === 'object' ){ 
					$.each( data.functionOtherUpdate , function( k , v ){
						// alert( k + ' - ' + v );
						if( typeof( window[ v ] ) != 'undefined' ){
							window[ v ].update( false );
						}
					});
				}
			}
			*/
			
			// .. 
			jsFormblock_updatePageTime( info , layers , data );
			// ..
		} ,
		success : function( dataLoaded ){
			console.log( dataLoaded );
			if( dataLoaded == false ) return false ;
			info.scriptSize = dataLoaded.length;
			
			dataJSON = JSON.parse( dataLoaded );
		} ,
		error : function( jqXHR , textStatus , errorThrown ){
			console.log( 'FasteditSave : Error synchronisation data' );
		}
	} );
	return false;
}


// ..
// ..
jsFormblockFastpages = function( obj ){
	t = $( obj );
	// .. vars
	var data = {};
	var dataJSON = '';
	var returnError = '';
	var returnData = '';
	var countMessages = 0;
	
	var data_fastedit = t.data( 'fastpages' );
	if( data_fastedit ){
		jsFormblock_layers( t , info , layers={} );
		jsFormblock_form( t , info , form={} , data={} , data_fastedit );
		loaddata = jsFormblock_formdata_loaddata( data.preparedata );
		jsFormblock_formdata( t , info , form , data , loaddata );
	}
	else
		return false;
	
	// .. 
	jQuery.ajax({
		async : form.async ,
		url : form.action ,
		// dataType : form.dataType ,
		type : form.method ,
		data : form.data ,
		beforeSend : function( ){
			alert( this.data );
		} ,
		complete : function( msg , status ){
			if( ! dataJSON ) return false;
			
			returnError = dataJSON.returnError;
			returnData = dataJSON.returnData;
			
			mesagges_show( returnError , true );
			
			/*
			$( '#' + data.resultToLayer ).html( returnData.html );
			layers.tbload.removeClass( 'active' );
			layers.tbload_result.html( '' );
			
			if( returnData.is_updated == true ){
				window[ data.functionUpdate ].update( data.resultToLayer );
				
				if( typeof( data.functionOtherUpdate ) === 'object' ){ 
					$.each( data.functionOtherUpdate , function( k , v ){
						// alert( k + ' - ' + v );
						if( typeof( window[ v ] ) != 'undefined' ){
							window[ v ].update( false );
						}
					});
				}
			}
			*/
			// .. 
			jsFormblock_updatePageTime( info , layers , data );
			// ..
		} ,
		success : function( dataLoaded ){
			if( dataLoaded == false ) return false ;
			info.scriptSize = dataLoaded.length;
			
			dataJSON = JSON.parse( dataLoaded );
		} ,
		error : function( jqXHR , textStatus , errorThrown ){
			console.log( 'FasteditSave : Error synchronisation data' );
		}
	} );
	return false;
}


jsFormblockFastadd = function( obj ){
	var t = $( obj );
	// .. vars
	var dataJSON;
	var returnError;
	var returnData;
	// .. layers
	
	
	var data_prepare = t.data( 'fastadd' );
	jsFormblock_layers( t , info , layers={} );
	jsFormblock_form( t , info , form={} , data={} , data_prepare );
	jsFormblock_formdata( t , info , form , data );
	
	// .. ajax запрос
	jQuery.ajax({
		async : form.async ,
		url : form.action ,
		// dataType : form.dataType ,
		type : form.method ,
		data : form.data ,
		beforeSend : function( ){
			// form_bsubmit.prop( 'disabled' , true );
		} ,
		complete : function( msg , status ){
			// .. return .. type
			returnError = dataJSON.returnError;
			returnData = dataJSON.returnData;
			
			// .. table tbody
			var tbody = $( '#' + data_prepare.preparedata.resultToLayerId );
			var empty = tbody.find( '.blockform_rows.empty' );
			if( empty.length > 0 ){
				$( '#' + data_prepare.preparedata.resultToLayerId ).html( '' );
			}
			
			$( '#' + data_prepare.preparedata.resultToLayerId ).append( returnData.html );
			
			functionUpdate = data.functionUpdate ? data.functionUpdate : 'update2_';
			// alert( window[ functionUpdate ][ 'data' ][ 'fields' ][ 'status' ] );
			window[ functionUpdate ][ 'data' ][ 'preparedata' ][ 'data' ].push( returnData.id );
			
			// alert( data.functionUpdate + ' | ' + functionUpdate + ' | ' + window[ functionUpdate ][ 'data' ][ 'data' ] );
			
			if( returnData ){
				/*
				// .. обновляем строки в списке
				var updaterow = returnData[ 'updaterow' ] ? returnData[ 'updaterow' ] : false ;
				if( updaterow ){
					var updaterow_id = updaterow[ 'id' ];
					var updaterow_data = updaterow[ 'data' ];
				
					console.log( 'id: ' + updaterow_id );
					console.log( 'data: ' + updaterow_data );
					
					$( '#' + updaterow_id ).html( updaterow_data );
				}
				*/
				
				// .. включаем кнопку
				// form_bsubmit.prop( 'disabled' , false );
				// $( '.tb_preload' ).hide( );
				// .. Сбросить форму
				// if( returnData[ 'clear' ] == true ) form.trigger( 'reset' );
				// if( returnData[ 'update2_' ] ) update2_ = returnData[ 'update2_' ];
				// if( returnData[ 'reload' ] == true ) location.reload();
				
			}
			// .. показываем ошибку
			mesagges_show( returnError , true );
		} ,
		success : function( dataLoaded ){
			// console.log( 'dataLoaded: ' + dataLoaded );
			if( dataLoaded == false ) return false;
			
			dataJSON = JSON.parse( dataLoaded );
		} ,
		error : function ( jqXHR, textStatus, errorThrown ) {
			console.log( 'jsShgroupsAdd: Error synchronisation data' );
		}
	});
	return false;
}




jsFormblockFasteditPrepare = function( obj ){
	// .. объект
	var t = $( obj );
	// .. переменные
	var data = {};
	var dataJSON = '';
	var error = '';
	var result = '';
	var popup = false;
	var countError = 0;
	
	var data_fastedit = t.data( 'fastedit' );
	
	if( data_fastedit ){
		jsFormblock_layers( t , info , layers={} );
		jsFormblock_form( t , info , form={} , data={} , data_fastedit );
		jsFormblock_formdata( t , info , form , data );
	}
	else {
		return false;
	}
	
	
	if( t.data( 'loading' ) == true ) return false;
	
	jQuery.ajax( {
		async : form.async ,
		url : form.action ,
		// dataType : form.dataType ,
		type : form.method ,
		data : form.data ,
		beforeSend : function( ){
			// console.log( 'fastedit prepare :: START' );
			// .. Если расширенное редактирование - обновляем все элементы на странице
			if( data.preparedata.editextended == true ){
				// 	.log( 'Обновляем данные. resultToLayer: ' + resultToLayer );
				update2_( data.preparedata.resultToLayer );
			}
			// .. Ставим флаг, что в поле  уже происходит загрузка данных
			t.data( 'loading' , true );
		} ,
		complete : function( msg , status ){
			
			// .. Алгоритм
			if( data.preparedata.editpopup == true ){
				jsFormblockFasteditPrepare_resultPopup( dataJSON );
			}
			else {
				var col = t.parents( '.blockform_cols' );
				
				col = $( '#' + data.preparedata.resultToLayer );
				
				// .. return .. type
				var returnError = dataJSON.returnError;
				var returnData = dataJSON.returnData;
				
				t.data( 'loading' , false );
				if( returnData ){
					col.html( '' ).append( returnData.html );
					var inp = col.find( 'input , select , textarea' );
					// inp.on( 'focus' );
					inp.focus( );
				}
			}
			
			// .. показываем ошибку
			mesagges_show( returnError , true );
			
			$( document ).ready(function() {
				// tinymce_start( );
			});
			// console.log( 'fastedit prepare :: END' );
			
			// .. 
			jsFormblock_updatePageTime( info , layers , data );
		} ,
		success : function( dataLoaded ){
			if( dataLoaded == false ) return false ;
			info.scriptSize = dataLoaded.length;
			// .. 
			dataJSON = JSON.parse( dataLoaded );
		} ,
		error : function( jqXHR , textStatus , errorThrown ){
			console.log( 'Prepare : ERROR' );
		}
	});
}

jsFormblockFasteditPrepare_resultPopup = function( dataJSON ){
	if( ! dataJSON ) return false;
	
	var layerErrorMessages = $( '#tB_messages' );
	var layer_load = $( '#tB_load' );
	var layer_load_result = layer_load.find( '.result' ).eq( 0 );
	var error = dataJSON[ 'error' ];
	var result = dataJSON[ 'result' ];
	var countError = 0;
	
	if( error ){
		for( $q = 0 ; $q < error.length ; $q++ ){
			layerErrorMessages.append( error[ $q ] );
			countError++;
		}
		mesagges_show( );
	}
	
	if( result ){
		layer_load_result.css({ 'opacity' : '0.0' }).append( result );
		layer_load.addClass( 'active' );
		layer_load_result.animate( { 'opacity' : '1.0' } , 500 , function( ){
		});
		
		var col = layer_load_result;
		var inp = col.find( 'input , select , textarea' ).eq( 0 );
		inp.focus( );
	}
	
	return true;
}

/*
	.. Save ..
*/

jsFormblockFasteditSave = function( obj ){
	t = $( obj );
	// .. vars
	var dataJSON = '';
	var returnError = '';
	var returnData = '';
	var countMessages = 0;
	
	var data_fastedit = t.data( 'fastedit' );
	if( data_fastedit ){
		jsFormblock_layers( t , info , layers={} );
		jsFormblock_form( t , info , form={} , data={} , data_fastedit );
	}
	else
		return false;
	
	// .. простое редактирование
	if( data.editextended == false ){
		// select = $( '#' + resultToLayer ).find( 'select , input , textarea' ).eq( 0 );
		select = t.parents( '.div-el' ).eq( 0 ).find( 'select , input , textarea' ).eq( 0 );
		loaddata = select.prop( 'name' ) + '=' + select.val( );
	}
	else {
		var loaddata = form[ 'form' ].serialize();
	}
	jsFormblock_formdata( t , info , form , data , loaddata );
	// .. 
	jQuery.ajax({
		async : form.async ,
		url : form.action ,
		// dataType : form.dataType ,
		type : form.method ,
		data : form.data ,
		beforeSend : function( ){
		} ,
		complete : function( msg , status ){
			if( ! dataJSON ) return false;
			
			returnError = dataJSON.returnError;
			returnData = dataJSON.returnData;
			
			mesagges_show( returnError , true );
			
			$( '#' + data.preparedata.resultToLayer ).html( returnData.html );
			layers.tbload.removeClass( 'active' );
			layers.tbload_result.html( '' );
			
			if( returnData.is_updated == true ){
				window[ data.functionUpdate ].update( data.preparedata.resultToLayer );
				
				if( typeof( data.functionOtherUpdate ) === 'object' ){ 
					$.each( data.functionOtherUpdate , function( k , v ){
						// alert( k + ' - ' + v );
						if( typeof( window[ v ] ) != 'undefined' ){
							window[ v ].update( false );
						}
					});
				}
			}
			
			// .. 
			jsFormblock_updatePageTime( info , layers , data );
			// ..
		} ,
		success : function( dataLoaded ){
			if( dataLoaded == false ) return false ;
			info.scriptSize = dataLoaded.length;
			
			dataJSON = JSON.parse( dataLoaded );
		} ,
		error : function( jqXHR , textStatus , errorThrown ){
			console.log( 'FasteditSave : Error synchronisation data' );
		}
	} );
	return false;
}


jsFormblockFasteditUpdate = function( href , json_data , notupdated='' ){
	var layerErrorMessages = $( '#tB_messages' );
	// var data = JSON.parse( json_data );
	var form={};
	var data={};
	var dataJSON = '';
	var returnError = '';
	var returnData = '';
	var countMessages = 0;
	
	jsFormblock_layers( t , info , layers={} );
	jsFormblock_form( t , info , form={} , data={} , data_fastedit='' );
	loaddata = 'data=' + JSON.stringify( json_data.preparedata ) + '&notupdated=' + notupdated;
	
	form.data = loaddata;
	form.action = href;
	
	jQuery.ajax({
		async : form.async ,
		url : form.action ,
		// dataType : form.dataType ,
		type : form.method ,
		data : form.data ,
		// dataType : dataType ,
		beforeSend : function( ){
			console.log( 'Updated data: ' + this.data );
			layers.tb_preload.show( );
		} ,
		complete : function( msg , status ){
			
			returnError = dataJSON.returnError;
			returnData = dataJSON.returnData;
			
			$( returnData ).each( function( i , elem ){
				var re = elem.resultError;
				var rtl = elem.resultToLayer;
				var rhtml = elem.resultHTML;
				
				console.log( rtl + ' | ' + rhtml );
				
				var t = $( '#' + rtl );
				if( rhtml != false ) t.html( rhtml );
			});
			
			// .. показываем ошибку
			mesagges_show( returnError , true );
			// .. Если указан ID вызванного элементе
			if( gl_td_id != '' ){
				el = $( '#' + gl_td_id ).find( '.div-div-editable' );
				el.click( );
			}
			// .. показываем ошибку
			mesagges_show( dataJSON.returnError , true );
			
			layers.tb_preload.hide( );
			// ..
			jsFormblock_updatePageTime( info , layers , data );
		} ,
		success : function( dataLoaded ){
			if( dataLoaded == false ) return false ;
			info.scriptSize = dataLoaded.length;
			
			dataJSON = JSON.parse( dataLoaded );
		} ,
		error : function ( jqXHR, textStatus, errorThrown ) {
			console.log( 'FasteditUpdate : Error synchronisation data' );
		}
	});
	
	return false;
}


// .. удаление элемента
jsFormblockFasteditDelete = function( obj ){
	var t = $( obj );
	// .. vars
	var dataJSON = '';
	var returnError = '';
	var returnData = '';
	var countMessages = 0;
	// .. prepare
	var data_prepare = t.data( 'prepare' );
	// .. layers
	jsFormblock_layers( t , info , layers={} );
	jsFormblock_form( t , info , form={} , data={} , data_prepare );
	loaddata = jsFormblock_formuserdata( data.data );
	form.data = loaddata;
	form.action = data.href;
	
	$.each( form , function( k , v ){
		console.log( k + ' = ' + v );
	});
	
	jQuery.ajax({
		async : form.async ,
		url : form.action ,
		// dataType : form.dataType ,
		type : form.method ,
		data : form.data ,
		// dataType : dataType ,
		beforeSend : function( ){
			
		} ,
		complete : function( msg , status ){
			
			returnError = dataJSON.returnError;
			returnData = dataJSON.returnData;
			// .. показываем ошибку
			mesagges_show( returnError , true );
			
			// ..
			jsFormblock_updatePageTime( info , layers , data );
		} ,
		success : function( dataLoaded ){
			if( dataLoaded == false ) return false ;
			info.scriptSize = dataLoaded.length;
			
			dataJSON = JSON.parse( dataLoaded );
		} ,
		error : function ( jqXHR, textStatus, errorThrown ) {
			console.log( 'FasteditUpdate : Error synchronisation data' );
		}
	});
}


// .. настройка
jsFormblockFasteditSettings = function( obj ){
	var t = $( obj );
	// .. vars
	// .. prepare
	var data_prepare = t.data( 'prepare' );
	// .. layers
	jsFormblock_layers( t , info , layers={} );
	
	// ..
	var selected = false;
	sel = t.parents( '.blockform_rows' );
	if( sel.hasClass( 'selected' ) ) selected = true;
	unsel = t.parents( '.blockform_type' ).find( '.blockform_rows' ).removeClass( 'selected' );
	
	if( selected ){
		selected = false;
		sel.removeClass( 'selected' );
	}
	else {
		selected = true;
		sel.addClass( 'selected' );
	}
	
	$.each( data_prepare , function( k , v ){
		alert( 'K: ' + k + ' = ' + v );
		// form.data += k + '=' + v + '&';
		var resultToLayerId = v.resultToLayerId;
		var action = v.action;
		var href = v.href;
		var data = v.data;
		
		if( action == 'upload' ){
			if( selected )		jsFormblockFasteditSettings_upload( t , v );
			else						$( '#' + resultToLayerId ).html( '' );
		}
		else if( action == 'clear' ){
			$( '#' + resultToLayerId ).html( '' );
		}
	});
}

jsFormblockFasteditSettings_upload = function( obj , uploadData ){
	var t = $( obj );
	// .. vars
	var dataJSON = '';
	var returnError = '';
	var returnData = '';
	var countMessages = 0;
	
	$.each( uploadData , function( k , v ){
		console.log( 'data: ' + k + ' = ' + v );
	});
	// .. prepare
	var data_prepare = t.data( 'prepare' );
	// .. layers
	jsFormblock_layers( t , info , layers={} );
	jsFormblock_form( t , info , form={} , data={} , data_prepare );
	data.resultToLayerId = uploadData.resultToLayerId;
	form.action = uploadData.href;
	// alert( uploadData.resultToLayerId + ' - ' + form.action );
	form.data = jsFormblock_formuserdata( uploadData.data , true );
	
	// .. 
	jQuery.ajax({
		async : form.async ,
		url : form.action ,
		// dataType : form.dataType ,
		type : form.method ,
		data : form.data ,
		beforeSend : function( ){
		} ,
		complete : function( msg , status ){
			if( ! dataJSON ) return false;
			returnError = dataJSON.returnError;
			returnData = dataJSON.returnData;
			/*
			returnDataData = returnData[ 'data' ];
			returnDataIs_updated = returnData[ 'is_updated' ];
			
			if( returnDataIs_updated == true ) window[ data.functionUpdate ][ 'release' ]( data.resultToLayer );
			*/
			mesagges_show( returnError , true );
			
			$( '#' + data.resultToLayerId ).html( returnData.html );
			layers.tbload.removeClass( 'active' );
			layers.tbload_result.html( '' );
			// window.location.href = '#' + resultToLayer;
			
			// .. 
			jsFormblock_updatePageTime( info , layers , data );
			// ..
		} ,
		success : function( dataLoaded ){
			if( dataLoaded == false ) return false ;
			info.scriptSize = dataLoaded.length;
			
			dataJSON = JSON.parse( dataLoaded );
		} ,
		error : function( jqXHR , textStatus , errorThrown ){
			console.log( 'FasteditSave : Error synchronisation data' );
		}
	} );
	return false;
}

/*
	.. табуляция
*/
jsFormblockFasteditChangeElement = function( obj ){
	var t = $( obj );
	
	parent_td = t.parents( '.col' );
	next_td = parent_td.next( );
	next_el = next_td.find( 'div.div-div-editable' );
	// console.log( 'next el: ' + next_td.html( ) );
	next_el.click( );
	gl_td_id = next_td.prop( 'id' );
	// next_el = next_td.eq( 0 ).find( 'input , select , textarea' ).eq( 0 );
	// $( document ).ready(function(){
		// parent_td = t.parents( '.col' );
		// next_td = parent_td.next( );
		// console.log( 'next td: ' + next_td.html( ) );
		// console.log( 'next el: ' + next_el.html( ) );
		// next_el = next_td.find( 'input, select, textarea' ).eq( 0 );
		// console.log( 'next el: ' + next_td.html( ) + ' | ' + next_el.prop( 'type' ) );
		// next_el.focus( );
	// });
}

// .. синонимичные переменные
FBlFEPrepare = jsFormblockFasteditUpdate;


/*
	.. Действия на странице
*/
jsFormblockPagesActions = function( obj ){
	obj = $( obj );
	val = obj.val( );
	
	form = obj.parents( 'form' );
	form.prop( 'action' , val );
	form.submit( );
}

jsFormblockPagesChange = function( obj ){
	// obj = $( obj );
	// val = obj.val( );
	
	// form = obj.parents( 'form' );
	// form.prop( 'action' , val );
	// form.submit( );
}