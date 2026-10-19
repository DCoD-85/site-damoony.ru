// # 
// # 
// # 


$( document ).ready( function( ) {

	// Change "{}" to your options:
	// https://github.com/sampotts/plyr/#options
	if( typeof Plyr == 'function' ){
		console.log( '[ debug ][ plyr ] status : on' );
		const player = new Plyr( 'audio', {} );
		// Expose player so it can be used from the console
		window.player = player;
	}

	$( 'form' ).each( function( ) {
		form = $( this );

		form_jquery = form.data( 'form-jquery' );
		
		if( ! form_jquery ) return;
		else {
		}
		
		form.on( 'submit' , function( ){
			_form = $( this );
			_data = _form.data( 'load' );
			if( _data.on == true ){
				_layerto = _data.layerto;
				_src = _data.src;
				if( _layerto == 'form' ) layerto = _form; else layerto = '#' + _layerto;
				if( _src ) src = _src; else src = _form.prop( 'action' );
				
				engine.load.a( this , 'load' , { 'layerto' : layerto , 'src' : src } );
			}
			return false;
		} );
	} );

	dinamic_prepare( );
	
} );

function dinamic_prepare ( ) {
	ajax = $( 'html' ).data( 'ajax' );
	ajax_init = $( 'html' ).data( 'ajax-init' );
	links_load = $( 'a:is([data-load])' );
	links_ajax = $( 'html:is([data-ajax="1"]) a:not([data-load]):not([data-notajax="1"])' );

	links_load.each( function( ){
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

	links_ajax.each( function( ){
		obj = $(this);
		obj.unbind( 'click' );
		obj.bind( 'click' , function( ){
			engine.page.loadajax( this );
			return false;
		} );
	} );
	console.log( '[ debug ][ pageajax ] status : ' + ( ajax ? 'on' : 'off' ) + '; links count : ajax ( ' + links_ajax.length + ' ), load ( ' + links_load.length + ' ).' );

	if( ajax == 1 && ajax_init == 0 ){
		$( 'html' ).data( 'ajax-init' , 1 );

		const currentUrl = location.protocol + '//' + location.hostname  + location.pathname + location.search;
		const savedState = sessionStorage.getItem( 'state_' + currentUrl );

		if ( savedState ) {
		// 	const parsedState = JSON.parse( savedState );
		// 	// Возвращаем state в историю браузера после перезагрузки
		// 	history.replaceState( parsedState , '' , currentUrl );
		// 	console.log('Состояние успешно восстановлено из sessionStorage:', parsedState);
		}
		
		$( window ).on( 'popstate' , function( event ) {
			const state = event.originalEvent.state;
			let href = location.protocol + '//' + location.hostname  + location.pathname + location.search;
			
			obj = { 'href' : href };
			engine.page.loadpage( obj , false );
			// Загружаем контент для полученного состояния через AJAX
			return false;
		});
	}

	
	// --- data-action delegate (M1: без inline onclick) ---
	window.actions = window.actions || {};

	window.actions[ 'confidence-show' ] = function ( el ) {
		if ( window.engine && engine.confidence ) engine.confidence.showconfidence( el );
	};
	window.actions[ 'confidence-hide' ] = function ( el ) {
		if ( window.engine && engine.confidence ) engine.confidence.hide( el );
	};

	document.addEventListener( 'click' , function ( e ) {
		var el = e.target.closest( '[data-action]' );
		if ( ! el ) return;
		var name = el.getAttribute( 'data-action' );
		var fn = window.actions[ name ];
		if ( typeof fn === 'function' ) { fn( el ); }
	} );
}