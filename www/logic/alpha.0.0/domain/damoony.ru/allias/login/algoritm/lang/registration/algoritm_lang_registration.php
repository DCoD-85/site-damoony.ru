<?PHP

namespace damoony\login;

class algoritm_lang_registration
extends \algoritm_ {
# vars
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
}

public function release ( ) {
$main = $this->main;
$this->algoritmdata->set( );

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
$is_guest = $main->user->is_guest( );

if( $is_guest ) return $this->default_guest( );
else return $this->default_logged( );
}

public function default_guest ( ) {
$main = $this->algoritm->main;
$visuorelease = $main->visuorelease;

$library = $main->library;
$link = $library->link( );
$url = $library->url( );
$url_domain = $url->static_domain;
$url_base = $url->static_base;

$center = $main->alliasglobal->center;

# ─── Поля формы ───

# Логин
$input_login = new \visuoelement( $visuorelease );
$input_login->input( )->text( );
$input_login->get_input_text__data( )->set( 'form[login]' , label_comment : 'Логин' );

# Email
$input_email = new \visuoelement( $visuorelease );
$input_email->input( )->text( );
$input_email->get_input_text__data( )->set( 'form[email]' , label_comment : 'E-mail' );

# Пароль
$input_password = new \visuoelement( $visuorelease );
$input_password->input( )->password( );
$input_password->get_input_password__data( )->set( 'form[password]' , label_comment : 'Пароль' );

# Подтверждение пароля
$input_password_confirm = new \visuoelement( $visuorelease );
$input_password_confirm->input( )->password( );
$input_password_confirm->get_input_password__data( )->set( 'form[password_confirm]' , label_comment : 'Повторите пароль' );

# Согласие с правилами
$input_agree = new \visuoelement( $visuorelease );
$input_agree->input( )->checkbox( );
$input_agree->get_input_checkbox__data( )->set( 'form[agree]' , text : 'Я согласен с правилами и пользовательским соглашением' );

# Кнопки
$input_submit = new \visuoelement( $visuorelease );
$input_submit->input( )->submit( );
$input_submit->get_input_submit__data( )->set( 'form[submit]' , 'Зарегистрироваться' );

$input_reset = new \visuoelement( $visuorelease );
$input_reset->input( )->reset( );
$input_reset->get_input_reset__data( )->set( 'form[reset]' , 'Очистить' );

# ─── Таблица ───
# colelsinline : true  = элементы inline (рядом)
# colelsinline : false = элементы block (вертикально)

$table = new \visuoelement( $visuorelease );
$table->table( )->table( );
$table__data = $table->get_table_table__data( );
$table__data->clear( )
->add_tbody(
[ 'Данные регистрации:' , [ $input_login , $input_email ] ] ,
colelsinline : [ false , false ] ,
colwidth : [ '30%' , '70%' ]
)
->add_tbody(
[ 'Пароль:' , [ $input_password , $input_password_confirm ] ] ,
colelsinline : [ false , false ] ,
colwidth : [ '30%' , '70%' ]
)
->add_tbody(
[ 'Соглашение:' , [ $input_agree ] ] ,
colelsinline : [ false , false ] ,
colwidth : [ '30%' , '70%' ]
)
->add_tfoot(
[ 'Действия' , [ $input_submit , $input_reset ] ] ,
colelsinline : [ false , true ]
);

# ─── Форма ───

$form = new \visuoelement( $visuorelease );
$form->form( )->form( );
$form__data = $form->get_form_form__data( );
$href = $link->href( $url_domain , [ $url_base[ 0 ] , 'registration' ] , [ ] , use_all_adress_data : false );

$form__data->set(
'registration' ,
'post' ,
$href ,
title_text : 'Форма регистрации' ,
form_jquery : true
);
$form__data->add( $table );

# ─── Tablebox (обёртка) ───

$tablebox = new \visuoelement( $visuorelease );
$tablebox->table( )->tablebox( );
$tablebox->get_table_tablebox__data( )->clear( );
$tablebox->get_table_tablebox__data( )->add_box( 'Регистрация' , $form );

$vt_center = new \visuotag( );
$vt_center->element( 'tablebox' , $tablebox );

$center->visuotag( )->append( $vt_center );

return true;
}

public function default_logged ( ) {
$main = $this->algoritm->main;

$center = $main->alliasglobal->center;

$vt_center = new \visuotag( );
$vt_center->tag( 'div' , style : 'font-size : 20px ; margin : 100px auto ; text-align : center ; ' )
->content( 'Вы уже авторизованы. Для регистрации нового аккаунта выйдите из текущего.' );

$center->visuotag( )->append( $vt_center );

return true;
}
}

?>