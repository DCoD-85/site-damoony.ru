<?PHP

class CalculationManager {
    public function process() {
        $operation = $_POST['op'] ?? '';
        $a = $_POST['a'] ?? 0;
        $b = $_POST['b'] ?? 0;
        
        // Жесткое подключение к БД
        $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
        
        switch ($operation) {
            case 'add':
                $result = $a + $b;
                break;
            case 'sub':
                $result = $a - $b;
                break;
            case 'mul':
                $result = $a * $b;
                break;
            case 'div':
                if ($b == 0) {
                    echo "Error: Division by zero";
                    return false;
                }
                $result = $a / $b;
                break;
            default:
                echo "Unknown operation";
                return false;
        }
        
        // Прямой SQL-запрос (уязвим к инъекциям)
        $stmt = $pdo->query("INSERT INTO history (op, a, b, result) VALUES ('$operation', '$a', '$b', '$result')");
        
        echo "Result: " . $result;
        return true;
    }
}