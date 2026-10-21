<?PHP
# --- script information --- #
# script name: /main.php
# created : 10.09.2025
# author : default

require_once dirname( __DIR__ ) . '/visuo/VisuoHost.php';

class main implements VisuoHost {
	# vars
	public null|main_path $main_path = null;
	public null|main_settings $main_settings = null;

	# basevars
	# source
	public null|render $render = null;
	public null|user $user = null;
	public blacklist $blacklist;
	public vardump $vardump;
	public log $log;
	public core $core;
	public db $db;
	public null|library $library = null;
	public language $language;
	public messages $messages;
	public mod  $mod;
	public null|visuopage $visuopage = null;

	# domain
	public null|domain $domain = null;
	
	# allias
	public null|object $allias = null;
	public null|object $alliasglobal = null;
	public null|object $alliasscheme = null;
	public null|object $dataprovider = null;

	
	public \algoritm_ $algoritm_;
	public \algoritm_ $algoritm;
	
	# visuo
	public null|visuorelease $visuorelease = null;
	public null|visuostruct $visuostruct = null;
	public null|visuolog $visuolog = null;
	
	public array $algoritm_class = [ ];

	public string $release_min = '';
	
	public function __construct ( null|main_settings $main_settings = null ) {
		$this->main_settings = $main_settings ? $main_settings->main( $this ) : new main_settings( $this );
	}
	
	public function path ( null|string $url = null ) {
		$this->main_path = $this->main_path ?: new main_path( $this );
		$this->main_path->set( $url );
	}
	public function init( ) {
		# 
		require_once __DIR__ . '/autoload.php';
		( include_once $path = __DIR__ . DS . 'include.php' ) or error_file( $path , __FILE__ , __LINE__ );
		
		# 
		$this->visuorelease = $this->visuorelease ?: new visuorelease( $this );
		$this->visuopage = $this->visuopage ?: new visuopage( $this );
		$this->visuostruct = $this->visuostruct ?: new visuostruct( $this );
		
		$this->render = $this->render ?: new render( $this );
		# --- Включаем AJAX-режим, если запрос пришел от JS ---
		if ( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest' ) {
			$this->render->setdata( 'render_ajax' , true );
		}
		
		$this->core = new core( $this );
		# 
		$this->user = new user( $this );
		$this->blacklist = new blacklist( $this );
		$this->log = new log( $this );
		$this->vardump = new vardump( $this );
		$this->db = new db( $this );
		$this->library = new library( $this );
		$this->language = new language( $this );
		$this->messages = new messages( $this );
		$this->mod = new mod( $this );
		$this->main_settings->init( );
		$this->main_settings->set( 'connect_type' , 'http://' );
		
		$this->language->load( )->init( );
		
		$logic_ver = $this->main_settings->get( 'logic' );
		( include_once $path = dirname( __DIR__ ) . DS . 'engines' . DS . 'chain' . DS . 'include.php' ) or error_file( $path , __FILE__ , __LINE__ );

		$this->blacklist->init( );
		
		$db = $this->db->db( );
		$db->open( 'login_damoony' );
		$this->user->authorized( );
	}
	
	public function logic ( ) {
		$main = $this;
		
		$url = $main->library->url( );
		$static_domain = $main->main_settings->domain_accepted( false );
		$static_allias = $main->main_settings->allias_accepted( );

		# --- домен или аллиас выключен/не найден → 404 ---
		if ( empty( $static_domain ) || empty( $static_allias ) ) {
			$real = $this->main_settings->domain_real_name ?? '';
			$logic = $this->main_settings->logic ?? 'alpha.0.0';

			$stub_domain = $this->main_settings->domains_stub[ $real ] ?? null;
			$is_stub_domain = $stub_domain && ( $stub_domain['enabled'] ?? false ) && ( ( $stub_domain['until'] ?? 0 ) == 0 || time() < $stub_domain['until'] );

			$allias_key = $this->main_settings->allias_real_name ?? '';
			$stub_allias = $this->main_settings->alliases_stub[ $real ][ $allias_key ] ?? null;
			$is_stub_allias = $stub_allias && ( $stub_allias['enabled'] ?? false ) && ( ( $stub_allias['until'] ?? 0 ) == 0 || time() < $stub_allias['until'] );

			$active_stub = null;
			if ( $is_stub_domain ) $active_stub = $stub_domain;
			elseif ( $is_stub_allias ) $active_stub = $stub_allias;

			if ( $active_stub ) {
				$ns = $this->main_settings->domains_namespace[ $real ] ?? '';
				$class = '\\' . $ns . '\\www\\algoritm_stub';
				$file = dirname( __DIR__ ) . '/app/' . $logic . '/domain/' . $real . '/allias/www/algoritm/algoritm_stub.php';

				if ( file_exists( $file ) ) {
					require_once $file;
					if ( class_exists( $class ) ) {
						$instance = new $class( $this );
						$instance->render_stub( $active_stub );
						$this->release();
						exit;
					}
				}
			}

			http_response_code( 404 );
			die( 'Not Found' );
		}
		
		# domain
		$this->domain = new domain( $this );
		
		# dataprovider — фасад домена
		$this->dataprovider = new ($static_domain.'\\dataprovider')( $this );

		# allias
		$this->allias = new ($static_domain.'\\allias')( $this );
		$this->alliasglobal = new ($static_domain.'\\alliasglobal')( $this );
		$this->alliasscheme = new ($static_domain.'\\alliasscheme')( $this );
		
		# --- ВЫБОР ДВИЖКА ЛОГИКИ ---
		if ( $this->main_settings->get( 'logic_type' ) === 'router' ) {
			require_once dirname( __DIR__ ) . '/engines/router/bridge.php';
			router_resolve( $this , $static_domain , $static_allias );
		} else {
			# Старая логика (legacy)
			$main->algoritm = new ($static_domain . '\\' . $static_allias . '\\' . 'algoritm')( $this , [ ] );
			$main->algoritm->clear( );
			$main->algoritm->start( );
			$main->algoritm->debug( );
		}
	}

