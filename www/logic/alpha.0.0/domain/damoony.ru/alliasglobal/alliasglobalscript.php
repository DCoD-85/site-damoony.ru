<?PHP

namespace damoony;

# alliasglobalscript — база для не-визуальных скриптов alliasglobal.
#
# Скрипт, в отличие от блока:
#   - не имеет visuotag
#   - не отдаётся в слот dt_*
#   - работает с meta, куками, заголовками, БД, другими блоками
#
# Две фазы:
#   init() — конструктор (собрать данные, подготовить состояние)
#   run()  — применение (записать в meta, отправить заголовки, и т.п.)
#
# run() вызывается из main::release() — перед финальным рендером.

abstract class alliasglobalscript {
	# --- refs ---
	public alliasglobal $alliasglobal;
	public \main $main;

	# --- state ---
	public string $script_name = '';
	public bool   $run_done    = false;

	# --- methods ---
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal = $alliasglobal;
		$this->main         = $alliasglobal->main;

		$this->init( );
	}

	protected function init ( ) : void {
	}

	public function run ( ) : void {
		$this->run_done = true;
	}

	public function script_name ( ) : string {
		return $this->script_name;
	}
}