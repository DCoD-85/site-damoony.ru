<?PHP

class visuoscheme {
	# vars
	public null|visuorelease $visuorelease = null;
	public string $scheme_type = '';
	public string $scheme_name = '';
		
	public function set( string $scheme_name = 'default' ){
		$this->scheme_name = $scheme_name;
		
	}
	public function get( ) {
		$scheme_name = $this->scheme_name;
		
		$scheme_class = 'visuoscheme_' . $this->scheme_type . '_' . $scheme_name;
		$release = new $scheme_class( $this->visuorelease );
		$release = $release->release( );
		
		return $release;
	}
}

class visuoscheme_struct
	extends visuoscheme {
	# functions
	public function __construct( null|visuorelease $visuorelease , string $scheme_name='default' ){
		$this->visuorelease = $visuorelease;
		$this->scheme_name = $scheme_name;
		$this->scheme_type = 'struct';
	}
}
	
class visuoscheme_struct_default
	extends visuoscheme_struct {
	public function release( ){
		$structdata_add = [
			'astype'=>'div' ,
			'asblock'=>false ,
			'slider'=>true ,
			'slider_title'=>true ,
			'slider_tabs'=>true ,
			'slider_actions'=>false ,
			'slider_nums'=>false ,
			'slider_selected'=>(int)$_GET[ 'p' ] ?: 0 ,
			'body_show'=>true ,
			'title'=>'Настройка проекта' ,
			'body'=>false ,
			# 'id'=>'id_formclear' ,
			'class'=>'blockform' ,
			'class_indiv'=>'' ,
			'class_table'=>'blockform_table' ,
			'class_type'=>'' ,
			'class_rows'=>'blockform_rows' ,
			'class_cols'=>'blockform_cols' ,
			'class_block'=>'blockform_block' ,
		];
		
		$vr = $this->visuorelease;
		
		$vs = new visuostruct( $vr , null , 'root' , true );
		$vs->structdata( $structdata_add );
		
		$vs = $vs->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' )
			->root( )->struct( 'tableblock' )->struct( 'form' )->struct( 'table' )->struct( 'row' )->struct( 'col' )->struct( 'container' );
		
		return $vs->root( );
	}
}
?>