<?PHP

namespace damoony\login;

class algoritm_tests_visuoelement
	extends algoritm_tests {
	
	public array $available = [ '' ];
	protected string $page_name = 'visuoelement';
	
	protected function page_content ( ) {
		$vt = new \visuotag( );
		$visuorelease = $this->main->visuorelease;
		
		$vt->tag( 'h2' )->content( 'Visuoelement form' );
		$vt->tag( 'p' )->content( 'A simple form with three fields.' );
		
		$input_name = new \visuoelement( $visuorelease );
		$input_name->input( )->text( );
		$input_name->get_input_text__data( )->set( 'test[name]' , label_comment : 'Name' );
		
		$input_email = new \visuoelement( $visuorelease );
		$input_email->input( )->text( );
		$input_email->get_input_text__data( )->set( 'test[email]' , label_comment : 'Email' );
		
		$input_submit = new \visuoelement( $visuorelease );
		$input_submit->input( )->submit( );
		$input_submit->get_input_submit__data( )->set( 'test[submit]' , 'Send' );
		
		$vt->tag( 'div' , class : 'test-form' )
			->element( 'inside' , $input_name )
			->element( 'inside' , $input_email )
			->element( 'inside' , $input_submit );
		
		return $vt;
	}
}