<?PHP
// engines/alogic/bootstrap.php

// Автозагрузка для alogic\Router и т.д.
spl_autoload_register(function ($class) {
    $prefix = 'alogic\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) require $file;
});

// Автозагрузка для доменов (damoony_ru\visuo\...)
spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    if (count($parts) < 2) return;
    
    $domain_ns = $parts[0]; 
    $allias = $parts[1];
    $handler = $parts[2] ?? '';
    
    if (!$handler) return;

    // Формируем путь: app/alpha.0.0/domain/damoony.ru/allias/visuo/algoritm/...
    $config = json_decode(file_get_contents(dirname(dirname(__DIR__)) . '/config/site.config'), true);
    $domain_real = array_search($domain_ns, $config['domains_namespace'] ?? []);
    if (!$domain_real) return;

    $logic_ver = 'alpha.0.0'; // Пока хардкод
    $base_dir = dirname(dirname(__DIR__)) . '/app/' . $logic_ver . '/domain/' . $domain_real . '/allias/' . $allias . '/algoritm/';
    
    $class_path = str_replace('algoritm_', '', $handler);
    $class_path = str_replace('_', '/', $class_path);
    
    $file = ($class_path === '') 
        ? $base_dir . 'algoritm.php' 
        : $base_dir . $class_path . '/' . $handler . '.php';
        
    if (file_exists($file)) require $file;
});