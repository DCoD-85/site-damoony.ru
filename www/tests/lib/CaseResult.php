<?PHP

# ============================================================
#   CaseResult — результат одного кейса.
# ============================================================

class CaseResult {

    public string  $name;
    public bool    $ok        = true;
    public string  $message   = '';
    public ?float  $time_ms   = null;

    public function __construct ( string $name ) {
        $this->name = $name;
    }

    public function pass ( float $time_ms ) : void {
        $this->ok      = true;
        $this->time_ms = $time_ms;
    }

    public function fail ( string $message , float $time_ms ) : void {
        $this->ok      = false;
        $this->message = $message;
        $this->time_ms = $time_ms;
    }
}