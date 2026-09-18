<?PHP

class gmod_test {
    # vars
    public gmod $gmod;
    # methods
    public function __construct ( gmod $gmod ) {
        $this->gmod = $gmod;
    }
    public function init ( ) {
        return false;
    }
}

?>