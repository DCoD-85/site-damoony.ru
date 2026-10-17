<?PHP

abstract class visuoel_message__element extends visuoel_group_element {
	abstract protected function message_type ( ) : string;

	public function data ( ) : visuoel_message__element__data {
		/** @var visuoel_message__element__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_message__element__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->data();

		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : 'message message_' . $this->message_type( ) );

		if ( $d->text !== '' ) {
			$vt->content( $d->esc( $d->text ) );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_message__element__data extends visuoel_group_element__data {
	public string $text = '';

	public function set ( string $text ) : static {
		$this->text = $text;
		return $this;
	}
}