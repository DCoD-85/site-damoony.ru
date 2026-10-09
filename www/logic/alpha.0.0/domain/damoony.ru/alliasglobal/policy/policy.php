<?PHP

namespace damoony;

class alliasglobal_policy extends alliasblock {
	public string $alliasglobal_name = 'policy';

	# --- custom ---
	public string $cookie_name  = 'policy_agreement';
	public string $privacy_href = '/privacy';
	public string $privacy_text = 'Политика конфиденциальности';

	# --- data ---
	protected function default_data ( ) : void {
		# короткая информация — всегда
		$this->section_add( 'policy_shortagreement' , $this->make_shortagreement( ) );

		# popup — если показывать нужно
		if ( $this->should_show( ) ) {
			$this->section_add( 'policy_agreement' , $this->make_agreement( ) );
		}
	}

	# ===============================================================
	#   УСЛОВИЕ ПОКАЗА POPUP
	# ===============================================================

	protected function should_show ( ) : bool {
		if ( ( $_COOKIE[ $this->cookie_name ] ?? '' ) === 'accept' ) return false;
		return true;
	}

	# ===============================================================
	#   КОРОТКАЯ ИНФОРМАЦИЯ (всегда)
	# ===============================================================

	private function make_shortagreement ( ) : \visuotag {
		$lang = $this->main->language;

		$vt  = new \visuotag( );
		$box = $vt->tag( 'div' , class : 'policy_shortagreement' );

		$box->tag( 'a' )
			->attrs_add( 'href' , $this->privacy_href )
			->content( $lang->get( 'POLICY_PRIVACY_LINK' , [ ] , $this->privacy_text ) );

		return $vt->root( );
	}

	# ===============================================================
	#   POPUP СОГЛАШЕНИЯ
	# ===============================================================

	private function make_agreement ( ) : \visuotag {
		$vt = new \visuotag( );

		$root = $vt->tag( 'div' , class : 'policy_agreement' );
		$fon  = $root->tag( 'div' , class : 'policy_fon' , return : false );
		$body = $fon->tag( 'div' , class : 'policy_body' );

		$body->tag( 'div' , class : 'policy_click' )
			->attrs_actions_add( 'onclick' , 'engine.confidence.showconfidence( this );' )
			->content( 'Нажмите чтобы прочитать пользовательское соглашение.<br />Примите или отклоните предложенные условия пользования сайтом.' );

		$body->tag( 'div' , class : 'policy_confidence_close' )
			->attrs_actions_add( 'onclick' , 'engine.confidence.hide( this );' )
			->content( 'Закрыть пользовательское соглашение' );

		$body->tag( 'div' , class : 'policy_confidence' )
			->append( $this->make_confidence_form( ) );

		return $vt->root( );
	}

	# ===============================================================
	#   ТЕКСТ + ФОРМА
	# ===============================================================

	private function make_confidence_form ( ) : \visuotag {
		$main = $this->main;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;

		$vt_confidence = $this->make_confidence_text( );

		# --- чекбоксы ---
		$ve_input_checkbox = new \visuoelement( $this->visuorelease );
		$ve_input_checkbox->input( )->checkbox( $d1 = new \visuoelement_input_checkbox__data( ) );
		$d1->set( 'form[read]' , label : '' , label_comment : '' , text : 'Я внимительно прочитал условия соглашения' );

		$ve_input_checkbox_pd = new \visuoelement( $this->visuorelease );
		$ve_input_checkbox_pd->input( )->checkbox( $d2 = new \visuoelement_input_checkbox__data( ) );
		$d2->set( 'form[get]' , label : '' , label_comment : '' , text : 'Я даю право на обработку персональных данных' );

		$ve_input_checkbox_confirm = new \visuoelement( $this->visuorelease );
		$ve_input_checkbox_confirm->input( )->checkbox( $d3 = new \visuoelement_input_checkbox__data( ) );
		$d3->set( 'form[accept]' , label : '' , label_comment : '' , text : 'Я соглашаюсь с условиями пользовательского соглашения' );

		# --- submit / cancel ---
		$ve_input_submit = new \visuoelement( $this->visuorelease );
		$ve_input_submit->input( )->submit( $ds = new \visuoelement_input_submit__data( ) );
		$ds->set( 'form[submit]' , 'Принять' );

		$ve_input_cancel = new \visuoelement( $this->visuorelease );
		$ve_input_cancel->input( )->button( $dc = new \visuoelement_input_button__data( ) );
		$dc->set( 'form[cancel]' , 'Отказаться' );

		# --- таблица ---
		$ve_table = new \visuoelement( $this->visuorelease );
		$ve_table->table( )->table( $dt = new \visuoelement_table_table__data( ) );
		$dt->add_tbody( [ $vt_confidence ] , colspan : [ 2 ] );
		$dt->add_tfoot( [ [ $ve_input_checkbox ]         , [ ] ] , colelsinline : [ false , true ] );
		$dt->add_tfoot( [ [ $ve_input_checkbox_pd ]      , [ ] ] , colelsinline : [ false , true ] );
		$dt->add_tfoot( [ [ $ve_input_checkbox_confirm ] , [ $ve_input_submit ] ] , colelsinline : [ false , true ] );

		# --- форма ---
		$ve_form = new \visuoelement( $this->visuorelease );
		$ve_form->form( )->form( $df = new \visuoelement_form_form__data( ) );
		$df->set(
			'form' ,
			'post' ,
			$link->href( [ 'www' , $url_allias[ 0 ] ] , [ $url_base[ 0 ] , 'policy' , 'agreement' ] ) ,
			form_jquery : true
		);

		$ve_form->get_form_form__data( )->add( $ve_table );
		$ve_form->get_form_form__data( )->init( );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'policy_confidence_inner' )
			->element( 'form' , $ve_form );

