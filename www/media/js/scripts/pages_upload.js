pages_upload = function( url ){
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
			id_commentbox = $( '#' + dataJSON[ 'id_commentbox' ] );
			id_pagesbox = $( '#' + dataJSON[ 'id_pagesbox' ] );
			data_comments = dataJSON[ 'data_comments' ];
			data_pages = dataJSON[ 'data_pages' ];
			
			id_commentbox.append( data_comments );
			id_pagesbox.html( data_pages );
		} ,
		error : function ( jqXHR, textStatus, errorThrown ) {
			// loading_bgtext.html( 'Sinhronization ERROR' );
			// loading.animate({ 'opacity' : '0.0' } , 1200 , function( ){$(this).hide( );loading_bgtext.html( '' );});
		}
	});
	
	return false;
}