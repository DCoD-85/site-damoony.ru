<?PHP

namespace damoony\www;

class dataprovider_feedback extends dataprovider_base {

	public function save ( array $data ) : array {
		$name    = trim( $data[ 'name' ]    ?? '' );
		$email   = trim( $data[ 'email' ]   ?? '' );
		$message = trim( $data[ 'message' ] ?? '' );
		$user_id = (int) ( $data[ 'user_id' ] ?? 0 );

		if ( $name === '' ) {
			return [ 'error' => 'Имя обязательно' ];
		}
		if ( ! filter_var( $email , FILTER_VALIDATE_EMAIL ) ) {
			return [ 'error' => 'Email неверный' ];
		}
		if ( mb_strlen( $message ) < 5 ) {
			return [ 'error' => 'Сообщение слишком короткое' ];
		}

		$q = $this->query( 'www_damoony' );
		$q->query(
			'INSERT INTO feedback ( user_id , name , email , message , created_at , ip )
			 VALUES ( %s , %s , %s , %s , NOW( ) , %s )' ,
			[ $user_id , $name , $email , $message , $_SERVER[ 'REMOTE_ADDR' ] ?? '' ]
		);

		return [ 'id' => $q->insert_id( ) , 'error' => null ];
	}
}