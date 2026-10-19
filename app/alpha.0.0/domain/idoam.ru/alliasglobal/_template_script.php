<?PHP

# ==============================================================
#   ШАБЛОН СКРИПТА alliasglobal
# ==============================================================
#
#   Скрипт — не-визуальный обработчик. Не имеет visuotag,
#   не отдаётся в слот dt_*. Работает с meta, куками, заголовками,
#   БД, другими блоками.
#
#   Как использовать:
#
#   1. Скопируй этот файл в папку скриптов:
#        alliasglobal/script/<name>.php
#      Пример: alliasglobal/script/seo.php
#
#   2. Переименуй класс:
#        alliasglobal__template  →  alliasglobal_script_<name>
#      Пример: alliasglobal_script_seo
#
#   3. Поменяй $script_name на имя скрипта:
#        public string $script_name = 'seo';
#
#   4. Зарегистрируй скрипт в alliasglobal.php:
#      - в autoload():  include_once 'alliasglobal/script/seo.php'
#      - в init():      $this->seo = new alliasglobal_script_seo( $this );
#                       $this->scripts['seo'] = $this->seo;
#      - объяви свойство: public alliasglobal_script_seo $seo;
#
#   5. run() вызывается автоматически из main::release()
#      перед финальным рендером.
#
# ==============================================================

namespace idoam_ru;

class alliasglobal__template extends alliasglobalscript {
	# --- state ---
	public string $script_name = 'template';

	# --- custom ---
	# Публичные поля скрипта. Программист настраивает их из алгоритма:
	#   $main->alliasglobal->seo->description = '...';
	public string $description = '';
	public array  $meta_tags   = [ ];

	# --- methods ---

	# init() — конструктор. Собрать данные, подготовить состояние.
	# Вызывается один раз при создании alliasglobal.
	protected function init ( ) : void {
		# Пример: взять текущий URL
		# $this->description = '...';
	}

	# run() — применение. Вызывается из main::release()
	# до финального рендера. Здесь скрипт воздействует на систему:
	# пишет в meta, ставит куки, отправляет заголовки, дёргает другие блоки.
	public function run ( ) : void {
		if ( $this->run_done ) return;
		$this->run_done = true;

		$meta = $this->main->visuorelease->meta( );
		if ( $meta === null ) return;

		# Пример: добавить meta-теги
		# $meta->title( )->data( $this->description );

		# Пример: поставить куку
		# setcookie( 'seo_visited' , '1' , 0 , '/' );

		# Пример: дёрнуть другой блок
		# $this->alliasglobal->top->reload( );
	}
}