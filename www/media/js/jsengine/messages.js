

// Messages
$( document ).ready(function() {
	mesagges_show( false , true );
});

mesagges_show = function( appendData , show ){
	tB_messages = $( '#tB_messages' );
	
	// .. если есть данные для добавления
	if( appendData ){
		
		if( typeof appendData == 'object' ){
			$( appendData ).each( function( i , elem ){
				tB_messages.append( elem );
			});
		}
		else
			tB_messages.append( appendData );
	}
	


	messages = $( '.message' );
	message = $( '.message.new' ).not( '.process' );
	message_process = $( '.message.process.new' );
	message_old = $( '.message.old' );
	
	// message_process.stop( false , true ).hide( );
	// message_old.stop( false , true ).slideUp( 200 );

	message.addClass( 'process' );
	if( message ){
		message.hide( );
		// message_process.removeAttr( 'style' );
		message_process.stop( ).slideUp( 100 );
		// tB_messages.show( );
		message.slideDown( 500 , 'linear' , function( ) {
			$( this ).removeClass( 'new' ).removeClass( 'process' ).addClass( 'old' );
			tB_messages.addClass( 'loaded' );
		} );
		
		messages.find( '.button.full' ).bind( 'click' , function( ){
			message = $( this ).parents( '.message' );
			if( message.hasClass( 'fullwindow' ) ) message.removeClass( 'fullwindow' );
			else message.addClass( 'fullwindow' );
		});
		messages.find( '.button.close' ).bind( 'click' , function( ){
			message = $( this ).parents( '.message' );
			if( message.hasClass( 'fullwindow' ) ){
					message.detach( );
			}
			else {
				$(this).parents( '.message' ).slideUp( 500 , function( ){
					$(this).detach( );
				});
			}
		});
		
		
		messages.unbind( 'mouseover' ).bind( 'mouseover' , function( ){
			clearTimeout( window.ti );
		} );
	}

	
	if( show == true ){
		$( document ).ready( function( ){
			clearTimeout( window.ti );
			window.ti = window.setTimeout( function( ){
				messages = $( '#tB_messages' );
				message = messages.find( '.message.old' );
				message.slideUp( 500 , function( ){
					messages.removeClass( 'loaded' );
					$(this).detach( );
					clearTimeout( window.ti );
				} );
				
				message = messages.find( '.message.process' );
				message.slideUp( 500 , function( ){
					messages.removeClass( 'loaded' );
					$(this).detach( );
					clearTimeout( window.ti );
				} );
			} , 3000 );
		});
	}
}