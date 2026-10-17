<?PHP

interface db_driver_interface {
    public function open ( string $localname = '' , bool $reconnect = false ) : void;
    public function close ( ) : void;
    public function link ( ) : mixed;
    public function status ( ) : string;
    public function error ( ) : string;
    public function affected ( ) : int;
    public function last_id ( ) : int;
    public function escape ( string $string ) : string;
}