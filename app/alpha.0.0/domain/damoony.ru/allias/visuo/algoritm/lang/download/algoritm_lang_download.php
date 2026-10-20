<?PHP

namespace damoony_ru\visuo;

# Промежуточный уровень: /ru-ru/download/{slug}/
# Передаёт управление в download/page/algoritm_lang_download_page.php.
# Slug остаётся в $url_base[2] (оригинал не портится).

class algoritm_lang_download
	extends \algoritm_ {

	

	public function default ( ) { return false; }
}