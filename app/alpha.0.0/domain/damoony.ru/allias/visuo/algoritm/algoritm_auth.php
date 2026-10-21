<?PHP
namespace damoony_ru\visuo;

class algoritm_auth extends \algoritm_ {

    // Вызывается движком, если пользователь не авторизован
    public function guest ( ) {
        return $this->render( );
    }

    // Вызывается движком, если пользователь уже авторизован (но попал сюда)
    public function logged ( ) {
        // Отправляем его на главную
        $url = $this->main->library->url;
        $href = ( $url->static_domain[0] ?? '' ) . '/' . ( $url->static_base[0] ?? '' );
        header( 'Location: ' . $href );
        exit;
    }

    private function render ( ) {
        $main = $this->main;
        
        // Создаем блок контента
        $vt = new \visuotag( );
        $vt->tag( 'div' , class : 'auth-message' )
            ->tag( 'h2' )->content( 'Требуется авторизация' )->parent()
            ->tag( 'p' )->content( 'Для доступа к этой странице необходимо войти в систему.' )->parent()
            ;//->tag( 'a' , href : '/' . ( $main->library->url->static_base[0] ?? '' ) . '/login' , class : 'btn btn-primary' )->content( 'Войти' );

        $center = $main->alliasglobal->center;
        
        // Очищаем центр перед вставкой
        $center->visuotag( )->clear( );
        
        // Устанавливаем обертку для стилизации
        $center->wrapper_set( 'div' , [ 'class' => 'auth-wrapper' ] );
        
        // Вставляем наш блок
        $center->section_add( 'auth' , $vt );

        return true;
    }
}