<?PHP
namespace damoony_ru\www;

class algoritm_lang_test extends \algoritm_ {

    public function guest  ( ) { return $this->render( ); }
    public function logged ( ) { return $this->render( ); }

    private function render ( ) {
        $main = $this->main;
        $lang = $main->language;
        $vt = new \visuotag( );
        $vt->tag( 'div' , class : 'visuo visuo-test' )
            ->tag( 'h1' )->content( 'Тестовая страница (Data -> Logic)' )->parent( )
            ->tag( 'p'  )->content( 'Текущий URL: ' . implode('/', $main->library->url->static_base) )->parent( )
            ->tag( 'p'  )->content( 'Если вы видите этот текст на /test и на /test/list, значит JSON-маршрутизация работает!' )->parent( );

        $center = $main->alliasglobal->center;
        $center->wrapper_set( 'div' );
        $center->section_add( 'test' , $vt );

        return true;
    }
}
