<?PHP

namespace damoony_ru\www;

# ============================================================
#   algoritm_lang_{name} — алгоритм страницы
#
#   Путь:  .../{domain}/allias/{allias}/algoritm/lang/{name}/
#   Файл:  algoritm_lang_{name}.php
#   URL:   /{lang}/{name}/  (например /ru-ru/{name}/)
#
#   Регистрация: добавить '{name}' в $available родителя
#                www/.../algoritm/lang/algoritm_lang.php
# ============================================================

class algoritm_lang_{name}
	extends \algoritm_ {

	# ---------------------------------------------------------------
	#   SETTINGS — какие URL обрабатывает алгоритм
	# ---------------------------------------------------------------
	#   $available — список разрешённых сегментов URL:
	#       ''                  → /{lang}/         (корень аллиаса)
	#       'edit'              → /{lang}/edit/
	#       '([0-9]+)|id'       → /{lang}/123/     с regex-группой в $args
	#       '([a-z]+)|slug'     → /{lang}/hello/   с regex-группой в $args
	#
	#   $auth    — true = только авторизованным, false = всем
	#   $search  — true = разбирать URL через regex, false = только литералы
	#   $default — true = вызвать default(), если URL совпал с позицией класса
	#              (используем $this->is_default() в release())
	# ---------------------------------------------------------------
	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	# ---------------------------------------------------------------
	#   RELEASE — подготовка данных ДО рендера
	#   Здесь: чтение из БД, установка заголовков, изменение $default,
	#          разбор $args (если URL пришёл с regex-группой)
	# ---------------------------------------------------------------
	public function release ( ) {
		$default = $this->is_default( );

		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $default );

		$this->release_( $this );
	}

	# ---------------------------------------------------------------
	#   DEFAULT — главный роутер
	#   Выбирайте ветку: гость / авторизованный / ajax / роль.
	# ---------------------------------------------------------------
	public function default ( ) {
		return $this->main->user->is_guest( )
			? $this->default_guest( )
			: $this->default_logged( );
	}

	# ---------------------------------------------------------------
	#   GUEST — контент для неавторизованных
	# ---------------------------------------------------------------
	public function default_guest ( ) {
		$main = $this->main;

		# --- доступные хелперы ---
		$library    = $main->library;
		$link       = $library->link( );
		$url        = $library->url( );
		$url_domain = $url->static_domain;
		$url_base   = $url->static_base;
		$url_vars   = $url->static_vars;

		$center = $main->alliasglobal->center;

		# --- контент ---
		$vt = new \visuotag( );

		# ... наполнение ...

		$center->visuotag( )->append( $vt );

		return true;
	}

	# ---------------------------------------------------------------
	#   LOGGED — контент для авторизованных
	# ---------------------------------------------------------------
	public function default_logged ( ) {
		$main = $this->main;

		$library    = $main->library;
		$link       = $library->link( );
		$url        = $library->url( );
		$url_domain = $url->static_domain;
		$url_base   = $url->static_base;
		$url_vars   = $url->static_vars;

		$center = $main->alliasglobal->center;

		# --- контент ---
		$vt = new \visuotag( );

		# ... наполнение ...

		$center->visuotag( )->append( $vt );

		return true;
	}
}

?>