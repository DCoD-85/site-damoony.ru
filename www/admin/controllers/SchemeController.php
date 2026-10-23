<?PHP

class SchemeController extends BaseController {

    public function list(): void {
        $domain = (string) $this->get('domain');
        if ( $domain === '' ) { $this->redirect('?p=dashboard'); }
        
        $dir = Paths::domain( $domain ) . '/alliasscheme'; $schemes = [];
        if ( is_dir( $dir ) ) { 
            foreach ( glob( $dir . '/alliasscheme_*.php' ) as $file ) { 
                if ( str_ends_with( $file, '.data.php' ) ) continue; 
                $schemes[] = str_replace( [ 'alliasscheme_', '.php' ], '', basename( $file ) ); 
            } 
        }
        
        $this->render( 'schemes_list', [
            'domain' => $domain, 'schemes' => $schemes,
            'meta' => [ 'title' => 'Схемы', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => ''] ], 'actions' => '<a href="?p=scheme:new&domain=' . urlencode($domain) . '" class="btn-primary">+ Добавить схему</a>', 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ]
        ] );
    }

    public function copy(): void {
        $domain = (string) $this->get('domain'); $source = (string) $this->get('scheme');
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $target = trim((string)$this->post('target'));
            if ( $target === '' || !preg_match('/^[a-z][a-z0-9_]*$/', $target) ) { 
                $this->render( 'result', [ 'result' => new Result(), 'title' => 'Некорректное имя схемы', 'back_url' => '?p=schemes:list&domain=' . urlencode($domain), 'back_label' => 'Назад' ] ); 
                return; 
            }
            $dir = Paths::domain( $domain ) . '/alliasscheme'; 
            $src_php = $dir . '/alliasscheme_' . $source . '.php'; 
            $src_data = $dir . '/alliasscheme_' . $source . '.data.php'; 
            $dst_php = $dir . '/alliasscheme_' . $target . '.php'; 
            $dst_data = $dir . '/alliasscheme_' . $target . '.data.php';
            
            if ( is_file($src_php) && is_file($src_data) ) {
                $c_php = file_get_contents($src_php); 
                $c_php = str_replace( 'class alliasscheme_' . $source, 'class alliasscheme_' . $target, $c_php ); 
                $c_php = str_replace( 'alliasscheme_' . $source . '.data.php', 'alliasscheme_' . $target . '.data.php', $c_php ); 
                Fs::writeAtomic( $dst_php, $c_php );
                Fs::writeAtomic( $dst_data, file_get_contents($src_data) ); 
                
                $main_file = Paths::domain($domain) . '/alliasscheme.php';
                if (is_file($main_file)) {
                    $main_c = file_get_contents($main_file); $prop_src = 'alliasscheme_' . $source; $prop_dst = 'alliasscheme_' . $target;
                    if (strpos($main_c, $prop_dst) === false) {
                        $new_prop = 'public null|' . $prop_dst . ' $' . $target . ' = null;'; 
                        $old_prop = 'public null|' . $prop_src . ' $' . $source . ' = null;'; 
                        $main_c = str_replace($old_prop, $old_prop . "\n\t" . $new_prop, $main_c);
                        
                        $new_init = '$this->' . $target . ' = new ' . $prop_dst . '( $this );'; 
                        $old_init = '$this->' . $source . ' = new ' . $prop_src . '( $this );'; 
                        $main_c = str_replace($old_init, $old_init . "\n\t\t" . $new_init, $main_c);
                        
                        $new_inc = "( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_dst . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );"; 
                        $old_inc = "( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_src . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );"; 
                        $main_c = str_replace($old_inc, $old_inc . "\n\t\t\t" . $new_inc, $main_c);
                        
                        Fs::writeAtomic($main_file, $main_c);
                    }
                }
                $this->redirect( '?p=schemes:list&domain=' . urlencode($domain) . '&copied=1' );
            }
        }
        $this->render( 'scheme_copy', [ 'domain' => $domain, 'source' => $source, 'meta' => [ 'title' => 'Копировать схему', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], ['label' => 'Копировать', 'url' => ''] ], 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ] ] );
    }

    public function remove(): void {
        $domain = (string) $this->get('domain'); $scheme = (string) $this->get('scheme');
        if ( $scheme === 'www' || $scheme === 'visuo' || $scheme === 'error' ) { die('Базовые схемы удалять нельзя'); }
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $confirm = trim((string)$this->post('confirm'));
            if ( $confirm === $scheme ) {
                $dir = Paths::domain( $domain ) . '/alliasscheme'; 
                @unlink( $dir . '/alliasscheme_' . $scheme . '.php' ); 
                @unlink( $dir . '/alliasscheme_' . $scheme . '.data.php' );
                
                $main_file = Paths::domain($domain) . '/alliasscheme.php';
                if (is_file($main_file)) { 
                    $main_c = file_get_contents($main_file); 
                    $prop = 'alliasscheme_' . $scheme; 
                    $lines = explode("\n", $main_c); 
                    $new_lines = []; 
                    foreach ($lines as $line) { 
                        if (strpos($line, $prop) === false) $new_lines[] = $line; 
                    } 
                    Fs::writeAtomic($main_file, implode("\n", $new_lines)); 
                }
                $this->redirect( '?p=schemes:list&domain=' . urlencode($domain) . '&removed=1' );
            }
        }
        $this->render( 'scheme_remove', [ 'domain' => $domain, 'scheme' => $scheme, 'meta' => [ 'title' => 'Удалить схему', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], ['label' => 'Удалить', 'url' => ''] ], 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ] ] );
    }

    public function new(): void {
        $domain = (string) $this->get('domain');
        if ( $domain === '' ) { $this->redirect('?p=dashboard'); }
        
        $errors = [];
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $target = trim((string)$this->post('target')); 
            $source = trim((string)$this->post('source'));
            
            if ( $target === '' || !preg_match('/^[a-z][a-z0-9_]*$/', $target) ) { 
                $errors[] = 'Некорректное имя схемы (только a-z, 0-9, _)'; 
            } else {
                $dir = Paths::domain( $domain ) . '/alliasscheme'; 
                $src_php = $dir . '/alliasscheme_' . $source . '.php'; 
                $src_data = $dir . '/alliasscheme_' . $source . '.data.php'; 
                $dst_php = $dir . '/alliasscheme_' . $target . '.php'; 
                $dst_data = $dir . '/alliasscheme_' . $target . '.data.php';
                
                if ( is_file($src_php) && is_file($src_data) && !is_file($dst_php) ) {
                    $c_php = file_get_contents($src_php); 
                    $c_php = str_replace( 'class alliasscheme_' . $source, 'class alliasscheme_' . $target, $c_php ); 
                    $c_php = str_replace( 'alliasscheme_' . $source . '.data.php', 'alliasscheme_' . $target . '.data.php', $c_php ); 
                    Fs::writeAtomic( $dst_php, $c_php );
                    Fs::writeAtomic( $dst_data, file_get_contents($src_data) ); 
                    
                    $main_file = Paths::domain($domain) . '/alliasscheme.php';
                    if (is_file($main_file)) {
                        $main_c = file_get_contents($main_file); 
                        $prop_src = 'alliasscheme_' . $source; 
                        $prop_dst = 'alliasscheme_' . $target;
                        if (strpos($main_c, $prop_dst) === false) {
                            $main_c = str_replace('public null|' . $prop_src . ' $' . $source . ' = null;', 'public null|' . $prop_src . ' $' . $source . ' = null;' . "\n\t" . 'public null|' . $prop_dst . ' $' . $target . ' = null;', $main_c);
                            $main_c = str_replace('$this->' . $source . ' = new ' . $prop_src . '( $this );', '$this->' . $source . ' = new ' . $prop_src . '( $this );' . "\n\t\t" . '$this->' . $target . ' = new ' . $prop_dst . '( $this );', $main_c);
                            $main_c = str_replace("( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_src . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );", "( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_dst . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );\n\t\t\t( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_src . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );", $main_c);
                            Fs::writeAtomic($main_file, $main_c);
                        }
                    }
                    $this->redirect( '?p=schemes:list&domain=' . urlencode($domain) . '&saved=1' );
                } else { 
                    $errors[] = 'Исходная схема не найдена или такая уже существует'; 
                }
            }
        }
        
        $dir = Paths::domain( $domain ) . '/alliasscheme'; $schemes = [];
        if ( is_dir($dir) ) { 
            foreach ( glob( $dir . '/alliasscheme_*.php' ) as $file ) { 
                if ( str_ends_with( $file, '.data.php' ) ) continue; 
                $schemes[] = str_replace( [ 'alliasscheme_', '.php' ], '', basename( $file ) ); 
            } 
        }
        
        $this->render( 'scheme_new', [ 'domain' => $domain, 'schemes' => $schemes, 'errors' => $errors, 'meta' => [ 'title' => 'Новая схема', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], ['label' => 'Новая схема', 'url' => ''] ], 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ] ] );
    }

    public function edit(): void {
        $domain = (string) $this->get('domain'); $scheme = (string) $this->get('scheme');
        
        $file_php  = Paths::domain( $domain ) . '/alliasscheme/alliasscheme_' . $scheme . '.php'; 
        $file_data = Paths::domain( $domain ) . '/alliasscheme/alliasscheme_' . $scheme . '.data.php';
        
        if ( ! is_file( $file_data ) ) { 
            $this->render( 'result', [ 
                'result' => new Result(), 
                'meta' => [ 'title' => 'Файл схемы не найден', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], ['label' => $scheme ?: 'Неизвестно', 'url' => ''] ] ],
                'back_url' => '?p=schemes:list&domain=' . urlencode($domain), 'back_label' => 'К списку схем' 
            ] ); 
            return; 
        }
        
        $data = require $file_data;
        
        $dir = Paths::domain( $domain ) . '/alliasscheme';
        $schemes = [];
        if ( is_dir( $dir ) ) {
            foreach ( glob( $dir . '/alliasscheme_*.php' ) as $f ) {
                if ( str_ends_with( $f, '.data.php' ) ) continue;
                $name = str_replace( [ 'alliasscheme_', '.php' ], '', basename( $f ) );
                if ( is_file( $dir . '/alliasscheme_' . $name . '.data.php' ) ) { $schemes[] = $name; }
            }
        }
        sort( $schemes );

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $json = (string) $this->post('scheme_json'); 
            $new_data = json_decode( $json, true );
            if ( is_array( $new_data ) ) { 
                $php_code = '<?PHP' . "\n# ============================================================\n#   alliasscheme_" . $scheme . ".data.php — структура схемы\n# ============================================================\nreturn " . var_export($new_data, true) . ";\n"; 
                Fs::writeAtomic($file_data, $php_code); 
            }
            $this->redirect('?p=scheme:edit&domain=' . urlencode($domain) . '&scheme=' . urlencode($scheme) . '&saved=1');
        }
        
        $this->render( 'scheme_edit', [
            'domain' => $domain, 'scheme' => $scheme, 'schemes' => $schemes, 'scheme_data' => $data,
            'meta' => [ 'title' => 'Схема: '.$scheme, 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], ['label' => $scheme, 'url' => ''] ], 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ]
        ] );
    }
}