<?PHP

function alogic_resolve( $main , $static_domain , $static_allias ) {
    // 1. Подключаем автозагрузчик и маршруты
    require_once dirname( __DIR__ ) . '/alogic/bootstrap.php';
    $router = require_once dirname( __DIR__ ) . '/alogic/routes.php';
    
    $uri = $_SERVER[ 'REQUEST_URI' ] ?? '/';
    $match = $router->match( $uri );
    
    if ( $match === null ) {
        http_response_code( 404 );
        die( '404 Not Found (alogic)' );
    }
    
    $handler = $match[ 'handler' ]; // например, 'algoritm_profile_user'
    $params  = $match[ 'params'  ]; // например, ['login' => 'SameUser']
    
    // 2. Загружаем схему
    $scheme = $main->alliasscheme;
    $scheme->load( 'visuo' );

    // 3. Строим цепочку классов (эмуляция старого include_ext)
    // algoritm_profile_user -> ['algoritm', 'algoritm_profile', 'algoritm_profile_user']
    $parts = explode( '_' , str_replace( 'algoritm_' , '' , $handler ) );
    $chain = [ 'algoritm' ];
    $current = 'algoritm';
    
    foreach ( $parts as $part ) {
        if ( $part === '' ) continue;
        $current .= '_' . $part;
        $chain[] = $current;
    }

    // 4. Запускаем цепочку
    foreach ( $chain as $class_name ) {
        $full_class = $static_domain . '\\' . $static_allias . '\\' . $class_name;
        
        if ( class_exists( $full_class ) ) {
            // Создаем и запускаем каждый уровень
            $instance = new $full_class( $main , $params );
            $instance->algoritm();
            
            // Сохраняем последний (самый глубокий) класс в main->algoritm
            $main->algoritm = $instance;
        }
    }
    
    // 5. Финализация схемы
    $scheme->reload( );
}