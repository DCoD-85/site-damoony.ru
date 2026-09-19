<?PHP

class visuodata {
	
	private null|visuotag|visuostruct $struct = null;
	public array $data = [ ];
	public function __construct( null|visuotag|visuostruct $struct=null ){
		$this->struct = $struct;
	}

	public function add( string $data_name , null|string|int|array $data_value , bool $data_tojson  = true ){
		$data_json = $data_tojson ? $this->json_encode( $data_value ) : $data_value ;
		
		$this->data[ $data_name ] = [ $data_value , $data_tojson , $data_json ];
		return $this->struct;
	}

	public function get( string $attr_name ){
		return $this->data[ $attr_name ] ?? null;
	}
	
	public function json_encode( null|string|int|array $data ){
		return str_replace( '"' , '&quot;' , json_encode( $data ) );
	} 
	public function json_decode( string $data ){
		return json_decode( str_replace( '&quot;' , '"' , $data ) , true );
	} 
	
	public function show( ){
		$data = $this->data;
		
		return $this->data;
	}
	public function prerelease( ){
	}
	public function release( ){
		$data = $this->data;
		$html = '';

		if( ! empty( $data ) ){
			foreach( $data as $k=>$v ){
				$html .= ' data-' . $k . '="' . $v[ 2 ] . '"';
			}
		}

		return $html;
	}
}

?>