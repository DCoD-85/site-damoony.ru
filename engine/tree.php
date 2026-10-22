<?PHP

/**
 * ============================================================
 *   tree.php — единый файловый роутер SimplCMS
 *   Заменяет: engines/chain/ и engines/router/
 * ============================================================
 *
 * Правила (.rules.json) — только 2 ключа:
 *   available : [ ... ]
 *   auth      : true|false
 *
 * available:
 *   ""               → default() текущего файла (для пустого URL)
 *   "index"          → строгий матч сегмента → папка index/
 *   "|list"          → пустой сегмент → папка list/
 *   "[a-z0-9-]+|slug" → regex + имя параметра → папка slug/, args['slug']=сегмент
 *
 * auth: true          → только для авторизованных (гость → 403)
 *
 * default() возвращает:
 *   true  → OK, страница отрендерена
 *   false → 404
 *
 * Ошибки (поиск в порядке):
 *   1. allias/{alias}/errors/{code}.php   (локальная)
 *   2. domain/errors/{code}.php           (глобальная)
 *   3. Встроенный HTML fallback
 * ============================================================
 */
class tree {

    /** Кеш правил в рамках одного запроса (in-memory) */
    private static array $rules_cache = [ ];

    /** main */
    private main $main;

    /** Базовый путь к algoritm/ аллиаса (абсолютный) */
    private string $app_root;

    /** Namespace аллиаса: damoony_ru\www */
    private string $namespace;

    /** Корневой класс: algoritm */
    private string $root_class;

    /** Сегменты URL (без trailing пустых) */
    private array $segments = [ ];

    /** Параметры, накопленные при матчинге |target */
    private array $args = [ ];

    /** Отладочный лог (включается константой TREE_DEBUG) */
    private bool $debug = false;

    public function __construct ( main $main ) {
        $this->main  = $main;
        $this->debug = defined( 'TREE_DEBUG' ) && TREE_DEBUG === true;

        $logic       = $main->main_settings->get( 'logic' );
        $domain_real = $main->main_settings->domain_real_name;
        $allias      = $main->main_settings->allias_real_name;

        $project_root = defined( 'PROJECT_ROOT' ) ? PROJECT_ROOT : dirname( __DIR__ );

        $this->app_root = $project_root . '/app/' . $logic . '/domain/' . $domain_real . '/allias/' . $allias . '/algoritm';

        $domain_ns       = $main->main_settings->domains_namespace[ $domain_real ] ?? str_replace( [ '.', '-' ], '_', $domain_real );
        $this->namespace = $domain_ns . '\\' . $allias;
        $this->root_class = 'algoritm';

        // Сегменты URL (static_base из library->url)
        $raw = $main->library->url->static_base ?? [ ];

        // Убираем trailing пустые
        while ( ! empty( $raw ) && end( $raw ) === '' ) {
            array_pop( $raw );
        }
        // Убираем ведущие пустые (для URL вида /ru-ru/index)
        while ( ! empty( $raw ) && reset( $raw ) === '' ) {
            array_shift( $raw );
        }

        $this->segments = array_values( $raw );

        $this->log( 'INIT segments=[' . implode( '|', $this->segments ) . '] app_root=' . $this->app_root );
    }

    /**
     * Точка входа. Запускает обход дерева.
     */
    public function run () : bool {
        return $this->walk( 0, $this->root_class, '' );
    }

