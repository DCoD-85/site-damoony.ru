<?PHP

# visuorender — чистое преобразование visuotag → HTML-строка.
#
# Никакого состояния. Никаких ссылок на main. Никаких настроек.
# Только: получить готовое дерево + meta → отдать строку.

class visuorender {

	public function html ( visuotag $vt , visuometa $meta ) : string {
		# встроить meta в <head>
		$head = $vt->root( )->find( 'head' );
		if ( $head !== null ) {
			$head->meta( $meta );
		}

		# подготовить и релизнуть
		$vt->prepare( );
		$vt->release( );

		return $vt->final( );
	}
}