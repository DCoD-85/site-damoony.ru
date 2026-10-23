<?PHP

class DbSave {
    public static function run( array $params, ?Context $ctx = null ) : Result {
        $r = new Result();
        $path = PROJECT_ROOT . '/secure/db.config';
        
        $bases = [];
        if ( is_file($path) ) {
            $data = json_decode(file_get_contents($path), true);
            $bases = $data['bases'] ?? [];
        }

        // Ищем базу по localname, если нет — создаем новую
        $target = trim($params['localname'] ?? 'default_db');
        $found = false;
        
        foreach ( $bases as &$base ) {
            if ( ($base['localname'] ?? '') === $target ) {
                $base['dbname']   = trim($params['name'] ?? '');
                $base['dbhost']   = trim($params['host'] ?? 'localhost');
                $base['username'] = trim($params['user'] ?? '');
                $base['password'] = (string)($params['pass'] ?? '');
                $found = true;
                break;
            }
        }
        unset($base);

        if ( ! $found ) {
            $bases[] = [
                'localname' => $target,
                'dbname'    => trim($params['name'] ?? ''),
                'dbhost'    => trim($params['host'] ?? 'localhost'),
                'username'  => trim($params['user'] ?? ''),
                'password'  => (string)($params['pass'] ?? ''),
                'default'   => empty($bases), // если это первая база, делаем её дефолтной
            ];
        }

        Fs::mkdir(dirname($path));
        Json::writeAtomic($path, ['bases' => $bases]);
        
        $r->step("Настройки для {$target} сохранены", true);
        return $r;
    }
}