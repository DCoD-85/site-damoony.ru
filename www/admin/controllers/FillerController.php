<?PHP

class FillerController extends BaseController {

    public function new(): void {
        require_once ADMIN_ROOT . '/_lib/Scan.php';
        $domain = (string) ( $this->get('domain') ?: $this->post('domain') );
        $allias = (string) ( $this->get('allias') ?: $this->post('allias') );
        $block  = (string) $this->get('block');
        
        if ( $domain === '' ) { $this->redirect('?p=dashboard'); }
        
        $cfg = new Config( Paths::config() ); 
        $real = $cfg->getKeys([ 'domains_real', $domain ]);
        if ( $real === null ) { $this->redirect('?p=dashboard'); }
        
        $alliases = []; 
        foreach ( (array) $cfg->getKeys([ 'alliases_real', $real ], []) as $a => $t ) { 
            if ( $a !== '' ) $alliases[] = $a; 
        } 
        $blocks = Scan::blocksFor( $domain );

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $allias = (string) $this->post('allias'); 
            $block = (string) $this->post('block'); 
            $enable = ! empty( $this->post('enable') ); 
            $result = new Result();
            
            $block_path = Paths::block( $domain, $block ); 
            $filler_php = $block_path . '/' . $block . '_' . $allias . '.php';
            
            if ( is_file( $filler_php ) ) { 
                $result->step( 'Filler уже существует', true, Fs::rel( $filler_php ) ); 
            } else { 
                require_once CS_CMD . '/FillerNew.php'; 
                $result = FillerNew::run([ 'name' => $allias, 'block' => $block, 'domain' => $domain, 'allias' => $allias ]); 
            }
            
            if ( $result->ok && $enable ) {
                try { 
                    Config::mutate( Paths::config(), function ( Config $cfg ) use ( $real, $allias, $block ) { 
                        $cur = $cfg->getKeys([ 'fillers', $real, $allias, $block ]); 
                        if ( $cur !== true ) { $cfg->setKeys([ 'fillers', $real, $allias, $block ], true); } 
                    }); 
                    $result->step( 'Включено в site.config', true, "fillers[{$real}][{$allias}][{$block}] = true" ); 
                    AuditLog::action_ok( 'filler:new', [ 'domain' => $real, 'allias' => $allias, 'block' => $block ] ); 
                } catch ( Throwable $e ) { 
                    $result->warn( 'Не удалось включить в конфиге: ' . $e->getMessage() ); 
                }
            }
            
            $this->render( 'result', [ 
                'result' => $result, 
                'title' => "Filler: {$domain} / {$allias} / {$block}", 
                'back_url' => '?p=allias:list&domain=' . urlencode( $domain ), 
                'back_label' => 'К аллиасам' 
            ] );
            return;
        }
        
        $this->render( 'filler_new', [ 
            'domain' => $domain, 
            'allias' => $allias, 
            'alliases' => $alliases, 
            'blocks' => $blocks, 
            'meta' => [ 
                'title' => 'Создать филлер', 
                'breadcrumbs' => [ 
                    ['label' => 'Домены', 'url' => '?p=dashboard'], 
                    ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], 
                    ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], 
                    ['label' => 'Филлеры', 'url' => '?p=allias:fillers&domain='.urlencode($domain).'&allias='.urlencode($allias)], 
                    ['label' => 'Новый', 'url' => ''] 
                ] 
            ] 
        ] );
    }

    public function edit(): void {
        $domain = (string) $this->get('domain'); 
        $allias = (string) $this->get('allias'); 
        $block  = (string) $this->get('block');
        
        $f_dir  = Paths::block( $domain, $block ); 
        $php_f  = $f_dir . '/' . $block . '_' . $allias . '.php'; 
        $data_f = $f_dir . '/' . $block . '_' . $allias . '.data.php';
        
        if ( ! is_file( $php_f ) ) { 
            $this->redirect( '?p=allias:fillers&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) ); 
        }
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            Fs::writeAtomic( $php_f, $this->post('php') ); 
            Fs::writeAtomic( $data_f, $this->post('data') );
            $this->redirect( '?p=filler:edit&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&block=' . urlencode($block) . '&saved=1' );
        }
        
        $php_content = file_get_contents( $php_f );
        $data_content = is_file( $data_f ) ? file_get_contents( $data_f ) : "<?PHP\nreturn [\n\t\n];\n";

        $this->render( 'filler_edit', [
            'domain' => $domain, 'allias' => $allias, 'block' => $block, 'php' => $php_content, 'file_data' => $data_content,
            'meta' => [ 'title' => 'Редактор филлера', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Филлеры', 'url' => '?p=allias:fillers&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => $block, 'url' => ''] ] ]
        ] );
    }

    public function toggle(): void {
        $this->requirePost();
        $domain = (string) $this->post('domain'); 
        $allias = (string) $this->post('allias'); 
        $block = (string) $this->post('block'); 
        $value = (string) $this->post('value');
        
        require_once CS_CMD . '/FillerToggle.php'; 
        $result = FillerToggle::run([ 'domain' => $domain, 'allias' => $allias, 'block' => $block, 'value' => $value ]);
        AuditLog::action_ok( 'filler:toggle', [ 'domain' => $domain, 'allias' => $allias, 'block' => $block, 'ok' => $result->ok ] );
        
        $this->redirect( '?p=allias:fillers&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) );
    }

    public function remove(): void {
        $this->requirePost();
        $domain = (string) $this->post('domain'); 
        $allias = (string) $this->post('allias'); 
        $block = (string) $this->post('block');
        
        require_once CS_CMD . '/FillerRemove.php'; 
        $result = FillerRemove::run([ 'domain' => $domain, 'allias' => $allias, 'block' => $block ]);
        AuditLog::action_ok( 'filler:remove', [ 'domain' => $domain, 'allias' => $allias, 'block' => $block, 'ok' => $result->ok ] );
        
        $this->redirect( '?p=allias:fillers&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) );
    }
}