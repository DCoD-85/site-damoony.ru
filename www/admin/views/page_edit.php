<h1>Страница: <code><?= htmlspecialchars( $path === '' ? '(корень lang)' : $path ) ?></code></h1>

<p class="hint">
	Домен: <code><?= htmlspecialchars( $domain ) ?></code>
	&nbsp;·&nbsp; аллиас: <code><?= htmlspecialchars( $allias ) ?></code>
	&nbsp;·&nbsp; класс: <code><?= htmlspecialchars( $cls_short ) ?></code>
	<br>
	<a href="?p=pages:tree&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>">← К дереву</a>
	<?php if ( $path !== '' ) : ?>
		&nbsp;·&nbsp;
		<a href="?p=page:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&parent=<?= urlencode( $path ) ?>">+ Подстраница</a>
	<?php endif ?>
</p>

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

<?php if ( ! is_file( $json_file ) ) : ?>
	<div class="error-msg">
		Файл переменных не найден: <code><?= htmlspecialchars( $json_file ) ?></code><br>
		Будут применены дефолты из класса.
	</div>
<?php endif ?>

<form method="post" action="?p=page:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $path ) ?>" class="form">
	<?= Csrf::field( ) ?>

	<label>Правила URL (<code>available</code>) — по одному на строку
		<textarea name="available" rows="8" style="width:100%;font-family:ui-monospace,Consolas,monospace;"><?= "\n" . htmlspecialchars( implode( "\n" , $current[ 'available' ] ) , ENT_QUOTES ) ?></textarea>
	</label>

	<label class="checkbox">
		<input type="checkbox" name="auth" value="1" <?= $current[ 'auth' ] ? 'checked' : '' ?>>
		<code>auth</code> — только для авторизованных
	</label>

	<label class="checkbox">
		<input type="checkbox" name="search" value="1" <?= $current[ 'search' ] ? 'checked' : '' ?>>
		<code>search</code> — разбирать URL через regex
	</label>

	<label class="checkbox">
		<input type="checkbox" name="default" value="1" <?= $current[ 'default' ] ? 'checked' : '' ?>>
		<code>default</code> — вызывать default() для пустого URL
	</label>

	<button type="submit">Сохранить</button>
	<a href="?p=pages:tree&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>" class="btn-cancel">Отмена</a>
</form>