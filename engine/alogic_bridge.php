<?PHP

function alogic_resolve( $main , $static_domain , $static_allias ) {
    require_once dirname( __DIR__ ) . '/alogic/bootstrap.php';
    $router = require_once dirname( __DIR__ ) . '/alogic/routes.php';
    
    $uri = $_SERVER[ 'REQUEST_URI' ] ?? '/';
    $match = $router->match( $uri );
    
    // 1. Если роутер не нашел маршрут
    if ( $match === null ) {
        alogic_error( $main , $static_domain , $static_allias , 404 );
        return;
    }
    
    $handler = $match[ 'handler' ];
    $params  = $match[ 'params'  ];
    
    $scheme = $main->alliasscheme;
    $scheme->load( 'visuo' );

    // 2. Строим цепочку
    $parts = explode( '_' , str_replace( 'algoritm_' , '' , $handler ) );
    $chain = [ 'algoritm' ];
    $current = 'algoritm';
    
    foreach ( $parts as $part ) {
        if ( $part === '' ) continue;
        $current .= '_' . $part;
        $chain[] = $current;
    }

    // 3. Проходим по цепочке
    foreach ( $chain as $class_name ) {
        $full_class = $static_domain . '\\' . $static_allias . '\\' . $class_name;
        
        // Если файла нет — вызываем ошибку
        if ( ! class_exists( $full_class ) ) {
            alogic_error( $main , $static_domain , $static_allias , 404 );
            return;
        }
        
        $instance = new $full_class( $main , $params );
        
        // Загружаем .data.json
        $reflection = new ReflectionClass($instance);
        $data_file = preg_replace('/\.php$/', '.data.json', $reflection->getFileName());
        
        if (file_exists($data_file)) {
            $data = json_decode(file_get_contents($data_file), true)['default'] ?? [];
            $instance->available = $data['available'] ?? [];
            $instance->auth      = $data['auth'] ?? false;
            $instance->search    = $data['search'] ?? true;
            $instance->default   = $data['default'] ?? false;
        } else {
            // Если .data.json нет (а он должен быть по правилам), можно тоже кинуть ошибку,
            // но пока оставим дефолтные значения.
        }

        // Проверка авторизации
        if ( $instance->auth === true && $main->is_guest() ) {
            $auth_class = $static_domain . '\\' . $static_allias . '\\algoritm_auth';
            if ( class_exists( $auth_class ) ) {
                $auth_instance = new $auth_class( $main , $params );
                $main->algoritm = $auth_instance;
                $auth_instance->release();
                $scheme->reload();
                return;
            }
        }

        $main->algoritm = $instance;
        $instance->release();
    }
    
    // 4. Вызов рендера для конечного узла
    if ( $main->is_guest() ) {
        $main->algoritm->guest();
    } else {
        $main->algoritm->logged();
    }
    
    $scheme->reload( );
}

// Вспомогательная функция для отрисовки страницы ошибки
function alogic_error( $main , $static_domain , $static_allias , $code = 404 ) {
    http_response_code( $code );
    
    $scheme = $main->alliasscheme;
    if ( $scheme ) $scheme->load( 'visuo' );

    $logic_ver = $main->main_settings->get('logic');
    $domain = $main->main_settings->domain_real_name;
    $base_dir = dirname(__DIR__) . "/app/{$logic_ver}/domain/{$domain}/allias/";
    
    // 1. Ищем в текущем аллиасе (например, visuo)
    $current_path = $base_dir . $static_allias . '/algoritm/algoritm_error.php';
    $current_class = $static_domain . '\\' . $static_allias . '\\algoritm_error';
    
    // 2. Фоллбэк на www
    $www_path = $base_dir . 'www/algoritm/algoritm_error.php';
    $www_class = $static_domain . '\\www\\algoritm_error';

    if ( file_exists( $current_path ) ) {
        require_once $current_path;
        $final_class = $current_class;
    } elseif ( file_exists( $www_path ) ) {
        require_once $www_path;
        $final_class = $www_class;
    } else {
        die( '404 Not Found (no error class found)' );
    }
    
    if ( class_exists( $final_class ) ) {
        $instance = new $final_class( $main , [ ] );
        $main->algoritm = $instance;
        $instance->release();
        
        if ( $main->is_guest() ) {
            $instance->guest();
        } else {
            $instance->logged();
        }
    } else {
        die( '404 Not Found (class not found)' );
    }
    
    if ( $scheme ) $scheme->reload();
}