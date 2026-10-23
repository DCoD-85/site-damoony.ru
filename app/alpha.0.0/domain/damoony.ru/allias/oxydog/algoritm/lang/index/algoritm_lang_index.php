<?PHP

namespace damoony_ru\oxydog;

class algoritm_lang_index extends \algoritm_ {
	public function guest  ( ) { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		$vt = new \visuotag();
		$vt->tag('h1')->content('Oxydog — Главная');
		$this->main->alliasglobal->center->section_add('main', $vt);
		return true;
	}
}
