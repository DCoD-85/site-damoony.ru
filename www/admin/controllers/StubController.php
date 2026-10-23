<?PHP

class StubController extends BaseController {

    public function edit(): void {
        $domain = (string) $this->get('domain');
        if ( $domain === '' ) { $this->redirect('?p=dashboard'); }
        
        $cfg = new Config( Paths::config() ); 
        $real = Paths::real( $domain );
        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $stub_title = trim( (string) $this->post('stub_title') ); 
            $stub_text = trim( (string) $this->post('stub_text') );
            Config::mutate( Paths::config(), function ( Config $cfg ) use ( $real, $stub_title, $stub_text ) { 
                $stub = $cfg->getKeys([ 'domains_stub', $real ], []); 
                $stub['title'] = $stub_title; 
                $stub['text'] = $stub_text; 
                $cfg->setKeys([ 'domains_stub', $real ], $stub); 
            } );
            AuditLog::action_ok( 'stub:edit', [ 'domain' => $real, 'title' => $stub_title ] );
            $this->redirect( '?p=stub:edit&domain=' . urlencode($domain) . '&saved=1' );
        }
        
        $stub = $cfg->getKeys( [ 'domains_stub', $real ], [] );
        
        $this->render( 'stub_edit', [
            'domain' => $domain, 'real' => $real, 'stub' => $stub, 'saved' => ! empty($this->get('saved')),
            'meta' => [ 'title' => 'Заглушка домена', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Заглушка', 'url' => ''] ], 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'stub'] ]
        ] );
    }

    public function list(): void {
        $stubs_cfg = new Config( PROJECT_ROOT . '/config/site.stubs.config' );
        $stubs = $stubs_cfg->get( 'stubs' ) ?? [];
        
        $this->render( 'stubs_list', [
            'stubs' => $stubs,
            'meta' => [ 'title' => 'Библиотека заглушек', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Заглушки', 'url' => ''] ] ]
        ] );
    }
    
    public function save(): void {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $stubs_data = $_POST['stubs'] ?? [];
            try {
                Config::mutate( PROJECT_ROOT . '/config/site.stubs.config', function ( Config $cfg ) use ( $stubs_data ) {
                    $cfg->setKeys( [ 'stubs' ], $stubs_data );
                } );
                $this->redirect( '?p=stubs:list&saved=1' );
            } catch ( Throwable $e ) {}
        }
        $this->redirect('?p=stubs:list');
    }
}