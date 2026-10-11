<h1>Домены</h1>

<?php if ( empty( $domains ) ) : ?>
	<p>Доменов нет.</p>
<?php else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Домен</th>
				<th>Namespace</th>
				<th>Активен</th>
				<th>Файлы</th>
				<th>Аллиасы</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $domains as $d ) : ?>
				<tr>
					<td><b><?= htmlspecialchars( $d[ 'domain' ] ) ?></b></td>
					<td><code><?= htmlspecialchars( $d[ 'ns' ] ) ?></code></td>
					<td><?= $d[ 'avail' ] ? '✓' : '✗' ?></td>
					<td><?= $d[ 'fs' ]    ? '✓' : '✗' ?></td>
					<td><?= htmlspecialchars( implode( ', ' , $d[ 'alliases' ] ) ) ?></td>
					<td>
						<a href="?p=domain:edit&domain=<?= urlencode( $d[ 'domain' ] ) ?>"
							class="btn-edit">Редактировать</a>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>