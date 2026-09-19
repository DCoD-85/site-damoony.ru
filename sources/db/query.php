<?PHP

interface db_query_interface {
    public function query ( string $sql , array $values = [ ] , array $types = [ ] ) : self;
    
    public function one ( ) : self;
    public function page ( int $page , ?int $size = null ) : self;
    public function limit ( int $n ) : self;
    public function no_limit ( ) : self;
    
    public function result ( bool $assoc = true ) : array;
    public function row ( bool $assoc = true ) : ?array;
    public function scalar ( ) : mixed;
    public function count_rows ( ) : int;
    public function affected ( ) : int;
    public function insert_id ( ) : int;
    
    public function show ( ) : string;
}