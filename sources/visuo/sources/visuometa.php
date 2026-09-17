<?PHP
class visuometa {
### vars
public null|visuorelease $visuorelease = null;
public null|visuotag $visuotag = null;
public array $elements = [ ];
### methods
public function __construct( null|visuorelease $visuorelease = null ){
# log
if( $this->visuorelease ) $this->visuorelease->visuolog->add( 'Создаем объект visuometa.' , __METHOD__ , __FILE__ , __LINE__ );
#
$this->visuorelease = $visuorelease;
$this->visuotag = new visuotag( );
}
public function visuorelease( ){
return $this->visuorelease ?: null;
}
public function visuotag ( ){
return $this->visuotag;
}
public function visuotag_set ( visuotag $visuotag ) {
$this->visuotag = $visuotag;
return $this;
}
public function clear( ){
# unset( $this->elements );
$this->elements = [ ];
}
# добавляет
public function append( visuometa $visuometa ){
foreach( $visuometa->elements as $k=>$v ){
$this->elements[ ] = $v;
}
return $this;
}

public function title ( ) {
# log
if( $this->visuorelease ) $this->visuorelease->visuolog->add( 'Создаем тег TITLE.' , __METHOD__ , __FILE__ , __LINE__ );
#
return $this->elements[ ] = new visuometa_title( $this );
}

public function link ( ) {
# log
if( $this->visuorelease ) $this->visuorelease->visuolog->add( 'Создаем тег LINK.' , __METHOD__ , __FILE__ , __LINE__ );
#

return $this->elements[ ] = new visuolink( $this );
}

public function as_tag( ){
$vt = $this->visuotag;

foreach( $this->elements as $k=>$v ){
$this->elements[ $k ]->visuotag = $this->visuotag;
$vt = $vt->append( $v->as_tag( ) );
}

return $vt;
}

public function tohtml( ){
$elements = $this->elements;
$html = '';
foreach( $elements as $k=>$v ){
$html .= $v->release( );
}
return $html;
}

# tests
public function test_elements ( ) {
$elements = $this->elements;
foreach( $elements as $k => $v ) {
print $k . ' = ' . gettype( $v ) . ' ' . get_class( $v ) . '<br />';
print $v->as_tag( )->release( );
}
}
}

class visuometa_title {
public null|visuometa $visuometa = null;
public string $content = '';
public null|visuotag $visuotag = null;

public function __construct( visuometa $visuometa ){
$this->visuometa = $visuometa;
}
public function back( ){
return $this->visuometa;
}

public function data(
string $content = '' ,
bool $true = false
){
$this->content = $content;
$this->true = $true;
return $this->visuometa;
}
public function attr( string $name , string $value ){
$this->{$name} = $value;
return $this;
}
public function as_tag( ){
$vt = new visuotag( );
$vt->tag( 'title' , true )->content( $this->content );
return $vt;
}
}

?>