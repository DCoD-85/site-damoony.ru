<?PHP

class visuorelease {
public null|main $main = null;
public visuolog $visuolog;
public visuorelease_settings $settings;
public null|visuometa $visuometa = null;
public null|visuotag $visuotag = null;
public null|visuotag $visuotag_final = null;
public string $release_alternative = '';

public function __construct( null|main $main = null ,
null|visuorelease_settings $settings = null ){
$this->visuolog = new visuolog( );
$this->main = $main;
$this->settings = $settings !== null ? $settings : new visuorelease_settings( $this );

$this->visuometa = new visuometa( $this );
$this->visuotag = new visuotag( $this );
$this->visuotag_final = new visuotag( $this );

# Инициализация
$this->init( );
}
public function settings( ) {
return $this->settings;
}

public function visuotag_clear ( ) {
$this->visuotag->root( )->clear( );
$this->visuotag_final->root( )->clear( );
}
public function init( ){
$this->schemenone_init( );
}
# without scheme
private function schemenone_init( ){
$this->visuotag->tag( '! doctype' , false , return: false )
->tag( 'html' , class: 'cl_html' )
->tag( 'head' , class: 'cl_head' , return : false )
->tag( 'body' , class: 'cl_body' )->root( );
}

public function meta( ){
return $this->visuometa;
}

public function visuotag( ){
return $this->visuotag->_body( ) ?: $this->visuotag;
}
public function visuotag_set ( visuotag $visuotag ) {
$this->visuotag = $visuotag;
}


private function release ( bool $log = false , bool $debug = false ){
#
$this->visuotag->root( )->find( 'head' )->meta( $this->visuometa );
#
$this->visuotag->prepare( );
$this->visuotag->release( );
#
$this->visuotag_final = $this->visuotag;

while( true ){
global $start;
$end = microtime( true );
$number = ( ( $end - $start ) * 1000 );
$body = $this->visuotag_final->body( );

if( $log === true || $debug === true ){
global $start;
$end = microtime( true );
$number = ( ( $end - $start ) * 1000 );

$deb = new visuotag( );
$deb = $deb->tag( 'div' , class : 'debugging hidden' )
->tag( 'div' , class : 'debug_head' )
->tag( 'div' , class : 'debug_head_title' , return : false )
->tag( 'div' , class : 'debug_head_link' , return : false )
->tag( 'div' , class : 'debug_head_time' , return : false )
->parent( )->tag( 'div' , id : 'debug_log' , class : 'debug_log' )
->parent( )->tag( 'div' , id : 'debug_tree' , class : 'debug_tree' )
->root( );

$deb_title = $deb->find( class : 'debug_head_title' );
$deb_link = $deb->find( class : 'debug_head_link' );
$deb_time = $deb->find( class : 'debug_head_time' );
$deb_log = $deb->find( class : 'debug_log' );
$deb_tree = $deb->find( class : 'debug_tree' );

$deb_title->content( 'Debugging' );
$deb_time->attrs_actions_add( 'onclick' , 'd = $(\'.debugging\'); if( d.hasClass(\'hidden\') ){d.removeClass( \'hidden\' )}else{d.addClass(\'hidden\');}' )->content( '' . round( $number , 3 ) . 'ms' );
$deb_link->tag( 'a' )->attrs_add( 'href' , 'javascript:;' )
->attrs_actions_add( 'onClick' , 'dl = $(\'#debug_log\'); dt = $(\'#debug_tree\'); dl.show( ); dt.hide( );' )->content( 'Log' );
$deb_link->tag( 'div' , class : 'between' )->content( '|' );
$deb_link->tag( 'a' )->attrs_add( 'href' , 'javascript:;' )
->attrs_actions_add( 'onClick' , 'dl = $(\'#debug_log\'); dt = $(\'#debug_tree\'); dl.hide( ); dt.show( );' )->content( 'Debug' );

if( $log === true ){
$vt_log = $this->visuolog->as_tag( );
$deb_log->append( $vt_log );
}

if( $debug === true ){
$vt_debug = new visuotag( );
$vt_debug = $body->root( )->releasetree( );
$deb_tree->content( $vt_debug );
}
$dt_debug = $body->find( 'div' , class : 'dt_debug' );
# append
! $dt_debug ?: $dt_debug->append( $deb );
} else {
$vt_debug = new visuotag( );
$vt_debug->tag( 'div' , class : 'debugging_onlytime' )->content( round( $number , 3 ) . 'ms' );

$dt_debug = $body->find( 'div' , class : 'dt_debug' );
! $dt_debug ?: $dt_debug->append( $vt_debug );
}
break;
}

$final = $this->visuotag_final->final( );

return $final;
}

public function release_min (
null|visuostruct_min $struct = null ,
bool $min = true ,
bool $json = true
) {
$this->main->render->setdata( 'min' , $min );

$release_json = [
'error'=>$struct->error( )->release( ) ,
'messages'=>$struct->messages( )->release( ) ,
'release'=>$struct->release( )->release( ) ,
'form'=>$struct->form( )->release( ) ,
'referer'=>$struct->referer( )->release( )
];

! $json ?: $data = json_encode( $release_json );

$this->release_alternative = $data;

}

public function release_struct (
null|visuostruct_ajax $struct = null ,
bool $min = true ,
bool $json = true
) {
$this->main->render->setdata( 'min' , $min );
$data = $struct->as_json( );

$this->release_alternative = $data;
}

private function release_alternative ( ) {
$final = $this->release_alternative;
return $final;
}

public function final ( bool $log , bool $debug ){
if( $this->main->render->getdata( 'min' ) == false ){
print $this->release( $log , $debug );
} else {
print $this->release_alternative( ) ;
}
}

public function final_ajax ( bool $log , bool $debug ){

if( ! is_null( $html = $this->visuotag->root( )->find( 'html' ) ) ){
$html->data_add( 'ajax' , true );
$html->data_add( 'ajax-init' , false );
}

if( $this->main->render->getdata( 'min' ) == false ){
print $this->release( $log , $debug );
} else {
print $this->release_alternative( ) ;
}

}
}

?>