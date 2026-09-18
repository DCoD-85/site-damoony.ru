<?PHP

class messages {
	# vars
	public null|main $main = null;
	public array $messages = [ ];
    # methods
	public function __construct ( null|main $main = null ) {
		$this->main = $main;
	}

    public function count ( ) {
        return count( $this->messages );
    }

    public function add ( string $status , string $title , string $text ) {
        $this->messages[ ] = [ 'status'=>$status , 'title'=>$title , 'text'=>$text ];
    }

    public function as_tag ( ) {
        $main = $this->main;

        $vt = new visuotag( );

        $messages = $this->messages;
        
        if( ! empty( $messages ) ){
            foreach( $messages as $key=>$value ){
                $status = $value[ 'status' ];
                $title = $value[ 'title' ];
                $text = $value[ 'text' ];
                $image_close = DSA . 'media' . DSA . 'release' . DSA . $main->main_settings->skin( 'skin' ) . DSA . 'icons' . DSA . 'min' . DSA . 'close.png';
                $image_full = DSA . 'media' . DSA . 'release' . DSA . $main->main_settings->skin( 'skin' ) . DSA . 'icons' . DSA . 'min' . DSA . 'full.png';
                $vt->tag( 'div' , class : 'message message_' . $status . ' new' )
                    ->tag( 'div' , class : 'container' )
                        ->tag( 'div' , class : 'divInside' )
                            # ->tag( 'div' , class : 'title' )->content( $title )
                            # ->parent( )
                            ->tag( 'div' , class : 'time' )->content( date( 'H:i d/m/Y' , time( ) ) )
                            ->parent( )
                            ->tag( 'div' , class : 'messagebody' )
                                ->tag( 'div' , class : 'textbox' )->content( $text )
                                ->parent( )
                            ->parent( )
                            # ->tag( 'div' , class : 'debug' )->content( 'debug' )
                            # ->parent( )
                        ->parent( )
                        ->tag( 'div' , class : 'buttons' )
                            ->tag( 'div' , class : 'button close' )->attrs_add( 'alt' , 'Закрыть' )->attrs_add( 'title' , 'Закрыть' )
                                ->tag( 'img' , false )->attrs_add( 'src' , $image_close )
                                ->parent( )
                            ->parent( )
                            ->tag( 'div' , class : 'button full' )->attrs_add( 'alt' , 'Раскрыть' )->attrs_add( 'title' , 'Раскрыть' )
                                ->tag( 'img' , false )->attrs_add( 'src' , $image_full );

            }
        } else {
			$vt->content( '' );
		}
		
        return $vt;
    }

    public function release ( ) {

        return $this->count( ) ? $this->as_tag( )->root( )->release( ) : null;
    }
}

?>