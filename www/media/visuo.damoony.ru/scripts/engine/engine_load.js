/*

	javascript file
	formblock class

var formr = { };
var count = 0;
var info = {  'install' : true , 'scriptTimeLoading' : { 'd_all_' : 0 } }; //d_all_ = 0;
formblock_info = {
	scriptname : 'formblock'
}

*/
engine_load = {
	engine : null ,
	name : 'engine.load' ,
	popup : false ,
	popup_count : 0 ,
	check : function ( engine ) {
		this.engine = engine;

		console.log( '[ check ][ ' + this.name + ' ] Engine: ' + this.engine._scriptname + '.' );
	} ,
	// layers : { } , form : { } , data : { } ,
	// ..
	// .. prepare
	// ..
	/*
	dialog : {
		name : 'formblock : prepare' ,
		ajax : false , layers : { } , form : { } , data : { } ,
		dialogOn : false ,
		blockCount : 0 ,
		on : function( html , htmlappend=true , opt=false ){
			formblockP.layers( t , this.layers );
			
			if( this.blockCount < 0 ) this.blockCount = 0;
			
			if( htmlappend == false ) this.blockCount = 0;
				
			if( typeof html == 'string' && opt == false ){
				inserthtml = html;
				if( this.blockCount == 0 ){
					// html = '<block' + this.blockCount + ' id="block' + this.blockCount + '">' + inserthtml + '</block' + this.blockCount + '>';
					html = '<div id="block' + this.blockCount++ + '">' + inserthtml + '</div>';
				}
				else {
				}
			}
			else if( typeof html == 'object' ){
				var obj = html;
				
				if( opt == 'texttopopup' ){
					
					html = '<div id="block' + this.blockCount++ + '">' + '<div class="blocktopopup">' + $(obj).parents( '.blockmaxheight' ).find( '.hide' ).html( ) + '</div>' + '</div>';
				}
			}
			else {
				return false;
			}
			
			
			lastblock = this.layers.tbload_datablock.find( '#block' + ( this.blockCount - 2 ) );
			lastblock.hide( );
			$( 'html' ).addClass( 'overflowHidden' );
			if( htmlappend == true ) this.layers.tbload_datablock.append( html ); else this.layers.tbload_datablock.html( html );
			this.layers.tbload_result.css( { 'opacity' : '0.0' } );
			this.layers.tbload.addClass( 'active' );
			this.layers.tbload_result.animate( { 'opacity' : '1.0' } , 300 , function( ){
				// .. after .. animate
			});
			this.layers.tbload.animate( { 'opacity' : '1.0' } , 300 , function( ){
				// .. after .. animate
			});
			
			// ..
			this.dialogOn = true;
			
			return false;
		} ,
		html : function( html ){
			html = '<div class="fon" onClick="formblock.dialog.off( );"><div class="note">Нажмите, чтобы закрыть окно.</div></div><div class="data">' + html + '</div>';
			
			this.layers.tbload_result.html( html );
		} ,
		off : function( htmlappend=true ){
			this.dialogOn = false;
			formblockP.layers( t , this.layers );
			
			layers = this.layers;
			
			$( 'html' ).removeClass( 'overflowHidden' );
			layers.tbload.addClass( 'active2' );
			
			
			if( htmlappend == true ){
				this.blockCount--;
				
				lastblock = this.layers.tbload_datablock.find( '#block' + this.blockCount );
				preblock = this.layers.tbload_datablock.find( '#block' + ( this.blockCount - 1 ) );
				
				if( this.blockCount == 0 ){
					this.layers.tbload_result.animate( { 'opacity' : '0.0' } , 300 , function( ){
						// .. after .. animate
						lastblock.detach(  );
						$(this).html(  );
						layers.tbload.removeClass( 'active' );
						layers.tbload.removeClass( 'active2' );
					});
					this.layers.tbload.animate( { 'opacity' : '0.0' } , 300 , function( ){
						// .. after .. animate
					});
					// this.layers.tbload_result.html( '' );
					// this.layers.tbload.removeClass( 'active' );
				}
				else {
					lastblock.animate( { 'opacity' : '0.0' } , 300 , function( ){
						$(this).detach(  );
					} );
					preblock.css( { 'opacity' : '0.0' } ).show( ).animate( { 'opacity' : '1.0' } , 300 , function( ){
					} );
				}
			}
			else {
				alert( 'wewew' );
			}
		} ,
		is : function( ){
			return this.dialogOn;
		}
	} ,
	prepare : {
		name : 'formblock : prepare' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			if( t.hasClass( 'updated' ) ){
				this.ajax = false;
				return ;
			}
			else {
				this.ajax = true;
			}
			
			if( data_prepare ){
				formblockP.layers( t , this.layers );
				formblockP.form( t , this.form , this.data , data_prepare );
				formblockP.formdata( t , this.form , this.data );
			}
			else
				return false;
			
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			var t = $( obj );
			var data = this.data;
			
			// .. Алгоритм
			if( data.editpopup == true ){
				// .. jsFormblockFasteditPrepare_resultPopup( dataJSON );
			}
			else {
				var block = $( '#' + data.resultToLayerId );
				// .. return .. type
				var returnError = dataJSON.returnError;
				var returnData = dataJSON.returnData;
				
				if( returnData ){
					if( returnData.type == '' ){
						block.html( '' ).append( returnData.html );
						var inp = block.find( 'input , select , textarea' );
						inp.focus( );
					}
					else if( returnData.type == 'popup' || returnData.type == 'file' ){
						formblock.dialog.on( returnData.html );
					}
				}
			}
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
		}
	} ,
	// ..
	// .. Save Data
	// ..
	complete : {
		name : 'formblock : complete' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var action = attrs.action;
			var formtype = $( t ).parents( 'form' ).eq( 0 ).attr( 'formtype' );
			
			var loaddata = '';
			var data_prepare = t.data( 'prepare' );
			
			if( typeof attrs.action == 'object' ){
				if( attrs.action.actionsub == 'save' ){
					// .. 
					val = t.data( 'val' );
					var field = t.parents( 'div.elbox' ).eq( 0 ).find( 'div.elinput input[type=hidden]' ).eq( 0 );
					field.val( val );
				}
				// console.log( typeof attrs.action + ' | ' + attrs.action );
			}
			
			if( action == 'cancel' ){
				this.ajax = false;
				formblock.dialog.off( false );
			}
			else {
				this.ajax = true;
				if( data_prepare ){
					formblockP.layers( t , this.layers );
					formblockP.form( t , this.form , this.data , data_prepare );
					// .. не файлы
					if( formtype != 'FILES' ){
						// .. простое редактирование
						if( this.data.editextended == false ){
							select = t.parents( '.div-el' ).eq( 0 ).find( 'input , select , textarea' ).eq( 0 );
							loaddata = { [select.prop( 'name' )] : select.val( ) };
						}
						else {
							// loaddata = this.form.form.serialize();
						}
						
					}
					formblockP.formdata( t , this.form , this.data , loaddata );
				}
				else
					return false;
			}
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			var t = $( obj );
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData; // [ html{text} , updated{false,true} , ]
			
				// .. показываем сообщения
			mesagges_show( returnError , true );
			// ..
			if( returnData ){
				var resultToLayerId = $( '#' + this.data.resultToLayerId );
				switch( returnData.action ){
					// .. layer | layerpopupclose | popupopen | popupclose | clear
					case 'layer' : resultToLayerId.html( returnData.html ); break;
					case 'layerpopupclose' : resultToLayerId.html( returnData.html ); formblock.dialog.off( ); break;
					case 'popupopen' : formblock.dialog.on( returnData.html ); break;
					case 'popupclose' : formblock.dialog.off( ); break;
					case 'clear' : resultToLayerId.html( '' ); break;
					default : break;
				}
				
				// .. если обновились данные
				if( returnData.updated == true ){
					//.. снимаем выбор элемента
					clearRowsId = returnData.clearRowsId;
					curRowsId = $( '#' + this.data.resultToLayerId ).parents( '.blockform_rows' ).attr( 'id' );
					
					if( clearRowsId == curRowsId ){
						$( '#' + this.data.resultToLayerId ).parents( '.blockform_type' ).find( '.blockform_rows' ).removeClass( 'selected' );
					}
					
					// .. вызываем функцию обновления
					if( window[ this.data.functionUpdate ] != 'undefined' ){
						// alert( 'COMPLETE! ' + this.data.functionUpdate + ' | ' + this.data.resultToLayerId );
						// window[ this.data.functionUpdate ].update( this.data.resultToLayerId );
						window[ this.data.functionUpdate ].update( false );
					}
					
					// .. прогоняем другие функции обнвления					
					if( attrs.functionUpdate ){
						var funcs = window[ attrs.functionUpdate ].functionUpdateSettings;
						
						// ..
						if( funcs != null ){
							$.each( funcs , function( k , v ){
								if( window[ v.functionUpdate ] ){
									if( v.action == 'refresh' ){
										window[ v.functionUpdate ].update( false );
									}
									else if( v.action == 'upload' ){
										window[ v.functionUpdate ].update( false );
									}
									else if( v.action == 'clear' ){
										window[ v.functionUpdate ].clear( );
									}
								}
								
							} );
						}
					}
				}
				else {
				}
			}
		} ,
	} ,
	// ..
	// .. Save Data
	// ..
	change : {
		name : 'formblock : complete' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var action = attrs.action;
			var formtype = $( t ).parents( 'form' ).eq( 0 ).attr( 'formtype' );
			
			var loaddata = { };
			var data_prepare = t.data( 'prepare' );
			
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			
			select = t.parents( '.div-el' ).eq( 0 ).find( 'input , select , textarea' );
			$.each( select , function( k , v ){
				loaddata[ $(v).prop( 'name' ) ] = $(v).val( );
			} );
			// loaddata = { [select.prop( 'name' )] : select.val( ) };
			formblockP.formdata( t , this.form , this.data , loaddata );
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			var t = $( obj );
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData; // [ html{text} , updated{false,true} , ]
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			elselect = t.parents( '.elbox' ).eq( 0 ).find( '.elselect div' ).eq( 0 );
			
			elselect.html( returnData.html );
		} ,
	} ,
	update : {
		name : 'Updated' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var href = attrs.data.href;
			var json_data = attrs.data;
			var notupdated = attrs.notupdated;
			
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , '' );
			formblockP.formdata( t , this.form , this.data , { 'data' : JSON.stringify( json_data.preparedata ) , 'notupdated' : notupdated } );
			this.form.action = href;
			
			formblock.acttype = '';
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			var t = $( obj );
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			if( returnData ){
				$.each( returnData , function( i , elem ){
					var re = elem.resultError;
					var rtl = elem.resultToLayerId;
					var rhtml = elem.resultHTML;
					
					// console.log( 'Updated! rtl: ' + rtl );
					
					t = $( '#' + rtl );
					if( rhtml != false ){
						t.animate( { 'opacity' : 0.5 } , 50 , function( ){
							$( this ).html( rhtml ).animate( { 'opacity' : 1.0 } , 200 , function( ){ });
						} );
					}
				} );
			}
			// .. показываем сообщения
			mesagges_show( returnError , true );
		} ,
	} ,
	update_fastpages : {
		name : 'Updated' ,
		ajax : false , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			$.each( attrs , function( k , v ){
				resultToLayerId = v[ 'resultToLayerId' ];
				action = v[ 'action' ];
				
				if( action == 'clear' ) $( '#' + resultToLayerId ).html( '' );
			});
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
		} ,
	} ,
	// ..
	// .. Save Data
	// ..
	inputsearch : {
		name : 'formblock : complete' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var action = attrs.action;
			var formtype = $( t ).parents( 'form' ).eq( 0 ).attr( 'formtype' );
			
			var loaddata = '';
			var data_prepare = t.data( 'prepare' );
			
			this.ajax = true;
			if( data_prepare ){
				formblockP.layers( t , this.layers );
				formblockP.form( t , this.form , this.data , data_prepare );
				
				select = t.parents( '.div-el' ).eq( 0 ).find( 'input[type=text] , select , textarea' ).eq( 0 );
				loaddata = { [select.prop( 'name' )] : select.val( ) };
				
				formblockP.formdata( t , this.form , this.data , loaddata , true );
				$.each( this.data.preparedata , function( k , v ){
					console.log( 'loaddata: ' + k + ' = ' + v );
				} );
			}
			else
				return false;
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			var t = $( obj );
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData; // [ html{text} , updated{false,true} , ]
			
				// .. показываем сообщения
			mesagges_show( returnError , true );
			
			var el = t.parents( '.el' );
			var elselectbox = el.find( '.elselectbox' );
			var elselectbox_span = elselectbox.find( 'span' );
			var elselectbox_span_selected = elselectbox.find( 'span.selected' );
			
			elselectbox.html( returnData.html );
			# $.each( returnData , function( k , v ){
			# 	console.log( 'k: ' + k + ' = ' + v );
			# } );
			# 
			# // ..
			# if( returnData ){
			# 	var resultToLayerId = $( '#' + this.data.resultToLayerId );
			# 	switch( returnData.action ){
			# 		// .. layer | layerpopupclose | popupopen | popupclose | clear
			# 		case 'layer' : resultToLayerId.html( returnData.html ); break;
			# 		case 'layerpopupclose' : resultToLayerId.html( returnData.html ); formblock.dialog.off( ); break;
			# 		case 'popupopen' : formblock.dialog.on( returnData.html ); break;
			# 		case 'popupclose' : formblock.dialog.off( ); break;
			# 		case 'clear' : resultToLayerId.html( '' ); break;
			# 		default : break;
			# 	}
			# }

            if( attrs.other ){
				// eval( attrs.other );
				# alert( 'OK: ' + attrs.action );
				# var o = attrs.other[ 0 ];
				# var a = attrs.other[ 1 ];
				# var v = attrs.other[ 2 ];
				# 
				# o.a( v )
			}
		} ,
	} ,
	fasttablebox : {
		name : 'fasttablebox' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var href = t.data( 'href' );
			var realhref = t.data( 'realhref' );
			
			if( typeof realhref == 'object' ) realhref = JSON.stringify( realhref );
			
			if( t.data( 'href' ) ) t.data( 'prepare' , { 'href' : t.data( 'href' ) , 'preparedata' : { 'preload' : true , 'resultToLayerId' : t.prop( 'id' ) , 'realhref' : realhref } } );
			else t.data( 'prepare' , { 'href' : document.location.pathname , 'preparedata' : { 'preload' : true , 'resultToLayerId' : t.prop( 'id' ) , 'realhref' : realhref  } } );
			
			var data_fastedit = t.data( 'prepare' );
			if( data_fastedit ){
				formblockP.layers( t , this.layers );
				formblockP.form( t , this.form , this.data , data_fastedit );
				formblockP.formdata( t , this.form , this.data );
			}
			else {
				return false;
			}
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var t = $( obj );
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;	// [ ids , html{text} , preparedata{} ]
			
			var $scripts = returnData.scripts;
			var $html = returnData.html;
			
			$.each( $scripts , function( k , v ){
				var script = document.createElement( 'script' );
				script.type = 'text/javascript';
				// script.async = true;
				// script.src = 'link to your script here';
				
				script.innerHTML = v;
				$( 'head' ).append( script );
				// $( '#body' ).html( $( '#body' ).html( ) + '<div>' + v + '</div>' );
				
				# console.log( v );
				# // $( 'head' ).append( '<script type="text/javascript">' + v + '</script>' );
				# $( '#body' ).html( $( '#body' ).html( ) + '<div>' + v + '</div>' );
			} );
			
			t.html( $html );
			returnData.metatitle != false ? $( 'title' ).text( returnData.metatitle ) : '';
			returnData.metakeywords != false ? $( 'meta[name="keywords"]' ).prop( 'content' , returnData.metakeywords ) : '';
			returnData.metadescription != false ? $( 'meta[name="description"]' ).prop( 'content' , returnData.metadescription ) : '';
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			# var t = $( obj );
			# var data_fastedit = t.data( 'prepare' );
			# 
			# $( '#' + attrs.resultToLayerId ).fadeToggle( 100 , function( ){
			# 	$( this).html( returnData.html ).fadeToggle( 300 );
			# } );
			# 
			# if( returnData.ids ){
			# 		window[ attrs.functionUpdate ].dataupdate.preparedata.ids = returnData.ids;
			# }
			# else {
			# 	window[ attrs.functionUpdate ].dataupdate.preparedata.ids = [ ];
			# }
		} ,
	} ,
	fasttable : {
		name : 'Fasttable' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			var data_fastedit = t.data( 'prepare' );
			if( data_fastedit ){
				formblockP.layers( t , this.layers );
				formblockP.form( t , this.form , this.data , data_fastedit );
				formblockP.formdata( t , this.form , this.data );
			}
			else {
				return false;
			}
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			 var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;	// [ ids , html{text} , preparedata{} ]
			
			var t = $( obj );
			var data_fastedit = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			// $.each( returnData , function( k , v ){
				// console.log( attrs.resultToLayerId + ' | k: ' + k + ' = ' + v );
			// } );
			
			$( '#' + attrs.resultToLayerId ).fadeToggle( 100 , function( ){
				$( this).html( returnData.html ).fadeToggle( 300 );
			} );
			
			if( typeof returnData.ids === 'object' ){
				window[ attrs.functionUpdate ].dataupdate.preparedata.ids = returnData.ids;
			}
			else {
				window[ attrs.functionUpdate ].dataupdate.preparedata.ids = [ ];
			}
		} ,
	} ,
	fasttableact : {
		name : 'Fasttable Act' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			var data_fastedit = t.data( 'prepare' );
			
			if( data_fastedit ){
				formblockP.layers( t , this.layers );
				formblockP.form( t , this.form , this.data , data_fastedit );
				formblockP.formdata( t , this.form , this.data );
			}
			else
				return false;
			//
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;	//
			
			var data_fastedit = t.data( 'prepare' );
			
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			functionUpdate = data_fastedit.functionUpdate;
			
			// console.log( this.name + ' [ returnData.ids : ' + returnData.ids + ' ]' );
			// console.log( 'Добавляем в слой: ' + this.data.resultToLayerId );
			if( typeof window[ functionUpdate ] == 'object' ){
				// .. добавляем IDs элементов на странице
				window[ functionUpdate ].dataupdate.preparedata.ids = returnData.ids;
				// .. вставляем полученные данные HTML
				if( attrs.show ){
					
					if( returnData.html ){
						$( '#' + attrs.show.resultToLayerId ).fadeToggle( 200 ).html( $( '#' + attrs.show.resultToLayerId ).html( ) + returnData.html ).fadeToggle( 1000 );
						
						var form = $( '#' + attrs.show.resultToLayerId ).parents( 'form' ).eq( 0 );
						var a = form.find( '.blockform_pages a' );
						var prepare = a.data( 'prepare' );
						prepare.preparedata.p += 1;
						a.data( 'prepare'  , prepare );
					}
					else {
						var form = $( '#' + attrs.show.resultToLayerId ).parents( 'form' ).eq( 0 );
						var blockform_pages = form.find( '.blockform_pages' );
						blockform_pages.hide( 100 );
					}
				}
				else {
					$( '#' + this.data.resultToLayerId ).fadeToggle( 100 , function( ){
						$( this ).html( returnData.html ).fadeToggle( 300 );
					} );
				}
				// .. обновляем слои
				window[ functionUpdate ].update_fastpages( );
			}
		}
	} ,
	back : {
	} ,
	settings : {
		name : 'Settings' ,
		ajax : false , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			// .. vars
			// .. prepare
			var data_prepare = t.data( 'prepare' );
			// .. layers
			formblockP.layers( t , this.layers );
			
			var selected = t.parents( '.blockform_rows' ).hasClass( 'selected' );
			
			// .. 
			var type = window[ attrs.functionUpdate ].functionUpdateType;
			var settings = window[ attrs.functionUpdate ].functionUpdateSettings;
			var clear = window[ attrs.functionUpdate ].functionUpdateClear;
			
			switch( type ){
				case 'slider' :
					if( attrs.actionsub == 'back' ){
						
						var obj = window[ attrs.functionUpdate ].datafasttable.preparedata.resultToLayerId;
						$( '#' + obj ).slideToggle( 200 , function( ){
							$(this).html( "" ).show( );
						} );
						
						$.each( window[ attrs.functionUpdate ].functionUpdateSettingsBack , function( k , v ){
							if( window[ v.functionUpdate ] !== undefined ){
								
								console.log( 'Settings Back: ' + k );
								
								if( v.action == 'upload' ){
									t.data( 'prepare_tmp' , t.data( 'prepare' ) );
									var attrsNew = v;
									attrsNew.href = window[ v.functionUpdate ].datafasttable.href;
									attrsNew.tableToLoadData = t.parents( '[data-div="tableToLoadData"]' ).attr( 'id' );
									attrsNew.trcurrent = t.parents( '.blockform_rows' );
									var tnew = $( '#' + v.resultToLayerId );
									tnew.data( 'prepare' , $.extend( true , tnew.data( 'prepare' ) , t.data( 'prepare' ) , attrsNew ) );
									formblock.a( t , 'settingsUpload' , v );
								}
								else if( v.action == 'reload' ){
									t.data( 'prepare_tmp' , t.data( 'prepare' ) );
									var attrsNew = v;
									attrsNew.href = window[ v.functionUpdate ].datafasttable.href;
									attrsNew.tableToLoadData = t.parents( '[data-div="tableToLoadData"]' ).attr( 'id' );
									attrsNew.trcurrent = t.parents( '.blockform_rows' );
									var tnew = $( '#' + v.resultToLayerId );
									tnew.data( 'prepare' , $.extend( true , tnew.data( 'prepare' ) , t.data( 'prepare' ) , attrsNew ) );
									formblock.a( t , 'settingsUpload' , v );
								}
								else if( v.action == 'clear' ){
									window[ v.functionUpdate ].clear( );
								}
							}
						} );
					}
					else {
						var obj = window[ attrs.functionUpdate ].datafasttable.preparedata.resultToLayerId;
						$( '#' + obj ).slideToggle( 200 , function( ){
							$(this).html( "" ).show( );
						} );
						
						$.each( window[ attrs.functionUpdate ].functionUpdateSettings , function( k , v ){
							if( window[ v.functionUpdate ] !== undefined ){
								
								if( v.action == 'upload' ){
									t.data( 'prepare_tmp' , t.data( 'prepare' ) );
									var attrsNew = v;
									attrsNew.href = window[ v.functionUpdate ].datafasttable.href;
									attrsNew.tableToLoadData = t.parents( '[data-div="tableToLoadData"]' ).attr( 'id' );
									attrsNew.trcurrent = t.parents( '.blockform_rows' );
									var tnew = $( '#' + v.resultToLayerId );
									tnew.data( 'prepare' , $.extend( true , tnew.data( 'prepare' ) , t.data( 'prepare' ) , attrsNew ) );
									formblock.a( t , 'settingsUpload' , v );
								}
								else if( v.action == 'reload' ){
									t.data( 'prepare_tmp' , t.data( 'prepare' ) );
									var attrsNew = v;
									attrsNew.href = window[ v.functionUpdate ].datafasttable.href;
									attrsNew.tableToLoadData = t.parents( '[data-div="tableToLoadData"]' ).attr( 'id' );
									attrsNew.trcurrent = t.parents( '.blockform_rows' );
									var tnew = $( '#' + v.resultToLayerId );
									tnew.data( 'prepare' , $.extend( true , tnew.data( 'prepare' ) , t.data( 'prepare' ) , attrsNew ) );
									formblock.a( t , 'settingsUpload' , v );
								}
								else if( v.action == 'clear' ){
									window[ v.functionUpdate ].clear( );
								}
							}
						} );
					}
					break;
				case '' :
				default :
					switch( selected ){
						case false :
							t.parents( '.blockform_type' ).find( '.blockform_rows' ).removeClass( 'selected' );
							t.parents( '.blockform_rows' ).addClass( 'selected' );
							
							if( settings != null ){
								$.each( window[ attrs.functionUpdate ].functionUpdateSettings , function( k , v ){
									if( window[ v.functionUpdate ] !== undefined ){
										
										if( v.action == 'upload' ){
											t.data( 'prepare_tmp' , t.data( 'prepare' ) );
											var attrsNew = v;
											attrsNew.href = window[ v.functionUpdate ].datafasttable.href;
											attrsNew.tableToLoadData = t.parents( '[data-div="tableToLoadData"]' ).attr( 'id' );
											attrsNew.trcurrent = t.parents( '.blockform_rows' );
											var tnew = $( '#' + v.resultToLayerId );
											tnew.data( 'prepare' , $.extend( true , tnew.data( 'prepare' ) , t.data( 'prepare' ) , attrsNew ) );
											formblock.a( t , 'settingsUpload' , v );
										}
										else if( v.action == 'reload' ){
											t.data( 'prepare_tmp' , t.data( 'prepare' ) );
											var attrsNew = v;
											attrsNew.href = window[ v.functionUpdate ].datafasttable.href;
											attrsNew.tableToLoadData = t.parents( '[data-div="tableToLoadData"]' ).attr( 'id' );
											attrsNew.trcurrent = t.parents( '.blockform_rows' );
											var tnew = $( '#' + v.resultToLayerId );
											tnew.data( 'prepare' , $.extend( true , tnew.data( 'prepare' ) , t.data( 'prepare' ) , attrsNew ) );
											formblock.a( t , 'settingsUpload' , v );
										}
										else if( v.action == 'clear' ){
											window[ v.functionUpdate ].clear( );
										}
									}
								} );
							}
							break;
						case true :
							t.parents( '.blockform_type' ).find( '.blockform_rows' ).removeClass( 'selected' );
							if( clear != null ){
								$.each( window[ attrs.functionUpdate ].functionUpdateClear , function( k , v ){
									if( window[ v ] !== undefined ){
										window[ v ].clear( );
									}
								} );
							}
							break;
						default :
							break;
					}
					break;
			}
			
			return ;
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
		} ,
	} ,
	settingsUpload : {
		name : 'settingsUpload' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			// .. prepare
			var data_prepare = $( '#' + attrs.resultToLayerId ).data( 'prepare' );
			$( '#' + attrs.resultToLayerId ).data( 'prepare' , $( '#' + attrs.resultToLayerId ).data( 'prepare_tmp' ) );
			
			// .. layers
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			formblockP.formdata( t , this.form , this.data );
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			// ..
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			// .. prepare
			var data_prepare = attrs;
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			# if( typeof( returnData.preparedata ) == 'object' ){
			# 	$.each( returnData.preparedata , function( k , v ){
			# 		console.log( 'including prepare data: ' + k + ' = ' + v );
			# 		window[ attrs.functionUpdate ].datafasttable.preparedata[ k ] = v;
			# 	});
			# }
			
			// .. обновляем ID
			if( typeof( window[ attrs.functionUpdate ] ) === 'object' ){
				if( returnData.ids ) window[ attrs.functionUpdate ].dataupdate.preparedata.ids = returnData.ids;
				else window[ attrs.functionUpdate ].dataupdate.preparedata.ids = [ ];
				
				// alert( 'functionUpdate: ' + attrs.functionUpdate + ' | ' + window[ attrs.functionUpdate ].dataupdate.preparedata.ids );
				// console.log( '1 Return ids: ' + returnData.ids + ' | ' + window[ attrs.element.functionUpdate ].dataupdate.preparedata.ids );
				// console.log( '2 Return ids: ' + returnData.ids + ' | ' + window[ attrs.element.functionUpdate ].dataupdate.preparedata.ids );
			}
			
			if( returnData ){
				
				$( '#' + attrs.resultToLayerId ).hide( ).html( returnData.html ).fadeToggle( 200 , function( ){
					var top = $( '#' + attrs.resultToLayerId ).offset( ).top;
					$( [ document.documentElement , document.body ] ).animate( { 'scrollTop' : top } , 500 );
				} );
				
				var trcurrent = attrs.trcurrent;
				var tableToLoadData = $( '#' + attrs.tableToLoadData );
				var rowsbody = tableToLoadData.find( '[data-div="tbody"]' ).find( '[data-div="tr"]' );
				
				if( $( '#' + attrs.resultToLayerId ).html( ) != '' && trcurrent.hasClass( 'selected' ) == false ){
					trcurrent.addClass( 'selected' );
				}
			}
			else {
				$( '#' + attrs.resultToLayerId ).html( '' );
			}
		} ,
	} ,
	// .. fastactions
	fastactions : {
		name : 'fastactions' ,
		ajax : false , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			// .. 
			this.ajax = false;
			
			if( attrs[0] ){
				attrs.action = attrs[ 0 ];
				attrs.actionsub = attrs[ 1 ];
			}
			
			var t = $( obj );
			
			if( attrs.action ) t.data( 'prepare' ).preparedata.action = attrs.action;
			if( attrs.actionsub ) t.data( 'prepare' ).preparedata.actionsub = attrs.actionsub;
			
			// ..
			if( formblock[ attrs.action ] != undefined && window[ attrs.functionUpdate ] != undefined ){
				// console.log( 'Var 1' );
				
				if( window[ attrs.functionUpdate ][ attrs.action ] != undefined ){
					// console.log( 'Var 1 1' );
					
					window[ attrs.functionUpdate ][ attrs.action ]( t , attrs );
				}
				else if( formblock[ attrs.action ] != undefined ){
					// console.log( 'Var 1 2: ' + attrs.action + ' | ' + formblock[ attrs.action ] );
					
					formblock.a( t , attrs.action , attrs );
				}
				else {
					// console.log( 'Var 1 3' );
					
					alert( 'Bad command!');
				}
			}
			else if( formblock[ attrs.action ] != undefined && window[ attrs.functionUpdate ] == undefined ){
				// console.log( 'Var 2' );
				formblock.a( obj , attrs.action , attrs );
			}
			else {
				// console.log( 'Var 3' );
				
				// .. ajax
				this.ajax = true;
				// .. prepare
				var data_prepare = t.data( 'prepare' );
				// .. actionsub
				if( data_prepare.preparedata.length == 0 ) data_prepare.preparedata = { 'actionsub' : attrs.actionsub };
				else data_prepare.preparedata.actionsub = attrs.actionsub;
				
				if( data_prepare ){
					formblockP.layers( t , this.layers );
					formblockP.form( t , this.form , this.data , data_prepare );
					
					formblockP.formdata( t , this.form , this.data , data_prepare );
				}
				else
					return false;
			}
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			// ..
			if( returnData ){
				if( returnData[ 0 ] != undefined ){
					$.each( returnData , function( k , v ){
						var returnDataCur = returnData[ k ];
						
						if( returnDataCur.action == undefined ){
							t.html( returnDataCur.html ).addClass( returnDataCur.addclass ).removeClass( returnDataCur.removeclass );
						}
						else {
							resultToLayerId = returnDataCur.resultToLayerId ? $( returnDataCur.resultToLayerId ) : $( '#' + attrs.resultToLayerId );
							
							// .. выполняем действие
							switch( returnDataCur.action ){
								// .. layer | layerpopupclose | popupopen | popupclose | clear
								case 'layer' : resultToLayerId.html( returnDataCur.html , returnDataCur.htmlappend ); break;
								case 'layerpopupclose' : resultToLayerId.html( returnDataCur.html , returnDataCur.htmlappend ); formblock.dialog.off( ); break;
								case 'popup' : formblock.dialog.on( returnDataCur.html , returnDataCur.htmlappend ); break;
								case 'popuphtml' :  formblock.dialog.html( returnDataCur.html , returnDataCur.htmlappend ); break;
								case 'save' :
								case 'cancel' :
								case 'popupclose' : formblock.dialog.off( ); break;
								case 'clear' : resultToLayerId.html( '' ); break;
								case 'redirect' :
									document.location.href = returnDataCur.redirecthref;
									break;
								case 'update' :
									console.log( 'Update!' );
									var f = returnDataCur.update.functionUpdate;			// .. functionUpdate
									var r = returnDataCur.update.resultToLayerId;			// .. resultToLayerId
									if( window[ f ] ){
										window[ f ].fasttable( $( '#' + r ) );
									}
									
									break;
								default : break;
							}
							
							
							if( returnDataCur.updated == true ){
								if( window[ attrs.functionUpdate ] )
									window[ attrs.functionUpdate ].update( );
									// window[ attrs.functionUpdate ].update_fastpages( );
							}
						}
						
					} );
				}
				else {
					var returnDataCur = returnData;
					resultToLayerId = returnDataCur.resultToLayerId ? $( returnDataCur.resultToLayerId ) : $( '#' + attrs.resultToLayerId );
					
					// .. выполняем действие
					switch( returnDataCur.action ){
						// .. layer | layerpopupclose | popupopen | popupclose | clear
						case 'layer' : resultToLayerId.html( returnDataCur.html , returnDataCur.htmlappend ); break;
						case 'layerpopupclose' : resultToLayerId.html( returnDataCur.html , returnDataCur.htmlappend ); formblock.dialog.off( ); break;
						case 'popup' : formblock.dialog.on( returnDataCur.html , returnDataCur.htmlappend ); break;
						case 'popupopen' : formblock.dialog.on( returnDataCur.html , returnDataCur.htmlappend ); break;
						case 'save' :
						case 'cancel' :
						case 'popupclose' : formblock.dialog.off( ); break;
						case 'clear' : resultToLayerId.html( '' ); break;
						default : break;
					}
					
					if( returnDataCur.updated == true ){
						if( window[ attrs.functionUpdate ] ){
							
							window[ attrs.functionUpdate ].update( );
							
							// window[ attrs.functionUpdate ].update_fastpages( );
						}
					}
				}
			}
		} ,
	} ,
	// ..
	// ..
	// ..
	fastadd : {
		name : 'formblock : fastadd' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var data_prepare = t.data( 'prepare' );
			
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			formblockP.formdata( t , this.form , this.data , data_prepare );
			
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			if( returnData.action == 'popup' ){
				formblock.dialog.on( returnData.html );
			}
			else if( returnData.action == 'cancel' ){
				formblock.dialog.off( );
			}
			else if( returnData.action == 'save' ){
				
				if( typeof( returnData.id ) == 'number' && returnData.id != 0 ){
					
					var table = $( '#' + this.data.resultToLayerId ).parents( '[data-div="table"]' );
					var tbody = $( '#' + this.data.resultToLayerId );
					var tbodytr = tbody.find( '[data-div="tr"]' );
					var tfoot = table.find( '[data-div="tfoot"]' );
					var tfoottrempty = tfoot.find( '[data-div="tr"]' ).eq( 0 );
					var tfoottrnew = tfoot.find( '[data-div="tr"]' ).eq( 1 );
					
					if( tbodytr.length == 0 ){
						# tfoottrempty.toggle( 300 , function( ){
						# 	tfoottrempty.addClass( 'hide' );
						# } );

						window[ attrs.functionUpdate ].fasttable( t );
					}
					else {
						tbody.removeClass( 'hide' ).append( returnData.html );
					
						var tr = tbody.find( '.blockform_rows:last' );
						var bgcolor = tr.css( 'background-color' );
						$( tr ).css( { 'background-color' : '#' + $( tfoottrnew ).css( 'background-color' ) } ).animate( { 'background-color' : bgcolor } , 2000 , function( ){
							$( this ).removeClass( 'blockform_rows_new' ).css( { 'background-color' : '' } );
						} );
						
						if( typeof( window[ this.data.functionUpdate ] ) === 'object' ){
							if( window[ attrs.functionUpdate ].dataupdate.preparedata.ids === null ){
								window[ attrs.functionUpdate ].dataupdate.preparedata.ids = [ ];
							}
							window[ attrs.functionUpdate ].dataupdate.preparedata.ids.push( returnData.id );
						}
					}
					
					// ..
					pageactions.act( $( tr ) , 'tablecheckboxes' , { 'checkname' : 'form[edit]' , 'mainname' : 'test' } );
					
					// window[ attrs.functionUpdate ].update( false );
					
					// ..
					if( formblock.dialog.is( ) ) formblock.dialog.off( );
				}
				else {
					console.log( 'Incorrect input data!' );
				}
			}
			else {
				// ..
			}
		} ,
	} ,
	fastdelete : {
		name : 'Fastpages' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			var data_fastedit = t.data( 'prepare' );
			if( data_fastedit ){
				formblockP.layers( t , this.layers );
				formblockP.form( t , this.form , this.data , data_fastedit );
				formblockP.formdata( t , this.form , this.data , '' );
			}
			else
				return false;
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_fastedit = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			if( returnData[ 0 ] != undefined ){
				$.each( returnData , function( k , v ){
					var curReturnData = returnData[ k ];
					
					if( k == 0 ){
						if( curReturnData.updated !== undefined || curReturnData.deleted_id !== undefined ){
							// .. удаляем ID из массива
							var deleted_id = curReturnData.deleted_id;
							ids = window[ attrs.functionUpdate ].dataupdate.preparedata.ids;
							if( ids !== null ){
								$.each( ids , function( k , v ){ if( v == deleted_id ) window[ attrs.functionUpdate ].dataupdate.preparedata.ids.splice( k , 1 ); } );
							}
							else { }
							
							var body = t.parents( '.blockform_type' );
							row = t.parents( '.blockform_rows' );
							
							if( $( row ).hasClass( 'selected' ) ){
								var fuc = window[ attrs.functionUpdate ].functionUpdateClear;
								$.each( fuc , function( k , v ){
									// if( window[ v ] !== undefined ) window[ v ].clear( );
								} );
							}
							
							$( row ).css( { 'background' : '#F00' } ).slideDown( 300 , function( ){
								$( this ).detach( );
								var body_rows = body.find( '.blockform_rows:not(.poditog)' );
								if( body_rows.length <= 0 ){
									window[ attrs.functionUpdate ].fasttable( t );
								}
								// обновляем данные // .. вызываем функцию обновления
								else {
									window[ attrs.functionUpdate ].update( false );
								}
							} );
							window[ attrs.functionUpdate ].update_fastpages( body );
						}
						else {
							
						}
					}
					else if( curReturnData.action == 'update' ){
						// console.log( 'curReturnData.update.f: ' + curReturnData.update.f + ' | ' + curReturnData.update.resultToLayerId );
						
						console.log( 'Update table after deleted ROW.' );
						
						window[ curReturnData.update.f ].fasttable( '#' + curReturnData.update.resultToLayerId );
						// $.each( curReturnData.update , function( k , v ){
						// 	console.log( 'K: ' + k + ' = ' + v );
						// } );
					}
					else {
						if( curReturnData.action == undefined ){
							t.html( curReturnData.html ).addClass( curReturnData.addclass ).removeClass( curReturnData.removeclass );
						}
						else {
							resultToLayerId = curReturnData.resultToLayerId ? curReturnData.resultToLayerId : attrs.resultToLayerId;
							resultToLayerId = $( '#' + resultToLayerId );
							
							// .. выполняем действие
							switch( curReturnData.action ){
								// .. layer | layerpopupclose | popupopen | popupclose | clear
								case 'layer' : resultToLayerId.html( curReturnData.html ); break;
								case 'layerpopupclose' : resultToLayerId.html( curReturnData.html ); formblock.dialog.off( ); break;
								case 'popupopen' : formblock.dialog.on( curReturnData.html ); break;
								case 'popupclose' : formblock.dialog.off( ); break;
								case 'clear' : resultToLayerId.html( '' ); break;
								default : break;
							}
							
							
							if( curReturnData.updated == true ){
								if( window[ attrs.functionUpdate ] )
									window[ attrs.functionUpdate ].update( );
									// window[ attrs.functionUpdate ].update_fastpages( );
							}
						}
					}
				} );
			}
			else {
				curReturnData = returnData;
				if( curReturnData.updated !== undefined || curReturnData.deleted_id !== undefined ){
					// .. удаляем ID из массива
					var deleted_id = curReturnData.deleted_id;
					ids = window[ attrs.functionUpdate ].dataupdate.preparedata.ids;
					if( ids !== null ){
						$.each( ids , function( k , v ){ if( v == deleted_id ) window[ attrs.functionUpdate ].dataupdate.preparedata.ids.splice( k , 1 ); } );
					}
					else { }
					
					var body = t.parents( '.blockform_type' );
					row = t.parents( '.blockform_rows ' );
					
					if( $( row ).hasClass( 'selected' ) ){
						var fuc = window[ attrs.functionUpdate ].functionUpdateClear;
						$.each( fuc , function( k , v ){
							// if( window[ v ] !== undefined ) window[ v ].clear( );
						} );
					}
					
					$( row ).css( { 'background' : '#F00' } ).slideUp( 300 , function( ){
						$( this ).detach( );
						var body_rows = body.find( '.blockform_rows' );
						// обновляем данные 
						if( body_rows.length == 0 ){
							window[ attrs.functionUpdate ].fasttable( t );
						}
						// .. вызываем функцию обновления
						else {
							window[ attrs.functionUpdate ].update( false );
						}
					} );
					window[ attrs.functionUpdate ].update_fastpages( body );
				}
				else {
					
				}
			}
		} ,
	} ,
	fastdeleteselected : {
		name : 'fastdeleteselected' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			var data_fastedit = t.data( 'prepare' );
			
			
			var table = t.parents( 'form' ).eq( 0 );
			var tbody = table.find( 'tbody' ).eq( 0 );
			var inputs = tbody.find( 'input[type=checkbox]:checked' );
			
			this.ajax = true;
			
			// .. проверяем выделенные элементы
			if( inputs.length <= 0 ){
				this.ajax = false;
				alert( 'Выберите элементы для удаления' );
				return false;
			}
			
			// .. Просим подтвердить удаление
			confirm1 = confirm( 'Are you sure?' );
			if( confirm1 == false ){
				alert( 'Actions is canceled!' );
				this.ajax = false;
				return false;
			}
			
			if( data_fastedit ){
				formblockP.layers( t , this.layers );
				formblockP.form( t , this.form , this.data , data_fastedit );
				formblockP.formdata( t , this.form , this.data , data_fastedit );
				formblockP.datafromform( t , this.form , this.data , data_fastedit );
			}
			else
				return false;
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_fastedit = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			// ..
			
			var rows = $( '#' + attrs.resultToLayerId + '_tablebody' ).find( '.blockform_rows' );
			var body = rows.eq( 0 ).parents( '.blockform_type' );
			
			var rowsnum = rows.length;
			var rowscount = 0;
			$.each( returnData , function( k , v ){
				
				var deleted_row = v[ 'deleted_row' ];
				var deleted_id = v[ 'deleted_id' ];
				
				window[ attrs.functionUpdate ].dataupdate.preparedata.ids.splice( ( deleted_row - rowscount ) , 1 );
				
				var row = rows.eq( deleted_row );
				$( row ).animate( { 'background-color' : '#F00' } , 300 , function( ){
					$( this ).slideUp( 300 , function( ){
						$( this ).detach( );
					} );
					
					
					// .. обновляем таблицу
					window[ attrs.functionUpdate ].update_fastpages( body );
				} );
				
				// ..
				pageactions.act( $( row ) , 'tablecheckboxes' , { 'checkname' : 'form[edit]' , 'mainname' : 'test' } );
				rowscount++;
			} );
			rowscurrent = rowsnum - rowscount;
			
			if( rowscurrent <= 0 ){		// .. перезагружаем таблицу
				window[ attrs.functionUpdate ].fasttable( body );
			} else {									// .. вызываем данные таблицы
				window[ attrs.functionUpdate ].update( false );
			}
		} ,
	} ,
	// ..
	// ..
	// ..
	fastcopy : {
		name : 'formblock : fastadd' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var data_prepare = t.data( 'prepare' );
			
			
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			formblockP.formdata( t , this.form , this.data , data_prepare );
			
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			if( returnData.action == 'popup' ){
				formblock.dialog.on( returnData.html );
			}
			else if( returnData.action == 'cancel' ){
				formblock.dialog.off( );
			}
			else if( returnData.action == 'save' ){
				
				
				if( typeof( returnData.id ) == 'number' && returnData.id != 0 ){
					
					var table = $( '#' + this.data.resultToLayerId ).parents( '[data-div="table"]' );
					var tbody = $( '#' + this.data.resultToLayerId );
					var tbodytr = tbody.find( '[data-div="tr"]' );
					var tfoot = table.find( '[data-div="tfoot"]' );
					var tfoottrempty = tfoot.find( '[data-div="tr"]' ).eq( 0 );
					var tfoottrnew = tfoot.find( '[data-div="tr"]' ).eq( 1 );
					
					
					tbody.removeClass( 'hide' ).append( returnData.html );
					if( tbodytr.length == 0 ){
						tfoottrempty.toggle( 300 , function( ){
							tfoottrempty.addClass( 'hide' );
						} );
					}
					else {
					}
					
					var tr = tbody.find( '.blockform_rows_new:last' );
					$( tr ).animate( { 'background-color' : '#' + $( tfoottrnew ).css( 'background-color' ) } , 2000 , function( ){
						$( this ).removeClass( 'blockform_rows_new' ).css( { 'background-color' : '' } );
					} );
					
					if( typeof( window[ attrs.functionUpdate ] ) === 'object' ){
						// console.log( '1: ' + window[ attrs.functionUpdate ].dataupdate.preparedata.ids );
						if( window[ attrs.functionUpdate ].dataupdate.preparedata.ids === null ){
							window[ attrs.functionUpdate ].dataupdate.preparedata.ids = [ ];
						}
						window[ attrs.functionUpdate ].dataupdate.preparedata.ids.push( returnData.id );
					}
					else {
						console.log( 'BAD' );
					}
					
					// ..
					pageactions.act( $( tr ) , 'tablecheckboxes' , { 'checkname' : 'form[edit]' , 'mainname' : 'test' } );
					
					// ..
					if( formblock.dialog.is( ) ) formblock.dialog.off( );
				}
				else {
					console.log( 'Incorrect input data!' );
				}
			}
			else {
				// ..
			}
		} ,
	} ,
	// .. fastfilters
	fastsort : {
		name : 'fastsort' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var data_prepare = t.data( 'prepare' );
			
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			formblockP.formdata( t , this.form , this.data );
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			if( returnData.action == 'reset' ){
				functionUpdate = data_prepare.functionUpdate;
				window[ attrs.functionUpdate ].fasttable( $( '#' + attrs.resultToLayerId  ) );
				formblock.dialog.off( );
			}
			else if( returnData.action == 'cancel' ){
				formblock.dialog.off( );
			}
			else if( returnData.action == 'save' ){
				functionUpdate = data_prepare.functionUpdate;
				window[ attrs.functionUpdate ].fasttable( $( '#' + attrs.resultToLayerId  ) );
				formblock.dialog.off( );
				
				// .. обновляем слои
				window[ functionUpdate ].update_fastpages( );
			}
			else if( returnData.action == 'popup' ){
				formblock.dialog.on( returnData.html );
			}
			else {
				console.log( 'Incorrect input data!' );
			}
		} ,
	} ,
	// .. fastfilters
	fastfilters : {
		name : 'Fastfilters' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var data_prepare = t.data( 'prepare' );
			
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			formblockP.formdata( t , this.form , this.data );
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			if( returnData.action == 'reset' ){
				functionUpdate = data_prepare.functionUpdate;
				window[ attrs.functionUpdate ].fasttable( $( '#' + attrs.resultToLayerId  ) );
				formblock.dialog.off( );
				
				// .. обновляем слои
				window[ functionUpdate ].update_fastpages( );
			} else if( returnData.action == 'save' ){
				functionUpdate = data_prepare.functionUpdate;
				window[ attrs.functionUpdate ].fasttable( $( '#' + attrs.resultToLayerId  ) );
				formblock.dialog.off( );
				
				// .. обновляем слои
				window[ functionUpdate ].update_fastpages( );
			} else if( returnData.action == 'savefastfilters' ){
				functionUpdate = data_prepare.functionUpdate;
				window[ attrs.functionUpdate ].fasttable( $( '#' + attrs.resultToLayerId  ) );
				
				// .. обновляем слои
				window[ functionUpdate ].update_fastpages( );
			} else if( returnData.action == 'cancel' ){
				formblock.dialog.off( );
			} else if( returnData.action == 'popup' ){
				formblock.dialog.on( returnData.html );
			}
			else {
				console.log( 'Incorrect input data!' );
			}
		} ,
	} ,
	// .. fastexport
	fastexport : {
		name : 'fastexport' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var data_prepare = t.data( 'prepare' );
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			formblockP.formdata( t , this.form , this.data );
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			if( returnData.action == 'delete_file' ){
				var d = $( '#' + returnData.deleted_id );
				
				var container = d.parents( 'div' );
				var empty = container.find( '.fastexport_div_empty' );
				
				d.remove( );
				var rows = container.find( '.fastexport_div' );
				
				console.log( 'Rows length: ' + rows.length );
				
				if( rows.length <= 0 ) empty.removeClass( 'hide' );
			}
			else if( returnData.action == 'popup' ){
				formblock.dialog.on( returnData.html );
			}
			else if( returnData.action == 'cancel' ){
				formblock.dialog.off( );
			}
			else if( returnData.action == 'save' ){
				var container = $( '#' + returnData.resultToLayerId );
				var empty = container.find( '.fastexport_div_empty' );
				
				empty.addClass( 'hide' );
				
				var idnew = container.find( '.fastexport_div.fastexport_div_new' );
				idnew.removeClass( 'fastexport_div_new' );
				
				container.append( returnData.html );
				var id = $( '#' + returnData.id );
				id.show( );
			}
			else {
				console.log( 'Incorrect input data!' );
			}
		} ,
	} ,
	// .. fastimport
	fastimport : {
		name : 'fastimport' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var data_prepare = t.data( 'prepare' );
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			formblockP.formdata( t , this.form , this.data );
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			if( returnData.action == 'shablons' ){
				window.open( returnData.html , '_target' );
			}
			else if( returnData.action == 'delete_file' ){
				var d = $( '#' + returnData.deleted_id );
				d.remove( );
			}
			else if( returnData.action == 'popup' ){
				formblock.dialog.on( returnData.html );
			}
			else if( returnData.action == 'cancel' ){
				formblock.dialog.off( );
			}
			else if( returnData.action == 'save' ){
				functionUpdate = data_prepare.functionUpdate;
				
				window[ attrs.functionUpdate ].fasttable( $( '#' + attrs.resultToLayerId  ) );
				
				formblock.dialog.off( );
				# var d = $( '#' + returnData.resultToLayerId );
				# 
				# var idnew = d.find( '.fastexport_div.fastexport_div_new' );
				# idnew.removeClass( 'fastexport_div_new' );
				# 
				# d.append( returnData.html );
				# var id = $( '#' + returnData.id );
				# id.show( );
			}
			else {
				console.log( 'Incorrect input data! ' + returnData.action );
			}
		} ,
	} ,
	// .. fastfilters
	inputchange : {
		name : 'Fastfilters' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
			var t = $( obj );
			
			var data_prepare = t.data( 'prepare' );
			
			formblockP.layers( t , this.layers );
			formblockP.form( t , this.form , this.data , data_prepare );
			formblockP.formdata( t , this.form , this.data , attrs );
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
			if( ! dataJSON ) return false;
			
			var returnError = dataJSON.returnError;
			var returnData = dataJSON.returnData;
			
			var t = $( obj );
			var data_prepare = t.data( 'prepare' );
			
			var toLayerId = $( attrs.toLayerId );
			
			// .. показываем сообщения
			mesagges_show( returnError , true );
			
			if( returnData.action == 'layer' ){
				toLayerId.html( returnData.html );
			}
			else if( returnData.action == 'save' ){
				functionUpdate = data_prepare.functionUpdate;
				window[ attrs.functionUpdate ].fasttable( $( '#' + attrs.resultToLayerId  ) );
				formblock.dialog.off( );
				
				// .. обновляем слои
				window[ functionUpdate ].update_fastpages( );
			}
			else if( returnData.action == 'cancel' ){
				formblock.dialog.off( );
			}
			else if( returnData.action == 'popup' ){
				formblock.dialog.on( returnData.html );
			}
			else {
				console.log( 'Incorrect input data!' );
			}
		} ,
	} ,
	update1 : {
		name : 'Updated' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		prepare : function( obj , attrs ){
		} ,
		beforeSend : function( obj , attrs ){
		} ,
		complete : function( obj , dataJSON , attrs ){
		} ,
	} ,
    */
   popup : {
		ajax : false , layers : { } , form : { } , data : { } ,
		dialogOn : false ,
		blockCount : 0 ,
		layers : function( ){
			/*
			this.layers.html = $( 'html' );
			this.layers.popup = $( '#tB_load' );	// tB_load
			this.layers.popup_left = $( '#tB_load .resultpos.resultleft' );
			this.layers.popup_right = $( '#tB_load .resultpos.resultright' );
			this.layers.popup_result = $( '#tB_load .result' );
			*/
			this.layers.html = $( 'html' );
			this.layers.popup = $( '.popupload' );	// tB_load
			this.layers.popup_bg = $( '.popupload .bg-nav' );
			this.layers.popup_bg_left = $( '.popupload .bg-nav .pos.left' );
			this.layers.popup_bg_right = $( '.popupload .bg-nav .pos.right' );
			this.layers.popup_result = $( '.popupload .result' );
			
			// return false;
		} ,
		on : function ( html ){
			this.layers( );
			this.layers.html.addClass( 'overflowHidden' );
			this.layers.popup.addClass( 'active' );
			this.layers.popup_bg_left.on( 'click' , function( ){ engine.load.popup.off( ); } );
			this.layers.popup_bg_right.on( 'click' , function( ){ engine.load.popup.off( ); } );
			this.layers.popup_result.html( html );

			// alert( 'POPUP: ON' );
		} ,
		off : function ( ) {
			this.layers.html.removeClass( 'overflowHidden' );
			this.layers.popup.removeClass( 'active' );
		}
   } ,
   html : function( obj , result ){
		html = result.html;
		tolayerid = $( '#' + result.layer );

		tolayerid.html( html );
		/*
		set : function ( result ){

		} ,
		insert : function ( result ) {

		} ,
		action : function ( result ) {
			if( result.layerpopup == true ){
				engine.popup.dialog.on( result.html , result.action );
			} else {
				$( '#' + result.layerto ).html( result.layerhtml );
			}
			// alert( 'engine.load.html.action | RESULT' );
		}
		*/
   } ,
   loaduno : {
		name : 'loaduno' ,
		t : this , ajax : true , layers : { } , form : { 'test' : 'This is test' , on : true } , data : { } , stop : false ,
		init : function( obj , attrs ){
            this.ajax = true;
			return true;
        } ,
        prepare : function( obj , attrs ){
            let t = $( this );
            let o = $( obj );
			
            let data_prepare = '';

			// engine.prepare.formcheck( o , this.form , this.data );
			// engine.prepare.form( o , this.form , this.data , data_prepare );
			// engine.prepare.formdata( o , this.form , this.data , attrs );
			// ###
			engine.prepare._form( t , o , this.form , this.data , attrs );
			
			// engine.prepare.formcheck( o , this.form , this.data );
			// engine.prepare.formeck( o , this.form , this.data );
			// engine.prepare.formcheck( o , this.form , this.data );
        } ,
        beforeSend : function( obj , attrs ){
        } ,
        complete : function( obj , dataJSON , attrs ){
			let t = $( this );
            let o = $( obj );
			
			console.log( 'dataJSON error: ' + dataJSON.error );
			console.log( 'dataJSON messages: ' + dataJSON.messages );
			console.log( 'dataJSON result: ' + dataJSON.result );
			console.log( 'dataJSON form: ' + dataJSON.form );

			error = dataJSON.error;
			messages = dataJSON.messages;
			result = dataJSON.result;
			// [ tolayer , html , popup ]
			form = dataJSON.form;
			
			if( messages != null ) engine.load.messages( messages );// .init( dataJSON.result );
			if( result != null ){
				result_popup = result.popup;
				result_tolayerid = result.layer;
				result_html = result.html;
					alert( 'result_popup: ' + result_popup );
				if( result_popup ){
					engine.popup.dialog.on( result );
				} else if( result_tolayerid ){
					engine.load.html( result_html );
				}
			}
			
			if( form ){
				// .. сбросить форму
				if( form.reset == true ) engine.load.load.form.form.find( 'input[type=reset]' ).click( );
				// .. перенаправление
				if( form.redirect ){
					if( form.redirect_time ){
						setTimeout( function( ){
							window.location.href = form.redirect;
						} , form.redirect_time ); // 1000 milliseconds equals 1 second
					} else window.location.href = form.redirect;
				}
			}
			return false;
        }
    } ,
   load : {
		name : 'load' ,
		o : this , ajax : true , layers : { } , form : { 'test' : 'This is test' , on : true } , data : { } ,
        init : function( obj , attrs ){
            this.ajax = true;

			return true;
        } ,
        prepare : function( obj , attrs ){
            let t = this;
            let o = obj;
            
            let data_prepare = '';

			engine.prepare._form( t , o , this.form , this.data , attrs );
			// engine.prepare.formcheck( o , this.form , this.data );
			// engine.prepare.form( o , this.form , this.data , data_prepare );
			// engine.prepare.formdata( o , this.form , this.data , attrs );
        } ,
        beforeSend : function( obj , attrs ){
        } ,
        complete : function( obj , dataJSON , attrs ){
			console.log( '[ DEBUG ][ dataJSON error ] ' + dataJSON.error );
			console.log( '[ DEBUG ][ dataJSON messages ] ' + dataJSON.messages );
			console.log( '[ DEBUG ][ dataJSON release ] ' + dataJSON.release );
			console.log( '[ DEBUG ][ dataJSON form ] ' + dataJSON.form );
			console.log( '[ DEBUG ][ dataJSON referer ] ' + dataJSON.referer );

			error = dataJSON.error;
			messages = dataJSON.messages;
			release = dataJSON.release;
			// [ tolayer , html , popup ]
			form = dataJSON.form;
			
			if( messages != null ) engine.load.messages( messages );// .init( dataJSON.result );
			
			if( release != null ){
				console.log( '[ DEBUG ][ release : html ] ' + release.html );
				console.log( '[ DEBUG ][ release : layer ] ' + release.layer );
				console.log( '[ DEBUG ][ release : popup ] ' + release.popup );
				release_popup = release.popup;
				if( release_popup == true ){
					engine.popup.dialog.on( obj , release );
				} else {
					engine.load.html( obj , release );
				}
			}

			if( form ){
				console.log( '[ DEBUG ][ form : reset ] ' + form.reset );
				console.log( '[ DEBUG ][ form : redirect ] ' + form.redirect );
				console.log( '[ DEBUG ][ form : redirect_time ] ' + form.redirect_time );
				// .. сбросить форму
				if( form.reset == true ) engine.load.load.form.form.find( 'input[type=reset]' ).click( );
				// .. перенаправление
				if( form.redirect ){
					if( form.redirect_time ){
						setTimeout( function( ){
							window.location.href = form.redirect;
						} , form.redirect_time ); // 1000 milliseconds equals 1 second
					} else window.location.href = form.redirect;
				}
			}
        }
    } ,
	messages : function( messages ){
		$( '.tB_messages' ).html( $( '.tB_messages' ).html( ) + messages );
		mesagges_show( true , true );
	} ,
    // .. ajax
	ajax : function( obj , action , attrs ){
		// .. переменные
		var dataJSON;
		var form;
		var actionName = action;
		tmp_action = action;

		action = this[ action ];

        if( action.init( obj , attrs ) == false ) return false;
        // .. подготовительные функции
		action.prepare( obj , attrs );

		var layers = [ ];
		// formblockP.layers( t , layers );
		
		if( action.ajax == true ){
			// .. AJAX запрос
			jQuery.ajax( {
				async : action.form.async ,
				url : action.form.action ,
				dataType : action.form.dataType ,
				type : action.form.method ,
				data : action.form.data ,
				cache : false ,
				contentType : action.form.contentType == false ? false : "application/x-www-form-urlencoded; charset = UTF-8" ,
				processData : false ,
				xhrFields: {
					withCredentials: true // Обязательно для передачи кук
				},
				beforeSend : function( ){
					console.log( '[ DEBUG ][ url ] ' + this.url );
					// ## formblockP.info.scriptTimeLoading.d_start = new Date( ).getTime( );
					// .. показываем PRELOAD
					// ## layers.tb_preload.addClass( 'active' ).show( );
					action.beforeSend( obj , attrs );
					// .. debug
					// console.log( '> ' + tmp_action + '\n >> url: ' + this.url + ' [ ' + this.type + ' ]' );
					console.log( '[ ' + tmp_action + ' ] [ url: ' + this.url + ' ] [ type: ' + this.type + ' ]' );
				} ,
				complete : function( msg , status ){
					if( ! dataJSON ) return false;
					action.complete( obj , dataJSON , attrs );
					dinamic_prepare( );
				} ,
				success : function( dataLoaded ){
					// console.log( '> success: ' + JSON.stringify( dataLoaded ) );
					
					// .. если ничего не получили - выходим из цикла
					if( dataLoaded == false ) return false ;
					
					// console.log( '[ ' + tmp_action + ' ] Данные успешно загружены! [ ' + JSON.stringify( dataLoaded ).length + ' bytes ] [ ' + JSON.stringify( dataLoaded ) + ' ]' );
					// ## console.log( '[ ' + tmp_action + ' ] Данные успешно загружены! [ ' + JSON.stringify( dataLoaded ).length + ' bytes ]' );
					
					// .. обновляем информацию о странице
					// ## formblockP.updatePageTime( layers , this.data , JSON.stringify( dataLoaded ).length );
					// .. преобразуем данные
					if( this.dataType == 'JSON' ) dataJSON = dataLoaded;
					else dataJSON = JSON.parse( dataLoaded );
					// .. скрываем PRELOAD
					// ## layers.tb_preload.removeClass( 'active' ).hide( );
				} ,
				error : function( jqXHR , textStatus , errorThrown ){
					console.log( '> ' + tmp_action + ' : Error synchronisation data\n >> url : ' + this.url + '\n >> textStatus : ' + textStatus + '\n >> errorThrown : ' + errorThrown + '' );
					// $.each( jqXHR , function( k , v ){ console.log( actionName + ' : Error synchronisation data. Form [ jqXHR : ' + k + ' = ' + v + ' ]' ); } );
				}
			});
		}
		
		return false;
	} ,
	a : function( obj , action , attrs ){
        // if( typeof obj == 'string' ) obj = $( obj );
        
        this.data = { };
		this.form = { };
		

		if( ! this[ action ] ){
			console.log( 'Нет вызванной функции: ' + action + '.' );
			return;
		} else {
            return this.ajax( obj , action , attrs );
        }
	}
};
