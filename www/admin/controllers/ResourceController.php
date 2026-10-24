<?PHP

class ResourceController extends BaseController {

    public function index(): void {
        $domain = (string) $this->get('domain');
        $allias = (string) $this->get('allias');

        $resources_dir = PROJECT_ROOT . "/app/alpha.0.0/domain/{$domain}/allias/{$allias}/resources";
        $resources = [];
        
        if (is_dir($resources_dir)) {
            foreach (glob($resources_dir . '/*.resource.php') as $file) {
                $res = require $file;
                $res['id'] = basename($file, '.resource.php');
                $resources[] = $res;
            }
        }

        $this->render('resources_list', [
            'domain' => $domain, 'allias' => $allias, 'resources' => $resources,
            'meta' => [
                'title' => 'Управление данными',
                'breadcrumbs' => [['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)]],
                'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'db']
            ]
        ]);
    }

	public function sync(): void {
        $this->requirePost(); // Защита от случайных кликов

        $domain = (string) $this->get('domain');
        $allias = (string) $this->get('allias');
        $resources_dir = PROJECT_ROOT . "/app/alpha.0.0/domain/{$domain}/allias/{$allias}/resources";
        
        $results = ResourceManager::sync($resources_dir);
        
        $synced = count(array_filter($results, fn($r) => $r['ok']));
        $errors = count($results) - $synced;
        
        AuditLog::action_ok('resource:sync', ['synced' => $synced, 'errors' => $errors]);
        
        // Передаем результат в URL, чтобы показать пользователю
        $this->redirect("?p=resources&domain={$domain}&allias={$allias}&synced={$synced}&errors={$errors}");
    }

    public function edit(): void {
        $domain = (string) $this->get('domain');
        $allias = (string) $this->get('allias');
        $resource_id = (string) $this->get('resource');

        $resource_file = PROJECT_ROOT . "/app/alpha.0.0/domain/{$domain}/allias/{$allias}/resources/{$resource_id}.resource.php";
        if (!is_file($resource_file)) { $this->redirect("?p=resources&domain={$domain}&allias={$allias}"); }

        $resource = require $resource_file;
        $table = $resource['table'];
        $rows = DataGateway::query("SELECT * FROM `{$table}` ORDER BY id DESC LIMIT 100");

        // Подтягиваем данные для связей
        $relations = [];
        foreach ($resource['fields'] as $name => $field) {
            if ($field['type'] === 'relation') {
                $relations[$name] = DataGateway::query("SELECT id, `{$field['display']}` AS label FROM `{$field['table']}`");
            }
        }

        $this->render('resource_table', [
            'domain' => $domain, 'allias' => $allias, 'resource' => $resource, 'resource_id' => $resource_id, 'rows' => $rows, 'relations' => $relations,
            'meta' => [
                'title' => $resource['title'],
                'breadcrumbs' => [['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Данные', 'url' => '?p=resources&domain='.urlencode($domain).'&allias='.urlencode($allias)]],
                'actions' => '<a href="?p=resource:form&domain='.urlencode($domain).'&allias='.urlencode($allias).'&resource='.urlencode($resource_id).'" class="btn-primary">+ Добавить запись</a>'
            ]
        ]);
    }

    public function form(): void {
        $domain = (string) $this->get('domain');
        $allias = (string) $this->get('allias');
        $resource_id = (string) $this->get('resource');
        $id = (int) $this->get('id');

        $resource_file = PROJECT_ROOT . "/app/alpha.0.0/domain/{$domain}/allias/{$allias}/resources/{$resource_id}.resource.php";
        if (!is_file($resource_file)) { $this->redirect("?p=resources&domain={$domain}&allias={$allias}"); }

        $resource = require $resource_file;
        $table = $resource['table'];
        $fields = $resource['fields'];
        
        $data = [];
        if ($id > 0) {
            $found = DataGateway::find($table, $id);
            if ($found === null) {
                $this->redirect("?p=resource:edit&domain={$domain}&allias={$allias}&resource={$resource_id}");
            }
            $data = $found;
        }

        // Подтягиваем данные для связей
        $relations = [];
        foreach ($fields as $name => $field) {
            if ($field['type'] === 'relation') {
                $relations[$name] = DataGateway::query("SELECT id, `{$field['display']}` AS label FROM `{$field['table']}`");
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = [];
            foreach ($fields as $name => $field) {
                if ($name === 'id' || $name === 'created_at') continue;
                
                $val = $this->post($name, null);
                
                if ($field['type'] === 'boolean') {
                    $postData[$name] = !empty($val) ? 1 : 0;
                } elseif ($field['type'] === 'integer' || $field['type'] === 'relation') {
                    $postData[$name] = $val !== '' ? (int) $val : null;
                } else {
                    $postData[$name] = (string) $val;
                }
            }

            if ($id > 0) {
                DataGateway::update($table, $postData, $id);
            } else {
                if (isset($fields['created_at'])) {
                    $postData['created_at'] = date('Y-m-d H:i:s');
                }
                DataGateway::insert($table, $postData);
            }
            $this->redirect("?p=resource:edit&domain={$domain}&allias={$allias}&resource={$resource_id}");
        }

        $this->render('resource_form', [
            'domain' => $domain, 'allias' => $allias, 'resource' => $resource, 'resource_id' => $resource_id, 'row' => $data, 'id' => $id, 'relations' => $relations,
            'meta' => [
                'title' => ($id ? 'Редактировать' : 'Создать') . ' — ' . $resource['title'],
                'breadcrumbs' => [['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Данные', 'url' => '?p=resources&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => $resource['title'], 'url' => '?p=resource:edit&domain='.urlencode($domain).'&allias='.urlencode($allias).'&resource='.urlencode($resource_id)]]
            ]
        ]);
    }
}