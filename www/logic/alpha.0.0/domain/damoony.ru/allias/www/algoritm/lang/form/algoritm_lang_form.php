<?PHP

class algoritm_lang_form {
	# vars
	public algoritm $algoritm;
	public visuotag $vt;
	public array $args = [ ];
	public string $path_object;
	# methods
	public function __construct ( algoritm $algoritm , array $args = [ ] ) {
		$this->algoritm = $algoritm;
		$this->args = $args;
	}
	
	public function args ( array $args ) {
		$this->args = $args;
	}
	public function algoritm ( ) {
	}
	public function release ( ) {
		# $available = $this->available ?: [ 'index' , 'form' , 'login' , 'logout' , 'redirect' , 'development.admin.panel' ];
		$available = $this->available ?: [ '' ];
		$search = false;
		$default = true;
		# 
		# $this->algoritm->include_ext( $this , $available , $search , $default );
		$this->default( );
	}
	
	public function default ( ) {
		$jsonencode = fn( $value ) => str_replace( '"' , '&quot;' , json_encode( $value ) );
		
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		$center = $main->alliasglobal->center;

		$db = $main->db->db( );
		$query = $db->query( );
		$query->droptable( 'testingtable' );
		
		$query = $db->query( );
		$query->createtable( 'testingtable' , [
			'`id` int( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT' ,
			'`status` enum( \'activated\' , \'unactivated\' ) NOT NULL DEFAULT \'activated\'' ,
			# "`created_timestamp` timestamp NOT NULL DEFAULT current_timestamp( ) COMMENT 'Дата создания'" ,
			# "`created_users_id` int( 10 ) UNSIGNED NOT NULL COMMENT 'Автор'"
		] );
		# print $db->log( );

		$logged_guest = $main->user->is_guest( );
		
		if( $logged_guest == true ){
			
			$form = new visuoelement( $visuorelease );
			$form->form( )->form( );
			$form->get_element( )->data( )->set( 'form11' , 'GET' );
			$input_hidden = new visuoelement( $visuorelease );
			$input_hidden->input( )->hidden( );
			$input_hidden->get_input_hidden__data( )->set_var( 'name' , 'input_hidden' )->set_var( 'value' , 'input_hidden_value' );
			
			$input_text_on = new visuoelement( $visuorelease );
			$input_text_on->input( )->text( );
			$input_text_on->get_input_text__data( )->set_var( 'readonly' , false )
				->set_label( 'Test' )
				->set_label_comment( 'Арбелоа об ответной игре с «Ман Сити»: «Реал» должен играть так же, как и в первом матче, или даже лучше. Скромность и амбиции – вот что нам нужно увидеть»
	22:00 Тесты и игры Кто дороже –' )
				->set_popup( true , 'bottom' )
				->set_var( 'name' , 'input_text_on' )->set_var( 'value' , 'input_text_on_value' );
			$input_text_off = new visuoelement( $visuorelease );
			$input_text_off->input( )->text( );
			$input_text_off->get_input_text__data( )->set_var( 'readonly' , true )
				->set_label( 'Test' )
				->set_label_comment( 'Арбелоа об ответной игре с «Ман Сити»: «Реал» должен играть так же, как и в первом матче, или даже лучше. Скромность и амбиции – вот что нам нужно увидеть»
	22:00 Тесты и игры Кто дороже –' )
				->set_var( 'name' , 'input_text_off' )->set_var( 'value' , 'input_text_off_value' );
			
			$input_password_on = new visuoelement( $visuorelease );
			$input_password_on->input( )->password( );
			$input_password_on->get_input_password__data( )->set_var( 'readonly' , false )
				->set_var( 'name' , 'input_password_on' )->set_var( 'value' , 'input_password_on_value' );
			$input_password_off = new visuoelement( $visuorelease );
			$input_password_off->input( )->password( );
			$input_password_off->get_input_password__data( )->set_var( 'readonly' , true )
				->set_var( 'name' , 'input_password_off' )->set_var( 'value' , 'input_password_off_value' );
			
			$input_button_on = new visuoelement( $visuorelease );
			$input_button_on->input( )->button( );
			$input_button_on->get_input_button__data( )->set_var( 'readonly' , false )
				->set_var( 'name' , 'input_button_on' )->set_var( 'value' , 'input_button_on_value' );
			$input_button_off = new visuoelement( $visuorelease );
			$input_button_off->input( )->button( );
			$input_button_off->get_input_button__data( )->set_var( 'readonly' , true )
				->set_var( 'name' , 'input_button_off' )->set_var( 'value' , 'input_button_off_value' );
			
			$input_submit_on = new visuoelement( $visuorelease );
			$input_submit_on->input( )->submit( );
			$input_submit_on->get_input_submit__data( )->set_var( 'readonly' , false )
				->set_var( 'name' , 'input_submit_on' )->set_var( 'value' , 'input_submit_on_value' );
			$input_submit_off = new visuoelement( $visuorelease );
			$input_submit_off->input( )->submit( );
			$input_submit_off->get_input_submit__data( )->set_var( 'readonly' , true )
				->set_var( 'name' , 'input_submit_off' )->set_var( 'value' , 'input_submit_off_value' );
			
			$input_checkbox_on = new visuoelement( $visuorelease );
			$input_checkbox_on->input( )->checkbox( );
			$input_checkbox_on->get_input_checkbox__data( )->set_var( 'readonly' , false )
				->set_var( 'name' , 'input_checkbox_on' )->set_var( 'value' , 'input_checkbox_on_value' );
			$input_checkbox_off = new visuoelement( $visuorelease );
			$input_checkbox_off->input( )->checkbox( );
			$input_checkbox_off->get_input_checkbox__data( )->set_var( 'readonly' , true )
				->set_var( 'name' , 'input_checkbox_off' )->set_var( 'value' , 'input_checkbox_off_value' );
			
			$input_radio_on = new visuoelement( $visuorelease );
			$input_radio_on->input( )->radio( );
			$input_radio_on->get_input_radio__data( )->set_var( 'readonly' , false )
				->set_var( 'name' , 'input_radio' )->set_var( 'value' , 'input_radio_on_value' );
			$input_radio_off = new visuoelement( $visuorelease );
			$input_radio_off->input( )->radio( );
			$input_radio_off->get_input_radio__data( )->set_var( 'readonly' , true )
				->set_var( 'name' , 'input_radio' )->set_var( 'value' , 'input_radio_off_value' );
				
			$input_select_on = new visuoelement( $visuorelease );
			$input_select_on->input( )->select( );
			$input_select_on->get_input_select__data( )->set_var( 'readonly' , false )
				->set_var( 'name' , 'input_select_on' )->set_var( 'value' , 'input_select_on_value' );
			$input_select_off = new visuoelement( $visuorelease );
			$input_select_off->input( )->select( );
			$input_select_off->get_input_select__data( )->set_var( 'readonly' , true )
				->set_var( 'name' , 'input_select_off' )->set_var( 'value' , 'input_select_off_value' );
			
			$input_textarea_on = new visuoelement( $visuorelease );
			$input_textarea_on->input( )->textarea( );
			$input_textarea_on->get_input_textarea__data( )->set_var( 'readonly' , false )
				->set_var( 'name' , 'input_textarea_on' )->set_var( 'value' , 'input_textarea_on_value' );
			$input_textarea_off = new visuoelement( $visuorelease );
			$input_textarea_off->input( )->textarea( );
			$input_textarea_off->get_input_textarea__data( )->set_var( 'readonly' , true )
				->set_var( 'name' , 'input_textarea_off' )->set_var( 'value' , 'input_textarea_off_value' );


			$input_text2 = new visuoelement( $visuorelease );
			$input_text2->input( )->text( );
			# $input_radio1 = new visuoelement( $visuorelease );
			# $input_radio1->input( )->radio( );
			$input_checkbox1 = new visuoelement( $visuorelease );
			$input_checkbox1->input( )->checkbox( );
			
			$input_button1 = new visuoelement( $visuorelease );
			$input_button1->input( )->button( );
			$input_submit1 = new visuoelement( $visuorelease );
			$input_submit1->input( )->submit( );

			$input_reset1 = new visuoelement( $visuorelease );
			$input_reset1->input( )->reset( );
			# $textarea = new visuoelement( $visuorelease );
			# $vt_test->tag( 'div' , style : 'padding : 10px' )->element( 'select12' , $select12 );
			# $textarea->input( )->checkbox( );
					
			$blocktext_hidden1 = new visuoelement( $visuorelease );
			$blocktext_hidden1->block( )->text( );
			$blocktext_hidden1->block_text__data( )->set( 'input[ hidden ]' );
			
			$blocktext_hidden2 = new visuoelement( $visuorelease );
			$blocktext_hidden2->block( )->text( );
			$blocktext_hidden2->block_text__data( )->set( 'Скрытое поле' );
			
			$testload_result = new visuotag( );
			$testload_result->tag( 'div' , id : 'load_result' , return : false );
			$testload = new visuotag( );
			$testload
				->tag( 'a' )
					->attrs_add( 'href' , 'javascript:;' )
					->attrs_actions_add( 'onclick' , 'engine.load.a( this , \'load\' , ' . $jsonencode( [ 'layerto'=>'#load_result' , 'src'=>'index2' ] ) . ' );' , true )
					->content( 'Test load' );
			
			$table = new visuoelement( $visuorelease );
			$table->table( )->table( );
			$table__data = $table->get_table_table__data( );
			$table__data->clear( )
				->add_thead( [ 'Элемент' , 'Описание' , 'Представление: on | off' ] , colwidth : [ '30%' , '30%' , '40%' ] )
				->add_tbody( [ [ $testload_result ] , [ $testload ] ] , colspan : [ 2 , 1 ] , colwidth : [ '60%' , '40%' ] )
				->add_tbody( [ $blocktext_hidden1 , $blocktext_hidden2 , [ $input_hidden ] ] , colwidth : [ '30%' , '30%' , '40%' ] )
				->add_tbody( [ 'input[text]' , 'Текстовое поле text.' , [ $input_text_on , $input_text_off ] ] , colelsinline : [ false , false , true ] )
				->add_tbody( [ 'input[password]' , '' , [ $input_password_on , $input_password_off ] ] )
				->add_tbody( [ 'input[button]' , '' , [ $input_button_on , $input_button_off ] ] )
				->add_tbody( [ 'input[submit]' , '' , [ $input_submit_on , $input_submit_off ] ] )
				->add_tbody( [ 'input[checkbox]' , '' , [ $input_checkbox_on , $input_checkbox_off ] ] , colelsinline : [ false , false , true ] )
				->add_tbody( [ 'input[radio]' , '' , [ $input_radio_on , $input_radio_off ] ] )
				->add_tbody( [ 'select' , '' , [ $input_select_on , $input_select_off ] ] )
				->add_tbody( [ 'textarea' , '' , [ $input_textarea_on , $input_textarea_off ] ] )
				## ->add_tbody( [ 'select' , '123' , $select1 ] )
				->add_tfoot( [ '' , [ $input_submit1 , $input_button1 , $input_reset1 ] ] , colspan : [ 1 , 2 ] , colelsinline : [ false , true ] );
			
			$form->get_form_form__data( )->set( 'testform', 'post' , $link->href( $url_allias , [ 'login' ] , $url_vars ) );
			$form->get_form_form__data( )->add( $table );
			

			$inputlogin_login = new visuoelement( $main->visuorelease );
			$inputlogin_login->input( )->text( );
			$inputlogin_login->get_input_text__data( )->set( 'form[login]' , label_comment : 'Логин' );
			$inputlogin_password = new visuoelement( $main->visuorelease );
			$inputlogin_password->input( )->password( );
			$inputlogin_password->get_input_password__data( )->set( 'form[password]' , label_comment : 'Пароль' );
			$inputlogin_remember = new visuoelement( $main->visuorelease );
			$inputlogin_remember->input( )->checkbox( );
			$inputlogin_remember->get_input_checkbox__data( )
				->set( 'form[remember]' , text : 'Запомнить' , input_checked : false );
			$inputlogin_submit = new visuoelement( $main->visuorelease );
			$inputlogin_submit->input( )->submit( );
			$inputlogin_submit->get_input_submit__data( )->set_label( 'Пароль' );
			$inputlogin_reset = new visuoelement( $main->visuorelease );
			$inputlogin_reset->input( )->reset( );
			$inputlogin_reset->get_input_reset__data( )->set_label( 'Пароль' );

			
			$tablelogin = new visuoelement( $main->visuorelease );
			$tablelogin->table( )->table( );
			$tablelogin__data = $tablelogin->get_table_table__data( );
			$tablelogin__data->clear( )
				// ->add_thead( [ 'Логин' , [ $inputlogin_login ] ] )
				->add_tbody( [ 'Данные для авторизации:' , [ $inputlogin_login , $inputlogin_password ] ] , colelsinline : [ false , true ] )
				->add_tbody( [ 'Контроль:' , [ 'Загрузка капчи' , 'Second' ] ] , colelsinline : [ false , true ] )
				->add_tbody( [ 'Дополнительно:' , [ $inputlogin_remember ] ] )
				->add_tfoot( [ 'Действия' , [ $inputlogin_submit , $inputlogin_reset ] ] , colelsinline : [ false , true ] );
			
			# die( $form->show( ) );
			$form2 = new visuoelement( $visuorelease );
			$form2->form( )->form( );
			$form2->get_form_form__data( )->set( 'testform', 'post' , $link->href( $url_domain , [ $url_base[ 0 ] , 'login' ] , $url_vars ) , title_text : 'Форма авторизации' );
			$form2->get_form_form__data( )->add( $tablelogin );
			
			

			$vars = $main->library->vars;
			

			$el1 = new visuoelement( $visuorelease );
			$el1->input( )->text( );
			$el1->get_input_text__data( )->set_label( 'Мой новый мир!' )->set_label_comment( 'Да здравствует ЮССР' );
			$el2 = new visuoelement( $visuorelease );
			$el2->input( )->text( );
			$el2->get_input_text__data( )->set_label( 'Мой новый мир!' )->set_label_comment( 'Да здравствует ЮССР' );
			$el3 = new visuoelement( $visuorelease );
			$el3->input( )->text( );
			$el3->get_input_text__data( )->set_label( 'Мой новый мир!' )->set_label_comment( 'Да здравствует ЮССР' );
			$el4 = new visuoelement( $visuorelease );
			$el4->input( )->submit( );
			$el4->get_input_submit__data( )->set_label( 'Мой новый мир!' )->set_label_comment( 'Да здравствует ЮССР' );

			$tablebox = new visuoelement( $visuorelease );
			$tablebox->table( )->tablebox( );
			$tablebox->get_table_tablebox__data( )->clear( );
			$tablebox->get_table_tablebox__data( )->add_box( 'Форма авторизации' , $form2 );
			$tablebox->get_table_tablebox__data( )->add_box( 'Восстановление учетной записи' , [ $el1 , $el2 , $el3 , $el4 ] );
			# $tablebox->init( );
			
			$vt_center = new visuotag( );
			$vt_center
					->element( 'tablebox' , $tablebox , true );
		} else {

			$vt_center = new visuotag( );
			$vt_center->tag( 'div' )->content( 'Вы уже авторизованы!' );
		}

		$center->visuotag( )->append( $vt_center );

	}
	
}

?>