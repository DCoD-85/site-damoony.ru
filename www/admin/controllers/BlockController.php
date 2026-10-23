<?PHP

class BlockController extends BaseController {

    public function new(): void {
        require_once CS_CMD . '/BlockNew.php'; 
        $domain = (string) ( $this->get('domain') ?: $this->post('domain') );
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $result = BlockNew::run([ 'name' => $this->post('name'), 'domain' => $domain ]);
            $this->render( 'result', [ 'result' => $result, 'title' => 'Новый блок', 'back_url' => '?p=blocks:list&domain=' . urlencode($domain), 'back_label' => 'К блокам' ] );
            return;
        }
        
        $this->render( 'block_new', [ 
            'domain' => $domain, 
            'meta' => [ 
                'title' => 'Новый блок', 
                'breadcrumbs' => [ 
                    ['label' => 'Домены', 'url' => '?p=dashboard'], 
                    ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], 
                    ['label' => 'Блоки', 'url' => '?p=blocks:list&domain='.urlencode($domain)], 
                    ['label' => 'Новый блок', 'url' => ''] 
                ] 
            ] 
        ] );
    }

    public function edit(): void {
        $domain = (string) $this->get('domain'); 
        $name   = (string) $this->get('name');
        
        $php_file  = Paths::block( $domain, $name ) . '/' . $name . '.php'; 
        $data_file = Paths::block( $domain, $name ) . '/' . $name . '.data.php';
        
        if ( ! is_file( $php_file ) ) { 
            $this->redirect( '?p=blocks:list&domain=' . urlencode($domain) ); 
        }
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            Fs::writeAtomic( $php_file, $this->post('php') ); 
            Fs::writeAtomic( $data_file, $this->post('data') );
            $this->redirect( '?p=block:edit&domain=' . urlencode($domain) . '&name=' . urlencode($name) . '&saved=1' );
        }
        
        $php_content = file_get_contents( $php_file );
        $data_content = is_file( $data_file ) ? file_get_contents( $data_file ) : "<?PHP\nreturn [\n\t\n];\n";

        $this->render( 'block_edit', [
            'domain' => $domain, 'name' => $name, 'php' => $php_content, 'file_data' => $data_content,
            'meta' => [ 'title' => 'Редактор блока: '.$name, 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Блоки', 'url' => '?p=blocks:list&domain='.urlencode($domain)], ['label' => $name, 'url' => ''] ] ]
        ] );
    }

    public function remove(): void {
        $domain = (string) $this->get('domain'); 
        $name = (string) $this->get('name');
        
        require_once CS_CMD . '/BlockRemove.php'; 
        $result = BlockRemove::run([ 'domain' => $domain, 'name' => $name ]);
        
        $this->render( 'result', [ 'result' => $result, 'title' => 'Удаление блока', 'back_url' => '?p=blocks:list&domain=' . urlencode($domain), 'back_label' => 'К блокам' ] );
    }

    public function list(): void {
        $domain = (string) $this->get('domain');
        if ( $domain === '' ) { $this->redirect('?p=dashboard'); }
        
        require_once ADMIN_ROOT . '/_lib/Scan.php'; 
        $blocks = Scan::blocksDetailsFor( $domain ); 
        $layouts = Scan::layoutsFor( $domain ); 
        $cfg = new Config( Paths::config() ); 
        $alliases = array_values(array_unique((array)$cfg->getKeys(['alliases_real', Paths::real($domain)], [])));
        
        $this->render( 'blocks_list', [
            'domain' => $domain, 'blocks' => $blocks, 'layouts' => $layouts, 'alliases' => $alliases,
            'meta' => [ 'title' => 'Блоки', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Блоки', 'url' => ''] ], 'actions' => '<a href="?p=block:new&domain=' . urlencode($domain) . '" class="btn-primary">+ Создать блок</a>', 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'blocks'] ]
        ] );
    }
}