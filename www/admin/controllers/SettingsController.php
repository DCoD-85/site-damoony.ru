<?PHP

class SettingsController extends BaseController {

    public function index(): void {
        require_once ADMIN_ROOT . '/_lib/Scan.php';
        $allowed = [ 'logic', 'connect_type', 'ui_ajax_forms' ];
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            require_once CS_CMD . '/SettingsSet.php';
            $params = [];
            foreach ( $allowed as $k ) {
                if ( array_key_exists( $k, $_POST ) ) { $params[$k] = $_POST[$k]; }
            }
            $result = SettingsSet::run( $params );
            AuditLog::action_ok( 'settings:set', [ 'params' => $params, 'ok' => $result->ok ] );
            
            if ( $result->ok ) { $this->redirect( '?p=settings&saved=1' ); }
            
            $this->render( 'result', [ 'result' => $result, 'title' => 'Настройки — ошибка', 'back_url' => '?p=settings', 'back_label' => 'К настройкам' ] );
            return;
        }
        
        $engine_cfg = new Config( Paths::engine() );
        $site_cfg   = new Config( Paths::config() );

        $this->render( 'settings', [
            'meta' => [ 'title' => 'Настройки' ], 'title' => 'Настройки', 'saved' => ! empty( $this->get('saved') ),
            'current' => [ 
                'logic'        => (string) ( $engine_cfg->get( 'logic' ) ?? '' ), 
                'ui_ajax_forms'=> ! empty( $engine_cfg->get( 'ui_ajax_forms' ) ), 
                'connect_type' => (string) ( $engine_cfg->get( 'connect_type' ) ?? 'https://' ) 
            ],
            'skins' => Scan::skinsFor( array_key_first((array)$site_cfg->get('domains_real', [])) ?? 'damoony.ru' ), 
            'logics' => Scan::logics(), 
            'connectTypes' => Scan::connectTypes()
        ] );
    }

    public function db(): void {
        $config_path = PROJECT_ROOT . '/secure/db.config'; 
        $data = is_file($config_path) ? json_decode(file_get_contents($config_path), true) : []; 
        $bases = $data['bases'] ?? []; 
        $db = $bases[0] ?? ['localname' => 'visuo_damoony'];
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            require_once CS_CMD . '/DbSave.php'; 
            $result = DbSave::run( $_POST );
            if ( $result->ok ) { $this->redirect( '?p=db:settings&saved=1' ); }
            $this->render( 'result', [ 'result' => $result, 'title' => 'Ошибка', 'back_url' => '?p=db:settings', 'back_label' => 'Назад' ] );
            return;
        }
        
        $this->render( 'db_settings', [ 
            'db' => $db, 
            'saved'=> ! empty( $this->get('saved') ), 
            'meta' => [ 'title' => 'Настройки Базы Данных', 'breadcrumbs' => [ ['label' => 'Настройки', 'url' => '?p=settings'], ['label' => 'База данных', 'url' => ''] ] ] 
        ] );
    }
}