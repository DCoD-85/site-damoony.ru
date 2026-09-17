engine_confidence = {
	engine : null ,
	name : 'engine.confidence' ,
	check : function ( engine ) {
		this.engine = engine;
		console.log( '[ check ][ ' + this.name + ' ] Engine: ' + this.engine._scriptname + '.' );
	} ,
	showconfidence : function( obj ) {
		let o = $( obj );
		o.parents( '.bottom_body' ).addClass( 'show' );
		return false;
	} ,
	hide : function ( obj ) {
		let o = $( obj );
		o.parents( '.bottom_agreement' ).addClass( 'hide' );
		return false;
	} , 
	show : function ( obj ) {

	}
};