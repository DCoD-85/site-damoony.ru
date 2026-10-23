<?PHP

class DomainController extends BaseController {

    public function new(): void {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            require_once CS_CMD . '/DomainNew.php';
            $result = DomainNew::run([
                'name' => $this->post('domain'),
                'register' => ! empty( $this->post('register') )
            ]);
            AuditLog::action_ok('domain:new', [
                'domain' => $this->post('domain'),
                'register' => ! empty( $this->post('register') ),
                'ok' => $result->ok
            ]);
            $this->render('result', [
                'result' => $result,
                'title' => 'Создание домена',
                'back_url' => '?p=dashboard',
                'back_label' => 'К списку домен'
            ]);
            return;
        }
        
        $this->render('domain_new', [
            'meta' => [
                'title' => 'Создать домен',
                'breadcrumbs' => [
                    ['label' => 'Домены', 'url' => '?p=dashboard'],
                    ['label' => 'Создать домен', 'url' => '']
                ]
            ]
        ]);
    }

    public function edit(): void {
        $domain = (string) $this->get('domain');
        if ( Validate::domain($domain) !== null ) { $this->redirect('?p=dashboard'); }
        
        $cfg = new Config( Paths::config() );
        $real = Paths::real($domain);
        if ( $cfg->getKeys(['domains_real', $domain]) === null ) { $this->redirect('?p=dashboard'); }
        
        $stubs_cfg = new Config( PROJECT_ROOT . '/config/site.stubs.config' );
        $stubs = $stubs_cfg->get('stubs') ?? [];

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $title = trim((string)$this->post('title'));
            $errors = [];
            if ($title === '') $errors[] = 'Title не может быть пустым';
            
            $domain_available = !empty($this->post('domain_available'));
            $stub_current = trim((string)$this->post('stub_current', 'maintenance'));
            $stub_until_date = trim((string)$this->post('stub_until_date'));
            $stub_until = $stub_until_date !== '' ? strtotime($stub_until_date) : 0;

            if (empty($errors)) {
                try {
                    Config::mutate( Paths::config(), function ( Config $cfg ) use ( $real, $title, $domain_available, $stub_current, $stub_until ) {
                        $cfg->setKeys(['domains_titles', $real], $title);
                        $cfg->setKeys(['domains_available', $real], $domain_available);
                        $cfg->setKeys(['domains_stub', $real, 'current'], $stub_current);
                        $cfg->setKeys(['domains_stub', $real, 'until'], $stub_until);
                    });
                    AuditLog::action_ok('domain:edit', ['domain' => $real, 'title' => $title]);
                    $this->redirect('?p=domain:edit&domain=' . urlencode($domain) . '&saved=1');
                } catch (Throwable $e) {
                    $errors[] = 'Ошибка сохранения: ' . $e->getMessage();
                }
            }
            
            $this->render('domain_edit', [
                'domain' => $domain,
                'current' => ['title' => $title, 'avail' => $domain_available, 'stub' => $stub_current, 'until' => $stub_until],
                'stubs' => $stubs,
                'saved' => false,
                'errors' => $errors,
                'meta' => [
                    'title' => 'Настройки домена',
                    'breadcrumbs' => [
                        ['label' => 'Домены', 'url' => '?p=dashboard'],
                        ['label' => $domain, 'url' => '']
                    ],
                    'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'main']
                ]
            ]);
            return;
        }

        $title = (string)($cfg->getKeys(['domains_titles', $real]) ?? $cfg->get('title', ''));
        $this->render('domain_edit', [
            'domain' => $domain,
            'current' => [
                'title' => $title,
                'avail' => !empty($cfg->getKeys(['domains_available', $real])),
                'stub' => $cfg->getKeys(['domains_stub', $real, 'current'], 'maintenance'),
                'until' => $cfg->getKeys(['domains_stub', $real, 'until'], 0)
            ],
            'stubs' => $stubs,
            'saved' => !empty($this->get('saved')),
            'errors' => [],
            'meta' => [
                'title' => 'Настройки домена',
                'breadcrumbs' => [
                    ['label' => 'Домены', 'url' => '?p=dashboard'],
                    ['label' => $domain, 'url' => '']
                ],
                'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'main']
            ]
        ]);
    }

    public function toggle(): void {
        $this->requirePost();
        $domain = (string)$this->post('domain');
        $value = (string)$this->post('value');
        if ($domain === '') $this->redirect('?p=dashboard');
        
        require_once CS_CMD . '/DomainToggle.php';
        $result = DomainToggle::run(['domain' => $domain, 'value' => $value]);
        AuditLog::action_ok('domain:toggle', ['domain' => $domain, 'value' => $value, 'ok' => $result->ok]);
        
        $this->redirect('?p=dashboard');
    }

    public function remove(): void {
        $domain = (string)$this->get('domain');
        if ($domain === '' || Validate::domain($domain) !== null) $this->redirect('?p=dashboard');
        
        $cfg = new Config( Paths::config() );
        if ($cfg->getKeys(['domains_real', $domain]) === null) $this->redirect('?p=dashboard');
        
        $real = Paths::real($domain);
        $alliases = [];
        foreach ((array)$cfg->getKeys(['alliases_real', $real], []) as $a => $t) {
            if ($a !== '') $alliases[] = $a;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $confirm_name = trim((string)$this->post('confirm_name'));
            $confirm_checked = !empty($this->post('confirm_checked'));
            $errors = [];
            
            if (!$confirm_checked) $errors[] = 'Подтвердите, что понимаете необратимость';
            if ($confirm_name !== $real) $errors[] = "Имя real-домена введено неверно (нужно: {$real})";
            
            if (empty($errors)) {
                require_once CS_CMD . '/DomainRemove.php';
                $result = DomainRemove::run(['name' => $real, 'force' => true]);
                AuditLog::action_ok('domain:remove', ['domain' => $real, 'ok' => $result->ok]);
                $this->render('result', [
                    'result' => $result,
                    'title' => 'Удаление домена: ' . $real,
                    'back_url' => '?p=dashboard',
                    'back_label' => 'К списку домен'
                ]);
                return;
            }
            
            $this->render('domain_remove', [
                'domain' => $domain,
                'real' => $real,
                'errors' => $errors,
                'alliases' => $alliases,
                'meta' => [
                    'title' => 'Удалить домен',
                    'breadcrumbs' => [
                        ['label' => 'Домены', 'url' => '?p=dashboard'],
                        ['label' => 'Удалить домен', 'url' => '']
                    ]
                ]
            ]);
            return;
        }

        $this->render('domain_remove', [
            'domain' => $domain,
            'real' => $real,
            'errors' => [],
            'alliases' => $alliases,
            'meta' => [
                'title' => 'Удалить домен',
                'breadcrumbs' => [
                    ['label' => 'Домены', 'url' => '?p=dashboard'],
                    ['label' => 'Удалить домен', 'url' => '']
                ]
            ]
        ]);
    }
}