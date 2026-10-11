<h1>Домен: <code><?= htmlspecialchars( $domain ) ?></code></h1>

<?php if ( ! empty( $saved ) ) : ?>
	<div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<?php if ( ! empty( $errors ) ) : ?>
	<div class="error-msg">
		<?php foreach ( $errors as $e ) : ?>
			<div>✘ <?= htmlspecialchars( $e ) ?></div>
		<?php endforeach ?>
	</div>
<?php endif ?>

<form method="post" action="?p=domain:edit&domain=<?= urlencode( $domain ) ?>" class="form">

	<label>Заголовок (title)
		<input type="text" name="title" maxlength="100" required
			value="<?= htmlspecialchars( $current[ 'title' ] , ENT_QUOTES ) ?>">
	</label>

	<label>Тема (skin)
		<select name="skin">
			<?php foreach ( $skins as $s ) : ?>
				<option value="<?= htmlspecialchars( $s , ENT_QUOTES ) ?>"
					<?= $s === $current[ 'skin' ] ? 'selected' : '' ?>>
					<?= htmlspecialchars( $s ) ?>
				</option>
			<?php endforeach ?>
		</select>
	</label>

	<button type="submit">Сохранить</button>
	<a href="?p=dashboard" class="btn-cancel">Отмена</a>
</form>