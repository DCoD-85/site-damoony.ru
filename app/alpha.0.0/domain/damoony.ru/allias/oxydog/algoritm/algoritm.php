<?PHP

namespace damoony_ru\oxydog;

class algoritm extends \algoritm_ {
    public function default() {
        $lang = $this->main->main_settings->get( 'language' ) ?? 'ru-ru';
        $url  = $this->main->library->url( );
        $domain = implode( '.', $url->static_domain ); // Собираем полный домен
        $scheme = $this->main->main_settings->get( 'connect_type' ) ?? 'https://';
        
        header( 'Location: ' . $scheme . $domain . '/' . $lang . '/index', true, 307 );
        exit;
    }
}