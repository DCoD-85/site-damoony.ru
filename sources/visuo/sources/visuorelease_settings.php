<?PHP

class visuorelease_settings {
# vars
public visuorelease $visuorelease;
private string $language = '';
# methods
public function __construct ( visuorelease $visuorelease ) {
$this->visuorelease = $visuorelease;
if( $_COOKIE[ 'language' ] ) $this->language = $_COOKIE[ 'language' ] ?: 'ru';
}
public function get ( string $varname ) {
return $this->{$varname}?:null;
}
}

?>