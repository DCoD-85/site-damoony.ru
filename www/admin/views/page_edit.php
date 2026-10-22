<?PHP if ( ! empty( $saved ) ) : ?>
	<div class="ok-msg">✓ Сохранено</div>
<?PHP endif ?>

<?PHP if ( ! empty( $errors ) ) : ?>
	<div class="error-msg">
		<?PHP foreach ( $errors as $e ) : ?>
			<div>✘ <?= htmlspecialchars( $e ) ?></div>
		<?PHP endforeach ?>
	</div>
<?PHP endif ?>

<?PHP if ( ! is_file( $json_file ) ) : ?>
	<div class="error-msg">
		Файл правил не найден: <code><?= htmlspecialchars( $json_file ) ?></code><br>
		Будут применены дефолты (пустой список).
	</div>
<?PHP endif ?>

<form method="post" action="?p=page:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $path ) ?>" class="form">
	<?= Csrf::field( ) ?>

	<label>Правила URL (<code>available</code>) — по одному на строку
		<textarea name="available" rows="8" class="code-editor" spellcheck="false"><?= "\n" . htmlspecialchars( implode( "\n" , $current[ 'available' ] ) , ENT_QUOTES ) ?></textarea>
	</label>

	<label class="checkbox">
		<input type="checkbox" name="auth" value="1" <?= $current[ 'auth' ] ? 'checked' : '' ?>>
		<code>auth</code> — только для авторизованных
	</label>

	<div style="display:flex; gap:10px; align-items:center; margin-top:20px;">
		<button type="submit" class="btn-primary">Сохранить</button>
		<a href="?p=pages:tree&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>" class="btn-cancel">Отмена</a>
	</div>
</form>