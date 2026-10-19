<?PHP
// engine/autoload.php

spl_autoload_register(function ($class) {
    // 1. Автозагрузка для движка alogic (alogic\Router)
    if (str_starts_with($class, 'alogic\\')) {
        $file = dirname(__DIR__) . '/engines/alogic/' . str_replace('\\', '/', substr($class, 7)) . '.php';
        if (file_exists($file)) require $file;
        return;
    }

    // 2. Автозагрузка для доменов (damoony_ru\visuo\algoritm_lang_index)
    $parts = explode('\\', $class);
    if (count($parts) >= 3) {
        $domain_ns = $parts[0];
        $allias = $parts[1];
        
        // Собираем оставшуюся часть пути (lang/index/algoritm_lang_index)
        $handler = array_pop($parts);
        $path_parts = array_slice($parts, 2);
        
        // Преобразуем algoritm_lang_index -> lang/index/algoritm_lang_index.php
        $class_path = str_replace('algoritm_', '', $handler);
        $class_path = str_replace('_', '/', $class_path);
        
        $file_path = ($class_path === '') 
            ? $handler . '.php' 
            : $class_path . '/' . $handler . '.php';

        // Ищем в конфиге реальное имя домена
        $config_file = dirname(__DIR__) . '/config/site.config';
        if (file_exists($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
            $domain_real = array_search($domain_ns, $config['domains_namespace'] ?? []);
            
            if ($domain_real) {
                $logic_ver = 'alpha.0.0';
                $base_dir = dirname(__DIR__) . "/app/{$logic_ver}/domain/{$domain_real}/allias/{$allias}/algoritm/";
                $file = $base_dir . $file_path;
                
                if (file_exists($file)) require $file;
            }
        }
    }
});