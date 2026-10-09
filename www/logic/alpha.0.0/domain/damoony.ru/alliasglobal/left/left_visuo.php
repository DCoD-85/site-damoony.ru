<?PHP

namespace damoony;

class alliasglobal_left_visuo {

	public \main $main;
	public alliasglobal_left $left;

	public bool $enabled = true;

	public function __construct ( alliasglobal_left $left ) {
		$this->main = $left->main;
		$this->left = $left;
	}

	public function data ( ) : void {
		# wrapper задаётся здесь — default_data при активном filler'е не вызывается
		$this->left->wrapper_set( 'div' , [ 'class' => 'left' ] );

		$main = $this->main;
		$lib  = $main->library;
		$url  = $lib->url( );

		$url_base     = $url->static_base;
		$current_page = (string) ( $url_base[ 1 ] ?? '' );

		$menu = $this->left->menu;
		$menu->separator = '';

		switch ( $current_page ) {
			case 'index'        : $this->fill_index( $menu );        break;
			case 'docs'         : $this->fill_docs( $menu );         break;
			case 'news'         : $this->fill_news( $menu );         break;
			case 'downloads'    : $this->fill_downloads( $menu );    break;
			case 'login'        : $this->fill_login( $menu );        break;
			case 'registration' : $this->fill_registration( $menu ); break;
		}
	}

	# ===============================================================
	#   index — статистика
	# ===============================================================

	private function fill_index ( alliasglobal_menu $menu ) : void {
		$main = $this->main;
		$lang = $main->language;
		$url  = $main->library->url( );
		$url_base = $url->static_base;

		$dp = $main->dataprovider->current ?? null;

		$count_sections = 0;
		$count_articles = 0;
		$count_news     = 0;
		$count_files    = 0;

		if ( $dp !== null ) {
			$sections = $dp->doc->sections_tree( true );
			$count_sections = count( $sections );

			foreach ( $sections as $s ) {
				$count_articles += count( $dp->doc->articles_by_section( (int) $s[ 'id' ] , true ) );
			}

			$count_news  = count( $dp->news->recent( 1000 , (string) ( $url_base[ 0 ] ?? 'ru-ru' ) ) );
			$count_files = count( $dp->files->all_active( ) );
		}

		$menu->text( 'stats' , $lang->get( 'LEFT_STATS_TITLE'    , [ ] , 'Статистика' ) );
		$menu->text( 'stats' , $lang->get( 'LEFT_STATS_SECTIONS' , [ ] , 'Разделов'   ) . ': ' . $count_sections );
		$menu->text( 'stats' , $lang->get( 'LEFT_STATS_ARTICLES' , [ ] , 'Статей'     ) . ': ' . $count_articles );
		$menu->text( 'stats' , $lang->get( 'LEFT_STATS_NEWS'     , [ ] , 'Новостей'   ) . ': ' . $count_news     );
		$menu->text( 'stats' , $lang->get( 'LEFT_STATS_FILES'    , [ ] , 'Файлов'     ) . ': ' . $count_files    );

		$this->left->section_add( 'stats' , $menu->as_list( 'stats' ) );
	}

	# ===============================================================
	#   docs
	# ===============================================================

	private function fill_docs ( alliasglobal_menu $menu ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base     = $url->static_base;
		$current_slug = (string) ( $url_base[ 2 ] ?? '' );

		$menu->text( 'docs' , $lang->get( 'DOCS_TITLE' , [ ] , 'Документация' ) );

		$dp = $main->dataprovider->current ?? null;
		if ( $dp === null ) {
			$this->left->section_add( 'docs' , $menu->as_list( 'docs' ) );
			return;
		}

		$sections = $dp->doc->sections_tree( true );
		if ( empty( $sections ) ) {
			$menu->text( 'docs' , $lang->get( 'DOCS_EMPTY' , [ ] , 'Статей пока нет' ) );
			$this->left->section_add( 'docs' , $menu->as_list( 'docs' ) );
			return;
		}

		$docs_href = $link->href( false , [ $url_base[ 0 ] , 'docs' ] , use_all_adress_data : false );

		foreach ( $sections as $s ) {
			$menu->link( 'docs' , (string) $s[ 'title' ] , $docs_href );

			$articles = $dp->doc->articles_by_section( (int) $s[ 'id' ] , true );
			foreach ( $articles as $a ) {
				$href = $link->href(
					false ,
					[ $url_base[ 0 ] , 'docs' , (string) $a[ 'slug' ] ] ,
					use_all_adress_data : false
				);
				$menu->link( 'docs' , (string) $a[ 'title' ] , $href , $current_slug === (string) $a[ 'slug' ] );
			}
		}

		$this->left->section_add( 'docs' , $menu->as_list( 'docs' ) );
	}

	# ===============================================================
	#   news — две секции: список + действия
	# ===============================================================