	public function savedata ( ) {
		$main = $this;
		$main->language->savedefaults( );
	}
	
	public function release ( bool $log = false , bool $debug = false ){
		# scripts: применяем эффекты до финального рендера
		if ( $this->alliasglobal !== null ) {
			foreach ( $this->alliasglobal->scripts as $script ) {
				$script->run( );
			}
		}

		$this->render->release( $log , $debug );
	}

	public function main ( null|string $address = null ) {
		$this->path( $address );
		$this->init( );
		$this->logic( );
		$this->savedata( );
		$this->release( GL_LOG , GL_DEBUG);
	}

	public function redirect ( null|string $address = null  ) {
		print '<div>REDIRECT: <b>' . $address . '</b></div>';

		$this->path( $address );
		$this->main_settings->init( );
		$this->logic( );
	}
	
	private function main_error( string $title , string $message ){
		die( '<! doctype><html><head><title>' . $title . '</title><head><body><div>' . $message . '</div></body></html>' );
	} 

	public function getRootTag(): visuotag {
        return $this->visuorelease->visuotag->root();
    }

    public function applyPageOverrides(visuostruct_html $struct): void {
        if ($this->visuopage !== null) {
            $this->visuopage->apply($struct);
        }
    }
	
	# --- guard: is user a guest? (works even if user module is disabled) ---
	public function is_guest ( ) : bool {
		return $this->user === null ? true : $this->user->is_guest( );
	}
	
	# --- guard: is user authorized? ---
	public function is_auth ( ) : bool {
		return ! $this->is_guest( );
	}
}

# class path
class main_path {
	# vars
	private main $main;
	public string $domain;
	public string $url;
	# methods
	public function __construct( main $main ) {
		$this->main = $main;
	}

	public function set ( null|string $path = null ) {
		if( ! $path ) $path = $_SERVER[ 'REQUEST_SCHEME' ] . '://' . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
		
		$returnData = [ ];
		preg_match( '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i' , $path , $returnData );

		$this->domain = $returnData[ 4 ];
		$this->url = $returnData[ 0 ];
	}
	public function get_domain ( ) {
		return $this->domain;
	}
	public function get_url ( ) {
		return $this->url;
	}
}

class main_settings {
	# vars
	public null|main $main = null;
	public string $logic_type = 'chain'; // 'chain' или 'router'
	private string $logic = 'alpha.0.0';
	public bool $ui_ajax_forms = true;
	public string $domain_real_name;
	public string $allias_real_name;
	private string $language;
	private string $render;
	private string $skin;
	private null|string $skin_allias = 'www';
	private string $connect_type = '';
	public string $title = 'Site';
	public array $domains_real = [ ];
	public array $domains_available = [ ];
	public array $alliases_real = [ ];
	public array $alliases_available = [ ];
	public array $domains_namespace = [ ];
	public array $domains_titles = [ ];
	public array $domains_skins = [ ];
	public array $domains_stub = [ ];
	public array $stubs = [ ];
	public array $alliases_stub = [ ];
	public array $fillers = [ ];

	# render engine
	public array $render_engine = [ 'default' => 'algoritm' ];

