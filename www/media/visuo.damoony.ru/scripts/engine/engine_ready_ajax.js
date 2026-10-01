// # 
// # 
// # 


$( document ).ready( function( ) {
	$( 'form' ).each( function( ) {
		form = $( this );

		form_jquery = form.data( 'form-jquery' );
		
		if( ! form_jquery ) return;
		else {
		}
		
		form.on( 'submit' , function( ){
			/*
			alert( $( this ) );
			return false;
			if( change = $( this ).find( 'input[type=text]' ).eq( 0 ).val( ) == '' ){
				$( '#' + 'popup_result' ).html( $( '#' + 'popup_result' ).html( ) + '<div>Changed</div>' );
				return false;
			} else {
				$( this ).unbind( 'submit' );
				$( this ).submit( );
			}
			*/


			_form = $( this );
			_data = $( this ).data( 'load' );
			if( _data.on == true ){
				_layerto = _data.layerto;
				_src = _data.src;
				if( _layerto == 'form' ) layerto = _form; else layerto = '#' + _layerto;
				if( _src ) src = _src; else src = _form.prop( 'action' );
				
				engine.load.a( this , 'load' , { 'layerto' : layerto , 'src' : src } );
			}
			
			return false;
		} );

		
		/*
		$( 'form' ).find( 'input[type=submit]' ).on( 'click' , function( ){
			// alert( 'Submit: ' + $( this ).parents( 'form' ).prop( 'action' ) );
			
			_form = $( this ).parents( 'form' );
			_data = $( this ).parents( 'form' ).data( 'load' );
			
			if( _data.on == true ){
				_layerto = _data.layerto;
				_src = _data.src;
				if( _layerto == 'form' ) layerto = _form; else layerto = '#' + _layerto;
				if( _src ) src = _src; else src = _form.prop( 'action' );

				engine.load.a( this , 'load' , { 'layerto' : layerto , 'src' : src } );
			}
			// return false;
		} );
		*/
	} );

	dinamic_prepare( );
	
} );

function dinamic_prepare ( ) {
	
	
	$( 'a[data-load]' ).each( function( ){
		// alert( 'A: data-popup' );
		obj = $(this);
		popup = obj.data( 'load' ).popup;
		
		if( popup == 1 ) {
			obj.unbind( 'click' );
			obj.bind( 'click' , function( ){
				// engine.popup.dialog.on( this );
				engine.popup.dialog.load( this );
				return false;
 			} );
		} else {
			obj.unbind( 'click' );
			obj.bind( 'click' , function( ){
				engine.load.a( this , 'load' );
				return false;
 			} );
		}
	} );

	$( 'a:not([data-load])' ).each( function( ){
		alert( 'data not' );
		/*
		obj = $(this);
		popup = obj.data( 'load' ).popup;
		
		if( popup == 1 ) {
			obj.unbind( 'click' );
			obj.bind( 'click' , function( ){
				// engine.popup.dialog.on( this );
				engine.popup.dialog.load( this );
				return false;
 			} );
		} else {
			obj.unbind( 'click' );
			obj.bind( 'click' , function( ){
				engine.load.a( this , 'load' );
				return false;
 			} );
		}
			*/
	} );

	/*

	$( 'a[data-popup=1]' ).each( function( ){
		// alert( 'A: data-popup' );
		obj = $(this);
		popup = obj.data( 'popup' );

		if( popup == 1 ) {
			obj.unbind( 'onclick' );
			obj.bind( 'click' , function( ){
				engine.popup.dialog.load( this );
				return false;
 			} );
		}
	} );
	 */
}