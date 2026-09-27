function visuotabs_show ( el ) {
	var tabs = el.closest( '.tabs' );
	if ( ! tabs ) return;

	var target = el.getAttribute( 'data-tab-target' );
	if ( ! target ) return;

	tabs.querySelectorAll( '.tab_title' ).forEach( function ( t ) {
		t.classList.remove( 'selected' );
	} );
	tabs.querySelectorAll( '.tab_page' ).forEach( function ( p ) {
		p.classList.remove( 'selected' );
	} );

	el.classList.add( 'selected' );

	var page = tabs.querySelector( '.tab_page#' + target );
	if ( page ) page.classList.add( 'selected' );
}