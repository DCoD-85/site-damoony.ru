<h1>Удалить домен?</h1>

<div class="error-msg">
	<strong>Внимание!</strong> Действие необратимо. Будет удалено:
</div>

<ul>
	<li>Папка <code>www/logic/alpha.0.0/domain/<?= htmlspecialchars( $domain ) ?>/</code></li>
	<?php if ( ! empty( $alliases ) ) : ?>
		<li>Все аллиасы домена: <b><?= htmlspecialchars( implode( ', ' , $alliases ) ) ?></b></li>
	<?php endif ?>
	<li>Все filler-файлы во всех блоках</li>
	<li>Все записи в <code>site.config</code>: domains_real, domains_available,
		domains_namespace, domains_titles, domains_skins, alliases_real,
		alliases_available, fillers</li>
</ul>

<?php if ( ! empty( $errors ) ) : ?>
	<div class="error-msg">
		<?php foreach ( $errors as $e ) : ?>
			<div>✘ <?= htmlspecialchars( $e ) ?></div>
		<?php endforeach ?>
	</div>
<?php endif ?>

<form method="post" action="?p=domain:remove&domain=<?= urlencode( $domain ) ?>" class="form">
	<?= Csrf::field( ) ?>

	<label class="checkbox">
		<input type="checkbox" name="confirm_checked" value="1">
		Я понимаю, что действие необратимо
	</label>

	<label>Введите имя домена для подтверждения:
		<input type="text" name="confirm_name" placeholder="<?= htmlspecialchars( $domain , ENT_QUOTES ) ?>" autocomplete="off">
	</label>

	<button type="submit" class="btn-danger-solid">Удалить навсегда</button>
	<a href="?p=dashboard" class="btn-cancel">Отмена</a>
</form>