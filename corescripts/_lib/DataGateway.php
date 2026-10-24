<?PHP

class DataGateway {
    private static ?PDO $pdo = null;

    private static function connect(?string $dbLocalName = null): PDO {
        if (self::$pdo !== null) return self::$pdo;

        $path = PROJECT_ROOT . '/secure/db.config';
        $data = is_file($path) ? json_decode(file_get_contents($path), true) : [];
        $bases = $data['bases'] ?? [];

        $db = null;
        if ($dbLocalName !== null) {
            foreach ($bases as $b) {
                if (($b['localname'] ?? '') === $dbLocalName) { $db = $b; break; }
            }
        }
        if ($db === null) {
            $db = $bases[0] ?? [];
        }

        if (empty($db['dbname'])) {
            throw new RuntimeException("Database config not found or invalid in secure/db.config");
        }

        $dsn = "mysql:host=" . ($db['dbhost'] ?? 'localhost') . ";dbname=" . $db['dbname'] . ";charset=utf8mb4";
        self::$pdo = new PDO($dsn, $db['username'] ?? '', $db['password'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        return self::$pdo;
    }

    public static function query(string $sql, array $params = [], ?string $dbLocalName = null): array {
        $stmt = self::connect($dbLocalName)->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function execute(string $sql, array $params = [], ?string $dbLocalName = null): bool {
        $stmt = self::connect($dbLocalName)->prepare($sql);
        return $stmt->execute($params);
    }

    public static function insert(string $table, array $data, ?string $dbLocalName = null): int {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $sql = "INSERT INTO `{$table}` ({$fields}) VALUES ({$placeholders})";
        
        $stmt = self::connect($dbLocalName)->prepare($sql);
        $stmt->execute(array_values($data));
        return (int) self::connect($dbLocalName)->lastInsertId();
    }

    // ВОТ ЗДЕСЬ БЫЛА ВЕРОЯТНАЯ ОШИБКА (возвращал массив массивов)
    public static function find(string $table, int $id, ?string $dbLocalName = null): ?array {
        $res = self::query("SELECT * FROM `{$table}` WHERE id = ?", [$id], $dbLocalName);
        return !empty($res) ? $res[0] : null;
    }

    public static function update(string $table, array $data, int $id, ?string $dbLocalName = null): bool {
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "`{$key}` = ?";
        }
        $sql = "UPDATE `{$table}` SET " . implode(', ', $sets) . " WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;
        return self::execute($sql, $params, $dbLocalName);
    }
}