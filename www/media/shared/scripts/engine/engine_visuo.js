// engine_visuo.js — контроллер visuo-виджетов (M1)
var engine_visuo = {
    engine  : null ,
    name    : 'engine.visuo' ,
    actions : {} ,

    check : function ( engine ) {
        this.engine = engine;
        console.log( '[ check ][ ' + this.name + ' ] Engine: ' + this.engine._scriptname + '.' );
    } ,
    on : function ( name , fn ) { this.actions[ name ] = fn; return this; } ,
    run : function ( name , el , event ) {
        var fn = this.actions[ name ];
        if ( typeof fn === 'function' ) return fn( el , event );
        console.warn( '[ Visuo ] unknown action: ' + name );
    }
};
window.Visuo = engine_visuo;

engine_visuo.on( 'confidence-show' , function ( el ) {
    if ( window.engine_confidence ) engine_confidence.showconfidence( el );
} );
engine_visuo.on( 'confidence-hide' , function ( el ) {
    if ( window.engine_confidence ) engine_confidence.hide( el );
} );

engine_visuo.on( 'tabs-show' , function ( el ) {
    var target = el.getAttribute( 'data-tab-target' );
    if ( ! target ) return;
    var tabs = el.closest( '.tabs' );
    if ( ! tabs ) return;
    tabs.querySelectorAll( '.hhead .tab_title' ).forEach( function ( t ) { t.classList.remove( 'selected' ); } );
    tabs.querySelectorAll( '.bbody .tab_page'  ).forEach( function ( p ) { p.classList.remove( 'selected' ); } );
    el.classList.add( 'selected' );
    var page = tabs.querySelector( '.bbody #' + target );
    if ( page ) page.classList.add( 'selected' );
} );

document.addEventListener( 'click' , function ( e ) {
    var el = e.target.closest( '[data-visuo-action], [data-action]' );
    if ( ! el ) return;
    var name = el.getAttribute( 'data-visuo-action' ) || el.getAttribute( 'data-action' );
    if ( ! name ) return;
    engine_visuo.run( name , el , e );
} );