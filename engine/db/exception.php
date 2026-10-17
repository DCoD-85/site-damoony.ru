<?PHP

class db_exception extends Exception {
    public function __construct ( string $message , string $sql = '' , array $values = [ ] ) {
        $full = $message;
        if ( $sql !== '' ) $full .= "\nSQL: " . $sql;
        if ( ! empty( $values ) ) $full .= "\nVALUES: " . json_encode( $values , JSON_UNESCAPED_UNICODE );
        parent::__construct( $full );
    }
}