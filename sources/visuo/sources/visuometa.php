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
	
	public function favicon ( ) {
		return $this->elements[ ] = new visuometa_favicon( $this );
	}
	
	public function style ( ) {
		return $this->elements[ ] = new visuometa_style( $this );
	}
	
	public function script ( ) {
		return $this->elements[ ] = new visuometa_script( $this );
	}
	
	public function as_tag( ){
		$vt = $this->visuotag;
		
		foreach( $this->elements as $k=>$v ){
			$this->elements[ $k ]->visuotag = $this->visuotag;
			$vt = $vt->append( $v->as_tag( ) );
		}
		
		return $vt;
	}
	
	public function as_tag_ARHIV( ){
		$vt = new visuotag( );
		
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

class visuometa_favicon {
	# vars
	public null|visuometa $visuometa = null;
	public string $content = '';
	public null|visuotag $visuotag = null;
	# vars : data
	public string $d_rel = 'icon';
	public string $d_href = '/media/favicon.ico';
	public string $d_type = 'image/x-icon';
	# methods
	public function __construct( visuometa $visuometa ){
		$this->visuometa = $visuometa;
	}
	public function data(
		string $content = '' ,
		bool $new = false
	){
		$new === false ? $this->content .= $content : $this->content = $content;
		return $this->visuometa;
	}
	public function attr( string $name , string $value ){
		$this->{$name} = $value;
		return $this;
	}
	public function as_tag( ){
		$vt = new visuotag( null , null );
		$vt = $vt->tag( 'link' );
		
		
		$vt->content( $this->content );
		
		return $vt;
	}
}

class visuometa_style {
	public null|visuometa $visuometa = null;
	public string $content = '';
	public null|visuotag $visuotag = null;
	
	public function __construct( visuometa $visuometa ){
		$this->visuometa = $visuometa;
	}
	public function data(
		string $content = '' ,
		bool $new = false
	){
		$new === false ? $this->content .= $content : $this->content = $content;
		return $this->visuometa;
	}
	public function attr( string $name , string $value ){
		$this->{$name} = $value;
		return $this;
	}
	public function as_tag( ){
		$vt = new visuotag( null , null );
		$vt = $vt->tag( 'style' )->attrs_add( 'type' , 'text/css' );
		
		$attrs = [ ];
		if( ! empty( $attrs ) ){
			foreach( $attrs as $k=>$v ){
				$vt->attrs( )->add( $k , $v );
			}
		}
		$vt->content( $this->content );
		
		return $vt;
	}
}

class visuometa_script {
	public null|visuometa $visuometa = null;
	public string $content = '';
	public null|visuotag $visuotag = null;
	
	public function __construct( visuometa $visuometa ){
		$this->visuometa = $visuometa;
	}
	public function data(
		string $content = '' ,
		bool $new = false
	){
		$new === false ? $this->content .= $content : $this->content = $content;
		return $this->visuometa;
	}
	public function attr( string $name , string $value ){
		$this->{$name} = $value;
		return $this;
	}
	public function as_tag( ){
		$vt = new visuotag( null , null );
		$vt = $vt->tag( 'script' )->attrs_add( 'type' , 'text/javascript' );
		
		$attrs = [ ];
		if( ! empty( $attrs ) ){
			foreach( $attrs as $k=>$v ){
				$vt->attrs_add( $k , $v );
			}
		}
		$vt->content( $this->content );
		
		return $vt;
	}
}

?>