		return $vt->root( );
	}

	private function make_confidence_text ( ) : \visuotag {
		$vt = new \visuotag( );

		$vt->tag( 'h1' )->content( 'Пользовательское соглашение' );

		$vt->tag( 'h2' )->content( '1. Термины и определения' );
		$vt->tag( 'p' )->content( '1. 1. Сайт — интернет-ресурс, доступный по адресам: damoony.ru и damoony.online.' );
		$vt->tag( 'p' )->content( '1. 2. Администрация Сайта — лица, осуществляющие управление сайтом и его контентом.' );
		$vt->tag( 'p' )->content( '1. 3. Пользователь — любое лицо, использующее функциональные возможности Сайта.' );
		$vt->tag( 'p' )->content( '1. 4. Контент — все объекты, размещённые на Сайте: тексты, изображения, видео, программы, дизайн, логотипы и др.' );

		$vt->tag( 'h2' )->content( '2. Статус соглашения' );
		$vt->tag( 'p' )->content( '2.1. Настоящее Пользовательское соглашение определяет условия использования Сайта, права и обязанности сторон.' );
		$vt->tag( 'p' )->content( '2.2. Использование Сайта означает полное и безоговорочное принятие условий настоящего соглашения (акцепт).' );
		$vt->tag( 'p' )->content( '2.3. Соглашение может быть изменено Администрацией в одностороннем порядке. Новая редакция вступает в силу с момента публикации на Сайте.' );

		$vt->tag( 'h2' )->content( '3. Общие условия' );
		$vt->tag( 'p' )->content( '3.1. Администрация не гарантирует абсолютную достоверность и полноту Контента.' );
		$vt->tag( 'p' )->content( '3.2. Весь Контент носит ознакомительный характер.' );
		$vt->tag( 'p' )->content( '3.3. Ссылки на сторонние ресурсы не являются одобрением со стороны Администрации.' );

		$vt->tag( 'h2' )->content( '4. Обязанности Пользователя' );
		$vt->tag( 'p' )->content( '4.1. Пользователь обязуется не нарушать законодательство РФ, права третьих лиц, не размещать запрещённую информацию.' );
		$vt->tag( 'p' )->content( '4.2. Пользователь несёт ответственность за размещаемый им Контент.' );
		$vt->tag( 'p' )->content( '4.3. Запрещается использовать Сайт для рассылки спама, вредоносных программ, а также нарушать работу Сайта.' );

		$vt->tag( 'h2' )->content( '5. Политика конфиденциальности' );
		$vt->tag( 'p' )->content( '5.1. Пользователь даёт согласие на обработку своих персональных данных в соответствии с Федеральным законом № 152-ФЗ «О персональных данных».' );
		$vt->tag( 'p' )->content( '5.2. Порядок обработки персональных данных определяется Политикой конфиденциальности Сайта.' );

		$vt->tag( 'h2' )->content( '6. Интеллектуальные права' );
		$vt->tag( 'p' )->content( '6.1. Все права на Контент принадлежат Администрации или иным правообладателям.' );
		$vt->tag( 'p' )->content( '6.2. Запрещается копирование, распространение, переработка Контента без согласия правообладателя.' );
		$vt->tag( 'p' )->content( '6.3. Пользователь, размещая свой Контент, предоставляет неисключительное право на его использование другим пользователям для личного некоммерческого использования.' );

		$vt->tag( 'h2' )->content( '7. Реклама' );
		$vt->tag( 'p' )->content( '7.1. На Сайте может размещаться реклама. Администрация не несёт ответственности за её содержание.' );
		$vt->tag( 'p' )->content( '7.2. Пользователь соглашается на получение рекламной информации.' );

		$vt->tag( 'h2' )->content( '8. Разрешение споров' );
		$vt->tag( 'p' )->content( '8.1. Все споры решаются путём переговоров. При недостижении согласия — в суде по месту нахождения Администрации.' );
		$vt->tag( 'p' )->content( '8.2. Претензии направляются в письменной форме через форму обратной связи Сайта.' );

		$vt->tag( 'h2' )->content( '9. Прочие условия' );
		$vt->tag( 'p' )->content( '9.1. Сайт не предназначен для лиц младше 18 лет без согласия законных представителей.' );
		$vt->tag( 'p' )->content( '9.2. Бездействие Администрации при нарушении Пользователем условий не лишает её права принять меры позднее.' );
		$vt->tag( 'p' )->content( '9.3. Если какое-либо положение соглашения признано недействительным, остальные сохраняют силу.' );
		$vt->tag( 'p' )->content( '9.4. При несогласии с условиями Пользователь обязан прекратить использование Сайта.' );

		return $vt->root( );
	}
}