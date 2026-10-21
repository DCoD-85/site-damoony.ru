<?php $active_tab = 'schemes'; require __DIR__ . '/_domain_tabs.php'; ?>

<table class="table">
	<thead>
		<tr>
			<th>Схема</th>
			<th>Файл</th>
			<th>Data (редактор)</th>
			<th>Действия</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $schemes as $s ) : 
			$data_file = Paths::domain( $domain ) . '/alliasscheme/alliasscheme_' . $s . '.data.php';
			$has_data = is_file( $data_file );
		?>
			<tr>
				<td><b><?= htmlspecialchars( $s ) ?></b></td>
				<td>✓</td>
				<td><?= $has_data ? '✓' : '✗ (нет)' ?></td>
				<td>
					<?php if ( $has_data ) : ?>
						<div class="td-actions">
                        <a href="?p=scheme:edit&domain=<?= urlencode( $domain ) ?>&scheme=<?= urlencode( $s ) ?>" class="btn-edit">Ред.</a>
                        <a href="?p=scheme:copy&domain=<?= urlencode( $domain ) ?>&scheme=<?= urlencode( $s ) ?>" class="btn-edit">Копировать</a>
                        <?php if ( !in_array($s, ['www', 'visuo', 'error']) ) : ?>
                            <a href="?p=scheme:remove&domain=<?= urlencode( $domain ) ?>&scheme=<?= urlencode( $s ) ?>" class="btn-danger">Удалить</a>
                        <?php endif ?>
                    </div>
					<?php else : ?>
						<span class="btn-cancel" style="opacity:0.5; cursor:not-allowed;">Нет data.php</span>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
