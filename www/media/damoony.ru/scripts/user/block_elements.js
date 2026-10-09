

// Messages
$( document ).ready(function() {
	mesagges_show( false , true );
});

mesagges_show = function( appendData , show ){
	tB_messages = $( '#tB_messages' );
	
	// .. если есть данные для добавления
	if( appendData ){
		
		if( typeof appendData == 'object' ){
			$( appendData ).each( function( i , elem ){
				tB_messages.append( elem );
			});
		}
		else
			tB_messages.append( appendData );
	}
	
	messages = $( '.message' );
	message = messages.not( '.fer' );
	
	
	message.hide( );
	tB_messages.show( );
	message.slideToggle( 500 );
	message.addClass( 'fer' );
	message.find( '.button.full' ).bind( 'click' , function( ){
		message = $( this ).parents( '.message' );
		if( message.hasClass( 'fullwindow' ) ) message.removeClass( 'fullwindow' );
		else message.addClass( 'fullwindow' );
	});
	message.find( '.button.close' ).bind( 'click' , function( ){
		message = $( this ).parents( '.message' );
		if( message.hasClass( 'fullwindow' ) ){
				message.detach( );
		}
		else {
			$(this).parents( '.message' ).slideToggle( 500 , function( ){
				$(this).detach( );
			});
		}
	});
	
	
	message.unbind( 'mouseover' ).bind( 'mouseover' , function( ){
		clearTimeout( window.ti );
	} );
	
	if( show == true ){
		$( document ).ready( function( ){
			clearTimeout( window.ti );
			window.ti = window.setTimeout( function( ){
				messages = $( '#tB_messages' ).find( '.message.fer' );
				// console.log( 'M: ' + this + ' | ' + $( message ) + ' | ' + $( message ).html( ) );
				messages.slideToggle( 500 , function( ){
					$(this).detach( );
					clearTimeout( window.ti );
				} );
			} , 3000 );
		});
	}
}


filters_show = function( ){
	filters = $( '.form_filters' );
	span_message = $( '.form_filters .span_message' );
	span_body = $( '.form_filters .span_body' );
	
	if( filters.hasClass( 'visible' ) ){
	}
	else {
		filters.addClass( 'visible' );
		
		span_message.hide( );
		span_body.show( );
	}
}

formfilters = function( obj ){
	$this = $( obj );
	bodymain = $this.parents( 'div' ).eq( 2 ).find( '.bodymain' );
	div = bodymain.find( 'div.filters' );
	/*
	if( bodymain.hasClass( 'filtersactive' ) ){
		bodymain.removeClass( 'filtersactive' );
	}
	else {
		bodymain.show( ).addClass( 'filtersactive' );
	}
	*/
	if( bodymain.hasClass( 'filtersactive' ) ){
		bodymain.removeClass( 'filtersactive' );
	}
	else {
		bodymain.show( ).addClass( 'filtersactive' );
	}
	
	return true;
}

form_filters2 = function( ){
	ap = $( '.ap' );
	ap_topmenu = $( 'span.form_filters' );
	
	if( ap_topmenu.is( ':hidden' ) ){
		$( 'html , body' ).animate( { scrollTop : 0 } , 500 , function( ){
			ap_topmenu.css( { 'top' : '-100%' } ).show( );
			ap_topmenu.animate( { 'top' : '0px' } , 300 );
			// Плавно перемещаемся наверх
		});
	}
	else{
		//ap_topmenu.hide( );
		ap_topmenu.animate( { 'top' : '-100%' } , 300 , function( ){ ap_topmenu.hide( ); });
		
	}
	return false;
}

mainmenu = function( ){
	ap = $( '.ap' );
	ap_topmenu = $( '.ap_topmenu' );
	
	/*
	if( ap_topmenu.is( ':hidden' ) ){
		ap_topmenu.show( );
		ap.hide( );
	}
	else{
		ap_topmenu.hide( );
		ap.show( );
	}
	*/
	if( ap_topmenu.is( ':hidden' ) ){
		ap_topmenu.css( { 'left' : '-100%' } ).show( );
		ap_topmenu.animate( { 'left' : '0px' } , 300 , function( ){ ap.hide( ); } );
		
	}
	else{
		//ap_topmenu.hide( );
		ap.show( );
		ap_topmenu.animate( { 'left' : '-100%' } , 300 , function( ){ ap_topmenu.hide( ); });
		
	}
	
	return false;
}

