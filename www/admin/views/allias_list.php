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
						<form method="post" action="?p=allias:toggle" class="inline">
							<?= Csrf::field( ) ?>
							<input type="hidden" name="domain" value="<?= htmlspecialchars( $domain , ENT_QUOTES ) ?>">
							<input type="hidden" name="name"   value="<?= htmlspecialchars( $r[ 'name' ] , ENT_QUOTES ) ?>">
							<input type="hidden" name="value"  value="<?= $r[ 'avail' ] ? '0' : '1' ?>">
							<button type="submit"
								class="btn-toggle <?= $r[ 'avail' ] ? 'btn-toggle-on' : 'btn-toggle-off' ?>"
								title="<?= $r[ 'avail' ] ? 'Выключить' : 'Включить' ?>">
								<?= $r[ 'avail' ] ? '● вкл' : '○ выкл' ?>
							</button>
						</form>

						<a href="?p=pages:tree&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $r[ 'name' ] ) ?>"
							class="btn-edit">Страницы</a>
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