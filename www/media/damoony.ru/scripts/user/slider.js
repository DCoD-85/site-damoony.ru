

slider = {
	mode : 'min' ,
	images : { num : 0 , cur : 0 } ,
	slide : function( obj , attrs ){
		var t = $( obj ).parents( '.sliderbox' );
		var content = t.find( '.sliderbox_content' );
		
		var prepare = t.data( 'prepare' );
		
		if( ! prepare ){
			var data = { };
			data.mode = slider.mode;
			data.images = t.find( '.sliderbox_content .image' );
			data.all = t.find( '.sliderbox_content .image' ).length-1;
			data.cur = 0;
			
			t.data( 'prepare' , data );
			// alert( 'data: ' + data.cur + ' | ' + t.data( 'prepare' ) );
		}
		
		prepare = t.data( 'prepare' );
		
		// alert( 'prepare: ' + prepare );
		$.each( prepare , function( k , v ){
			console.log( k + ' | ' + v );
		} );
		
		switch( attrs.action ){
			case 'back' :
				var cur = prepare.cur - 1;
				break;
			case 'forward' :
				var cur = prepare.cur + 1;
				break;
			case 'full' :
				slider.mode = ( slider.mode == 'min' ? 'fullscreen' : 'min' );
				
				switch( slider.mode ){
					case 'min' :
						formblock.dialog.off( );
						break;
					case 'fullscreen' :
						var html = t.parents( '.divinside' ).html( );
						
						formblock.dialog.on( html );
						images = $( '#tB_load .sliderbox' ).addClass( 'fullscreen' );
						images = $( '#tB_load .sliderbox_content' ).find( 'img' );
						
						$.each( images , function( k , v ){
							altsrc = $( this ).data( 'altsrc' );
							altsrc ? $( this ).attr( 'src' , altsrc ) : false;
						} );
						
						break;
					default : break;
				}
				return;
				break;
			default : break;
		}
		bok = false;
		
		if( cur <= 0  ) cur = 0;
		else if( cur >= prepare.all ) cur = prepare.all;
		else bok = true;
		
		$.each( prepare.images , function( k , v ){
			if( k == cur ) $( v ).show(  );
			else $( v ).hide(  );
		} );
		prepare.cur = cur;
		t.data( 'prepare' , data );
		
		return false;
	} ,
	process : function( obj , action , attrs ){
		
		action = this[ action ];
		
		action( obj , attrs );
	} ,
	a : function( obj , action , attrs ){
		this.data = { };
		this.form = { };
		if( ! this[ action ] ){
			console.log( 'Нет вызванной функции: ' + action + '.' );
			return;
		}
		else return this.process( obj , action , attrs );
	}
}