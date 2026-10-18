<?PHP

function alogic_resolve( $main , $static_domain , $static_allias ) {
    // Подключаем автозагрузчик
    require_once dirname( __DIR__ ) . '/alogic/bootstrap.php';

    // Загружаем маршруты
    $router = require_once dirname( __DIR__ ) . '/alogic/routes.php';
    
    $uri = $_SERVER[ 'REQUEST_URI' ] ?? '/';
    $match = $router->match( $uri );
    
    if ( $match === null ) {
        http_response_code( 404 );
        die( '404 Not Found (alogic)' );
    }
    
    $handler = $match[ 'handler' ];
    $full_handler = $static_domain . '\\' . $static_allias . '\\' . $handler;
    
    // Теперь класс должен найтись автоматически
    if ( ! class_exists( $full_handler ) ) {
        http_response_code( 500 );
        die( 'Class not found: ' . htmlspecialchars( $full_handler ) );
    }
    
    // Загружаем схему
    $scheme = $main->alliasscheme;
    $scheme->load( 'visuo' );

    // Запускаем алгоритм
    $main->algoritm = new $full_handler( $main , $match[ 'params' ] );
    $main->algoritm->algoritm();
    
    // Финализация
    $scheme->reload( );
}