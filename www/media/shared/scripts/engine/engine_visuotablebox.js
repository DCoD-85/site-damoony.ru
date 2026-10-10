
visuotablebox = {
	formblockP : { } ,
	layers : { } , form : { } , data : { } ,
	// ..
	// .. prepare
	// ..
	
	show : function ( obj , idhead , idbody ) {
		vtb = $( obj ).parents( '.visuotablebox' );
		hhead = vtb.find( '.hhead .span-link' ).removeClass( 'selected' );
		bbody_box = vtb.find( '.bbody .bbodybox' ).removeClass( 'selected');
		$( '#' + idhead ).addClass( 'selected' );
		$( '#' + idbody ).addClass( 'selected' );
	} ,
	a : function( obj , action , attrs ){
		if( typeof obj == 'string' ) obj = $( obj );
		
		this.data = { };
		this.form = { };
		
		if( ! this[ action ] ){
			console.log( 'Нет вызванной функции: ' + action + '.' );
			return;
		}
		else return this.ajax( obj , action , attrs );
	}
};