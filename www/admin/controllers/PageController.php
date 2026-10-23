<?PHP

class PageController extends BaseController {

    public function tree(): void {
        $domain = (string) $this->get('domain'); 
        $allias = (string) $this->get('allias');
        
        if ( $domain === '' || $allias === '' ) { $this->redirect('?p=dashboard'); }
        if ( Validate::domain( $domain ) !== null || Validate::allias_name( $allias ) !== null ) { $this->redirect('?p=dashboard'); }
        
        $cfg = new Config( Paths::config() );
        if ( $cfg->getKeys([ 'domains_real', $domain ]) === null ) { $this->redirect('?p=dashboard'); }
        
        require_once ADMIN_ROOT . '/_lib/Scan.php';
        $base = Paths::algoritm( $domain, $allias ); 
        $tree = is_dir( $base ) ? Scan::pageTree( $base ) : [];
        
        $this->render( 'pages_tree', [
            'domain' => $domain, 'allias' => $allias, 'logic' => Paths::logic(), 'tree' => $tree, 'has_dir' => is_dir( $base ),
            'meta' => [
                'title' => 'Страницы',
                'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Страницы', 'url' => ''] ],
                'actions' => '<a href="?p=page:new&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '" class="btn-primary">+ Новая страница</a>',
                'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages']
            ]
        ] );
    }

    public function visual(): void {
        $domain = (string) $this->get('domain'); 
        $allias = (string) $this->get('allias'); 
        $path = trim( (string) $this->get('path'), '/' );
        
        $base = Paths::algoritm( $domain, $allias ); 
        $cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/', '_', $path ) ); 
        $sub_dir = $path === '' ? $base : $base . '/' . $path; 
        $json_file = $sub_dir . '/' . $cls_short . '.json';
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $json_code = (string) $this->post('json_code');
            try { 
                if ( $json_code !== '' ) Fs::writeAtomic( $json_file, $json_code ); 
                AuditLog::action_ok( 'page:visual', [ 'domain' => $domain, 'allias' => $allias, 'path' => $path ] ); 
            } catch ( Throwable $e ) {}
            $this->redirect( '?p=page:visual&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&saved=1' );
        }
        
        $json_data = is_file( $json_file ) ? file_get_contents( $json_file ) : '{"tag":"div","class":"landing-container","children":[]}';
        $lang_cfg = is_file(PROJECT_ROOT . '/config/lang.config') ? json_decode(file_get_contents(PROJECT_ROOT . '/config/lang.config'), true) : [];
        $lang_default = $lang_cfg['default'] ?? 'ru-ru';
        $host = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'www.damoony2.ru');
        $url_path = ($allias === 'www' || $allias === '') ? '' : $allias . '/';
        $url_path .= ($path === '') ? $lang_default . '/index' : str_replace('lang', $lang_default, $path);
        $preview_url = rtrim($host . '/' . $url_path, '/') . '/';
        
        $this->render( 'page_visual', [
            'domain' => $domain, 'allias' => $allias, 'path' => $path, 'cls_short' => $cls_short, 'json_data' => $json_data, 'preview_url' => $preview_url,
            'meta' => [ 'title' => 'Визуальный редактор', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Визуально: '.($path===''?'(корень)':$path), 'url' => ''] ], 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ]
        ] );
    }

    public function code(): void {
        $domain = (string) $this->get('domain'); 
        $allias = (string) $this->get('allias'); 
        $path = trim( (string) $this->get('path'), '/' ); 
        $method_name = (string) $this->get('method');
        
        $base = Paths::algoritm( $domain, $allias ); 
        $cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/', '_', $path ) ); 
        $sub_dir = $path === '' ? $base : $base . '/' . $path; 
        $php_file = $sub_dir . '/' . $cls_short . '.php'; 
        $json_file = $sub_dir . '/' . $cls_short . '.rules.json';
        
        if ( ! is_file( $php_file ) ) { $this->redirect( '?p=pages:tree&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) ); }
        
        $parent_methods = [ 
            'algoritm' => "public function algoritm ( ) {\n\t\n}", 
            'guest' => "public function guest ( ) {\n\treturn false;\n}", 
            'logged' => "public function logged ( ) {\n\treturn false;\n}", 
            'ajax_guest' => "public function ajax_guest ( ) {\n\treturn false;\n}", 
            'ajax_logged' => "public function ajax_logged ( ) {\n\treturn false;\n}", 
            'default' => "public function default ( ) {\n\treturn parent::default();\n}" 
        ];
        
        $extract_methods = function( $code ) {
            $regex = '/(public|protected|private)?\s*function\s+(\w+)\s*\([^)]*\)\s*(?::\s*\w+)?\s*\{/'; 
            preg_match_all( $regex, $code, $matches, PREG_OFFSET_CAPTURE ); 
            $methods = [];
            foreach ( $matches[0] as $index => $match ) {
                $start_pos = $match[1]; 
                $name = $matches[2][$index][0]; 
                $brace_level = 1; 
                $pos = $start_pos + strlen($match[0]); 
                $len = strlen($code);
                while ( $pos < $len && $brace_level > 0 ) { 
                    $char = $code[$pos]; 
                    if ( $char === '{' ) $brace_level++; 
                    elseif ( $char === '}' ) $brace_level--; 
                    $pos++; 
                }
                $methods[$name] = [ 'code' => substr($code, $start_pos, $pos - $start_pos), 'start' => $start_pos, 'end' => $pos ];
            } 
            return $methods;
        };
        
        $php_code = file_get_contents( $php_file );
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            if ( isset( $_POST['save_json'] ) ) { 
                $json_code = (string) $this->post('json_code'); 
                try { if ( $json_code !== '' ) Fs::writeAtomic( $json_file, $json_code ); } catch ( Throwable $e ) {} 
                $this->redirect( '?p=page:code&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&saved=1' ); 
            }
            if ( isset( $_POST['save_method'] ) ) {
                $new_method_code = (string) $this->post('method_code'); 
                $methods = $extract_methods( $php_code );
                if ( isset( $methods[$method_name] ) ) { 
                    $m = $methods[$method_name]; 
                    $php_code = substr($php_code, 0, $m['start']) . $new_method_code . substr($php_code, $m['end']); 
                    try { Fs::writeAtomic( $php_file, $php_code ); } catch ( Throwable $e ) {} 
                }
                $this->redirect( '?p=page:code&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&method=' . urlencode($method_name) . '&saved=1' );
            }
            if ( isset( $_POST['add_method'] ) ) {
                $new_method = (string) $this->post('add_method'); 
                $methods = $extract_methods( $php_code );
                if ( isset( $parent_methods[$new_method] ) && !isset( $methods[$new_method] ) ) {
                    $last_brace = strrpos( $php_code, '}' ); 
                    if ( $last_brace !== false ) { 
                        $stub = "\n\t" . $parent_methods[$new_method] . "\n\n"; 
                        $php_code = substr_replace( $php_code, $stub, $last_brace, 0 ); 
                        try { Fs::writeAtomic( $php_file, $php_code ); } catch ( Throwable $e ) {} 
                    }
                }
                $this->redirect( '?p=page:code&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&method=' . urlencode($new_method) . '&saved=1' );
            }
        }
        
        $methods = $extract_methods( $php_code ); 
        $current_method_code = $method_name && isset($methods[$method_name]) ? $methods[$method_name]['code'] : ''; 
        $json_code = is_file( $json_file ) ? file_get_contents( $json_file ) : ''; 
        $tab = (string) $this->get('tab', 'logic');
        
        $this->render( 'page_code', [
            'domain' => $domain, 'allias' => $allias, 'path' => $path, 'cls_short' => $cls_short, 'methods' => $methods, 'parent_methods' => $parent_methods, 'method_name' => $method_name, 'method_code' => $current_method_code, 'json_code' => $json_code, 'tab' => $tab,
            'meta' => [ 'title' => 'Редактор кода', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Код: '.($path===''?'(корень)':$path), 'url' => ''] ], 'actions' => '<a href="?p=page:visual&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '" class="btn-edit">Визуально</a> <a href="?p=page:edit&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '" class="btn-edit">Настройки</a>', 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ]
        ] );
    }

    public function edit(): void {
        $domain = (string) $this->get('domain'); 
        $allias = (string) $this->get('allias'); 
        $path = trim( (string) $this->get('path'), '/' ); 
        $path_err = Validate::page_path( $path );
        
        if ( $path_err !== null ) { 
            $this->render( 'result', [ 'result' => new Result(), 'title' => 'Некорректный путь: ' . $path_err, 'back_url' => '?p=pages:tree&domain=' . urlencode($domain) . '&allias=' . urlencode($allias), 'back_label' => 'К страницам' ] ); 
            return; 
        }
        if ( $domain === '' || $allias === '' ) { $this->redirect('?p=dashboard'); }
        
        $cfg = new Config( Paths::config() ); 
        $real = $cfg->getKeys([ 'domains_real', $domain ]);
        if ( $real === null ) { $this->redirect('?p=dashboard'); }
        
        $base = Paths::algoritm( $domain, $allias ); 
        $cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/', '_', $path ) ); 
        $sub_dir = $path === '' ? $base : $base . '/' . $path; 
        $php_file = $sub_dir . '/' . $cls_short . '.php'; 
        $rules_file = $sub_dir . '/' . $cls_short . '.rules.json';
        
        if ( ! is_file( $php_file ) ) { 
            $this->render( 'result', [ 'result' => new Result(), 'title' => "Страница не найдена: {$path}", 'back_url' => '?p=pages:tree&domain=' . urlencode($domain) . '&allias=' . urlencode($allias), 'back_label' => 'К страницам' ] ); 
            return; 
        }
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $available_raw = (string) $this->post('available'); 
            $auth = ! empty( $this->post('auth') );
            
            $lines = preg_split( '/\r\n|\n|\r/', $available_raw ); 
            $available = []; $seen = [];
            foreach ( $lines as $line ) { 
                $line = trim( $line ); 
                if ( isset( $seen[$line] ) ) continue; 
                $seen[$line] = true; 
                $available[] = $line; 
            }
            while ( ! empty( $available ) && end( $available ) === '' ) { array_pop( $available ); }
            if ( empty( $available ) ) { $available = [ '' ]; }
            
            $data = [ 'available' => $available, 'auth' => $auth ];
            
            try { 
                Json::writeAtomic( $rules_file, $data ); 
                AuditLog::action_ok( 'page:edit', [ 'domain' => $domain, 'allias' => $allias, 'path' => $path ] ); 
            } catch ( Throwable $e ) { 
                $this->render( 'result', [ 'result' => new Result(), 'title' => 'Ошибка записи: ' . $e->getMessage(), 'back_url' => '?p=page:edit&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path), 'back_label' => 'Назад' ] ); 
                return; 
            }
            $this->redirect( '?p=page:edit&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&saved=1' );
        }
        
        $current = [ 'available' => [], 'auth' => false ];
        $json = Json::read( $rules_file );
        if ( is_array( $json ) ) {
            $current['available'] = isset( $json['available'] ) && is_array( $json['available'] ) ? $json['available'] : [];
            $current['auth'] = ! empty( $json['auth'] );
        }
        
        $this->render( 'page_edit', [
            'domain' => $domain, 'allias' => $allias, 'path' => $path, 'cls_short' => $cls_short, 'json_file' => $rules_file, 'current' => $current, 'saved' => ! empty( $this->get('saved') ),
            'meta' => [ 'title' => 'Настройки страницы: '.($path===''?'(корень)':$path), 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Страницы', 'url' => '?p=pages:tree&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => $path===''?'(корень)':$path, 'url' => ''] ], 'actions' => '<a href="?p=page:visual&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '" class="btn-edit">Визуально</a> <a href="?p=page:code&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '" class="btn-edit">Код</a>', 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ]
        ] );
    }

    public function new(): void {
        $domain = (string) $this->get('domain'); 
        $allias = (string) $this->get('allias');
        
        if ( $domain === '' || Validate::domain( $domain ) !== null ) { $this->redirect('?p=dashboard'); }
        $cfg = new Config( Paths::config() );
        if ( $cfg->getKeys([ 'domains_real', $domain ]) === null ) { $this->redirect('?p=dashboard'); }
        
        $real = Paths::real( $domain ); 
        $alliases = []; 
        foreach ( (array) $cfg->getKeys([ 'alliases_real', $real ], []) as $a => $t ) { 
            if ( $a !== '' ) $alliases[] = $a; 
        }
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $allias = (string) $this->post('allias'); 
            $name = trim( (string) $this->post('name') ); 
            $parent = trim( (string) $this->post('parent'), '/' ); 
            $path_err = Validate::page_path( $parent );
            
            if ( $path_err !== null ) { 
                $this->render( 'page_new', [ 'domain' => $domain, 'allias' => $allias, 'parent' => $parent, 'alliases' => $alliases, 'error' => $path_err, 'meta' => [ 'title' => 'Создать страницу', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Страницы', 'url' => '?p=pages:tree&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Создать страницу', 'url' => ''] ], 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ] ] ); 
                return; 
            }
            
            require_once CS_CMD . '/PageNew.php';
            $result = PageNew::run([ 'name' => $name, 'parent' => $parent, 'domain' => $domain, 'allias' => $allias ]);
            AuditLog::action_ok( 'page:new', [ 'domain' => $domain, 'allias' => $allias, 'name' => $name, 'ok' => $result->ok ] );
            
            $this->render( 'result', [ 'result' => $result, 'title' => "Создание страницы: {$domain} / {$allias} / {$name}", 'back_url' => '?p=pages:tree&domain=' . urlencode($domain) . '&allias=' . urlencode($allias), 'back_label' => 'К страницам' ] );
            return;
        }
        
        $this->render( 'page_new', [ 'domain' => $domain, 'allias' => $allias, 'parent' => (string) $this->get('parent'), 'alliases' => $alliases, 'meta' => [ 'title' => 'Создать страницу', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Страницы', 'url' => '?p=pages:tree&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Создать страницу', 'url' => ''] ], 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ] ] );
    }

    public function toggle(): void {
        $this->requirePost();
        $domain = (string) $this->post('domain'); 
        $allias = (string) $this->post('allias'); 
        $path = trim( (string) $this->post('path'), '/' ); 
        $key = (string) $this->post('key'); 
        $value = (string) $this->post('value');
        
        if ( $domain === '' || $allias === '' ) { $this->redirect('?p=dashboard'); }
        
        require_once CS_CMD . '/PageToggle.php'; 
        $result = PageToggle::run([ 'domain' => $domain, 'allias' => $allias, 'path' => $path, 'key' => $key, 'value' => $value ]);
        AuditLog::action_ok( 'page:toggle', [ 'domain' => $domain, 'allias' => $allias, 'path' => $path, 'key' => $key, 'ok' => $result->ok ] );
        
        $this->redirect( '?p=pages:tree&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) );
    }
}