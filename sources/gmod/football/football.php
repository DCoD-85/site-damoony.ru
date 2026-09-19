<?PHP

class gmod_football {
    # vars
    public gmod $gmod;
    public gmod_football__data $data;
    # methods
    public function __construct ( gmod $gmod ) {
        $this->gmod = $gmod;
    }
    public function init ( ) {
        return true;
    }

    public function test ( ) {
        return '<div>' . __CLASS__ . ' : ' . __METHOD__ . '</div>';
    }
}

class gmod_football__data {
    # vars
    public gmod_football $gmod_football;
    # methods
    public function __construct ( gmod_football $gmod_football ) {
        $this->gmod_football = $gmod_football;
    }
}

?>