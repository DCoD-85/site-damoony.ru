<?PHP

# ==============================================================
#   ШАБЛОН БЛОКА alliasglobal
# ==============================================================
#
#   Как использовать:
#
#   1. Скопируй этот файл в папку блока:
#        alliasglobal/<name>/<name>.php
#      Пример: alliasglobal/sidebar/sidebar.php
#
#   2. Переименуй класс:
#        alliasglobal__template  →  alliasglobal_<name>
#      Пример: alliasglobal_sidebar
#
#   3. Поменяй $alliasglobal_name на имя блока:
#        public string $alliasglobal_name = 'sidebar';
#
#   4. Зарегистрируй блок в alliasglobal.php:
#      - в autoload():  include_once 'alliasglobal/sidebar/sidebar.php'
#      - в init():      $this->sidebar = new alliasglobal_sidebar( $this );
#      - объяви свойство: public alliasglobal_sidebar $sidebar;
#
#   5. Слот для блока — dt_<name> в схеме (alliasscheme_www).
#
# ==============================================================

namespace idoam_ru;

class alliasglobal__template extends alliasblock {
	# --- state ---
	# Имя блока. Используется для:
	#   - поиска файла наполнителя  (<name>_<allias>.php)
	#   - префикса CSS-классов меню  (если menu_name задействован)
	public string $alliasglobal_name = 'template';
	public bool   $settings_asmenu   = false;

	# --- custom ---
	# Публичные поля блока. Программист может менять их из алгоритма:
	#   $main->alliasglobal->template->title = '...';
	#   $main->alliasglobal->template->reload();
	public string $title = '';
	public string $text  = '';

	# --- methods ---

	# scheme() — построить HTML-каркас блока.
	# Здесь создаются слоты, в которые потом вставляется содержимое.
	protected function scheme ( ) : void {
		$this->visuotag
			->tag( 'div' , class : 'template' , return : false )
			->tag( 'div' , class : 'template_title' , return : false )
			->tag( 'div' , class : 'template_text'  , return : false )
			->tag( 'div' , class : 'template_menu'  );
	}

	# data() — собрать данные и наполнить меню.
	# Здесь же можно подключить наполнитель для конкретного аллиаса:
	#   <name>_<allias>.php  →  class alliasglobal_<name>_<allias>
	protected function data ( ) : void {
		$main   = $this->main;
		$domain = $main->main_settings->domain_accepted( false );
		$accept = $main->main_settings->allias_accepted( );

		if ( is_null( $accept ) ) return;

		$path  = __DIR__ . DS . $this->alliasglobal_name . '_' . $accept . '.php';
		$class = '\\' . $domain . '\\alliasglobal_' . $this->alliasglobal_name . '_' . $accept;

		if ( include_once $path ) {
			if ( class_exists( $class ) ) {
				new $class( $this );
			}
		}

		# Пример наполнения меню:
		# $this->menu->link( 'nav' , 'Пункт 1' , '/url/1' );
		# $this->menu->link( 'nav' , 'Пункт 2' , '/url/2' );
	}

	# release() — вставить данные и меню в слоты каркаса.
	protected function release ( ) : void {
		$root = $this->visuotag->root( );

		# Заполнение полей
		$slot = $root->find( class : 'template_title' );
		if ( $slot !== null ) $slot->content( $this->title );

		$slot = $root->find( class : 'template_text' );
		if ( $slot !== null ) $slot->content( $this->text );

		# Вставка меню
		$slot = $root->find( class : 'template_menu' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'nav' ) );
	}
}