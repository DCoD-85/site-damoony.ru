<?PHP

class AlliasController extends BaseController {

    public function new(): void {
        $cfg = new Config( Paths::config() );
        $domains_raw = (array) $cfg->get( 'domains_real' );
        $domains = [];
        foreach ( $domains_raw as $d => $real ) {
            if ( $d === $real ) $domains[] = $d;
        }
        $prefill_domain = (string) $this->get( 'domain' );

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            require_once CS_CMD . '/AlliasNew.php';
            $post_domain = (string) $this->post( 'domain' );
            $result = AlliasNew::run([
                'name' => $this->post( 'name' ),
                'domain' => $post_domain,
                'register' => ! empty( $this->post( 'register' ) )
            ]);
            AuditLog::action_ok( 'allias:new', [
                'domain' => $post_domain,
                'name' => $this->post( 'name' ),
                'ok' => $result->ok
            ]);
            $this->render( 'result', [
                'result' => $result,
                'title' => 'Создание аллиаса',
                'back_url' => '?p=allias:list&domain=' . urlencode( $post_domain ),
                'back_label' => 'К аллиасам'
            ]);
            return;
        }

        $this->render( 'allias_new', [
            'domains' => $domains,
            'prefill_domain' => $prefill_domain,
            'meta' => [
                'title' => 'Создать аллиас',
                'breadcrumbs' => [
                    ['label' => 'Домены', 'url' => '?p=dashboard'],
                    ['label' => 'Создать аллиас', 'url' => '']
                ]
            ]
        ]);
    }

    public function edit(): void {
        $domain = (string) $this->get( 'domain' );
        $allias = (string) $this->get( 'allias' );
        if ( $domain === '' || $allias === '' ) {
            $this->redirect( '?p=dashboard' );
        }

        $cfg  = new Config( Paths::config() );
        $real = Paths::real( $domain );
        $is_avail = ! empty( $cfg->getKeys( [ 'alliases_available', $real, $allias ] ) );

        $stubs_cfg = new Config( PROJECT_ROOT . '/config/site.stubs.config' );
        $stubs = $stubs_cfg->get( 'stubs' ) ?? [];
        $current_stub = $cfg->getKeys( [ 'alliases_stub', $real, $allias, 'current' ], 'maintenance' );

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $avail = ! empty( $this->post( 'available' ) );
            $stub_current = trim( (string) $this->post( 'stub_current', 'maintenance' ) );

            Config::mutate( Paths::config(), function ( Config $cfg ) use ( $real, $allias, $avail, $stub_current ) {
                $cfg->setKeys( [ 'alliases_available', $real, $allias ], $avail );
                $cfg->setKeys( [ 'alliases_stub', $real, $allias, 'current' ], $stub_current );
            } );

            AuditLog::action_ok( 'allias:edit', [
                'domain' => $real,
                'allias' => $allias,
                'avail' => $avail,
                'stub' => $stub_current
            ]);
            $this->redirect( '?p=allias:edit&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) . '&saved=1' );
        }

        $this->render( 'allias_edit', [
            'domain' => $domain,
            'allias' => $allias,
            'avail' => $is_avail,
            'saved' => ! empty( $this->get( 'saved' ) ),
            'stubs' => $stubs,
            'current_stub' => $current_stub,
            'meta' => [
                'title' => 'Настройки аллиаса',
                'breadcrumbs' => [
                    ['label' => 'Домены', 'url' => '?p=dashboard'],
                    ['label' => $domain, 'url' => '?p=domain:edit&domain=' . urlencode( $domain )],
                    ['label' => $allias, 'url' => '']
                ],
                'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'main']
            ]
        ]);
    }

    public function list(): void {
        $domain = (string) $this->get( 'domain' );
        if ( $domain === '' ) {
            $this->render( 'result', [ 'result' => new Result(), 'title' => 'Домен не указан', 'back_url' => '?p=dashboard', 'back_label' => 'К списку домен' ] );
            return;
        }

        $cfg = new Config( Paths::config() );
        $real = $cfg->getKeys( [ 'domains_real', $domain ] );
        if ( $real === null ) {
            $this->render( 'result', [ 'result' => new Result(), 'title' => "Домен не найден: {$domain}", 'back_url' => '?p=dashboard', 'back_label' => 'К списку домен' ] );
            return;
        }

        $real = Paths::real( $domain );
        $alliases = (array) $cfg->getKeys( [ 'alliases_real', $real ], [] );
        $available = (array) $cfg->getKeys( [ 'alliases_available', $real ], [] );
        $fillers = (array) $cfg->getKeys( [ 'fillers', $real ], [] );
        $rows = [];

        foreach ( $alliases as $alias => $target ) {
            if ( $alias === '' ) continue;
            $fs_path = Paths::allias( $domain, $alias );
            $f = (array) ( $fillers[ $alias ] ?? [] );
            $enabled = 0;
            foreach ( $f as $v ) if ( $v ) $enabled++;
            $rows[] = [
                'name' => $alias,
                'avail' => ! empty( $available[ $alias ] ),
                'fs' => is_dir( $fs_path ),
                'fillers_enabled' => $enabled,
                'fillers_total' => count( $f )
            ];
        }

        $this->render( 'allias_list', [
            'domain' => $domain,
            'rows' => $rows,
            'meta' => [
                'title' => 'Аллиасы',
                'breadcrumbs' => [
                    ['label' => 'Домены', 'url' => '?p=dashboard'],
                    ['label' => $domain, 'url' => '?p=domain:edit&domain=' . urlencode( $domain )],
                    ['label' => 'Аллиасы', 'url' => '']
                ],
                'actions' => '<a href="?p=allias:new&domain=' . urlencode( $domain ) . '" class="btn-primary">+ Добавить аллиас</a>',
                'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'allias']
            ]
        ]);
    }

    public function toggle(): void {
        $this->requirePost();
        $domain = (string) $this->post( 'domain' );
        $name = (string) $this->post( 'name' );
        $value = (string) $this->post( 'value' );

        if ( $domain === '' || $name === '' ) {
            $this->redirect( '?p=dashboard' );
        }

        require_once CS_CMD . '/AlliasToggle.php';
        $result = AlliasToggle::run( [ 'domain' => $domain, 'name' => $name, 'value' => $value ] );
        AuditLog::action_ok( 'allias:toggle', [ 'domain' => $domain, 'name' => $name, 'value' => $value, 'ok' => $result->ok ] );

        $this->redirect( '?p=allias:list&domain=' . urlencode( $domain ) );
    }

    public function remove(): void {
        $domain = (string) $this->get( 'domain' );
        $name = (string) $this->get( 'name' );

        if ( $domain === '' || $name === '' ) {
            $this->redirect( '?p=dashboard' );
        }
        if ( Validate::domain( $domain ) !== null || Validate::allias_name( $name ) !== null ) {
            $this->redirect( '?p=dashboard' );
        }

        $cfg = new Config( Paths::config() );
        $real = Paths::real( $domain );
        $al = $cfg->getKeys( [ 'alliases_real', $real, $name ] );

        if ( $cfg->getKeys( [ 'domains_real', $domain ] ) === null || $al === null ) {
            $this->redirect( '?p=allias:list&domain=' . urlencode( $domain ) );
        }

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $confirm_checked = ! empty( $this->post( 'confirm_checked' ) );
            $confirm_name = trim( (string) $this->post( 'confirm_name' ) );
            $errors = [];

            if ( ! $confirm_checked ) $errors[] = 'Подтвердите, что понимаете необратимость';
            if ( $confirm_name !== $name ) $errors[] = 'Имя аллиаса введено неверно';

            if ( empty( $errors ) ) {
                require_once CS_CMD . '/AlliasRemove.php';
                $result = AlliasRemove::run( [ 'name' => $name, 'domain' => $real, 'force' => true ] );
                AuditLog::action_ok( 'allias:remove', [ 'domain' => $real, 'name' => $name, 'ok' => $result->ok ] );
                $this->render( 'result', [
                    'result' => $result,
                    'title' => "Удаление аллиаса: {$real} / {$name}",
                    'back_url' => '?p=allias:list&domain=' . urlencode( $domain ),
                    'back_label' => 'К аллиасам'
                ]);
                return;
            }
            
            $this->render( 'allias_remove', [
                'domain' => $domain,
                'name' => $name,
                'errors' => $errors,
                'meta' => [
                    'title' => 'Удалить аллиас',
                    'breadcrumbs' => [
                        ['label' => 'Домены', 'url' => '?p=dashboard'],
                        ['label' => 'Удалить аллиас', 'url' => '']
                    ]
                ]
            ]);
            return;
        }

        $this->render( 'allias_remove', [
            'domain' => $domain,
            'name' => $name,
            'errors' => [],
            'meta' => [
                'title' => 'Удалить аллиас',
                'breadcrumbs' => [
                    ['label' => 'Домены', 'url' => '?p=dashboard'],
                    ['label' => 'Удалить аллиас', 'url' => '']
                ]
            ]
        ]);
    }

    public function fillers(): void {
        $domain = (string) $this->get( 'domain' );
        $allias = (string) $this->get( 'allias' );

        if ( $domain === '' ) $this->redirect( '?p=dashboard' );
        if ( $allias === '' ) $this->redirect( '?p=allias:list&domain=' . urlencode( $domain ) );

        require_once ADMIN_ROOT . '/_lib/Scan.php';
        $rows = Scan::fillersFor( $domain, $allias );

        $this->render( 'allias_fillers', [
            'domain' => $domain,
            'allias' => $allias,
            'rows' => $rows,
            'meta' => [
                'title' => 'Филлеры',
                'breadcrumbs' => [
                    ['label' => 'Домены', 'url' => '?p=dashboard'],
                    ['label' => $domain, 'url' => '?p=domain:edit&domain=' . urlencode( $domain )],
                    ['label' => $allias, 'url' => '?p=allias:edit&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias )],
                    ['label' => 'Филлеры', 'url' => '']
                ],
                'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'fillers']
            ]
        ]);
    }
}