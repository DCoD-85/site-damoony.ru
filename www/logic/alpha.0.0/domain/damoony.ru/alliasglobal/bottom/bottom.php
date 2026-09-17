<?PHP

namespace damoony;

class alliasglobal_bottom {
	# vars
	public alliasglobal $alliasglobal;
	public \main $main;
	public \visuorelease $visuorelease;
	public \visuotag $visuotag;
	public array $vts = [ ];
	public null|\visuotag $visuotag_title = null;
	public string $alliasglobal_name = 'bottom';
	public string $allias_accepted = '';
	public bool $settings_on = false;
	public alliasglobal_menu $alliasglobal_menu;
	# methods
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal = $alliasglobal;
		$this->main = $alliasglobal->main;
		$this->visuorelease = $this->main->visuorelease;
		$this->visuotag = new \visuotag( $this->visuorelease );
		$this->allias_accepted = $this->main->main_settings->allias_accepted( );
		$this->alliasglobal_menu = new alliasglobal_menu( $this->alliasglobal , $this->alliasglobal_name );
		
		$this->init( );
	}
	
	public function init ( ) {
		$localname = $this->alliasglobal_name;
		$alliaslocal_on = $this->main->alliaslocal->$localname->settings_on;
		
		if( $alliaslocal_on === true ){
			$this->visuotag->clear( )->append( $this->main->alliaslocal->$localname->visuotag );
		} else {
			$this->algoritm( );
		}
	}

	public function algoritm ( ) {
		$this->scheme( );
		$this->data ( );
		$this->release ( );
	}
	
	public function scheme ( ) {
		$object = $this;
		
		$visuotag = $object->visuotag;
		$visuotag->tag( 'div' , class : 'bottom_nav' );
		$visuotag->tag( 'div' , class : 'bottom' );
		$visuotag->tag( 'div' , class : 'bottom_agreement' );

		
		$object->vts[ 'root' ] = $visuotag->root( );
		$root = $object->vts[ 'root' ];
		# 
		$object->vts[ 'bottom_nav' ] = $root->find( class : 'bottom_nav' );
		$object->vts[ 'bottom' ] = $root->find( class : 'bottom' );
		$object->vts[ 'bottom_agreement' ] = $root->find( class : 'bottom_agreement' );
	}

	public function data ( ) {
		$main = $this->main;
		$lang = $main->language;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		# data__init
		$this->data__init( );

		
		$data = [ ];
		
		$accept = $main->main_settings->allias_accepted( );

		if( ! is_null( $accept ) ){
			$classglobal_path = __DIR__ . DS;
			$classglobal_path .= $this->alliasglobal_name . '_' . $accept . '.php';
			$classglobal_name = 'alliasglobal_' . $this->alliasglobal_name . '_' . $accept;

			# print 'classglobal_path: ' . $classglobal_path . '<br />';
			# print 'classglobal_name: ' . $classglobal_name . '<br />';

			if( include_once $path = $classglobal_path ){
				if( class_exists( $classglobal_name ) ){
					$classglobal_class = new $classglobal_name( $this );
					# return ;
				}
			} else {
			}
		}
	}

	public function data__init ( ) {
		$main = $this->main;
		
		$lang = $main->language;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$vts = $this->vts;
		$vtd = $this->vtd;

		# bottom_nav
		$vt_bottom_nav = new \visuotag( );
		$this->bottom_nav( $vt_bottom_nav );

		# bottom_box
		$vt_bottom_box = new \visuotag( );
		$vt_bottom_box->tag( 'div' , class : 'bottom_box' )
			->tag( 'div' , class : 'agreement' )
				->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ $url_base[ 0 ] , 'agreement' ] ) )
					->content( 'Пользовательское соглашение' )
					->parent( )
			->parent( )
			->tag( 'div' , class : 'copyright' )->content( 'damoony&copy;2026' );
		
		# bottom_agreement
		$vt_bottom_agreement = new \visuotag( );
		if( $_COOKIE[ 'policy_agreement' ] == 'accept' ){
		} else {

			$vt_bottom_body = new \visuotag( );
			$vt_bottom_body_click = new \visuotag( );
			$vt_bottom_body_close = new \visuotag( );
			$vt_bottom_body_confidence = new \visuotag( );

			$vt_bottom_body_click->tag( 'div' , class : 'bottom_click' )
					->attrs_actions_add( 'onclick' , 'engine.confidence.showconfidence( this );' )
					->content( 'Нажмите чтобы прочитать пользовательское соглашение.<br />Примите или отклоните предложенные условия пользования сайтом.' );
			$vt_bottom_body_close->tag( 'div' , class : 'bottom_confidence_close' )
					->attrs_actions_add( 'onclick' , 'engine.confidence.hide( this );' )
					->content( 'Закрыть пользовательское соглашение' );

			$vt_confidence = new \visuotag( );
			$vt_confidence->tag( 'h1' )->content( 'Пользовательское соглашение' );
			# 1
			$vt_confidence->tag( 'h2' )->content( '1. Термины и определения' );
			$vt_confidence->tag( 'p' )->tag( 'p' )->content( '1. 1. Сайт — интернет-ресурс, доступный по адресам: damoony.ru и damoony.online.' );
			$vt_confidence->tag( 'p' )->content( '1. 2. Администрация Сайта — лица, осуществляющие управление сайтом и его контентом.' );
			$vt_confidence->tag( 'p' )->content( '1. 3. Пользователь — любое лицо, использующее функциональные возможности Сайта.' );
			$vt_confidence->tag( 'p' )->content( '1. 4. Контент — все объекты, размещённые на Сайте: тексты, изображения, видео, программы, дизайн, логотипы и др.' );
			# 2
			$vt_confidence->tag( 'h2' )->content( '2. Статус соглашения' );
			$vt_confidence->tag( 'p' )->content( '2.1. Настоящее Пользовательское соглашение определяет условия использования Сайта, права и обязанности сторон.' );
			$vt_confidence->tag( 'p' )->content( '2.2. Использование Сайта означает полное и безоговорочное принятие условий настоящего соглашения (акцепт).' );
			$vt_confidence->tag( 'p' )->content( '2.3. Соглашение может быть изменено Администрацией в одностороннем порядке. Новая редакция вступает в силу с момента публикации на Сайте.' );
			# 3
			$vt_confidence->tag( 'h2' )->content( '3. Общие условия' );
			$vt_confidence->tag( 'p' )->content( '3.1. Администрация не гарантирует абсолютную достоверность и полноту Контента.' );
			$vt_confidence->tag( 'p' )->content( '3.2. Весь Контент носит ознакомительный характер.' );
			$vt_confidence->tag( 'p' )->content( '3.3. Ссылки на сторонние ресурсы не являются одобрением со стороны Администрации.' );
			# 4
			$vt_confidence->tag( 'h2' )->content( '4. Обязанности Пользователя' );
			$vt_confidence->tag( 'p' )->content( '4.1. Пользователь обязуется не нарушать законодательство РФ, права третьих лиц, не размещать запрещённую информацию.' );
			$vt_confidence->tag( 'p' )->content( '4.2. Пользователь несёт ответственность за размещаемый им Контент.' );
			$vt_confidence->tag( 'p' )->content( '4.3. Запрещается использовать Сайт для рассылки спама, вредоносных программ, а также нарушать работу Сайта.' );
			# 5
			$vt_confidence->tag( 'h2' )->content( '5. Политика конфиденциальности' );
			$vt_confidence->tag( 'p' )->content( '5.1. Пользователь даёт согласие на обработку своих персональных данных в соответствии с Федеральным законом № 152-ФЗ «О персональных данных».' );
			$vt_confidence->tag( 'p' )->content( '5.2. Порядок обработки персональных данных определяется Политикой конфиденциальности Сайта.' );
			# 6
			$vt_confidence->tag( 'h2' )->content( '6. Интеллектуальные права' );
			$vt_confidence->tag( 'p' )->content( '6.1. Все права на Контент принадлежат Администрации или иным правообладателям.' );
			$vt_confidence->tag( 'p' )->content( '6.2. Запрещается копирование, распространение, переработка Контента без согласия правообладателя.' );
			$vt_confidence->tag( 'p' )->content( '6.3. Пользователь, размещая свой Контент, предоставляет неисключительное право на его использование другим пользователям для личного некоммерческого использования.' );
			# 7
			$vt_confidence->tag( 'h2' )->content( '7. Реклама' );
			$vt_confidence->tag( 'p' )->content( '7.1. На Сайте может размещаться реклама. Администрация не несёт ответственности за её содержание.' );
			$vt_confidence->tag( 'p' )->content( '7.2. Пользователь соглашается на получение рекламной информации.' );
			# 8
			$vt_confidence->tag( 'h2' )->content( '8. Разрешение споров' );
			$vt_confidence->tag( 'p' )->content( '8.1. Все споры решаются путём переговоров. При недостижении согласия — в суде по месту нахождения Администрации.' );
			$vt_confidence->tag( 'p' )->content( '8.2. Претензии направляются в письменной форме через форму обратной связи Сайта.' );
			# 9
			$vt_confidence->tag( 'h2' )->content( '9. Прочие условия' );
			$vt_confidence->tag( 'p' )->content( '9.1. Сайт не предназначен для лиц младше 18 лет без согласия законных представителей.' );
			$vt_confidence->tag( 'p' )->content( '9.2. Бездействие Администрации при нарушении Пользователем условий не лишает её права принять меры позднее.');
			$vt_confidence->tag( 'p' )->content( '9.3. Если какое-либо положение соглашения признано недействительным, остальные сохраняют силу.' );
			$vt_confidence->tag( 'p' )->content( '9.4. При несогласии с условиями Пользователь обязан прекратить использование Сайта.' );
			

			$ve_input_checkbox = new \visuoelement( $this->visuorelease );
			$ve_input_checkbox->input( )->checkbox( $ve_input_checkbox__data = new \visuoelement_input_checkbox__data( ) );
			$ve_input_checkbox__data->set( 'form[read]' , label : '' , label_comment : '' , text : 'Я внимительно прочитал условия соглашения' );

			$ve_input_checkbox_personaldata = new \visuoelement( $this->visuorelease );
			$ve_input_checkbox_personaldata->input( )->checkbox( $ve_input_checkbox_personaldata__data = new \visuoelement_input_checkbox__data( ) );
			$ve_input_checkbox_personaldata__data->set( 'form[get]' , label : '' , label_comment : '' , text : 'Я даю право на обработку персональных данных' );

			$ve_input_checkbox_confirm = new \visuoelement( $this->visuorelease );
			$ve_input_checkbox_confirm->input( )->checkbox( $ve_input_checkbox_confirm__data = new \visuoelement_input_checkbox__data( ) );
			$ve_input_checkbox_confirm__data->set( 'form[accept]' , label : '' , label_comment : '' , text : 'Я соглашаюсь с условиями пользовательского соглашения' );

			$ve_input_submit = new \visuoelement( $this->visuorelease );
			$ve_input_submit->input( )->submit( $ve_input_submit__data = new \visuoelement_input_submit__data( ) );
			$ve_input_submit__data->set( 'form[submit]' , 'Принять' );
			$ve_input_cancel = new \visuoelement( $this->visuorelease );
			$ve_input_cancel->input( )->button( $ve_input_cancel__data = new \visuoelement_input_button__data( ) );
			$ve_input_cancel__data->set( 'form[cancel]' , 'Отказаться' );
			
			$ve_table = new \visuoelement( $this->visuorelease );
			$ve_table->table( )->table( $ve_table__data = new \visuoelement_table_table__data( ) );
			$ve_table__data->add_tbody( [ $vt_confidence ] , colspan : [ 2 ] );
			$ve_table__data->add_tfoot( [ [ $ve_input_checkbox ] , [ ] ] , colelsinline : [ false , true ] );
			$ve_table__data->add_tfoot( [ [ $ve_input_checkbox_personaldata ] , [ ] ] , colelsinline : [ false , true ] );
			$ve_table__data->add_tfoot( [ [ $ve_input_checkbox_confirm ] , [ $ve_input_submit ] ] , colelsinline : [ false , true ] );
			
			$ve_form = new \visuoelement( $this->visuorelease );
			$ve_form->form( )->form( $ve_form__data = new \visuoelement_form_form__data( ) );
			$ve_form__data->set( 'form' , 'post' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ $url_base[ 0 ] , 'policy' , 'agreement' ] ) , form_jquery : true );

			$ve_form->get_form_form__data( )->add( $ve_table );
			$ve_form->get_form_form__data( )->init( );

			
			$vt_bottom_body_confidence
				->tag( 'div' , class : 'bottom_confidence' )
					->element( 'form' , $ve_form );

			# 
			$vt_bottom_body->append( $vt_bottom_body_click );
			$vt_bottom_body->append( $vt_bottom_body_close );
			$vt_bottom_body->append( $vt_bottom_body_confidence );

			$vt_bottom_agreement->tag( 'div' , class : 'bottom_fon' , return : false )
			->tag( 'div' , class : 'bottom_body' )->append( $vt_bottom_body );
		}

		$this->vtd[ 'bottom_nav' ] = new \visuotag( );
		$this->vtd[ 'bottom' ] = new \visuotag( );
		$this->vtd[ 'bottom_agreement' ] = new \visuotag( );
		
		! $this->vtd[ 'bottom_nav' ] ?: $this->vtd[ 'bottom_nav' ]->append( $vt_bottom_nav );
		! $this->vtd[ 'bottom' ] ?: $this->vtd[ 'bottom' ]->append( $vt_bottom_box );
		! $this->vtd[ 'bottom_agreement' ] ?: $this->vtd[ 'bottom_agreement' ]->append( $vt_bottom_agreement );
}
	// 
	public function release ( ) {
		$main = $this->main;
		$alliasglobal = $this->alliasglobal;

		
		$lang = $main->language;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$vts = $this->vts;
		$vtd = $this->vtd ?: [ ];

		$alliasglobal->append( $this , 'bottom_nav' , $vts , $vtd );
		$alliasglobal->append( $this , 'bottom' , $vts , $vtd );
		$alliasglobal->append( $this , 'bottom_agreement' , $vts , $vtd );
	}
	
	public function menu_reload ( ) {
		$this->visuotag->clear( );
		$this->init( );
	}
	
	public function as_tag ( ) {
		return $this->visuotag->root( );
	}

	# other functions
	public function bottom_nav ( \visuotag $visuotag ) {

		$object = $this;
		$main = $this->main;
		
		$lang = $main->language;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$domains = [
			'damoony'=>[
				'info'=>[
					'show'=>true ,
					'domain'=>'damoony.ru' ,
					'link'=>$link->href( [ $url_allias[ 0 ] ] )
				] , 'projects'=>[
					// main: news , \visuo , development , human ai
					// football , village the game , totalspace
					'news'=>[
						'name'=>'Новости' ,
						'link'=>$link->href( [ 'news' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment'=>''
					] , 'visuo'=>[
						'name'=>'Visuo' ,
						'link'=>$link->href( [ 'visuo' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment'=>''
					] , 'development'=>[
						'name'=>'Development' ,
						'link'=>$link->href( [ 'development' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment'=>''
					] ,'humanai'=>[
						'name'=>'Human AI' ,
						'link'=>$link->href( [ 'hai' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment'=>''
					] ,
				]
			] , 'damoonygame'=>[
				'info'=>[
					'domain'=>'damoony gaming' ,
					'link'=>$link->href( [ $url_allias[ 0 ] ] )
				] , 'projects'=>[
					'football'=>[
						'name'=>'Football Personal' ,
						'link'=>$link->href( [ 'football' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment'=>''
					] , 'village'=>[
						'name'=>'Village, the game' ,
						'link'=>$link->href( [ 'village' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment'=>''
					] , 'totalspace'=>[
						'name'=>'Total Space, outworld' ,
						'link'=>$link->href( [ 'totalspace' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment'=>''
					] ,
				]
			] , 'asptour'=>[
				'info'=>[
					'domain'=>'asptour.ru' ,
					'link'=>$link->href( [ $url_allias[ 0 ] ] )
				] , 'projects'=>[
					'Прогулки'=>[
						'name'=>'Прогулки' ,
						'link'=>$link->href( [ 'asptour' , $url_allias[ 0 ] ] , [ 'landing' ] , [ ] ) ,
						'comment'=>''
					] , 'Отзывы'=>[
						'name'=>'Отзывы' ,
						'link'=>$link->href( [ 'asptour' , $url_allias[ 0 ] ] , [ 'resent' ] , [ ] ) ,
						'comment'=>''
					] , 'Контакты'=>[
						'name'=>'Контакты' ,
						'link'=>$link->href( [ 'asptour' , $url_allias[ 0 ] ] , [ 'contacts' ] , [ ] ) ,
						'comment'=>''
					] ,
				]
			]
		];
		

		$vt_domain_damoony = new \visuotag( );
		$vt_domain_damoonygame = new \visuotag( );
		$vt_domain_asptour = new \visuotag( );
		$vt_info = new \visuotag( );
		$vt_policy = new \visuotag( );
		$vt_links = new \visuotag( );

		$vt_domain_damoony = $vt_domain_damoony->tag( 'div' , class : 'bottom_nav_project' );
		$vt_domain_damoonygame = $vt_domain_damoonygame->tag( 'div' , class : 'bottom_nav_project' );
		$vt_domain_asptour = $vt_domain_asptour->tag( 'div' , class : 'bottom_nav_project' );

		foreach( $domains as $domain_key=>$domain ){
			$domain_vt = 'vt_domain_' . $domain_key;
			$info = $domain[ 'info' ];
			${$domain_vt}->tag( 'div' , class : 'title' )
				->content( $info[ 'domain' ] );
			${$domain_vt} = ${$domain_vt}->tag( 'div' , class : 'links' );

			$projects = $domain[ 'projects' ];
			foreach( $projects as $key=>$project ){
				${$domain_vt}
					->tag( 'div' , class : 'project_link' )
						->tag( 'a' )->attrs_add( 'href' , $project[ 'link' ] )->attrs_add( 'target' , '_blanc' )
							->content( $project[ 'name' ] );
			}
			${$domain_vt} = ${$domain_vt}->root( );
		}

		#
		$vt_info = $vt_info->tag( 'div' , class : 'bottom_nav_info' );
		$vt_info->tag( 'div' , class : 'title' )->content( 'Версии сайта:');
		$vt_info->tag( 'div' , class : 'links' )
			->tag( 'a' )->attrs_add( 'href' , $link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , $url_base , [ 'version'=>'desctop' ] ) )->content( 'Десктопная версия' )
			->parent( )->tag( 'a' )->attrs_add( 'href' , $link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , $url_base , [ 'version'=>'mobile' ] ) )->content( 'Мобильная версия' )
			->parent( )->tag( 'a' )->attrs_add( 'href' , $link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , $url_base , [ 'version'=>'app' ] ) )->content( 'Приложение' );
		
		$vt_policy = $vt_policy->tag( 'div' , class : 'bottom_nav_policy' );
		$vt_policy->tag( 'div' , class : 'title' )->content( 'Документы:');
		$vt_policy->tag( 'div' , class : 'links' )
			->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'documents' , 'confidence' ] ) )->content( 'Пользовательское соглашение' )
			->parent( )->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'documents' , 'personal' ] ) )->content( 'Согласие на обработку персональных данных' )
			->parent( )->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'documents' , 'prilozhenie' ] ) )->content( 'Приложение №1, к пользовательскому соглашению' );

		$vt_links = $vt_links->tag( 'div' , class : 'bottom_nav_policy' );
		$vt_links->tag( 'div' , class : 'title' )->content( 'Ссылки:');
		$vt_links->tag( 'div' , class : 'links' )
			->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'about' ] ) )->content( 'О нас' )
			->parent( )->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'partner' ] ) )->content( 'Сотрудничество' )
			->parent( )->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'forum' ] ) )->content( 'Форум' )
			->parent( )->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'faq' ] ) )->content( 'Вопрос-Ответ' )
			->parent( )->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'tech' ] ) )->content( 'Тех-Поддержка' );

		$visuotag
			->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_domain_damoony )
			->parent( )->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_domain_damoonygame )
			->parent( )->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_domain_asptour )
			->parent( )->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_links )
			->parent( )->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_info )
			->parent( )->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_policy );

	}
}

?>