	# methods
	public function __construct ( null|main $main = null , array $settings = [ 'render'=>'visuo' , 'language'=>'ru-ru' , 'skin'=>'default' ] ) {
		$this->render   = $_COOKIE[ 'render'   ] ?? $settings[ 'render'   ];
		$this->language = $_COOKIE[ 'language' ] ?? $settings[ 'language' ];
		$this->skin     = $_COOKIE[ 'skin'     ] ?? $settings[ 'skin'     ];
	}
	public function init ( ) {
		# init
		$root = dirname( __DIR__ );

		$this->loadExternalConfig( $root . DS . 'config' . DS . 'cms.config'    );
		$this->loadExternalConfig( $root . DS . 'config' . DS . 'site.config'   );
		$this->loadExternalConfig( $root . DS . 'config' . DS . 'render.config' );
		$this->loadExternalConfig( $root . DS . 'config' . DS . 'lang.config'   );
		$this->loadExternalConfig( $root . DS . 'config' . DS . 'site.stubs.config'  );
		$this->loadExternalConfig( $root . DS . 'secure' . DS . 'db.secure'     );

		$url = $this->main->library->url( );
		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;

		# --- язык: URL приоритетнее куки ---
		$langs_available = [ 'ru-ru' , 'en-en' , 'ch-ch' ];
		$base0       = $url_base[ 0 ] ?? '';
		$cookie_lang = $_COOKIE[ 'language' ] ?? '';

		if ( in_array( $base0 , $langs_available , true ) ) {
			$this->language = $base0;
		} elseif ( in_array( $cookie_lang , $langs_available , true ) ) {
			$this->language = $cookie_lang;
		}
		# иначе — остаётся из __construct (по умолчанию 'ru-ru')

		# --- остальное как было ---
		$this->domain_real_name = $this->domains_real[ $url_allias[ 0 ] ];
		$this->allias_real_name = $this->alliases_real[ $this->domain_real_name ][ $url_allias[ 1 ] ?? '' ];

		$this->skin_allias = $this->allias_real_name;
	}
	/**
	 * Загружает внешнюю конфигурацию из JSON файла и динамически обновляет свойства класса.
	 * 
	 * @param string $configPath Абсолютный путь к файлу конфигурации.
	 * @return void
	 */
	public function loadExternalConfig( string $configPath ) {
		if( ! file_exists( $configPath ) ){
			return;
		}

		$jsonContent = file_get_contents( $configPath );
		$configData = json_decode( $jsonContent , true );

		if( json_last_error( ) !== JSON_ERROR_NONE || ! is_array( $configData ) ) {
			return;
		}

		foreach( $configData as $property=>$value ){
			if ( property_exists( $this , $property ) ){
				$this->$property = $value;
			}
		}
	}

	public function main ( main $main ) {
		$this->main = $main;
		return $this;
	}
	public function domains ( string $domain_name ) {
		return $this->domains_real[ $domain_name ] ?: null;
	}
	public function alliases ( string $allias_name ) {
		
		# return $this
		# return $this->{'alliases_' . $this->alliases_level}[ $this->domain_name ][ $allias_name ] ? $allias_name : null;
	}
	
	public function domain_accepted ( bool $full = true ) {
		# домен вообще не в списке доступных → null
		if ( empty( $this->domains_available[ $this->domain_real_name ] ) ) {
			return null;
		}
		$return = $this->domain_real_name;
		if ( $full == false ) {
			$ns = $this->domains_namespace[ $this->domain_real_name ] ?? null;
			if ( $ns !== null && $ns !== '' ) return $ns;
			$return = mb_ereg_replace( '\.ru' , '' , $return );
		}
		return $return;
	}
	
	public function allias_accepted ( ) {
		return $this->alliases_available[ $this->domain_real_name ][ $this->allias_real_name ] ? $this->allias_real_name : null;
	}
	public function engine ( ) : string {
		$allias = $this->allias_real_name;
		if ( $allias === null || $allias === '' ) {
			return $this->render_engine[ 'default' ] ?? 'algoritm';
		}
		return $this->render_engine[ $allias ]
			?? $this->render_engine[ 'default' ]
			?? 'algoritm';
	}
	public function default ( string $language ,
	                        string $render ,
	                        string $skin ) {
		$this->language = $language;
		$this->render = $render;
		$this->skin = $skin;
		$this->connect_type = 'https://';
	}
	public function set ( string $var , string $val , string $convert = 'string' ) {
		$this->{$var} = $val;
	}
	public function get ( string $var ) {
		return $this->{$var};
	}
	
	public function skin ( string $option ) {
		if ( $option === 'skin' ) {
			$domain = $this->domain_real_name ?? '';
			if ( $domain !== '' && ! empty( $this->domains_skins[ $domain ] ) ) {
				return $this->domains_skins[ $domain ];
			}
			return $this->skin;
		}
		if ( $option === 'allias' ) return $this->skin_allias;
	}

	# --- доменный title (с fallback на общий) ---
	public function title ( ) : string {
		$domain = $this->domain_real_name ?? '';
		if ( $domain !== '' && ! empty( $this->domains_titles[ $domain ] ) ) {
			return $this->domains_titles[ $domain ];
		}
		return $this->title !== '' ? $this->title : 'Site';
	}
}

?>