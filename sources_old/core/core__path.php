<?PHP

#
#
#

class core__path {
	# vars
	public main $main;
	# path
	public string
		$cms ,
		$install ,
		$cores ,
		$config ,
		$shablons ,
		$lang ,
		$system ,
		$lib ,
		$mod ,
		$sys ,
		$com ,
		$localization;
	public array
		$path ,
		$dirs ,
		$basepath ,
		$basepatha;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
	}
	public function core ( ) {
		return $this->main->core;
	}
	public function init ( ) {
		$this->path = [																	// Пути к базовым компонентам
			'cms'=>[ 'cms' ] ,
			'install'=>[ 'cms' , '_install' ] ,
			'cores'=>[ 'cms' , 'cores' ] ,
			'config'=>[ 'cms' , 'config' ] ,
			'shablons'=>[ 'cms' , 'shablons' ] ,
			'lang'=>[ 'cms' , 'lang' ] ,
			'system'=>[ 'cms' , 'system' ] ,
			'lib'=>[ 'cms' , 'lib' ] ,
			'mod'=>[ 'cms' , 'mod' ] ,
			'sys'=>[ 'cms' , 'sys' ] ,
			'com'=>[ 'cms' , 'com' ] ,
			'localization'=>[ 'cms' , 'localization' ] ,
			'core'=>[ 'cms' , 'core' ]
		];
		
		$this->dirs = [
			'media_js_structures'=>[ 'media' , 'js' , 'structures' ] ,
			'files'=>[ 'files' ] ,
			'coffeefiles'=>[ 'files' , 'coffeefiles' ] ,
			'coffeefilessimple'=>[ 'files' , 'coffeefilessimple' ] ,
			'files_countries'=>[ 'files' , 'countries' ] ,
			'files_brands'=>[ 'files' , 'brands' ] ,
		];
		$this->basepath = [														// .. базовый путь в системе
			''=>[ ] ,
			'files'=>[ 'files' ] ,
				'files_users'=>[ 'files' , 'u' ] ,
				'files_brands'=>[ 'files' , 'brands' ] ,
			'media'=>[ 'media' ] ,
				'media_temp'=>[ 'media' , 'temp' ] ,
				'media_avatars'=>[ 'media' , 'avatars' ] ,
				'media_js'=>[ 'media' , 'js' ] ,
					'media_js_structures'=>[ 'media' , 'js' , 'structures' ] ,
				'media_skin'=>[ 'media' , 'skin' ] ,
					'media_skin_current'=>[ 'media' , 'skin' , '%%skin%%' ] ,
			'cms'=>[ 'simplecms' ] ,
				'cms-classesfunctions'=>[ 'simplecms' , 'classesfunctions' ] ,
				'config'=>[ 'simplecms' , 'config' ] ,
				'cmslang'=>[ 'simplecms' , 'lang' ] ,
				'library'=>[ 'simplecms' , 'library' ] ,
					'library_db'=>[ 'simplecms' , 'library' , 'db' ] ,
				'classes'=>[ 'simplecms' , 'classes' ] ,
				'system'=>[ 'simplecms' , 'system' ] ,
					'mod'=>[ 'simplecms' , 'system' , 'mod' ] ,
					'sys'=>[ 'simplecms' , 'system' , 'sys' ] ,
					'com'=>[ 'simplecms' , 'system' , 'com' ] ,
				'core'=>[ 'simplecms' , 'core' ] ,
				'localization'=>[ 'simplecms' , 'localization' ] ,
					'lang'=>[ 'simplecms' , 'localization' , 'lang' ]
		];
		$this->basepatha = [																		// .. базовый путь URL
			'files'=>[ 'files' ] ,
				'files_users'=>[ 'files' , 'u' ] ,
				'files_countries'=>[ 'files' , 'countries' ] ,
			'media'=>[ 'media' ] ,
			'media_fonts'=>[ 'fonts' ] ,
			'media_avatars'=>[ 'media' , 'avatars' ] ,
			'media_skin'=>[ 'media' , 'skin' , '%%skin%%' ] ,
				'media_img'=>[ 'media' , 'skin' , '%%skin%%' , 'images' ] ,
				'media_iconsmin'=>[ 'media_icons' , 'icons_min' ] ,
			'media_css'=>[ 'media' , 'skin' , '%%skin%%' , 'css' ] ,
			'media_js'=>[ 'media' , 'js' ] ,
			'storeCatalogBrands'=>[ 'store' , 'catalog' ] ,
			'storeCatalogCategories'=>[ 'store' , 'catalog' ] ,
			'storeCatalogCollections'=>[ 'store' , 'catalog' ] ,
		];
		
		$this->path( $this->path );
		$this->path( $this->dirs );
		$this->path( $this->basepath );
		$this->patha( $this->basepatha );
	}
	
	private function path( array &$path , string $separator = DS ){
		
		## -- DEBUG -- ##
		
		# $this->debug->debugtmp( $this, '', [ __FILE__, __LINE__, __METHOD__ ] , $fo );
		
		$separator = ! $separator ? DS : $separator;
		if( ! empty( $path ) ){
			foreach( $path as $k0=>&$v0 ){
				$str = '';
				if( ! empty( $v0 ) && is_array( $v0 ) ){
					$count = count( $v0 );
					$count_q = 0;
					foreach( $v0 as $k1=>$v1 ){
						if( ! empty( $v1 ) ){
							$str .= $v1;
							if( $count_q < ( $count -1 ) ) $str .= $separator;
							$count_q++;
						}
					}
				}
				$v0 = $str;
			}
		}
	}
	

	private function patha( array &$path , string $separator=DSA ){

		## -- DEBUG -- ##

		# $this->debug->debugtmp( $this, '', [ __FILE__, __LINE__, __METHOD__ ] , $fo );
		$separator = ! $separator ? DS : $separator;
		if( ! empty( $path ) ){
			foreach( $path as $k0=>&$v0 ){
				$str = $separator == DSA ? DSA : '';
				if( ! empty( $v0 ) && is_array( $v0 ) ){
					$count = count( $v0 );
					$count_q = 0;
					foreach( $v0 as $k1=>$v1 ){
						if( ! empty( $v1 ) ){
							$str .= $v1;
							if( $count_q < ( $count -1 ) ) $str .= $separator;
							$count_q++;
						}
					}
				}
				$v0 = $str;
			}
		}
	}
}