    /**
     * Рекурсивный обход.
     *
     * @param int    $level       Индекс текущего сегмента
     * @param string $class_chain Полная цепочка класса: algoritm_lang_blog
     * @param string $path_rel    Относительный путь к папке: lang/blog
     */
    private function walk ( int $level, string $class_chain, string $path_rel ) : bool {
        $base_dir   = $path_rel === '' ? $this->app_root : $this->app_root . '/' . $path_rel;
        $rules_file = $base_dir . '/' . $class_chain . '.rules.json';

        $this->log( "WALK level=$level class=$class_chain path_rel=$path_rel" );

        if ( ! is_file( $rules_file ) ) {
            $this->log( 'NO RULES: ' . $rules_file );
            return $this->error( 404 );
        }

        $rules = $this->load_rules( $rules_file );
        if ( ! is_array( $rules ) ) {
            return $this->error( 500 );
        }

        // Проверка авторизации
        if ( ! empty( $rules[ 'auth' ] ) && $this->main->is_guest( ) ) {
            $this->log( "AUTH FAIL at $class_chain" );
            return $this->error( 403 );
        }

        $segment = $this->segments[ $level ] ?? '';

        [ $target, $arg_name ] = $this->match( $segment, $rules[ 'available' ] ?? [ ] );

        $this->log( 'MATCH segment=' . var_export( $segment, true ) . ' target=' . var_export( $target, true ) . ' arg=' . var_export( $arg_name, true ) );

        if ( $target === null ) {
            return $this->error( 404 );
        }

        // "" → default() текущего файла
        if ( $target === '' ) {
            $class_file = $base_dir . '/' . $class_chain . '.php';
            return $this->load_and_call( $class_file, $class_chain );
        }

        // |target → идём в папку
        $new_level       = $level + 1;
        $new_class_chain = $class_chain . '_' . $target;
        $new_path_rel    = $path_rel === '' ? $target : $path_rel . '/' . $target;

        $new_class_file  = $this->app_root . '/' . $new_path_rel . '/' . $new_class_chain . '.php';
        $new_rules_file  = $this->app_root . '/' . $new_path_rel . '/' . $new_class_chain . '.rules.json';

        // Сохраняем параметр (slug, name и т.д.)
        if ( $arg_name !== null ) {
            $this->args[ $arg_name ] = $segment;
        }

        // Ни php, ни rules — точно 404
        if ( ! is_file( $new_class_file ) && ! is_file( $new_rules_file ) ) {
            $this->log( "NOT FOUND: neither $new_class_file nor $new_rules_file" );
            return $this->error( 404 );
        }

		// Сегменты кончились → leaf
		if ( $new_level >= count( $this->segments ) ) {
			$leaf_rules = $this->load_rules( $new_rules_file );
			if ( ! is_array( $leaf_rules ) ) {
				return $this->error( 404 );
			}
			if ( ! empty( $leaf_rules[ 'auth' ] ) && $this->main->is_guest( ) ) {
				return $this->error( 403 );
			}
			return $this->load_and_call( $new_class_file, $new_class_chain );
		}

        // Иначе идём глубже (файл может быть только rules.json — роутинг-хаб)
        return $this->walk( $new_level, $new_class_chain, $new_path_rel );
    }

    /**
     * Матчинг сегмента по правилам.
     *
     * @return array{0:?string,1:?string}  [target, arg_name]
     *   target=''    → default текущего
     *   target='xxx' → папка xxx
     *   target=null  → не сматчилось
     */
    private function match ( string $segment, array $available ) : array {
        foreach ( $available as $pattern ) {
            if ( ! is_string( $pattern ) ) continue;

            // "" — default текущего (только для пустого URL)
            if ( $pattern === '' ) {
                if ( $segment === '' ) return [ '', null ];
                continue;
            }

            // "|target" или "regex|target"
            if ( str_contains( $pattern, '|' ) ) {
                [ $regex, $target ] = explode( '|', $pattern, 2 );

                if ( $regex === '' ) {
                    // "|list" — пустой сегмент → в list/
                    if ( $segment === '' ) return [ $target, null ];
                } elseif ( @preg_match( '~^' . $regex . '$~u', $segment ) === 1 ) {
                    // regex → в target/, сегмент в args[target]
                    return [ $target, $target ];
                }
                continue;
            }

            // Прямое совпадение: "index" === "index"
            if ( $pattern === $segment ) {
                return [ $pattern, null ];
            }
        }

        return [ null, null ];
    }

