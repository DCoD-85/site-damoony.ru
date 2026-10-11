<h1>Создать страницу</h1>

<p class="hint">
	Домен: <code><?= htmlspecialchars( $domain , ENT_QUOTES ) ?></code>
</p>

<form method="post" action="?p=page:new&domain=<?= urlencode( $domain ) ?>" class="form">

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
</form>