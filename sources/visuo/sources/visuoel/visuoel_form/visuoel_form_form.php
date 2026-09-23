<?PHP

# visuoel_form_form — элемент form\form.
#
# build() строит:
#   <form action name method enctype data-form-jquery data-load>
#     [<div class="form_title">…</div>]      ← если title_text
#     <div class="form_body"></div>          ← сюда идут дети
#   </form>
#
# vt_start = <form>                       (attach point для группы)
# vt_end   = <div class="form_body">      (слот для детей)

class visuoel_form_form extends visuoel_group_element {
	protected string $tag       = 'form';
	protected string $css_class = 'form_form';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_form_form__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$form = new visuotag( );
		$form = $form->tag( 'form' );

		if ( $v = $d->form_action  ) $form->attrs_add( 'action'  , $v );
		if ( $v = $d->form_name    ) $form->attrs_add( 'name'    , $v );
		if ( $v = $d->form_method  ) $form->attrs_add( 'method'  , $v );
		if ( $v = $d->form_enctype ) $form->attrs_add( 'enctype' , $v );

		if ( $d->form_jquery )       $form->data_add( 'form-jquery' , $d->form_jquery );
		if ( $d->form_data_jquery )  $form->data_add( 'load'        , $d->form_data_jquery );

		if ( $v = $d->title_text ) {
			$form = $form->tag( 'div' , class : 'form_title' )->content( $v )->parent( );
		}

		if ( $v = $d->form_class ) {
			$form->attrs_class_add( ' ' . $v );
		}

		$body = $form->tag( 'div' , class : 'form_body' );

		$this->vt_start = $form;   # <form> node
		$this->vt_end   = $body;   # <div class="form_body"> node
	}

	public function fieldset ( null|visuoel__data $__data = null ) : visuoel_form_fieldset {
		$data = $__data instanceof visuoel_form_fieldset__data ? $__data : null;
		$el = new visuoel_form_fieldset( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
}

class visuoel_form_form__data extends visuoel_group_element__data {
	# --- state ---
	public string $form_name        = 'form';
	public string $form_method      = 'post';
	public string $form_action      = '';
	public string $form_enctype     = 'application/x-www-form-urlencoded';
	public bool   $form_jquery      = false;
	public array  $form_data_jquery = [ 'on' => true , 'layerto' => 'load_result' ];
	public string $title_text       = '';
	public string $form_class       = '';

	# --- methods ---
	public function set ( string $form_name ,
	                      string $form_method = 'POST' ,
	                      string $form_action = '' ,
	                      string $form_enctype = 'application/x-www-form-urlencoded' ,
	                      bool   $form_jquery = false ,
	                      string $title_text = '' ,
	                      string $form_class = '' ) : static {

		$form_method = strtolower( $form_method );
		$this->form_name   = $form_name;
		$this->form_method = $form_method;
		$this->form_action = $form_action;

		if ( $form_method === 'get' ) {
			$this->form_enctype = 'get/html';
		} elseif ( $form_method === 'post' ) {
			$this->form_enctype = 'application/x-www-form-urlencoded';
		} elseif ( $form_method === 'file' ) {
			$this->form_method  = 'POST';
			$this->form_enctype = 'multipart/form-data';
		} else {
			$this->form_enctype = $form_enctype;
		}

		$this->form_jquery = $form_jquery;
		$this->title_text  = $title_text;
		$this->form_class  = $form_class;

		return $this;
	}
}