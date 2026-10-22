<?PHP

namespace damoony_ru\www;

class algoritm
	extends \algoritm_ {

	public string $basepath  = __DIR__ . DS;
	public string $baseclass = 'algoritm';

	public function __construct ( \main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritmdata = new \algoritmdata_( );
	}

	// URL / → редирект на /{lang}/
	public function default ( ) {
		$lang = $this->main->main_settings->get( 'language' ) ?? 'ru-ru';
		$url  = $this->main->library->url( );
		$domain = $url->static_domain[ 0 ] ?? '';

		header( 'Location: https://' . $domain . '/' . $lang . '/', true, 307 );
		exit;
	}
}
