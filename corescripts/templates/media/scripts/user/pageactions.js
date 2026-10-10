/*

	javascript file
	pageactions
	muinavigate
*/

pageactions = {
	test : {
		name : 'Updated' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		'' : function( obj , attrs ){
			console.log( 'test' );
		} ,
	} ,
	tablecheckboxes : {
		name : 'presentations' ,
		ajax : false , layers : { } , form : { } , data : { } ,
		start : function( obj , attrs ){
			obj = $(obj );
			
			var type = obj.prop( 'type' );
			
			if( type != 'checkbox' ){
				checkbox = obj.find( 'input[type=checkbox]' ).eq( 0 );
				obj = $( checkbox );
			}
			
			obj_name = obj.prop( 'name' );
			
			checkname = attrs.checkname;
			mainname = attrs.mainname;
			
			form = obj.parents( 'form' );
			checkboxes = form.find( 'input[name*="' + checkname + '[]"]' );
			all_checked = true;
			checkboxes.each( function( i , elem ){
				elem_checked = $(elem).prop( 'checked' );
				if( elem_checked == false ) all_checked = false;
			} );
			
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
	} ,
	presentations : {
		name : 'presentations' ,
		ajax : false , layers : { } , form : { } , data : { } ,
		start : function( obj , attrs ){
			var t = $( obj );
			
			var action = attrs.action;
			
			var body = t.parents( '.body' );
			var content = body.find( '.content' );
			var boxblock = content.find( '.boxblock' );
			var boxblock_length = boxblock.length;
			
			var content_data = content.data( 'contentdata' );
			
			if( content_data == undefined ){
				content.data( 'contentdata'  , { 'count' : 0 , 'length' : boxblock_length } )
			}
			
			var content_data = content.data( 'contentdata' );
			
			if( action == 'back' ) content_data.count--;
			else if( action == 'forward' ) content_data.count++;
			
			animate = true;
			if( content_data.count < 0 ){
				animate = false;
				content_data.count = 0;
			}
			else if( content_data.count >= content_data.length ){
				animate = false;
				content_data.count = content_data.length-1;
			}
			
			if( animate == true ){
				boxblock.slideUp( 400 );
				boxblock.eq( content_data.count ).slideDown( 400 );
			}
			
		} ,
	} ,
	popup_formblock_change : {
		name : 'Updated' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		'' : function( obj , attrs ){
			var t = $( obj );
			
			nodeName = t.prop('nodeName').toLowerCase( );
			
			if( nodeName == 'input' )
				var val = t.val( );
			else
				var val = t.data( 'val' );
			
			
			var tr = t.parents( '.blockform_table' ).find( '.blockform_rows.type' );
			
			console.log( 'tr: ' + tr.length + ' | val: ' + val + ' | nodeName: ' + nodeName );
			if( val == '' ){
				tr.removeClass( 'visible' );
				tr = t.parents( '.blockform_table' ).find( '.blockform_rows.type.type_' + val ).addClass( 'visible' );
			}
			else {
				tr.removeClass( 'visible' );
				tr = t.parents( '.blockform_table' ).find( '.blockform_rows.type.type_' + val ).addClass( 'visible' );
			}
		} ,
	} ,
	show : {
		name : 'Updated' ,
		ajax : true , layers : { } , form : { } , data : { } ,
		'' : function( obj , attrs ){
			this.show( obj );
		} ,
		show : function( obj ){
			var t = $( obj );
			var opened = t.hasClass( 'opened' );
			var inpcheck = t.parent( '.div' ).find( 'input' ).eq( 0 );
			
			// ..
			if( opened == false ){
				t.addClass( 'opened' ).text( 'скрыть лишнее ...' );
				t.parents( '.div_col' ).find( '.div-el.hiddened' ).removeClass( 'hidden' );
				// $( inpcheck ).prop( 'checked' , true );
				$( inpcheck ).val( '1' );
			}
			else {
				t.removeClass( 'opened' ).text( 'показать еще ...' );
				t.parents( '.div_col' ).find( '.div-el.hiddened' ).addClass( 'hidden' );
				// $( inpcheck ).prop( 'checked' , false );
				$( inpcheck ).val( '' );
			}
		}
	} ,
	filters : {
		name : 'formblock : prepare' ,
		on : function( html ){
			this.dialogOn = true;
			formblockP.layers( t , this.layers );
			
			html = '<div class="resultclose" onClick="formblock.dialog.off( );"></div>' + html;
			
			$( 'html' ).addClass( 'overflowHidden' );
			this.layers.tbload_result.css( { 'opacity' : '0.0' } ).html( html );
			this.layers.tbload.addClass( 'active' );
			this.layers.tbload_result.animate( { 'opacity' : '1.0' } , 500 , function( ){
				// .. after .. animate
			});
		} ,
		off : function( ){
			this.dialogOn = false;
			formblockP.layers( t , this.layers );
			
			$( 'html' ).removeClass( 'overflowHidden' );
			
			this.layers.tbload_result.html( '' );
			this.layers.tbload.removeClass( 'active' );
		} ,
		is : function( ){
			return this.dialogOn;
		}
	} ,
	// .. ajax
	ajax : function( obj , action , attrs ){
		// .. переменные
		var actionName = action;
		tmp_action = action;
		action = this[ action ];
		
		action[ '' ]( obj , attrs );
		
		return false;
	} ,
	a : function( obj , action , attrs=[] ){
		this.data = { };
		this.form = { };
		
		if( ! this[ action ] ){
			console.log( 'Нет вызванной функции: ' + action + '.' );
			return;
		}
		else return this.ajax( obj , action , attrs );
	} ,
	act : function( obj , action , attrs=[] ){
		this.data = { };
		this.form = { };
		
		
		if( ! this[ action ] ){
			console.log( 'Нет вызванной функции: ' + action + '.' );
			return;
		}
		else {
			if( this[ action ].ajax == true ) return this.ajax( obj , attrs );
			else return this[ action ].start( obj , attrs );
		}
	}
};

// ..
mui = {
	nav : function( obj , layerid ){
		var obj = $( obj );
		var layerid = $( layerid );
		var s = $( layerid ).offset().top;
		$( "html,body" ).animate( { 'scrollTop' : s + 'px' } , 500 );
		
		return false;
	}
};

$( document ).ready(function( ) {
	formblock.pageactions = pageactions;
});