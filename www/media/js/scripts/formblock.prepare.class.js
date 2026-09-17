/*
	.. javascript
	.. Formblock
	..
*/
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
	
	/*
	if( obj_name == mainname ){
		is_checked = obj.is( ':checked' );
		checkboxes.prop( 'checked' , is_checked ); 
	}
	else {
		// console.log( 'main checked : ' + all_checked );
		checkbox_main = form.find( 'input[name*="' + mainname + '"]' );
		checkbox_main.prop( 'checked' , all_checked );
	}
	*/
	
	if( obj_name == mainname ){
		is_checked = obj.is( ':checked' );
		
		checkboxes.each( function( i , elem ){
			formblockP.userform.onClick( $( elem ) , { 'checked' : is_checked } )
		} );
	}
	else {
		checkbox_main = form.find( 'input[name*="' + mainname + '"]' );
		formblockP.userform.onClick( $( checkbox_main ) , { 'checked' : all_checked } )
	}
}

formblockP = {
	info : { 'install' : true , 'scriptSize' : 0 , 'scriptTimeLoading' : { 'scriptSizeAll' : 0 , 'd_all_' : 0 } } ,
	updatePageTime : function( layers , data , pageLength ){
		this.info.scriptSize = pageLength;
		
		var d_ = 0;
		this.info.scriptTimeLoading.d_end = new Date( ).getTime( );
		
		var scriptSize = Number( parseFloat( this.info.scriptSize / 1024 ).toFixed( 0 ) );
		d_ = ( this.info.scriptTimeLoading.d_end - this.info.scriptTimeLoading.d_start );
		d_.toFixed ( 0 );
		this.info.scriptTimeLoading.d_all_ += d_;
		this.info.scriptTimeLoading.d_all_.toFixed ( 0 );
		this.info.scriptTimeLoading.scriptSizeAll += scriptSize;
		// this.info.scriptTimeLoading.scriptSizeAll.toFixed( 0 );
		// .. обновляем
		layers.err_updated.html( ' <span class="err1">' + d_ + 'мс</span>/' + this.info.scriptTimeLoading.d_all_ + 'мс ; <span class="err1">' + scriptSize + 'кб</span>/' + this.info.scriptTimeLoading.scriptSizeAll + 'кб' );
	} ,
	layers : function( obj , layers ){
		if( ! obj ) return;
		t = $( obj );
		
		
		layers.messages = $( '#tB_messages' );											// .. layerErrorMessages
		layers.tb_preload = $( '#tb_preload' );												// .. layerErrorMessages
		layers.tbload = $( '#tB_load' );																// .. load
		layers.tbload_fon = layers.tbload.find( '.fon' ).eq( 0 );					// .. resultToLayer_load_result
		layers.tbload_result = layers.tbload.find( '.result' ).eq( 0 );		// ..resultToLayer_load_result
		layers.tbload_datablock = layers.tbload_result.find( '.datablock' ).eq( 0 );		// ..resultToLayer_load_result
		
		layers.err_updated = $( '#err_updated' );
	} ,
	form : function( obj , form , data , data_prepare='' ){
		// .. object , vars
		var t = $( obj );
		var return_form = {};
		// .. data
		form.form = t.parents( 'form' );
		form.form_bsubmit = form.form.find( 'input[type=submit]' );
		
		// .. отключаем форму
		form.form.on( 'submit' , function( ){return false;} );
		
		// .. подготавливаем данные
		
		form.async = true;
		form.method = 'POST';
		form.dataType = 'JSON';
		// form.contentType = 'application/x-www-form-urlencoded';
		form.processData = false;
		form.contentType = false;
		
		// .. info
		// info.updateScript = {}; scriptTimeLoading
		// this.info.scriptTimeLoading.d_start = new Date( ).getTime( );
		
		
		// .. data_prepare : [ свойста , preparedata[ свойства , .. ] ]
		// .. data
		// .. data[ свойства ]
		// .. data.data_prepare = [ свойства , preparedata[ свойства ] ]
		if( data_prepare ){
			form.action = data_prepare.href;
			// ..
			if( data_prepare.preparedata ){
							
				data.data_prepare = data_prepare;
				$.each( data_prepare , function( k , v ){
					data[ k ] = v;
				});
				// console.log( 'START' );
				$.each( data_prepare.preparedata , function( k , v ){
					data[ k ] = v;
				});
			}
			
		}
		
		form.formdata = new FormData( );
		// return rform;
	} ,
	// ..
	formdata : function( obj , form , data , userdata='' , spec=false){
		// .. object , vars
		var t = $( obj );
		form.data = '';
		
		// .. добавляем кнопку Submit
		form.formdata.append( 'submit' , 'submit' );
		var formtype = t.parents( 'form' ).eq( 0 ).attr( 'formtype' );
		var formupload = t.parents( 'form' ).eq( 0 ).attr( 'formupload' );
		
		var fields = t.data( 'prepare-fields' );
		if( typeof fields == 'object' ){
			
			if( typeof fields.vals == 'object' ){
				$.each( fields.vals , function( k , v ){
					input = t.parents( 'form' ).find( 'input[name="' + v + '"]' );
					if( input != 'undefined' ){
						data.data_prepare.preparedata[ v ] = input.val( );
					}
				} );
			}
			
			if( typeof fields.reset == 'object' ){
				$.each( fields.reset , function( k , v ){
					input = t.parents( 'form' ).find( 'input[name="' + v + '"]' );
					if( input != 'undefined' ){
						formblockP.userform.reset( input );
					}
				} );
			}
		}
		
		// .. файлы
		if( ( formtype != 'FILES' && formupload != true ) || spec == true ){
			if( form.formdata ){
				if( data ){
					if( data.data_prepare ){
						$.each( data.data_prepare.preparedata , function( k , v ){
							$name = k;
							$value = v;
							form.formdata.append( [$name] , $value );
							// console.log( '! preparedata: ' + $name + ' | ' + form.formdata.get( [$name] ) );
						} );
					}
				}
				
				if( $( obj ).data( 'prepare' ) ){
					$prepare = $( obj ).data( 'prepare' );
					if( $prepare.preparedata.resultToLayerId ){
						var resultToLayerId = $( '#' + $prepare.preparedata.resultToLayerId );
						// console.log( 'Before: ' + resultToLayerId.data( 'prepare' ) );
						resultToLayerId.data( 'prepare' , $prepare );
						// console.log( 'After: ' + resultToLayerId.data( 'prepare' ) );
					}
				}
				// $.each( $( obj ).data( 'prepare' ) , function( k , v ){
				// 	console.log( k + ' = ' + v );
				// } );
				
				if( userdata ){
					// if( userdata.preparedata != undefined ){
						$.each( userdata , function( k , v ){
							// console.log( 'userdata: ' + k + ' = ' + v );
							form.formdata.append( k , v );
						} );
					// }
				}
			}
			else alert( 'Bad form.formdata!' );
			
			form.data = form.formdata;
			
			// console.log( '--- formAction: ' + form.action );
			// for( var pair of form.formdata.entries() ) {
			// 	console.log( 'form.formdata.entries: ' + pair[0]+ ' = '+ pair[1] );
			// }
		}
		else if( formtype == 'FILES' ){
			formblockP.datafromform( t , form , data , userdata );
		}
		else if( formupload != false ){
			formblockP.datafromform( t , form , data , userdata );
		}
		else {
			formblockP.datafromform( t , form , data , userdata );
		}
		
	} ,
	// .. преобразуем данные формы в строку
	datafromform : function( obj , form , data , userdata='' ){
		// .. object , vars
		var t = $( obj );
		
		// .. устанавливаем URL
		form.action = form.form.prop( 'action' );
		// .. contentType
		form.contentType = false;
		
		var formType = form.form.attr( 'formtype' );
		if( formType == 'FILES' ){
			// 
			if ( window.FormData === undefined ){
				alert( 'В вашем браузере FormData не поддерживается' );
			}
			else {
				/*
				file = form.form.find( 'input[type=file]' )[0].files[0];
				form.formdata.append( 'form[fastedit][file]' , file );
				*/
				$files = form.form.find( 'input[type=file]' );
				for( q = 0 ; q < $files.length ; q++ ){
					file = $files[q];
					files = file.files;
					
					for( q1 = 0 ; q1 < files.length ; q1++ ){
						input = files[q1];
						form.formdata.append( [file.name] , input );
					}
					
				}
			}
		}
		else {
		}
		
		// .. добавляем кнопку Submit
		form.formdata.append( 'form[submit]' , 'submit' );
		
		els = form.form.find( 'input , textarea , select' );
		$.each( els , function( k , v ){
			// console.log( 'el: ' + v.name + ' = ' + v.value );
			var $type = $( this ).attr( 'type' );
			var $name = v.name;
			// console.log( 'name: ' + $name + ' | type: ' + $type );
			// if( form.formdata.has( [$name] ) == false ){
			if( true ){
				if( $type == 'checkbox' ){
					$checked = $( this ).is( ':checked' );
					
					if( $checked == true ) var $value = v.value;
					else var $value = '';
					
					form.formdata.append( [$name] , $value );
				}
				else if( $type == 'radio' ){
					$checked = $( this ).is( ':checked' );
					
					if( $checked == true ){
						var $value = v.value;
						form.formdata.append( [$name] , $value );
					}
				}
				else {
					var $value = v.value;
					form.formdata.append( [$name] , $value );
				}
				
			}
			
		} );
		if( data ){
			if( data.href ){
				form.action = data.href;
			}
			
			if( data.data_prepare ){
				$.each( data.data_prepare.preparedata , function( k , v ){
					$name = k;
					$value = v;
					
					if( v != 'undefined' ){
						form.formdata.append( [$name] , $value );
					}
					// console.log( 'preparedata: ' + form.formdata.get( [$name] ) );
				} );
			}
		}
		
		if( userdata ){
			$.each( userdata , function( k , v ){
				$name = k;
				$value = v;
				
				if( v != 'undefined' ){
					form.formdata.append( [$name] , $value );
				}
				// console.log( 'preparedata: ' + form.formdata.get( [$name] ) );
			} );
		}
		
		// ..
		form.data = form.formdata;
	} ,
	setActionsub : function( obj , val ){
		t = $(obj);
		form = t.parents( 'form' ).eq( 0 );
		actionsub = form.find('input[name="form[actionsub]"]').eq(0);
		actionsub.val( val ); 
	} ,
	// ..
	userform : {
		focus : function( obj ){
			var t = $( obj );
			
			var el = t.parents( '.el' );
			var readonly = el.data( 'readonly' );
			if( readonly == '1' ) return false;
			
			var type = el.data( 'type' );
			var subtype = el.prop( 'type' );
			
			if( type == 'checkbox' || type == 'radio' ){
				var spanlabeltext = t.parents( '.span-label-text' );
				spanlabeltext.addClass( 'focused' );
				return ;
			}
			else if( type == 'select' ){
				inplabel = el.find( '.elinput input.elInputText' ).eq( 0 );
				inplabel.focus( );
			}
		} ,
		on : function( obj ){
			var t = $( obj );
			
			var el = t.parents( '.el' );
			var readonly = el.data( 'readonly' );
			if( readonly == '1' ) return false;
			
			var type = el.data( 'type' );
			var subtype = el.prop( 'type' );
			
			if( type == 'input-checkbox' || type == 'checkbox' || type == 'input-radio' || type == 'radio' ){
				var spanlabeltext = t.parents( '.span-label-text' );
				spanlabeltext.addClass( 'focused' );
				
				return ;
			}
			else if( type == 'select' ){
				
				var subtype = ( el.hasClass( 'select' ) || el.hasClass( 'search' ) ) ? 'select' : 'text';
				
				if( subtype == 'select' ){
			
					el.removeClass( 'hidden' );
					
					// var inpval = el.find( '.elinput input[type=hidden]' );
					// var inplabel = el.find( '.elinput input[type=text]' );
					var inpval = el.find( '.elinput input.elInputVal' );
					var inplabel = el.find( '.elinput input.elInputText' );
					
					// inplabel.off( 'select' );
					
					var defval = inpval.val( );
					var deflabel = inplabel.val( );
					
					inpval.data( 'def' , { 'label' : deflabel , 'val' : defval } );
					
				}
				else if( subtype == 'text' ){
					el.removeClass( 'hidden' );
				}
			}
			// return false;
		} ,
		off : function( obj , opts=false ){
			var t = $( obj );
			
			var el = t.parents( '.el' );
			var elselectbox = el.find( '.elselectbox' );
			
			el.addClass( 'hidden' );
			
			var fastedit = el.data( 'fastedit' );
			var readonly = el.data( 'readonly' );
			var ttype = el.data( 'type' );
			
			var elinput = el.find( '.elbox .elinput' );
			var elinputval = elinput.find( 'input.elInputVal' );
			var elinputlabel = elinput.find( 'input.elInputText' );
			
			// .. если элемент только для чтения
			if( readonly ){
				if( fastedit ){
					formblock.a( $( elinputlabel ) , 'fastactions' , { functionUpdate : opts.functionUpdate , action : 'complete' } );
				}
				return false;
			}
			// .. если элемент можно изменять
			else {
				// .. если элемнт для быстрого редактирования
				if( fastedit ){
					
					// formblock.a( $( elinputlabel ) , 'fastactions' , { functionUpdate : opts.functionUpdate , action : 'complete' } );
					
					return false;
				}
				// .. 
				else {
					if( opts.act ){
						if( opts.act == 'change' ){
							formblock.a( $( obj ) , 'fastactions' , { functionUpdate : opts.functionUpdate , action : 'complete' } );
						}
						else if( opts.act == 'inputsearch' ){
							// var inpval = el.find( '.elinput input[type=hidden]' );
							// var inplabel = el.find( '.elinput input[type=text]' );
							var elinput = el.find( '.elinput' );
							var inpval = elinput.find( 'input.elInputVal' );
							var inplabel = elinput.find( 'input.elInputText' );
							
							if( inpval.data( 'def' ) ){
								var defval = inpval.data( 'def' ).val;
								var deflabel = inpval.data( 'def' ).label;
							}
							inpval.val( defval );
							inplabel.val( deflabel );
						}
					}
					
					return ;
				}
				
				if( type == 'input-checkbox' || type == 'checkbox' || type == 'input-radio' || type == 'radio' ){
					var spanlabeltext = t.parents( '.span-label-text' );
					spanlabeltext.removeClass( 'focused' );
					return ;
				}
				
				if( opts == false ){
					// var inpval = el.find( '.elinput input[type=hidden]' );
					// var inplabel = el.find( '.elinput input[type=text]' );
					var inpval = el.find( '.elinput input.elInputVal' );
					var inplabel = el.find( '.elinput input.elInputText' );
					
					if( inpval.data( 'def' ) ){
						var defval = inpval.data( 'def' ).val;
						var deflabel = inpval.data( 'def' ).label;
					}
					
					inpval.val( defval );
					inplabel.val( deflabel );
				}
			}
			
			return false;
		} ,
		select : function( obj ){
			var t = $( obj );
			
			if( t.hasClass( 'readonly' ) ) return false;
			
			
			
			var el = t.parents( '.el' );
			var elinput = el.find( '.elbox .elinput' );
			var elinputval = elinput.find( 'input.elInputVal' );
			var elinputlabel = elinput.find( 'input.elInputText' );
			var elselectbox = el.find( '.elselectbox' );
			var elselectbox_li = elselectbox.find( '.elselectbox_li' );
			
			elselectbox_li.removeClass( 'selected' );
			t.parents( '.elselectbox_li' ).eq( 0 ).addClass( 'selected' );
			
			var val = t.data( 'val' );
			var label = t.data( 'label' ) ? t.data( 'label' ) : t.text( );
			
			elinputval.val( val );
			elinputlabel.val( label );
			
			elinputval.data( 'def' , { 'label' : label , 'val' : val } );
			elinputlabel.data( 'val' , { val } );
			// ..
			var func = elinputlabel.data( 'func' );
			if( func != undefined ){
				// formblockP.userform.func( t );
			}
			elinputlabel.focus( );
			
			// ..
			formblockP.userform.fields( t );
			
			this.off( obj , true );
		} ,
		selectmulty : function( obj , inputName ){
			var t = $( obj );
			
			if( t.hasClass( 'readonly' ) ) return false;
			
			
			// ..
			formblockP.userform.fields( t );
			
			var el = t.parents( '.el' );
			// .. elinput
			var elinputcontainer = el.find( '.elinputcontainer' );
			var elinputcontainervals = elinputcontainer.find( '.elinputcontainerval' );
			var elinputcontainervals_input = elinputcontainer.find( '.elinputcontainerval input' );
			var elinputcontainervals_input_deleted = elinputcontainer.find( '.elinputcontainerval input[value="' + t.data( 'val' ) + '"]' );
			var inplabel = el.find( '.elinput input.elInputText' );
			
			// .. elselect
			var elselectbox = el.find( '.elselectbox' );
			var elselectbox_li  = elselectbox.find( '.elselectbox_li ' );
			
			var current_li = t.parents( '.elselectbox_li' );	// .. текущий элемент
			
			if( current_li.hasClass( 'selected' ) ){
				// .. удаляем элемент
				elinputcontainervals_input_deleted.parents( '.elinputcontainerval' ).remove( );
				// .. удаляем выделение
				current_li.removeClass( 'selected' );
			}
			else {
				// .. добавляем элемент
				var newhtml = '<span class="elinputcontainerval" onclick="return formblockP.userform.removemulty( this );"><input type="hidden" name="' + inputName + '" value="' + t.data( 'val' ) + '" class="elInputVal">' + t.text( ) + '</span>';
				console.log( newhtml );
				elinputcontainer.append( newhtml );
				// .. добавляем выделение
				current_li.addClass( 'selected' );
			}
			
			inplabel.focus( );
			
			// var inpval = el.find( '.elinput input[type=hidden]' );
			// var inplabel = el.find( '.elinput input[type=text]' );
			
			/*
			var val = t.data( 'val' );
			var label = t.text( );
			
			inpval.val( val );
			inplabel.val( label );
			
			inpval.data( 'def' , { 'label' : label , 'val' : val } );
			inplabel.data( 'val' , { val } );
			*/
			// ..
			var func = inplabel.data( 'func' );
			if( func != undefined ){
				formblockP.userform.func( t );
			}
			
			// this.off( obj , true );
		} ,
		selecttext : function( obj ){
			var t = $( obj );
			
			// ..
			formblockP.userform.fields( t );
			
			var el = t.parents( '.el' );
			if( t.data( 'readonly' ) == true ) return false;
			
			// .. elinput
			var elinputcontainer = el.find( '.elinputcontainer' );
			var elinputcontainervals = elinputcontainer.find( '.elinputcontainerval' );
			var elinputcontainervals_input = elinputcontainer.find( '.elinputcontainerval input' );
			var elinputcontainervals_input_deleted = elinputcontainer.find( '.elinputcontainerval input[value="' + t.data( 'val' ) + '"]' );
			var inplabel = el.find( '.elinput input.elInputText' );
			
			
			var elname = inplabel.data( 'inputname' ) + '[]';
			var elvalue = inplabel.val( );
			
			console.log( 'elname: ' + elname + ' | elvalue: ' + elvalue );
			
			if( elvalue != '' ){
				text = '<span class="elinputcontainerval" onClick="return formblockP.userform.removemulty( this );"><input type="hidden" name="' + elname + '" value="' + elvalue + '" class="elInputVal" />' + elvalue + '</span>';
				console.log( 'text: ' + text );
				
				elinputcontainer.append( text );
				
				inplabel.val( '' );
			}
			else {
			}
			
			inplabel.focus( );
			
			var func = inplabel.data( 'func' );
			if( func != undefined ){
				formblockP.userform.func( t );
			}
			
			// this.off( obj , true );
		} ,
		removemulty : function( obj  , opts=false ){
			var t = $( obj );
			
			// ..
			formblockP.userform.fields( t );
			
			var el = t.parents( '.el' );
			if( el.hasClass( 'readonly' ) ) return false;
			
			// .. elinput
			var elinput = el.find( '.elinput' );
			var elinputcontainer = elinput.find( '.elinputcontainer' );
			var elinputcontainervals = elinputcontainer.find( '.elinputcontainerval' );
			var elselect = el.find( '.elselect' );
			var elselectbox = elselect.find( '.elselectbox' );
			var inplabel = elinput.find( 'input.elInputText' );
			
			/*
			// .. elselect
			var elselectbox = el.find( '.elselectbox' );
			var elselectbox_li  = elselectbox.find( '.elselectbox_li ' );
			
			var current_li = t.parents( '.elselectbox_li' );	// .. текущий элемент
			
			if( current_li.hasClass( 'selected' ) ){
				// .. удаляем элемент
				elinputcontainervals_input_deleted.parents( '.elinputcontainerval' ).remove( );
				// .. удаляем выделение
				current_li.removeClass( 'selected' );
			}
			else {
				// .. добавляем элемент
				var newhtml = '<span class="elinputcontainerval"><input type="hidden" name="' + inputName + '" value="' + t.data( 'val' ) + '" class="elInputVal">' + t.text( ) + '<span class="delete" onclick="return formblockP.userform.removemulty( this );"></span></span>';
				console.log( newhtml );
				elinputcontainer.append( newhtml );
				// .. добавляем выделение
				current_li.addClass( 'selected' );
			}
			
			inplabel.focus( );
			
			// var inpval = el.find( '.elinput input[type=hidden]' );
			// var inplabel = el.find( '.elinput input[type=text]' );
			
			*/
			// ..
			var type = el.data( 'type' );
			
			if( type == 'text' ){
			}
			else if( type == 'select' ){
				var subtype = ( el.hasClass( 'select' ) || el.hasClass( 'search' ) ? 'select' : 'text' );
				
				if( subtype == 'select' ){
					var value = t.find( 'input' ).val( );
					
					var li = elselectbox.find( 'span a[data-val="' + value + '"]' );
					console.log( 'li: ' + li.text( ) );
					li.parents( 'span.elselectbox_li' ).removeClass( 'selected' );
					
					t.remove( );
					
					inplabel.focus( );
				}
				else if( subtype == 'text' ){
					// var elinputcontainerval = t.parents( '.elinputcontainerval' );
					t.remove( );
					
					inplabel.focus( );
				}
			}
			
			var func = inplabel.data( 'func' );
			if( func != undefined ){
				formblockP.userform.func( t );
			}
			
			// this.off( obj , true );
		} ,
		func: function( obj , opts ){
			var t = $( obj );
			
			var el = t.parents( '.el' );
			
			// var inpval = el.find( '.elinput input[type=hidden]' );
			// var inplabel = el.find( '.elinput input[type=text]' );
			var inpval = el.find( '.elinput input.elInputVal' );
			var inplabel = el.find( '.elinput input.elInputText' );
			
			// ..
			var func = inplabel.data( 'func' );
			if( func != undefined ){
				if( func[ 0 ] == 'change' ){
					formblock.pageactions.a( inpval , 'popup_formblock_change' );
				}
			}
		} ,
		onKeyup : function( obj , opts='' ){
			var t = $( obj );
			
			if( t.hasClass( 'readonly' ) ) return false;
			
			/*
			var el = t.parents( '.el' );
			var elselectbox = el.find( '.elselectbox' );
			var elselectbox_span = elselectbox.find( 'span' );
			var elselectbox_span_selected = elselectbox.find( 'span.selected' );
			
			var elinput = el.find( '.elinput' );
			// var inpval = el.find( '.elinput input[type=hidden]' );
			// var inplabel = el.find( '.elinput input[type=text]' );
			var inpval = el.find( '.elinput input.elInputVal' );
			var inplabel = el.find( '.elinput input.elInputText' );
			*/
			
			var el = t.parents( '.el' );
			// .. elinput
			var elinput = el.find( '.elinput' );
			var elinputcontainer = elinput.find( '.elinputcontainer' );
			var elinputcontainervals = elinputcontainer.find( '.elinputcontainerval' );
			var elinputcontainervals_input = elinputcontainer.find( '.elinputcontainerval input' );
			var elinputcontainervals_input_deleted = elinputcontainer.find( '.elinputcontainerval input[value="' + t.data( 'val' ) + '"]' );
			var inpval = el.find( '.elinput input.elInputVal' );
			var inplabel = el.find( '.elinput input.elInputText' );
			
			// .. elselect
			var elselectbox = el.find( '.elselectbox' );
			var elselectbox_li  = elselectbox.find( '.elselectbox_li' );
			var elselectbox_li_selected  = elselectbox.find( '.elselectbox_li.selected' );
			
			var ttype = t.prop( 'type' );
			var eltype = el.data( 'type' );
			var readonly = el.data( 'readonly' );
			var disabled = el.data( 'disabled' );
			var multy = el.data( 'multy' );
			
			
			var e = event;
			
			switch( e.keyCode ){
				default :
					if( e.keyCode == 38 || e.keyCode == 40 ) return false;
					
					if( opts.act == 'change' || opts.act == 'inputsearch' ){
						var fastedit = el.data( 'fastedit' );
						var multiple = el.data( 'multy' );
						var adding_data = { };
						
						if( multiple ){
							$.each( inpval , function( k , v ){
								var o = $( v );
								name = t.data( 'inputname' ) ? t.data( 'inputname' ) + '[' + k + ']' : o.prop( 'name' );
								value = o.val( );
								adding_data[ name ] = value;
								
								console.log( '! ' + name + ' = ' + value );
							} );
						}
						else {
							var name = inpval.prop( 'name' );
							var value = inpval.val( );
							
							adding_data[ name ] = value;
						}
						
						// console.log( 'Name: ' + name + ' | value: ' + value );
						prepare = inplabel.data( 'prepare' );
						
						if( opts ){
							if( prepare ){
								// $.extend( true , prepare.preparedata , { [name] : value } );
								$.extend( true , prepare.preparedata , adding_data );
								
								inplabel.data( 'prepare' , prepare );
							}
							
							// ..
							formblock.a( $( obj ) , 'fastactions' , { functionUpdate : opts.functionUpdate , action : opts.act } );
							
							// elselectbox.css( { 'opacity' : 1 } );
						}
					}
					return false;
					break;
			}
			
			return true;
		} ,
		onKeydown : function( obj , opts='' ){
			var t = $( obj );
			
			/*
			var el = t.parents( '.el' );
			var elselectbox = el.find( '.elselectbox' );
			var elselectbox_span = elselectbox.find( 'span' );
			var elselectbox_span_selected = elselectbox.find( 'span.selected' );
			*/
			
			var el = t.parents( '.el' );
			var elfon = el.find( '.elfon' );
			// .. elinput
			var elinput = el.find( '.elinput' );
			var elinputcontainer = elinput.find( '.elinputcontainer' );
			var elinputcontainervals = elinputcontainer.find( '.elinputcontainerval' );
			var elinputcontainervals_input = elinputcontainer.find( '.elinputcontainerval input' );
			var elinputcontainervals_input_deleted = elinputcontainer.find( '.elinputcontainerval input[value="' + t.data( 'val' ) + '"]' );
			var inpval = el.find( '.elinput input.elInputVal' );
			var inplabel = el.find( '.elinput input.elInputText' );
			
			// .. elselect
			var elselectbox = el.find( '.elselectbox' );
			var elselectbox_li  = elselectbox.find( '.elselectbox_li' );
			var elselectbox_li_selected  = elselectbox.find( '.elselectbox_li.selected' );
			
			var ttype = t.prop( 'type' );
			var eltype = el.data( 'type' );
			var readonly = el.data( 'readonly' );
			var disabled = el.data( 'disabled' );
			var multy = el.hasClass( 'multy' );
			
			var e = event;
			
			if( ttype == 'checkbox' || ttype == 'radio' ){
				// .. пробел
				if( e.keyCode == '32' ){
					var checked = t.is( ':checked' );
					var check = t.parents( '.span-label-text' ).find( '.check' );
					
					if( checked ){
						t.prop( 'checked' , false );
						check.removeClass( 'on' );
					}
					else {
						t.prop( 'checked' , true );
						check.addClass( 'on' );
					}
					
					// ..
					if( t.data( 'tablecheckboxes' ) == true ){
						// ..
						pageactions.act( $( t ) , 'tablecheckboxes' , { 'checkname' : 'form[edit]' , 'mainname' : 'test' } );
					}
				}
				
				return;
			}
			
			if( eltype == 'text' || eltype == 'input-text' ){
			}
			else if( eltype == 'select' ){
				
				var subtype = ( el.hasClass( 'search' ) || el.hasClass( 'select' ) ? 'select' : 'text' );
				
				switch( e.keyCode ){
					case 38 :	// .. up
					case 40 : // .. down
						
						if( readonly ) return false;
						
						var el_hidden = el.hasClass( 'hidden' );
						
						if( el_hidden == false ){
							if( multy ){
								console.log( 'multy selection! Edit this later ...' );
							}
							else {
								if( elselectbox_li_selected.length <= 0 ){
									elselectbox_li.eq( 0 ).addClass( 'selected' );
								}
								else {
									if( e.keyCode == 38 ) t_next = elselectbox_li_selected.prev( 'span' ).eq( 0 );
									else if( e.keyCode == 40 ) t_next = elselectbox_li_selected.next( 'span' ).eq( 0 );
									
									if( t_next.prop( 'nodeName' ) != undefined ){
										elselectbox_li_selected.removeClass( 'selected' );
										t_next.addClass( 'selected' );
										
										var t_next_a = t_next.find( 'a' ).eq( 0 );
										var elinput = t.parents( '.elinput' );
										var inpval = el.find( '.elinput input.elInputVal' );
										var inplabel = el.find( '.elinput input.elInputText' );
										
										var newValue = t_next_a.data( 'val' );
										var newLabel = t_next_a.data( 'label' ) ? t_next_a.data( 'label' ) : t_next_a.text( );
										
										inpval.val( newValue );
										inplabel.val( newLabel );
									}
								}
							}
						}
						else {
							el.removeClass( 'hidden' );
						}
						
						return false;
						break;
					case 27 :	// .. escape
						/*
						if( readonly ) return false;
						
						this.off( t , true );
						break;
						*/
					case 13 : // .. enter
						
						if( readonly ) return false;
						
						if( subtype == 'select' ){
							var fastedit = el.data( 'fastedit' );
							
							if( fastedit ){
								elfon.click( );
							}
							else {
								if( multy ){
								}
								else {
									if( elselectbox_li_selected.length > 0 ){
										elselectbox.find( 'span.selected a' ).click( );
										// this.off( t , true );
									}
								}
							}
						}
						else if( subtype == 'text' ){
							
							elname = t.data( 'inputname' ) + '[]';
							elvalue = t.val( );
							
							if( elvalue != '' ){
								text = '<span class="elinputcontainerval" onClick="return formblockP.userform.removemulty( this );"><input type="hidden" name="' + elname + '" value="' + elvalue + '" class="elInputVal" />' + elvalue + '</span>';
								console.log( 'text: ' + text );
								
								elinputcontainer.append( text );
								
								t.val( '' );
							}
							else {
							}
						}
						
						return false;
						break;
					case 9 :	// .. tab
						
						inplabel.off( 'select' );
						
						if( readonly ){
							this.off( t );
						}
						else {
							
							// ..
							if( subtype == 'select' ){
								if( multy ){
									this.off( t );
								}
								else {
									
									if( inpval.data( 'def' ) ){
										var defval = inpval.data( 'def' ).val;
										var deflabel = inpval.data( 'def' ).label;
									}
									inpval.val( defval );
									inplabel.val( deflabel );
									
									// .. variant 2
									if( elselectbox_li_selected.length > 0 ){
										elselectbox_li_selected.find( 'a' ).click( );
										this.off( t , true );
									}
									else {
										this.off( t );
									}
								}
							}
							else if( subtype == 'text' ){
								elname = t.data( 'inputname' ) + '[]';
								elvalue = t.val( );
								
								if( elvalue != '' ){
									text = '<span class="elinputcontainerval" onClick="return formblockP.userform.removemulty( this );"><input type="hidden" name="' + elname + '" value="' + elvalue + '" class="elInputVal" />' + elvalue + '</span>';
									console.log( 'text: ' + text );
									
									elinputcontainer.append( text );
									
									t.val( '' );
									return false;
								}
								else {
								}
							}
						}
						break;
					default :
						console.log( 'Default' );
						break;
				}
			}
			
			return true;
		} ,
		onClick : function( obj , opts='' ){
			var t = $( obj );
			
			var el = t.parents( '.el' );
			var readonly = el.data( 'readonly' );
			if( readonly == '1' ) return false;
			
			var ttype = t.prop( 'type' );
			
			var type = el.data( 'type' );	// type [ text | textarea | select | checkbox | ... ]
			
			var e = event;
			
			switch( type ){
				case 'select' :
			
					// var inpval = el.find( '.elinput input[type=hidden]' );
					// var inplabel = el.find( '.elinput input[type=text]' );
					var inpval = el.find( '.elinput input.elInputVal' );
					var inplabel = el.find( '.elinput input.elInputText' );
					
					inplabel.focus( );
					
					break;
				default :
					if( ttype == 'checkbox' || ttype == 'radio' ){
						if( opts ){
							if( opts[ 'checked' ] == true ){
								$( t ).prop( 'checked' , true );
								var check = t.parents( '.span-label-text' ).find( '.check' );
								check.addClass( 'on' );
							}
							else if( opts[ 'checked' ] == false ){
								$( t ).prop( 'checked' , false );
								var check = t.parents( '.span-label-text' ).find( '.check' );
								check.removeClass( 'on' );
							}
						}
						else {
						// .. пробел
						// if( e.keyCode == '32' ){
							var checked = t.is( ':checked' );
							var check = t.parents( '.span-label-text' ).find( '.check' );
							
							if( checked == true ){
								// $( t ).prop( 'checked' , false );
								check.addClass( 'on' );
							}
							else {
								// $( t ).prop( 'checked' , true );
								check.removeClass( 'on' );
							}
						}
					}
					break;
			}
			return ;
		} ,
		onBlur : function( obj , opts='' ){
			var t = $( obj );
			
			var el = t.parents( '.el' );
			var readonly = el.data( 'readonly' );
			if( readonly == '1' ) return false;
			
			var type = el.data( 'type' );
			
			var e = event;
			if( type == 'input-checkbox' || type == 'checkbox' || type == 'input-radio' || type == 'radio' ){
				var spanlabeltext = t.parents( '.span-label-text' );
				spanlabeltext.removeClass( 'focused' );
			}
			else if( type == 'select' ){
				var subtype = ( el.hasClass( 'select' ) || el.hasClass( 'search' ) ) ? 'select' : 'text';
				
				if( subtype == 'select' ){
				}
				else if( subtype == 'text' ){
					el.addClass( 'hidden' );
				}
			}
			
			return ;
		} ,
		fields : function( obj , opts='' ){
			var t = $( obj );
			
			var el = t.parents( '.el' );
			var inputmain = el.find( '.el-select' );
			
			var elinput = el.find( '.elinput' );
			var elInputval = elinput.find( '.elInputVal' );
			
			var fields = inputmain.data( 'prepare-fields' );
			if( typeof fields == 'object' ){
				var fieldsreset = fields.reset;
				var fieldschange = fields.change;
				var fieldschangelayers = fields.changelayers;
				
				if( typeof fieldsreset == 'object' ){
					
					$.each( fieldsreset , function( k , v ){
						name = v;
						
						input = t.parents( 'form' ).find( 'input[name="' + name + '"]' );
						if( input != 'undefined' ){
							formblockP.userform.reset( input );
						}
					} );
				}
				
				if( typeof fieldschange == 'object' ){
					let count = 0;
					for ( let key in fieldschange ) count++;
					
					if( count > 0 ){
						
						$.each( fieldschange , function( k , v ){
							layer = k;
							tdata = v;
							
							input = t.parents( 'form' ).find( 'input.elInputText' );
							
							if( tdata[ 'function' ] == 'inputchange' ){
								window[ tdata[ 'object' ] ][ tdata[ 'function' ] ]( input , { 'toLayerId' : layer , 'field' : tdata[ 'field' ] , 'inputname' : tdata[ 'inputname' ] } );
							}
							/*
							if( input != 'undefined' ){
								formblockP.userform.change( input , layer );
							}
							*/
						} );
					}
				}
				
				if( typeof fieldschangelayers == 'object' ){
					let count = 0;
					for ( let key in fieldschangelayers ) count++;
					
					if( count > 0 ){
						var val = elInputval.val( );
						
						var prefix = fieldschangelayers.prefix;
						var layers = fieldschangelayers.layers;
						
						var tbody = el.parents( '.blockform_type' );
						
						$.each( layers , function( k , v ){
							
							console.log( 'k: ' + k + ' | ' + v );
							layer = v;
							
							var findlayer = '.' + prefix + layer;
							console.log( val + ' = ' + layer + ' | findlayer: ' + findlayer );
							if( val == layer ) tbody.find( findlayer ).addClass( 'visible' );
							else tbody.find( findlayer ).removeClass( 'visible' );
							
							/*
							input = t.parents( 'form' ).find( 'input.elInputText' );
							
							if( tdata[ 'function' ] == 'inputchange' ){
								window[ tdata[ 'object' ] ][ tdata[ 'function' ] ]( input , { 'toLayerId' : layer , 'field' : tdata[ 'field' ] , 'inputname' : tdata[ 'inputname' ] } );
							}
							/*
							if( input != 'undefined' ){
								formblockP.userform.change( input , layer );
							}
							*/
						} );
					}
				}
			}
			
			
			return ;
		} ,
		reset : function( obj , opts='' ){
			var t = $( obj );
			
			var el = t.parents( '.el' );
			
			var $type = el.data( 'type' );
			
			// ..
			switch( $type ){
				case 'input-password' :
				case 'input-text' :
					t.val( '' );
					break;
				// .. checkbox, radio
				case 'input-checkbox' :
					var elinput = el.find( 'input' ).eq( 0 );
					var elcheck = el.find( '.check' ).eq( 0 );
					
					elinput.prop( 'checked' , false );
					elcheck.removeClass( 'on' );
					break;
				case 'input-checkbox' :
					var elinput = el.find( 'input' ).eq( 0 );
					var elcheck = el.find( '.check' ).eq( 0 );
					
					elinput.prop( 'checked' , false );
					elcheck.removeClass( 'on' );
					break;
				// .. select
				case 'select' :
				case 'select-search' :
			
					var elinput = el.find( '.elinput' );
					var inpval = elinput.find( 'input[type=hidden]' );
					var inplabel = elinput.find( 'input[type=text]' );
					
					var elselectbox = el.find( '.elselectbox' );
					var elselectbox_span = elselectbox.find( 'span' );
					var elselectbox_span_selected = elselectbox.find( 'span.selected' );
					
					var a = elselectbox.find( '.elselectbox_li' ).eq( 0 ).find( 'a' ).eq( 0 );
					var reset = { 'value' : a.data( 'val' ) , 'label' : a.text( ) };
					
					inpval.val( reset.value );
					inplabel.val( reset.label );
					
					elselectbox.find( '.elselectbox_li' ).removeClass( 'selected' );
					elselectbox.find( '.elselectbox_li' ).eq( 0 ).addClass( 'selected' );
					break;
					var a = elselectbox.find( '.elselectbox_li' ).eq( 0 ).find( 'a' ).eq( 0 );
					var reset = { 'value' : a.data( 'val' ) , 'label' : a.text( ) };
					
					inpval.val( reset.value );
					inplabel.val( reset.label );
					
					elselectbox.find( '.elselectbox_li' ).removeClass( 'selected' );
					elselectbox.find( '.elselectbox_li' ).eq( 0 ).addClass( 'selected' );
					
					break;
				default :
					break;
			}
			
			return ;
		} ,
	}
}