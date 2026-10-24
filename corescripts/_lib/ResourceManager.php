<?PHP

class ResourceManager {
    private static array $map = [
        'id'       => 'INT AUTO_INCREMENT PRIMARY KEY',
        'string'   => 'VARCHAR(255)',
        'text'     => 'TEXT',
        'boolean'  => 'TINYINT(1) DEFAULT 0',
        'datetime' => 'DATETIME',
        'integer'  => 'INT',
        'relation' => 'INT',
    ];

    public static function load(string $path): array {
        if (!is_file($path)) throw new RuntimeException("Resource not found: $path");
        return require $path;
    }

    private static function getSqlType(string $type): string {
        return self::$map[$type] ?? 'VARCHAR(255)';
    }

    public static function generateCreateTableSQL(array $resource): string {
        $table = $resource['table'];
        $columns = [];

        foreach ($resource['fields'] as $name => $field) {
            $type = $field['type'] ?? 'string';
            $sqlType = self::getSqlType($type);
            $columns[] = "`{$name}` {$sqlType}";
        }

        $cols = implode(",\n  ", $columns);
        return "CREATE TABLE IF NOT EXISTS `{$table}` (\n  {$cols}\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    }

    public static function sync(string $resources_dir): array {
        $results = [];
        if (!is_dir($resources_dir)) return $results;

        foreach (glob($resources_dir . '/*.resource.php') as $file) {
            $res = require $file;
            $table = $res['table'];
            $sql = self::generateCreateTableSQL($res);
            
            try {
                // 1. Создаем таблицу, если её нет
                DataGateway::execute($sql);

                // 2. Проверяем существующие колонки
                $existingCols = DataGateway::query("DESCRIBE `{$table}`");
                $existingNames = array_column($existingCols, 'Field');

                $added = 0;
                // 3. Добавляем недостающие колонки через ALTER TABLE
                foreach ($res['fields'] as $name => $field) {
                    if (!in_array($name, $existingNames)) {
                        $sqlType = self::getSqlType($field['type'] ?? 'string');
                        $alterSql = "ALTER TABLE `{$table}` ADD COLUMN `{$name}` {$sqlType}";
                        DataGateway::execute($alterSql);
                        $added++;
                    }
                }

                $msg = 'Успешно';
                if ($added > 0) $msg .= " (добавлено колонок: {$added})";
                $results[] = ['table' => $table, 'ok' => true, 'msg' => $msg];
                
            } catch (Throwable $e) {
                $results[] = ['table' => $table, 'ok' => false, 'msg' => $e->getMessage()];
            }
        }
        return $results;
    }
}