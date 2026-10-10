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
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>