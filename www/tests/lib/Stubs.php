<?PHP

# ============================================================
#   Stubs — общие заглушки для suite'ов, требующих \main.
#   Подключается в index.php до discover, чтобы все тесты
#   использовали один и тот же класс main.
# ============================================================

if ( ! interface_exists( 'VisuoHost' ) ) {
    require_once dirname( __DIR__ , 3 ) . '/visuo/VisuoHost.php';
}

if ( ! class_exists( 'stub_main_settings' ) ) {
    class stub_main_settings {
        public string $domain_real_name = 'damoony.ru';
        public string $allias_real_name = 'www';

        public function get ( string $key ) : mixed {
            return $key === 'language' ? 'ru-ru' : null;
        }

        public function skin ( string $opt ) : string {
            if ( $opt === 'skin'   ) return 'default';
            if ( $opt === 'allias' ) return 'www';
            return '';
        }
    }
}

if ( ! class_exists( 'stub_render_data' ) ) {
    class stub_render_data {
        public array $data = [ ];
        public function set ( string $k , mixed $v ) : void { $this->data[ $k ] = $v; }
        public function get ( string $k ) : mixed { return $this->data[ $k ] ?? null; }
    }
}

if ( ! class_exists( 'stub_render' ) ) {
    class stub_render {
        public stub_render_data $data;
        public function __construct ( ) { $this->data = new stub_render_data( ); }
        public function setdata ( string $k , mixed $v ) : void { $this->data->set( $k , $v ); }
        public function getdata ( string $k ) : mixed { return $this->data->get( $k ); }
    }
}

if ( ! class_exists( 'main' ) ) {
    class main implements VisuoHost {
        public $main_settings;
        public $visuorelease = null;
        public $render;
        public $user = null;

        public function __construct ( ) {
            $this->main_settings = new stub_main_settings( );
            $this->render        = new stub_render( );
        }

        public function __get ( $n ) { return null; }

        // --- Реализация интерфейса VisuoHost ---
        public function getRootTag(): visuotag {
            return new visuotag();
        }

        public function applyPageOverrides(visuostruct_html $struct): void {
            // Заглушка для тестов
        }
    }
}