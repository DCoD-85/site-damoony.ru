<h1>Удалить аллиас?</h1>

<div class="error-msg">
	<strong>Внимание!</strong> Действие необратимо. Будет удалено:
</div>

<ul>
	<li>Папка <code>www/logic/alpha.0.0/domain/<?= htmlspecialchars( $domain ) ?>/allias/<?= htmlspecialchars( $name ) ?>/</code></li>
	<li>Filler-файлы <code><?= htmlspecialchars( $name ) ?></code> во всех блоках <code>alliasglobal/*/</code></li>
	<li>Записи в <code>site.config</code>: alliases_real, alliases_available, fillers для <code><?= htmlspecialchars( $domain ) ?> / <?= htmlspecialchars( $name ) ?></code></li>
</ul>

<?php if ( ! empty( $errors ) ) : ?>
	<div class="error-msg">
		<?php foreach ( $errors as $e ) : ?>
			<div>✘ <?= htmlspecialchars( $e ) ?></div>
		<?php endforeach ?>
	</div>
<?php endif ?>

<form method="post" action="?p=allias:remove&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $name ) ?>" class="form">
	<?= Csrf::field( ) ?>

	<label class="checkbox">
		<input type="checkbox" name="confirm_checked" value="1">
		Я понимаю, что действие необратимо
	</label>

	<label>Введите имя аллиаса для подтверждения:
		<input type="text" name="confirm_name" placeholder="<?= htmlspecialchars( $name , ENT_QUOTES ) ?>" autocomplete="off">
	</label>

	<button type="submit" class="btn-danger-solid">Удалить навсегда</button>
	<a href="?p=allias:list&domain=<?= urlencode( $domain ) ?>" class="btn-cancel">Отмена</a>
</form>