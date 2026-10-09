$( document ).ready(function() {
} );

window.addEventListener( 'scroll' , function( ){
	// console.log( pageYOffset + 'px' );
	
	if( pageYOffset > 100 ){
		$( '#' + 'storeBasketList' ).addClass( 'global' );
	}
	else {
		$( '#' + 'storeBasketList' ).removeClass( 'global' );
	}
} );