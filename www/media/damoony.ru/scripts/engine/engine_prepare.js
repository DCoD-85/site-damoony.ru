engine_prepare = {
	engine : null ,
	name : 'engine.prepare' ,
	check : function ( engine ) {
		this.engine = engine;
		console.log( '[ check ][ ' + this.name + ' ] Engine: ' + this.engine._scriptname + '.' );
	} ,

	_form : function( l , obj , form , data , attrs ) {
		o = $( obj );
		engine.prepare._formcheck( obj , form );
		engine.prepare._formdata( obj , form , data , attrs );

	} ,
	_formcheck : function( obj , form ) {
		o = $( obj );
		if( o.prop( 'tagName' ) == 'FORM' ){
			form.form = o;
		} else {
			if( o.data( 'load.from-this' ) ){
				form.form = null;
			} else if( o.parents( 'form' ).length > 0 ){
				form.form = o.parents( 'form' ).eq( 0 );
			} else {
				form.form = null;
			}
		}

		if( form.form == null )
			engine.prepare._formnot( obj , form );
		else
			engine.prepare._formis( obj , form );
	} ,
	_formnot : function( obj , form ) {
		o = $( obj );
		
		form.on = false;
		
		form.formdata = new FormData( );
		// 
		form.async = true;
		form.action = ( o.prop( 'href' ) != undefined ? o.prop( 'href' ) : o.href );
		form.dataType = 'JSON';
		form.method = 'POST';
		form.data = '';
		form.cache = false;
		form.contentType ='application/x-www-form-urlencoded; charset = UTF-8';
	} ,
	_formis : function( obj , form ) {
		o = $( obj );
		
		form.form_bsubmit = form.form.find( 'input[type=submit]' );
		form.action = form.form.prop( 'action' );
		console.log( '[ DEBUG : prepare ][ form.action ] ' + form.action );
		// .. отключаем форму
		form.form.on( 'submit' , function( ){return false;} );
		// .. подготавливаем данные
		form.async = true;
		form.method = 'POST';
		form.dataType = 'JSON';
		// form.contentType = 'application/x-www-form-urlencoded';
		form.processData = false;
		form.contentType = false;
		form.enctype = form.form.attr( 'enctype' );
		form.formdata = new FormData( );

		if( o.prop( 'tagName' ) == 'FORM' ){
			console.log( 'submit from form: form' );
			
			engine.prepare._formdatafromform( o , form );
		} else {
			console.log( 'submit from form: other' );

			popup = o.data( 'popup' );
			if( popup ){
				if( popup.on == true ){
					if( popup.link == false ) popup.link = form.form.prop( 'action' );

					if( popup.data ){
						data = popup.data;
						popup.data[ 'form' ] = [ ];
						popup.data.form[ 'submit' ] = true;
						popup.data[ 'uno' ] = true;
						popup.data[ 'tolayerid' ] = popup.tolayerid;
						// data = new FormData( );
						$.each( popup.data , function( k , v ){
							// data.append( [k] , v );
						} );
						$name = o.prop( 'name' );
						$value = o.prop( 'checked' );
						// data.{}
						data[ $name ] = $value;
						// data.o.name = o.value;
					} else data = { };

					alert( 'data: ' + typeof data );

					engine.prepare._formdatafromdata( o , form , data );
				}
			}
		}
		engine.prepare._formdata( o , form );
	} ,
	_formdata : function( o , form , data , attrs ) {
		if( form.formdata ) form.data = form.formdata;
	} ,
	_formdatafromform : function( o , form ) {
		
		form.formdata.append( 'form[submit]' , 'submit' );
		els = form.form.find( 'input , textarea , select' );
		$.each( els , function( k , v ){
			$this = $( this );
			let $type = $this.prop( 'type' );
			let $name = v.name;
			if( true ){
				if( $type == 'checkbox' ){
					if( $this.is( ':checked' ) == true ) var $value = v.value;
					else var $value = '';
					form.formdata.append( [$name] , $value );
				}
				else if( $type == 'radio' ){
					if( $this.is( ':checked' ) == true ){
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
	} ,
	_formdatafromdata : function( o , form , data , attrs ) {
		if( data ){
			$.each( data , function( k , v ){
				console.log( '[ var ] ' + k + ' = ' + v );

				$name = k;
				$value = v;
				
				if( $value != 'undefined' ){
					form.formdata.append( [$name] , $value );
				}
			} );
		} else console.log( 'Bad data' );
	} ,
	formcheck : function( obj , form , data ){
		var o = $( obj );
		// var form = o.parents( 'form' ).eq( 0 );
		if( o.prop( 'tagName' ) == 'FORM' ){
		} else if( o.prop( 'tagName' ) == 'A' ) {
			engine.prepare.formnot( o , form );
		} else if( o.prop( 'tagName' ) == 'INPUT' ) {
			engine.prepare.formnot( o , form );
		}
	} ,
	formnot : function ( obj , form ){
		o = $( obj );

		if( o.parents( 'form' ) ) return true;

		alert( 'TRUE' );
		form.on = false;
		// 
		form.async = true;
		form.action = o.prop( 'href' );
		form.dataType = 'JSON';
		form.method = 'POST';
		form.data = '';
		form.cache = false;
		form.contentType ='application/x-www-form-urlencoded; charset = UTF-8';
	} ,
	form : function( obj , form , data , data_prepare = '' ) {
		// .. object , vars
		var o = $( obj );
		var return_form = {};

		if( form.on == false ) return ;

		// .. data
		if( o.prop( 'tagName' ) == 'FORM' ) form.form = o;
		else form.form = o.parents( 'form' );

		form.form_bsubmit = form.form.find( 'input[type=submit]' );
		form.action = form.form.prop( 'action' );
		console.log( '[ DEBUG : prepare ][ form.action ] ' + form.action );

		// .. отключаем форму
		form.form.on( 'submit' , function( ){return false;} );
		
		// .. подготавливаем данные
		
		form.async = true;
		form.method = 'POST';
		form.dataType = 'JSON';
		// form.contentType = 'application/x-www-form-urlencoded';
		form.processData = false;
		form.contentType = false;
		form.enctype = form.form.attr( 'enctype' );

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
	formdata : function( obj , form , data , userdata = '' , spec = false ){
		if( form.on == false ) return ;
		// .. object , vars
		var o = $( obj );
		form.data = '';
		// if( form.form ) alert( 'FORM!' );

		// .. добавляем кнопку Submit
		form.formdata.append( 'submit' , 'submit' );
		let formtype = o.parents( 'form' ).attr( 'method' );
		let method = o.parents( 'form' ).attr( 'method' );
		// let formtype = o.parents( 'form' ).attr( 'formtype' );
		let formupload = o.parents( 'form' ).attr( 'formupload' );
		

		let fields = o.data( 'prepare-fields' );
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
						engine.prepare.userform.reset( input );
					}
				} );
			}
		}
		
		// .. файлы
		if( form.form.attr( 'method' ) == 'post' && form.form.attr( 'enctype' ) != 'application/x-www-form-urlencoded' ){
			
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
		else if( form.form.attr( 'method' ).toLowerCase( ) == 'post' ){
			engine.prepare.datafromform( o , form , data , userdata );
		}
		else if( form.form.attr( 'method' ).toLowerCase( ) == 'get' ){
			engine.prepare.datafromform( o , form , data , userdata );
		}
		else {
			engine.prepare.datafromform( o , form , data , userdata );
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
		
		// .. FILES
		if( form.form.attr( 'method' ).toLowerCase( ) == 'post'
			&& form.form.attr( 'enctype') != 'application/x-www-form-urlencoded' ){
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
			console.log( 'el: ' + v.name + ' = ' + v.value );
			let $type = $( this ).prop( 'type' );
			let $name = v.name;
			console.log( 'name: ' + $name + ' | type: ' + $type );
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
	}
}