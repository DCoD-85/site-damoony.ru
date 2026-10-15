<?PHP

# ============================================================
#   Suite — набор кейсов с общим setup.
# ============================================================

class Suite {

    public string   $name;
    public ?Closure $setup = null;
    public array    $tests = [ ];   # [ 'name' => Closure , ... ]

    public function __construct ( string $name , ?Closure $setup , array $tests ) {
        $this->name  = $name;
        $this->setup = $setup;
        $this->tests = $tests;
    }

    public function count ( ) : int {
        return count( $this->tests );
    }
}