<?php
function render_wireframe ( $nodes ) {
	foreach ( $nodes as $n ) {
		$class = $n['class'] ?? '';
		if ( !$class ) continue;
		$is_dt = str_starts_with( $class , 'dt_' );
		$enabled = $n['enabled'] ?? true;
		
		$name = str_replace('dt_', '', $class);
		$name = $name ?: $class;

		if ( $is_dt || $class === 'dt' ) {
			$color = $enabled ? 'rgba(42, 99, 214, 0.1)' : 'rgba(0,0,0,0.02)';
			$border = $enabled ? '#2a63d6' : '#ddd';
			$text = $enabled ? '#2a63d6' : '#aaa';
			echo "<div style='background:$color; border:2px dashed $border; color:$text; padding:10px; text-align:center; font-weight:bold; border-radius:4px; font-size:12px; margin:4px; flex:1;'>$name</div>";
		}
		
		if ( !empty($n['children']) ) {
			echo "<div style='display:flex; flex-direction:column; flex:1;'>";
			render_wireframe( $n['children'] );
			echo "</div>";
		}
	}
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
	<h1 style="margin:0;">Схема: <code><?= htmlspecialchars( $scheme ) ?></code></h1>
	<a href="?p=blocks:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">← Назад</a>
</div>

<?php if ( !empty($_GET['saved']) ) : ?><div class="ok-msg">✓ Сохранено</div><?php endif ?>

<form method="post" style="display:flex; gap:20px; align-items:flex-start;">
	<?= Csrf::field() ?>
	
	<div style="flex:1; background:#fff; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
		<h3>Блоки (Вкл / Выкл)</h3>
		
		<?php
		$blocks = [];
		foreach ( $data['children'] as $n ) {
			if ( isset($n['class']) && str_starts_with($n['class'], 'dt_') ) $blocks[] = $n;
			if ( !empty($n['children']) ) {
				foreach ( $n['children'] as $c ) {
					if ( isset($c['class']) && str_starts_with($c['class'], 'dt_') ) $blocks[] = $c;
				}
			}
		}
		foreach ( $blocks as $b ) :
			$enabled = $b['enabled'] ?? true;
		?>
			<label class="checkbox" style="margin-bottom:8px;">
				<input type="checkbox" name="blocks[]" value="<?= htmlspecialchars($b['class']) ?>" <?= $enabled ? 'checked' : '' ?>>
				<?= htmlspecialchars($b['class']) ?>
			</label>
		<?php endforeach ?>
		
		<button type="submit" style="margin-top:15px;">Сохранить схему</button>
	</div>

	<div style="flex:2; background:#fff; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
		<h3>Визуализация (Wireframe)</h3>
		<div style="display:flex; gap:10px; min-height:200px;">
			<?php render_wireframe( $data['children'] ); ?>
		</div>
	</div>
</form>
