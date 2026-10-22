<?PHP ?>
<p class="hint">
	Домен: <code><?= htmlspecialchars( $domain , ENT_QUOTES ) ?></code>
</p>

<?PHP if ( ! empty( $error ) ) : ?>
	<div class="error-msg">✘ <?= htmlspecialchars( $error ) ?></div>
<?PHP endif ?>

<?PHP if ( ! empty( $parent ) ) : ?>
	<p class="hint">
		Родитель: <code><?= htmlspecialchars( $parent ) ?></code>
		&nbsp;·&nbsp; файл: <code><?= htmlspecialchars( str_replace( '/' , '_' , $parent ) ) ?>_<span>имя</span></code>
	</p>
<?PHP endif ?>

<form method="post" action="?p=page:new&domain=<?= urlencode( $domain ) ?>" class="form">
	<?= Csrf::field( ) ?>

	<label>Аллиас
		<select name="allias" required>
			<?PHP foreach ( $alliases as $a ) : ?>
				<option value="<?= htmlspecialchars( $a , ENT_QUOTES ) ?>"
					<?= $a === $allias ? 'selected' : '' ?>>
					<?= htmlspecialchars( $a ) ?>
				</option>
			<?PHP endforeach ?>
		</select>
	</label>

	<label>Имя страницы (URL)
		<input type="text" name="name" required pattern="[a-z][a-z0-9_]*"
			maxlength="32" autocomplete="off" placeholder="news">
	</label>

	<label>Родительский путь (опционально)
		<input type="text" name="parent" value="<?= htmlspecialchars( $parent ?? '' , ENT_QUOTES ) ?>" placeholder="news/archive">
	</label>

	<div style="display:flex; gap:10px; align-items:center; margin-top:15px;">
		<button type="submit" class="btn-primary">Создать</button>
		<a href="?p=allias:list&domain=<?= urlencode( $domain ) ?>" class="btn-cancel">Отмена</a>
	</div>
</form>