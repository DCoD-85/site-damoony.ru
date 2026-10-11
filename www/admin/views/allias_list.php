<h1>Домен: <code><?= htmlspecialchars( $domain , ENT_QUOTES ) ?></code> — аллиасы</h1>

<p class="hint">
	<a href="?p=dashboard">← К списку домен</a>
	&nbsp;·&nbsp;
	<a href="?p=domain:edit&domain=<?= urlencode( $domain ) ?>">Редактировать домен</a>
</p>

<?php if ( empty( $rows ) ) : ?>
	<p>Аллиасов нет.</p>
<?php else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Аллиас</th>
				<th>Файлы</th>
				<th>Fillers</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $rows as $r ) : ?>
				<tr>
					<td><b><?= htmlspecialchars( $r[ 'name' ] ) ?></b></td>
					<td><?= $r[ 'fs' ]    ? '✓' : '✗' ?></td>
					<td><?= (int) $r[ 'fillers_enabled' ] ?> / <?= (int) $r[ 'fillers_total' ] ?></td>
					<td>
						<?php if ( $r[ 'avail' ] ) : ?>
							<a href="?p=allias:toggle&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $r[ 'name' ] ) ?>&value=0"
								class="btn-toggle btn-toggle-on"
								title="Выключить">● вкл</a>
						<?php else : ?>
							<a href="?p=allias:toggle&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $r[ 'name' ] ) ?>&value=1"
								class="btn-toggle btn-toggle-off"
								title="Включить">○ выкл</a>
						<?php endif ?>

						<a href="?p=page:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $r[ 'name' ] ) ?>"
							class="btn-edit">+ Страница</a>
						<a href="?p=filler:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $r[ 'name' ] ) ?>"
							class="btn-edit">+ Filler</a>
						<a href="?p=allias:remove&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $r[ 'name' ] ) ?>"
							class="btn-danger">Удалить</a>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>