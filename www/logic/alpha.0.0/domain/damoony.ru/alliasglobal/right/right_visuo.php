<?PHP

namespace damoony;

# ---------------------------------------------------------------
#   alliasglobal_right_visuo — контекстное меню аллиаса visuo.
#   Зависит от текущего раздела (url_base[1]):
#     docs         → дерево документации (разделы + статьи)
#     news         → список последних новостей
#     downloads    → список файлов
#     login        → ссылки входа / регистрации
#     registration → ссылки входа / регистрации
#     profile      → ссылки профиля
#     index / ''   → короткая сводка
#   Для остальных — пусто.
#
#   Наполняет список 'nav' в $right->menu —
#   родитель (alliasglobal_right::release) пришивает его в свой слот.
# ---------------------------------------------------------------

class alliasglobal_right_visuo {

	# --- refs ---
	public \main $main;
	public alliasglobal_right $right;

	# --- methods ---
	public function __construct ( alliasglobal_right $right ) {
		$this->main  = $right->main;
		$this->right = $right;
		$this->data( );
	}

	public function data ( ) : void {
		$main = $this->main;
		$lib  = $main->library;
		$url  = $lib->url( );

		$url_base     = $url->static_base;
		$current_page = (string) ( $url_base[ 1 ] ?? '' );

		switch ( $current_page ) {
			case 'docs'         : $this->menu_docs( );         break;
			case 'news'         : $this->menu_news( );         break;
			case 'downloads'    : $this->menu_downloads( );    break;
			case 'login'        :
			case 'registration' : $this->menu_auth( );         break;
			case 'profile'      : $this->menu_profile( );      break;
			case 'index'        :
			case ''             : $this->menu_index( );        break;
			default             : /* ничего */                 break;
		}
	}

	# ---------------------------------------------------------------
	#   docs — дерево разделов и статей
	# ---------------------------------------------------------------
	private function menu_docs ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base     = $url->static_base;
		$current_slug = (string) ( $url_base[ 2 ] ?? '' );

		$this->right->menu->text(
			'nav' ,
			$lang->get( 'DOCS_TITLE' , [ ] , 'Документация' )
		);

		$dp = $main->dataprovider->current ?? null;
		if ( $dp === null ) return;

		$sections = $dp->doc->sections_tree( true );
		if ( empty( $sections ) ) {
			$this->right->menu->text(
				'nav' ,
				$lang->get( 'DOCS_EMPTY' , [ ] , 'Статей пока нет' )
			);
			return;
		}

		$docs_href = $link->href( false , [ $url_base[ 0 ] , 'docs' ] , use_all_adress_data : false );

		foreach ( $sections as $s ) {
			$this->right->menu->link(
				'nav' ,
				(string) $s[ 'title' ] ,
				$docs_href ,
				false
			);

			$articles = $dp->doc->articles_by_section( (int) $s[ 'id' ] , true );
			foreach ( $articles as $a ) {
				$href = $link->href(
					false ,
					[ $url_base[ 0 ] , 'docs' , (string) $a[ 'slug' ] ] ,
					use_all_adress_data : false
				);
				$this->right->menu->link(
					'nav' ,
					'— ' . (string) $a[ 'title' ] ,
					$href ,
					$current_slug === (string) $a[ 'slug' ]
				);
			}
		}
	}

	# ---------------------------------------------------------------
	#   news — последние новости
	# ---------------------------------------------------------------
	private function menu_news ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base     = $url->static_base;
		$current_slug = (string) ( $url_base[ 2 ] ?? '' );

		$this->right->menu->text(
			'nav' ,
			$lang->get( 'NEWS_TITLE' , [ ] , 'Новости' )
		);

		$dp = $main->dataprovider->current ?? null;
		if ( $dp === null ) return;

		$news = $dp->news->recent( 20 , (string) ( $url_base[ 0 ] ?? 'ru-ru' ) );
		if ( empty( $news ) ) {
			$this->right->menu->text(
				'nav' ,
				$lang->get( 'NEWS_EMPTY' , [ ] , 'Новостей пока нет' )
			);
			return;
		}

		foreach ( $news as $n ) {
			$href = $link->href(
				false ,
				[ $url_base[ 0 ] , 'news' , (string) $n[ 'slug' ] ] ,
				use_all_adress_data : false
			);
			$this->right->menu->link(
				'nav' ,
				(string) $n[ 'title' ] ,
				$href ,
				$current_slug === (string) $n[ 'slug' ]
			);
		}
	}

	# ---------------------------------------------------------------
	#   downloads — список файлов
	# ---------------------------------------------------------------
	private function menu_downloads ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base = $url->static_base;

		$this->right->menu->text(
			'nav' ,
			$lang->get( 'DOWNLOADS_TITLE' , [ ] , 'Загрузки' )
		);

		$dp = $main->dataprovider->current ?? null;
		if ( $dp === null ) return;

		$files = $dp->files->all_active( );
		if ( empty( $files ) ) {
			$this->right->menu->text(
				'nav' ,
				$lang->get( 'DOWNLOADS_EMPTY' , [ ] , 'Файлов пока нет' )
			);
			return;
		}

		foreach ( $files as $f ) {
			$href = $link->href(
				false ,
				[ $url_base[ 0 ] , 'download' , (string) $f[ 'slug' ] ] ,
				use_all_adress_data : false
			);
			$this->right->menu->link(
				'nav' ,
				(string) $f[ 'title' ] ,
				$href ,
				false
			);
		}
	}

	# ---------------------------------------------------------------
	#   login / registration — ссылки авторизации
	# ---------------------------------------------------------------
	private function menu_auth ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base     = $url->static_base;
		$current_page = (string) ( $url_base[ 1 ] ?? '' );

		$this->right->menu->text(
			'nav' ,
			$lang->get( 'MENU_LOGIN' , [ ] , 'Вход' )
		);

		$this->right->menu->link(
			'nav' ,
			$lang->get( 'MENU_LOGIN' , [ ] , 'Вход' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'login' ] , use_all_adress_data : false ) ,
			$current_page === 'login'
		);
		$this->right->menu->link(
			'nav' ,
			$lang->get( 'MENU_REGISTRATION' , [ ] , 'Регистрация' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'registration' ] , use_all_adress_data : false ) ,
			$current_page === 'registration'
		);
	}

	# ---------------------------------------------------------------
	#   profile — ссылки профиля
	# ---------------------------------------------------------------
	private function menu_profile ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base = $url->static_base;

		$this->right->menu->text(
			'nav' ,
			$lang->get( 'PROFILE_TITLE' , [ ] , 'Профиль' )
		);

		$this->right->menu->link(
			'nav' ,
			$lang->get( 'MENU_PROFILE' , [ ] , 'Профиль' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'profile' ] , use_all_adress_data : false ) ,
			true
		);
		$this->right->menu->link(
			'nav' ,
			$lang->get( 'MENU_LOGOUT' , [ ] , 'Выход' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'logout' ] , use_all_adress_data : false ) ,
			false
		);
	}

	# ---------------------------------------------------------------
	#   index — короткая сводка
	# ---------------------------------------------------------------
	private function menu_index ( ) : void {
		$main = $this->main;
		$lang = $main->language;

		$this->right->menu->text(
			'nav' ,
			$lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);
		$this->right->menu->text(
			'nav' ,
			$lang->get( 'VISUO_INTRO' , [ ] , '' )
		);
		$this->right->menu->text(
			'nav' ,
			$lang->get( 'VISUO_TAGLINE' , [ ] , '' )
		);
	}
}