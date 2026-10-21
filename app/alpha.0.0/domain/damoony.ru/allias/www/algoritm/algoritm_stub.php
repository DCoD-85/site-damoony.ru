<?PHP
namespace damoony_ru\www;

class algoritm_stub extends \algoritm_ {
    public function render_stub( array $stub = [] ) {
        $main = $this->main;
        $vt = new \visuotag( $main->visuorelease );
        
        $vt->tag( 'div' , class : 'stub-container' , style : 'text-align:center; padding: 100px 20px; font-family: sans-serif;' )
           ->tag( 'h1' , style : 'font-size: 32px; margin-bottom: 20px;' )->content( $stub['title'] ?? 'Технические работы' )->parent()
           ->tag( 'p' , style : 'font-size: 18px; color: #666;' )->content( $stub['text'] ?? 'Мы скоро вернемся!' )->parent();

        $main->visuorelease->visuotag_set( $vt );
    }
}
