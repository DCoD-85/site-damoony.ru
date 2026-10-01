
calendar = function( url , fieldname ){
	console.log( 'url: ' + url );
	jQuery.ajax({
		url : url ,
		async : false ,
		type : 'POST' ,
		// dataType : dataType ,
		beforeSend : function( ){
		} ,
		complete : function( msg , status ){
		} ,
		success : function( dataLoaded ){
			var dataJSON = JSON.parse( dataLoaded );
			timestamp = dataJSON[ 'timestamp' ];
			formselect = dataJSON[ 'formselect' ];
			console.log( 'timestamp: ' + timestamp );
			// console.log( 'formselect: ' + formselect );
			$( '#' + fieldname + '_timestamp' ).val( timestamp );
			$( '#' + fieldname + '_formselect' ).html( formselect );
			console.log( '#' + fieldname + '_timestamp' );
			console.log( '#' + fieldname + '_formselect' );
		} ,
		error : function ( jqXHR, textStatus, errorThrown ) {
			// loading_bgtext.html( 'Sinhronization ERROR' );
			// loading.animate({ 'opacity' : '0.0' } , 1200 , function( ){$(this).hide( );loading_bgtext.html( '' );});
		}
	});
}