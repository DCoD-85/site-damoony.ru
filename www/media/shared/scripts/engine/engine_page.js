/*

	javascript file
	formblock class

var formr = { };
var count = 0;
var info = {  'install' : true , 'scriptTimeLoading' : { 'd_all_' : 0 } }; //d_all_ = 0;
formblock_info = {
	scriptname : 'formblock'
}

*/
engine_page = {
	engine : null ,
	name : 'engine.page' ,
	check : function ( engine ) {
		this.engine = engine;
		console.log( '[ check ][ ' + this.name + ' ] Engine: ' + this.engine._scriptname + '.' );
	} ,
   loadajax : function ( obj ) {
		engine.page.a( obj , 'load' );
		return false;
   } ,
   loadpage : function ( obj ) {
		engine.page.a( obj , 'load' , { 'history' : { 'update' : false } } );
		return false;
   } ,
   load : {
		name : 'load' ,
		o : this , ajax : true , layers : { } , form : { 'test' : 'This is test' , on : true } , data : { } ,
        init : function( obj , attrs ){
            this.ajax = true;

			return true;
        } ,
        prepare : function( obj , attrs ){
            let t = this;
            let o = obj;
            
            let data_prepare = '';

			engine.prepare._form( t , o , this.form , this.data , attrs );
        } ,
        beforeSend : function( obj , attrs ){
        } ,
        complete : function( obj , dataJSON , attrs ){			
			error = dataJSON.error;
			messages = dataJSON.messages;
			address = dataJSON.address;
			layers = dataJSON.layers;

			console.log( '[ debug ][ ' + engine.page.name + '.' + this.name +' ][ dataJSON ]\n--- error > ' + error + '\n--- messages > ' + messages + '\n--- address > ' + address + '\n--- layers > ' + layers.length );
			
			if( error != null ) true;
			if( messages != null ) engine.load.messages( messages );// .init( dataJSON.result );
			
			console.log( 'Message -- 1' );
			if( address ){
				if( attrs.history !== undefined && attrs.history.update == false ){
				} else {
					const stateData = { url : address , timestamp : Date.now() };
					history.pushState( stateData , 'Заголовок' , address );
					// Сохраняем это же состояние в sessionStorage, привязав к текущему URL
					state = 'state_' + address;
					sessionStorage.setItem( 'state_' + address , JSON.stringify( stateData ) );
				}
			}
			console.log( 'Message -- 2' );

			if( layers != null ){
				console.log( 'layers' );
				$.each( layers , function( key , val ){
					console.log( 'key : ' + key );
					if( val != null ){
						let layer = val.layer;
						let html = val.html;

						console.log( 'layer : ' + layer );
						console.log( 'html : ' + html );

						$( '.' + layer ).html( html );
					}
				} );
			}
			console.log( 'Message -- 3' );

		}
    } ,
	messages : function( messages ){
		$( '.tB_messages' ).html( $( '.tB_messages' ).html( ) + messages );
		mesagges_show( true , true );
	} ,
    // .. ajax
	ajax : function( obj , action , attrs ){
		// .. переменные
		var dataJSON;
		var form;
		var actionName = action;
		tmp_action = action;

		action = this[ action ];

        if( action.init( obj , attrs ) == false ) return false;
        // .. подготовительные функции
		action.prepare( obj , attrs );

		var layers = [ ];
		// formblockP.layers( t , layers );
		
		if( action.ajax == true ){
			// .. AJAX запрос
			jQuery.ajax( {
				async : action.form.async ,
				url : action.form.action ,
				dataType : action.form.dataType ,
				type : action.form.method ,
				data : action.form.data ,
				cache : false ,
				contentType : action.form.contentType == false ? false : "application/x-www-form-urlencoded; charset = UTF-8" ,
				processData : false ,
				xhrFields: {
					withCredentials: true // Обязательно для передачи кук
				},
				beforeSend : function( ){
					action.beforeSend( obj , attrs );
					// .. debug
					console.log( '[ ' + tmp_action + ' ] [ url: ' + this.url + ' ] [ type: ' + this.type + ' ]' );
				} ,
				complete : function( msg , status ){
					if( ! dataJSON ) return false;
					action.complete( obj , dataJSON , attrs );
					dinamic_prepare( );
				} ,
				success : function( dataLoaded ){
					// console.log( '> success: ' + JSON.stringify( dataLoaded ) );
					
					// .. если ничего не получили - выходим из цикла
					if( dataLoaded == false ) return false ;
					
					// console.log( '[ ' + tmp_action + ' ] Данные успешно загружены! [ ' + JSON.stringify( dataLoaded ).length + ' bytes ] [ ' + JSON.stringify( dataLoaded ) + ' ]' );
					// ## console.log( '[ ' + tmp_action + ' ] Данные успешно загружены! [ ' + JSON.stringify( dataLoaded ).length + ' bytes ]' );
					
					// .. обновляем информацию о странице
					// ## formblockP.updatePageTime( layers , this.data , JSON.stringify( dataLoaded ).length );
					// .. преобразуем данные
					if( this.dataType == 'JSON' ) dataJSON = dataLoaded;
					else dataJSON = JSON.parse( dataLoaded );
					// .. скрываем PRELOAD
					// ## layers.tb_preload.removeClass( 'active' ).hide( );
				} ,
				error : function( jqXHR , textStatus , errorThrown ){
					console.log( '> ' + tmp_action + ' : Error synchronisation data\n >> url : ' + this.url + '\n >> textStatus : ' + textStatus + '\n >> errorThrown : ' + errorThrown + '' );
					// $.each( jqXHR , function( k , v ){ console.log( actionName + ' : Error synchronisation data. Form [ jqXHR : ' + k + ' = ' + v + ' ]' ); } );
				}
			});
		}
		
		return false;
	} ,
	a : function( obj , action , attrs = { } ){
        // if( typeof obj == 'string' ) obj = $( obj );

        this.data = { };
		this.form = { };
		

		if( ! this[ action ] ){
			console.log( 'Нет вызванной функции: ' + action + '.' );
			return;
		} else {
            return this.ajax( obj , action , attrs );
        }
	}
};
