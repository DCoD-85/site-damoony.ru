<?PHP

# ============================================================
#   system module — placeholder
#
#   Подключается через sources\include.php.
#   Сейчас пустой: ни методов, ни свойств кроме ссылки на main.
#   Оставлен как точка расширения для системных утилит
#   (диагностика окружения, ФС, процессы, shutdown и т.п.).
#
#   В www\main.php объявлено свойство `public system $system`,
#   но НЕ инстанцируется. Grep по `new system(` — пусто.
#
#   Если через пару спринтов содержимое не появится — удалить
#   из switchboard'а и убрать свойство из main.
# ============================================================

class system {
	# --- refs ---
	public null|\main $main = null;

	# --- methods ---
	public function __construct ( null|\main $main = null ) {
		$this->main = $main;
	}
}