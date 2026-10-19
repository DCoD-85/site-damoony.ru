<?PHP

namespace damoony_ru;

class alliasglobal_bottom extends alliasblock {
	public string $alliasglobal_name = 'bottom';

	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'bottom.data.php';
		if ( is_file( $file ) ) include $file;
	}

	# ===============================================================
	#   СТРУКТУРА: многопанельная навигация
	# ===============================================================

	private function make_bottomnav ( ) : \visuotag {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;
		$url_vars   = $url->static_vars;

		$domains = [
			'damoony' => [
				'info' => [
					'show'   => true ,
					'domain' => 'damoony.ru' ,
					'link'   => $link->href( [ $url_allias[ 0 ] ] ) ,
				] ,
				'projects' => [
					'news' => [
						'name'    => 'Новости' ,
						'link'    => $link->href( [ 'news' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment' => '' ,
					] ,
					'visuo' => [
						'name'    => 'Visuo' ,
						'link'    => $link->href( [ 'visuo' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment' => '' ,
					] ,
					'development' => [
						'name'    => 'Development' ,
						'link'    => $link->href( [ 'development' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment' => '' ,
					] ,
					'humanai' => [
						'name'    => 'Human AI' ,
						'link'    => $link->href( [ 'hai' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment' => '' ,
					] ,
				] ,
			] ,
			'damoonygame' => [
				'info' => [
					'domain' => 'damoony gaming' ,
					'link'   => $link->href( [ $url_allias[ 0 ] ] ) ,
				] ,
				'projects' => [
					'football' => [
						'name'    => 'Football Personal' ,
						'link'    => $link->href( [ 'football' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment' => '' ,
					] ,
					'village' => [
						'name'    => 'Village, the game' ,
						'link'    => $link->href( [ 'village' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment' => '' ,
					] ,
					'totalspace' => [
						'name'    => 'Total Space, outworld' ,
						'link'    => $link->href( [ 'totalspace' , $url_allias[ 0 ] ] , [ ] , [ ] ) ,
						'comment' => '' ,
					] ,
				] ,
			] ,
			'asptour' => [
				'info' => [
					'domain' => 'asptour.ru' ,
					'link'   => $link->href( [ $url_allias[ 0 ] ] ) ,
				] ,
				'projects' => [
					'Прогулки' => [
						'name'    => 'Прогулки' ,
						'link'    => $link->href( [ 'asptour' , $url_allias[ 0 ] ] , [ 'landing' ] , [ ] ) ,
						'comment' => '' ,
					] ,
					'Отзывы' => [
						'name'    => 'Отзывы' ,
						'link'    => $link->href( [ 'asptour' , $url_allias[ 0 ] ] , [ 'resent' ] , [ ] ) ,
						'comment' => '' ,
					] ,
					'Контакты' => [
						'name'    => 'Контакты' ,
						'link'    => $link->href( [ 'asptour' , $url_allias[ 0 ] ] , [ 'contacts' ] , [ ] ) ,
						'comment' => '' ,
					] ,
				] ,
			] ,
		];

		$vt_domain = [ ];
		foreach ( $domains as $domain_key => $domain ) {
			$vt = new \visuotag( );
			$vt = $vt->tag( 'div' , class : 'bottom_nav_project' );

			$vt->tag( 'div' , class : 'title' )
				->content( $domain[ 'info' ][ 'domain' ] );

			$links = $vt->tag( 'div' , class : 'links' );

			foreach ( $domain[ 'projects' ] as $project ) {
				$links->tag( 'div' , class : 'project_link' )
					->tag( 'a' )
						->attrs_add( 'href' , $project[ 'link' ] )
						->attrs_add( 'target' , '_blank' )
						->content( $project[ 'name' ] );
			}

			$vt_domain[ $domain_key ] = $vt->root( );
		}

		$vt_info = new \visuotag( );
		$vt_info = $vt_info->tag( 'div' , class : 'bottom_nav_info' );

		$vt_info->tag( 'div' , class : 'title' )->content( 'Версии сайта:' );

		$ua = $url_allias;
		$info_links = $vt_info->tag( 'div' , class : 'links' );
		$info_links->tag( 'a' )
				->attrs_add( 'href' , $link->href( [ $ua[ 1 ] , $ua[ 0 ] ] , $url_base , [ 'version' => 'desctop' ] ) )
				->content( 'Десктопная версия' );
		$info_links->tag( 'a' )
				->attrs_add( 'href' , $link->href( [ $ua[ 1 ] , $ua[ 0 ] ] , $url_base , [ 'version' => 'mobile' ] ) )
				->content( 'Мобильная версия' );
		$info_links->tag( 'a' )
				->attrs_add( 'href' , $link->href( [ $ua[ 1 ] , $ua[ 0 ] ] , $url_base , [ 'version' => 'app' ] ) )
				->content( 'Приложение' );

		$vt_policy = new \visuotag( );
		$vt_policy = $vt_policy->tag( 'div' , class : 'bottom_nav_policy' );

		$vt_policy->tag( 'div' , class : 'title' )->content( 'Документы:' );

		$policy_links = $vt_policy->tag( 'div' , class : 'links' );
		$policy_links->tag( 'a' )
			->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'documents' , 'confidence' ] ) )
			->content( 'Пользовательское соглашение' );
		$policy_links->tag( 'a' )
			->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'documents' , 'personal' ] ) )
			->content( 'Согласие на обработку персональных данных' );
		$policy_links->tag( 'a' )
			->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'documents' , 'prilozhenie' ] ) )
			->content( 'Приложение №1, к пользовательскому соглашению' );

		$vt_links = new \visuotag( );
		$vt_links = $vt_links->tag( 'div' , class : 'bottom_nav_policy' );

		$vt_links->tag( 'div' , class : 'title' )->content( 'Ссылки:' );

		$other_links = $vt_links->tag( 'div' , class : 'links' );
		$other_links->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'about'   ] ) )->content( 'О нас'         );
		$other_links->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'partner' ] ) )->content( 'Сотрудничество' );
		$other_links->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'forum'   ] ) )->content( 'Форум'          );
		$other_links->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'faq'     ] ) )->content( 'Вопрос-Ответ'   );
		$other_links->tag( 'a' )->attrs_add( 'href' , $link->href( [ 'www' , $url_allias[ 0 ] ] , [ 'tech'    ] ) )->content( 'Тех-Поддержка'  );

		$vt = new \visuotag( );

		$vt->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_domain[ 'damoony'     ] );
		$vt->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_domain[ 'damoonygame' ] );
		$vt->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_domain[ 'asptour'     ] );
		$vt->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_links );
		$vt->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_info );
		$vt->tag( 'div' , class : 'bottom_nav_panel' )->append( $vt_policy );

		return $vt->root( );
	}
}