    /**
     * Подключить php-файл и вызвать default().
     */
    private function load_and_call ( string $file, string $class_chain ) : bool {
        if ( ! is_file( $file ) ) {
            $this->log( 'LOAD FAIL: ' . $file );
            return $this->error( 404 );
        }

        $full_class = '\\' . $this->namespace . '\\' . $class_chain;

        if ( ! class_exists( $full_class, false ) ) {
            require_once $file;
        }

        if ( ! class_exists( $full_class, false ) ) {
            $this->log( 'CLASS MISSING: ' . $full_class );
            return $this->error( 500 );
        }

        try {
            $obj = new $full_class( $this->main, $this->args );
        } catch ( \Throwable $e ) {
            $this->log( 'CONSTRUCT ERROR: ' . $e->getMessage( ) );
            return $this->error( 500 );
        }

        try {
            $result = $obj->default( );
        } catch ( \Throwable $e ) {
            $this->log( 'DEFAULT ERROR: ' . $e->getMessage( ) );
            return $this->error( 500 );
        }

        if ( $result === false ) {
            $this->log( "DEFAULT FALSE: $class_chain" );
            return $this->error( 404 );
        }

        $this->log( "OK: $class_chain" );
        return true;
    }

    /**
     * Загрузить .rules.json с кешем (в рамках запроса).
     */
    private function load_rules ( string $file ) : ?array {
        if ( isset( self::$rules_cache[ $file ] ) ) {
            return self::$rules_cache[ $file ];
        }

        if ( ! is_file( $file ) ) return null;

        $raw = @file_get_contents( $file );
        if ( $raw === false ) return null;

        $data = @json_decode( $raw, true );
        if ( ! is_array( $data ) ) return null;

        return self::$rules_cache[ $file ] = $data;
    }

    /**
     * Обработка ошибки.
     *
     * Поиск:
     *   1. allias/{alias}/errors/{code}.php
     *   2. domain/errors/{code}.php
     *   3. Встроенный HTML
     */
	private function error ( int $code ) : bool {
        if ( ! headers_sent( ) ) {
            http_response_code( $code );
        }

        $project_root = defined( 'PROJECT_ROOT' ) ? PROJECT_ROOT : dirname( __DIR__ );
        $logic        = $this->main->main_settings->get( 'logic' );
        $domain_real  = $this->main->main_settings->domain_real_name;
        $allias       = $this->main->main_settings->allias_real_name;

        $local  = $project_root . '/app/' . $logic . '/domain/' . $domain_real . '/allias/' . $allias . '/errors/' . $code . '.php';
        $global = $project_root . '/app/' . $logic . '/domain/' . $domain_real . '/errors/' . $code . '.php';

        if ( is_file( $local ) ) {
            $this->log( "ERROR $code (local): $local" );
            include $local;
            exit;
        }

        if ( is_file( $global ) ) {
            $this->log( "ERROR $code (global): $global" );
            include $global;
            exit;
        }

        $this->log( "ERROR $code (fallback HTML)" );

        $title = [
            403 => 'Доступ запрещён',
            404 => 'Страница не найдена',
            500 => 'Ошибка сервера',
        ][ $code ] ?? 'Ошибка';

        echo '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>' . $code . ' ' . htmlspecialchars( $title ) . '</title>';
        echo '<style>body{font-family:system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f8fafc;color:#1e293b;text-align:center;}';
        echo '.err{padding:40px}.err h1{font-size:72px;margin:0;color:#cbd5e1;line-height:1}.err p{color:#64748b;font-size:18px;margin-top:12px}</style>';
        echo '</head><body><div class="err"><h1>' . $code . '</h1><p>' . htmlspecialchars( $title ) . '</p></div></body></html>';
        exit;
    }

    /**
     * Дебаг-лог (только если TREE_DEBUG).
     */
    private function log ( string $msg ) : void {
        if ( ! $this->debug ) return;
        @file_put_contents(
            ( defined( 'PROJECT_ROOT' ) ? PROJECT_ROOT : dirname( __DIR__ ) ) . '/tree.log',
            date( 'H:i:s' ) . ' | ' . $msg . PHP_EOL,
            FILE_APPEND
        );
    }
}