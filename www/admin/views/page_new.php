<h1>Создать страницу</h1>

<p class="hint">
	Домен: <code><?= htmlspecialchars( $domain , ENT_QUOTES ) ?></code>
</p>

<?php if ( ! empty( $error ) ) : ?>
	<div class="error-msg">✘ <?= htmlspecialchars( $error ) ?></div>
<?php endif ?>

<?php if ( ! empty( $parent ) ) : ?>
	<p class="hint">
		Родитель: <code><?= htmlspecialchars( $parent ) ?></code>
		&nbsp;·&nbsp; файл: <code><?= htmlspecialchars( str_replace( '/' , '_' , $parent ) ) ?>_<span>имя</span></code>
	</p>
<?php endif ?>

<form method="post" action="?p=page:new&domain=<?= urlencode( $domain ) ?>" class="form">
	<?= Csrf::field( ) ?>

	<label>Аллиас
		<select name="allias" required>
			<?php foreach ( $alliases as $a ) : ?>
				<option value="<?= htmlspecialchars( $a , ENT_QUOTES ) ?>"
					<?= $a === $allias ? 'selected' : '' ?>>
					<?= htmlspecialchars( $a ) ?>
				</option>
			<?php endforeach ?>
		</select>
	</label>

	<label>Имя страницы (URL)
		<input type="text" name="name" required pattern="[a-z][a-z0-9_]*"
			maxlength="32" autocomplete="off" placeholder="news">
	</label>

	<button type="submit">Создать</button>
	<a href="?p=allias:list&domain=<?= urlencode( $domain ) ?>" class="btn-cancel">Отмена</a>

	<?php if ( ! empty( $parent ) ) : ?>
		<input type="hidden" name="parent" value="<?= htmlspecialchars( $parent , ENT_QUOTES ) ?>">
	<?php endif ?>

</form>