block_element_cur = function( el , el_num ){
	$this = $( el );
	slider = $this.parents( '.slider' );
	els_block = slider.find( '.element_block' );
	els_block.hide( ).css( { 'opacity' : '0.0' });
	els_block.eq( el_num ).show( ).css( { 'opacity' : '1.0' });
	
	els_tabs = slider.find( '.slider_title span' );
	els_tabs.removeClass( 'selected' );
	els_tabs.eq( el_num ).addClass( 'selected' );
}

block_element = function( el , action='' ){
	
	if( action == false ){
		// Работаем с переключателями
		$el = $( el ).parent( 'span' );
		$els = $el.parents( '.slider_title' ).eq( 0 ).find( 'span' );
		
		$els.removeClass( 'selected' );
		$el.addClass( 'selected' );
		$indexNumSelected = 0;
		$.each( $els , function( index , value ){
			if( $els.eq( index ).hasClass( 'selected' ) ){
				$indexNumSelected = index;
			}
		} );
		
		// Работаем с блоками
		$blocks = $el.parents( '.slider' ).find( '.element_block' );
		$block_hide = 0;
		$block_show = $indexNumSelected;
		
		$.each( $blocks , function( index , value ){
			if( $blocks.eq( index ).is( ':visible' ) ){
				$block_hide = index;
			}
		} );
		
		// Если блок действительно переключили
		if( $block_hide != $block_show ){
			elcur = $blocks.eq( $block_hide );
			elprev = $blocks.eq( $block_show );
			
			elcur.css( { 'opacity' : '1.0' } );
			elprev.css( { 'opacity' : '0.0' } );
			elcur.animate( { 'opacity' : '0.0' } , 200 , function( ){
				elcur.css( { 'display' : 'none' } );
				elprev.css( { 'display' : 'block' } );
				elprev.animate( { 'opacity' : '1.0' } , 200 , function( ){
				});
			});
		}
	}
	else{
		$el = $( el );
		// Ищем слайдер->все элементы
		$els = $el.parents( '.slider' ).find( '.element_block' );
		// Общее количество блоков
		$els_length = $els.length-1;
		// Перебираем блоки
		
		$block_hide = 0;
		$block_show = 0;
		
		$.each( $els , function( index ){
			if( $els.eq( index ).is( ':visible' ) ){
				$block_hide = index;
			}
		} );
		
		if( action == 'forward' && $block_hide < $els_length ){
			$block_show = $block_hide+1;
		}
		else if( action == 'back' && $block_hide > 0 ){
			$block_show = $block_hide-1;
		}
		else $block_show = $block_hide;
		
		// Работаем с блоками
		$slider = $el.parents( '.slider' );
		$tabs = $slider.find( '.slider_title' );
		if( $tabs.length > 0 ){
			$tabs = $tabs.eq( 0 ).find( 'span' );
			$tabs.eq( $block_hide ).removeClass( 'selected' );
			$tabs.eq( $block_show ).addClass( 'selected' );
		}
		
		if( $block_hide != $block_show ){
			elcur = $els.eq( $block_hide );
			elprev = $els.eq( $block_show );
			
			elcur.css( { 'opacity' : '1.0' } );
			elprev.css( { 'opacity' : '0.0' } );
			elcur.animate( { 'opacity' : '0.0' } , 200 , function( ){
				elcur.css( { 'display' : 'none' } );
				elprev.css( { 'display' : 'block' } );
				elprev.animate( { 'opacity' : '1.0' } , 200 , function( ){
				});
			});
		}
	}
	return true;
}


