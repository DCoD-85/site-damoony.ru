// # 
// # 
// # 


$( document ).ready( function( ) {
    engine = {
		_scriptname : 'This is engine script;' ,
		confidence : engine_confidence ,
		prepare : engine_prepare ,
		load : engine_load ,
		popup : engine_popup ,
		page : engine_page ,
	};
	// engine.prepare = engine_prepare;
	// engine.load = engine_load;
	// engine.popup = engine_popup;

	engine.confidence.check( engine );
	engine.prepare.check( engine );
	engine.popup.check( engine );
	engine.load.check( engine );
	engine.page.check( engine );
	// engine.html.check( engine );
} );