<?PHP
namespace damoony_ru\visuo;

class algoritm_error extends \algoritm_ {

    public function guest  ( ) { return $this->render(); }
    public function logged ( ) { return $this->render(); }

    private function render ( ) {
        $main = $this->main;
        $visuorelease = $main->visuorelease;
        $center = $main->alliasglobal->center;

        // Очищаем центр перед выводом ошибки
        $center->visuotag()->clear();

        $vt_center = new \visuotag( $visuorelease );
        $vt_center
            ->tag( 'div' , class : 'error' )
                ->tag( 'div' , class : 'error_head' )->content( '404' )->parent()
                ->tag( 'div' , class : 'error_title' )->content( 'Page not found<hr />Страница не найдена' );

        $center->wrapper_set( 'div' );
        $center->section_add( 'error' , $vt_center );

        return true;
    }
}