block_element2 = function( action ){
	elcur = $( '.element_block[style*="block"]' );
	elprev = elcur.prev( '.element_block' );
	elnext = elcur.next( '.element_block' );
	els = $( '.element_block' );
	
	// alert( elcur.attr(  'id' ) + ' + <> ' + elprev.attr( 'id' ) + ' + <> ' + elnext.attr( 'id' ) + ' + <> ' + action+ ' <> ' + els.length );
	
	if( action == 'back' ){
		if( elprev.attr( 'class' ) != undefined ){
			elcur.css( { 'opacity' : '1.0' } );
			elprev.css( { 'opacity' : '0.0' } );
			elcur.animate( { 'opacity' : '0.0' } , 200 , function( ){
				elcur.css( { 'display' : 'none' } );
				elprev.css( { 'display' : 'block' } );
				elprev.animate( { 'opacity' : '1.0' } , 200 , function( ){
				});
			});
			/*
			elcur.css( { 'display' : 'none' } );
			elprev.css( { 'display' : 'block' } );
			*/
		}
	}
	else if( action == 'forward' ){
		if( elnext.attr( 'class' ) != undefined ){
			elcur.css( { 'opacity' : '1.0' } );
			elnext.css( { 'opacity' : '0.0' } );
			elcur.animate( { 'opacity' : '0.0' } , 200 , function( ){
				elcur.css( { 'display' : 'none' } );
				elnext.css( { 'display' : 'block' } );
				elnext.animate( { 'opacity' : '1.0' } , 200 , function( ){
				});
			});
			/*
			elcur.css( { 'display' : 'none' } );
			elnext.css( { 'display' : 'block' } );
			*/
		}
	}
	
	return true;
}


headpopup = function( el , action='' ){
	$el = $( el );
	
	if( action == '' ){
		$par = $el.parents( '#testsort' );
		$inputs = $par.find( 'input , select' );
		
		console.clear( );
		text = '';
		elements = '';
		$.each( $inputs , function( index ){
			name = $inputs.eq( index ).attr( 'name' );
			val = $inputs.eq( index ).val( );
			console.log( 'index [ ' + index + ' ] ' + name + ' = ' + val );
			
			if( $inputs.eq( index ).is( ':checkbox' ) ){
				if( $inputs.eq( index ).is( ':checked' ) ){
					text += '&' + name + '=' + val;
					elements += '<input type="hidden" name="' + name + '" value="' + val + '">';
				}
			}
			else if( $inputs.eq( index ).is( 'select' ) ){
				text += '&' + name + '=' + val;
				elements += '<input type="hidden" name="' + name + '" value="' + val + '">';
			}
			else if( $inputs.eq( index ).is( ':text' ) || $inputs.eq( index ).is( ':hidden' ) ){
				text += '&' + name + '=' + val;
				elements += '<input type="hidden" name="' + name + '" value="' + val + '">';
			}
		} );
		h = document.location.href;
		console.log( 'link: ' + document.location.href );
		console.log( 'link: ' + $inputs.serialize() );
		
		h += text;
		// document.location.href = h;
		
		$form_p = $par.parents( 'form' );
		arr = '';
		$form_p.each( function( ){
			$.map( this.attributes , function( attribute ){
				arr += ' ' + attribute.name + '="' + attribute.value + '"';
			} );
		} );
		
		$tB_load = $( '#tB_load' );
		$tB_load.html( '' );
		$tB_load.html( '<form' + arr + '>' + elements + '<input type="submit" name="f" value="f" /></form>' );
		f = $tB_load.find( 'form' );
		f.attr( { 'method' : 'GET' } );
		f.submit( );
	}
	else {
		if( action == 'close' ){
			$par = $el.parents( '#testsort' ).eq( 0 );
			$par.animate( { 'opacity' : '0.0' } , 400 , function( ){
				$( this ).hide( );
			});
		}
		else if( action == 'open' ){
			$par = $el.parents( '#testsort_header' ).eq( 0 ).find( '#testsort' ).eq( 0 );
			$par.css( { 'opacity' : '0.0' } ).show( );
			$par.animate( { 'opacity' : '1.0' } , 400 , function( ){
			});
		}
	}
}