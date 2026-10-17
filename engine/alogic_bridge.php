<?PHP

function alogic_resolve( $main , $static_domain , $static_allias ) {
    $router = require_once dirname( __DIR__ ) . '/alogic/routes.php';
    $uri = $_SERVER[ 'REQUEST_URI' ] ?? '/';
    $match = $router->match( $uri );
    
    if ( $match === null ) {
        http_response_code( 404 );
        die( '404 Not Found (alogic)' );
    }
    
    $handler = $match[ 'handler' ];
    
    // 1. Формируем путь к файлу (эмуляция include_ext)
    $root = dirname( __DIR__ );
    $logic_ver = $main->main_settings->get( 'logic' );
    $domain_real = $main->main_settings->domain_real_name;
    
    $class_path = str_replace( 'algoritm_', '', $handler );
    $class_path = str_replace( '_', '/', $class_path );
    
    $base_dir = $root . '/logic/' . $logic_ver . '/domain/' . $domain_real . '/allias/' . $static_allias . '/algoritm/';
    
    if ( $class_path === '' ) {
        $file_path = $base_dir . 'algoritm.php';
    } else {
        $file_path = $base_dir . $class_path . '/' . $handler . '.php';
    }
    
    // Подключаем файл вручную, раз автозагрузка не справляется
    if ( file_exists( $file_path ) ) {
        require_once $file_path;
    }
    
    $full_handler = $static_domain . '\\' . $static_allias . '\\' . $handler;
    
    if ( ! class_exists( $full_handler ) ) {
        http_response_code( 500 );
        die( 'Class not found: ' . htmlspecialchars( $full_handler ) . ' (file: ' . htmlspecialchars( $file_path ) . ')' );
    }
    
    // 2. Загружаем схему (каркас страницы)
    $scheme = $main->alliasscheme;
    $scheme->load( 'visuo' );

    // 3. Создаем и запускаем алгоритм
    $main->algoritm = new $full_handler( $main , $match[ 'params' ] );
    $main->algoritm->algoritm(); // <--- вызываем algoritm(), а не start()
    
    // 4. Перезагружаем схему (финализация)
    $scheme->reload( );
}