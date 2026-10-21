<?PHP
namespace damoony_ru\www;

class algoritm_lang_test_auth extends \algoritm_ {

    public function guest  ( ) { return $this->render( ); } // Гостей перекинет на страницу логина
    public function logged ( ) { return $this->render( ); }

    private function render ( ) {
        $vt = new \visuotag( );
        $vt->tag( 'h1' )->content( 'Секретная страница (только для авторизованных)' );
        
        $this->main->alliasglobal->center->section_add( 'test' , $vt );
		
        return true;
    }
}
