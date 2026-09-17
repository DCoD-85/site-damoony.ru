<?PHP

class gmod_user {
    # vars
    public gmod $gmod;
    # methods
    public function __construct ( gmod $gmod ) {
        $this->gmod = $gmod;
    }
    public function init ( ) {
        return true;
    }

    # public function data ( null|gmod_user__data = null ) {
    # 
    # }

    public function test ( ) {
        return '<div>' . __CLASS__ . ' : ' . __METHOD__ . '</div>';
    }
}