	private function fill_news ( alliasglobal_menu $menu ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base     = $url->static_base;
		$current_slug = (string) ( $url_base[ 2 ] ?? '' );

		# --- newslist ---
		$menu->head( 'newslist' , $lang->get( 'NEWS_TITLE' , [ ] , 'Новости' ) );

		$dp = $main->dataprovider->current ?? null;
		if ( $dp !== null ) {
			$news = $dp->news->recent( 20 , (string) ( $url_base[ 0 ] ?? 'ru-ru' ) );
			if ( empty( $news ) ) {
				$menu->text( 'newslist' , $lang->get( 'NEWS_EMPTY' , [ ] , 'Новостей пока нет' ) );
			} else {
				foreach ( $news as $n ) {
					$href = $link->href(
						false ,
						[ $url_base[ 0 ] , 'news' , (string) $n[ 'slug' ] ] ,
						use_all_adress_data : false
					);
					$menu->link( 'newslist' , (string) $n[ 'title' ] , $href , $current_slug === (string) $n[ 'slug' ] );
				}
			}
		}
		$this->left->section_add( 'newslist' , $menu->as_list( 'newslist' ) );

		# --- newsactions ---
		$menu->head( 'newsactions' , $lang->get( 'NEWS_ACTION' , [ ] , 'Действия' ) );
		$menu->link( 'newsactions' , $lang->get( 'NEWS_ACT_LIST'   , [ ] , 'Список'        ) , $link->href( false , [ $url_base[ 0 ] , 'news'           ] , use_all_adress_data : false ) );
		$menu->link( 'newsactions' , $lang->get( 'NEWS_ACT_ADD'    , [ ] , 'Добавить'      ) , $link->href( false , [ $url_base[ 0 ] , 'news' , 'add'    ] , use_all_adress_data : false ) );
		$menu->link( 'newsactions' , $lang->get( 'NEWS_ACT_EDIT'   , [ ] , 'Редактировать' ) , $link->href( false , [ $url_base[ 0 ] , 'news' , 'edit'   ] , use_all_adress_data : false ) );
		$menu->link( 'newsactions' , $lang->get( 'NEWS_ACT_REMOVE' , [ ] , 'Удалить'       ) , $link->href( false , [ $url_base[ 0 ] , 'news' , 'remove' ] , use_all_adress_data : false ) );
		$this->left->section_add( 'newsactions' , $menu->as_list( 'newsactions' ) );
	}

	# ===============================================================
	#   downloads
	# ===============================================================

	private function fill_downloads ( alliasglobal_menu $menu ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base = $url->static_base;

		$menu->text( 'downloads' , $lang->get( 'DOWNLOADS_TITLE' , [ ] , 'Загрузки' ) );

		$dp = $main->dataprovider->current ?? null;
		if ( $dp === null ) {
			$this->left->section_add( 'downloads' , $menu->as_list( 'downloads' ) );
			return;
		}

		$files = $dp->files->all_active( );
		if ( empty( $files ) ) {
			$menu->text( 'downloads' , $lang->get( 'DOWNLOADS_EMPTY' , [ ] , 'Файлов пока нет' ) );
			$this->left->section_add( 'downloads' , $menu->as_list( 'downloads' ) );
			return;
		}

		foreach ( $files as $f ) {
			$href = $link->href(
				false ,
				[ $url_base[ 0 ] , 'download' , (string) $f[ 'slug' ] ] ,
				use_all_adress_data : false
			);
			$menu->link( 'downloads' , (string) $f[ 'title' ] , $href );
		}

		$this->left->section_add( 'downloads' , $menu->as_list( 'downloads' ) );
	}

	# ===============================================================
	#   login
	# ===============================================================

	private function fill_login ( alliasglobal_menu $menu ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base = $url->static_base;

		$menu->text( 'login' , $lang->get( 'PAGE_LOGIN' , [ ] , 'Авторизация' ) );
		$menu->link( 'login' ,
			$lang->get( 'LEFT_LOGIN_FORM' , [ ] , 'Форма авторизации' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'login' ] , use_all_adress_data : false ) ,
			true
		);
		$menu->link( 'login' ,
			$lang->get( 'LEFT_LOGIN_RESTORE' , [ ] , 'Восстановить пароль' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'login' , 'restore' ] , use_all_adress_data : false )
		);

		$this->left->section_add( 'login' , $menu->as_list( 'login' ) );
	}

	# ===============================================================
	#   registration
	# ===============================================================

	private function fill_registration ( alliasglobal_menu $menu ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base = $url->static_base;

		$menu->text( 'registration' , $lang->get( 'PAGE_REGISTRATION' , [ ] , 'Регистрация' ) );
		$menu->link( 'registration' ,
			$lang->get( 'LEFT_REG_FORM' , [ ] , 'Форма регистрации' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'registration' ] , use_all_adress_data : false ) ,
			true
		);
		$menu->link( 'registration' ,
			$lang->get( 'LEFT_REG_ACTIVATE' , [ ] , 'Активация учётной записи' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'registration' , 'activate' ] , use_all_adress_data : false )
		);

		$this->left->section_add( 'registration' , $menu->as_list( 'registration' ) );
	}
}