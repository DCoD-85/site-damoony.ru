<h1>Домены</h1>

<?php if ( empty( $domains ) ) : ?>
	<p>Доменов нет.</p>
<?php else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Домен</th>
				<th>Namespace</th>
				<th>Домен</th>
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
					<td>
						<?php if ( $d[ 'avail' ] ) : ?>
							<a href="?p=domain:toggle&domain=<?= urlencode( $d[ 'domain' ] ) ?>&value=0"
								class="btn-toggle btn-toggle-on"
								title="Выключить">● вкл</a>
						<?php else : ?>
							<a href="?p=domain:toggle&domain=<?= urlencode( $d[ 'domain' ] ) ?>&value=1"
								class="btn-toggle btn-toggle-off"
								title="Включить">○ выкл</a>
						<?php endif ?>
							<a href="?p=domain:remove&domain=<?= urlencode( $d[ 'domain' ] ) ?>"
								class="btn-danger">Удалить</a>
					</td>
					<td><?= $d[ 'fs' ]    ? '✓' : '✗' ?></td>
					<td><?= htmlspecialchars( implode( ', ' , $d[ 'alliases' ] ) ) ?></td>
					<td>
						<td>
							<a href="?p=domain:edit&domain=<?= urlencode( $d[ 'domain' ] ) ?>"
								class="btn-edit">Редактировать</a>
							<a href="?p=allias:list&domain=<?= urlencode( $d[ 'domain' ] ) ?>"
								class="btn-edit">Аллиасы</a>
						</td>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>