<?PHP

namespace damoony\login;

class algoritm_lang_form
	extends \algoritm_ {
	# vars
	# public algoritm $algoritm;
	public null|\algoritmdata_ $algoritmdata = null;
	public \visuotag $vt;
	public array $args = [ ];
	# methods
	public function __construct( \main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritm = $main->algoritm;
		$this->algoritmdata = new \algoritmdata_( );
	}
	
	
	public function args ( array $args ) {
		$this->args = $args;
	}
	public function algoritm ( ) {
		$_SESSION[ 'login' ][ 'redirect' ] = $_SERVER[ 'HTTP_REFERER' ];
	}

	public function release ( ) {
		$main = $this->main;
		if( $main->user->is_guest( ) ){
			$this->algoritmdata->set( );
		} else {
			$this->algoritmdata->set( );
		}

		$available = [ '' ];
		$auth = false;
		$search = true;
		$default = $this->main->library->url->static_base[ 2 ] == '' ? true : false;

		$this->algoritmdata->set( $available , $auth , $search , $default );

		# release
		$this->release_( $this );
	}
	
	public function default ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		$lang = $main->language;

		$is_ajax = $main->render->getdata( 'render_ajax' );
		$is_guest = $main->user->is_guest( );

		if( $is_ajax && $_POST ){
			return $this->ajax( );
		} else {
			if( $is_guest ) return $this->default_guest( );
			else return $this->default_logged( );
		}
	}

	public function ajax ( ) {
		$main = $this->algoritm->main;
		$is_guest = $main->user->is_guest( );
		if( $is_guest ) return $this->ajax_guest( );
		else return $this->ajax_logged( );
	}

	public function ajax_guest ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$var = $main->library->vars;
		$library = $main->library;
		$vars = $library->vars;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$top = $main->alliasglobal->top;
		$center = $main->alliasglobal->center;
		$bottom = $main->alliasglobal->bottom;
		
		$headers = $main->alliasglobal->headers;
		$scheme = $main->alliasscheme;
		
		# struct
		$struct = $main->visuostruct->ajax( );
		
		# struct : init
		$struct_address = $struct->address( );
		$struct_top = $struct->top( );
		$struct_center = $struct->center( );
		$struct_bottom = $struct->bottom( );
		
		$vt_top = new \visuotag( );
		$vt_top = $top->as_tag( );
		$vt_center = new \visuotag( );
		$vt_center->tag( 'div' )->content( 'login' );
		$vt_bottom = new \visuotag( );
		$vt_bottom = $bottom->as_tag( );

		# struct : set
		$struct_address->set( $link->href( true , true ) );
		$struct_top->set( $top->as_tag( ) );
		$struct_center->set( $vt_center );
		$struct_bottom->set( $bottom->as_tag( ) );
		
		$visuorelease->release_struct( $struct );
	}

	public function ajax_logged ( ) {

	}

	public function default_guest ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$left = $main->alliasglobal->left;
		$center = $main->alliasglobal->center;
		$right = $main->alliasglobal->right;
		
		/*
		$db = $main->db;
		$db->schema( )->drop( 'testusers' )->run( );
		$db->schema( )->create(
			table: 'testusers' ,
			columns: [
				col( name: 'id'     , type: 'int'    , unsigned: true , auto: true , nullable: false ) ,
				col( name: 'login'  , type: 'string' , length: 64 , nullable: false ) ,
				col( name: 'email'  , type: 'string' , length: 128 , nullable: false ) ,
				col( name: 'role'   , type: 'enum'   , values: [ 'user' , 'admin' ] , default: 'user' ) ,
				col( name: 'bio'    , type: 'text' ) ,
				col( name: 'created', type: 'timestamp' , default: 'CURRENT_TIMESTAMP' ) ,
			] ,
			primary: 'id' ,
			indexes: [ [ 'name'=>'idx_email' , 'columns'=>[ 'email' ] , 'unique'=>true ] ] ,
		)->run( );

		$inputlogin_login = new \visuoelement( $main->visuorelease );
		$inputlogin_login->input( )->text( );
		$inputlogin_login->get_input_text__data( )->set( 'form[login]' , label_comment : 'Логин' );
		$inputlogin_password = new \visuoelement( $main->visuorelease );
		$inputlogin_password->input( )->password( );
		$inputlogin_password->get_input_password__data( )->set( 'form[password]' , label_comment : 'Пароль' );
		$inputlogin_remember = new \visuoelement( $main->visuorelease );
		$inputlogin_remember->input( )->checkbox( );
		$inputlogin_remember->get_input_checkbox__data( )
			->set( 'form[remember]' , text : 'Включите данный пункт, чтобы запомнить учетную запись в системе' , input_checked : true );
		$inputlogin_submit = new \visuoelement( $main->visuorelease );
		$inputlogin_submit->input( )->submit( );
		$inputlogin_submit->get_input_submit__data( )->set_label( 'Пароль' );
		$inputlogin_reset = new \visuoelement( $main->visuorelease );
		$inputlogin_reset->input( )->reset( );
		$inputlogin_reset->get_input_reset__data( )->set_label( 'Пароль' );

		
		$tablelogin = new \visuoelement( $main->visuorelease );
		$tablelogin->table( )->table( );
		$tablelogin__data = $tablelogin->get_table_table__data( );
		$tablelogin__data->clear( )
			// ->add_thead( [ 'Логин' , [ $inputlogin_login ] ] )
			->add_tbody( [ 'Данные для авторизации:' , [ $inputlogin_login , $inputlogin_password ] ] , colelsinline : [ false , false ] , colwidth : [ '30%' , '70%' ] )
			->add_tbody( [ 'Контроль:' , [ 'Загрузка капчи' , 'Second' ] ] , colelsinline : [ false , true ] )
			->add_tbody( [ 'Дополнительно:' , [ $inputlogin_remember ] ] )
			->add_tfoot( [ 'Действия' , [ $inputlogin_submit , $inputlogin_reset ] ] , colelsinline : [ false , true ] );
		
			# die( $form->show( ) );
		$form = new \visuoelement( $visuorelease );
		$form->form( )->form( );
		$form__data = $form->get_form_form__data( );
		$href = $link->href( $url_domain , [ $url_base[ 0 ] , 'login' ] , [ ] , use_all_adress_data : false );
		
		$form__data->set( 'testform', 'post' , $href , title_text : 'Форма авторизации' , form_jquery : true );
		$form__data->add( $tablelogin );
		
		# $form->init( );
		# print $form->show( );
		# die( 'die: ' . __FILE__ . ' : ' . __LINE__ );

		$vars = $main->library->vars;

		$tablebox = new \visuoelement( $visuorelease );
		$tablebox->table( )->tablebox( );
		$tablebox->get_table_tablebox__data( )->clear( );
		$tablebox->get_table_tablebox__data( )->add_box( 'Форма авторизации' , $form );

		$vt_center = new \visuotag( );
		$vt_center->element( 'tablebox' , $tablebox );
		*/

		# testвц
		
		$vt_center = new \visuotag( );
		
		$inputlogin_login = new \visuoelement( $visuorelease );
		$inputlogin_login->input( )->text( );
		$inputlogin_login->get_input_text__data( )->set( 'form[login]' , label_comment : 'Логин' );
		
		$inputlogin_login2 = new \visuoelement( $visuorelease );
		$inputlogin_login2->input( )->text( );
		$inputlogin_login2->get_input_text__data( )->set( 'form[login]' , label_comment : 'Логин2' );

		$form = new \visuoelement( $visuorelease );
		$form->form( )->form( );
		$form__data = $form->get_form_form__data( );
		$form__data->set( 'testform', 'post' , 'test' , title_text : 'Форма авторизации' , form_jquery : true );
		$form__data->add( $inputlogin_login );
		$form__data->add( $inputlogin_login2 );
		
		$vt_center->element( 'tablebox' , $form );

		$center->visuotag( )->append( $vt_center );
		
		return true;
	}

	public function default_logged ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$left = $main->alliasglobal->left;
		$center = $main->alliasglobal->center;
		$right = $main->alliasglobal->right;

		$vt_center = new \visuotag( );
		$vt_center->tag( 'div' , style : 'font-size : 20px ; margin : 100px auto ; text-align : center ; ' )->content( 'Вы уже авторизованы' );

		$center->visuotag( )->append( $vt_center );

		return true;
	}
	
}

?>