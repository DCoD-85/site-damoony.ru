<h1>Создать filler</h1>

<p class="hint">
	Домен: <code><?= htmlspecialchars( $domain , ENT_QUOTES ) ?></code>
	&nbsp;·&nbsp;
	<a href="?p=allias:list&domain=<?= urlencode( $domain ) ?>">← К аллиасам</a>
</p>

<?php if ( empty( $alliases ) ) : ?>
	<div class="error-msg">У домена нет аллиасов.</div>
<?php elseif ( empty( $blocks ) ) : ?>
	<div class="error-msg">Блоки не найдены в <code>alliasglobal/</code>.</div>
<?php else : ?>
	<form method="post" action="?p=filler:new&domain=<?= urlencode( $domain ) ?>" class="form">
		<?= Csrf::field( ) ?>
		<input type="hidden" name="domain" value="<?= htmlspecialchars( $domain , ENT_QUOTES ) ?>">

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

		<label>Блок
			<select name="block" required>
				<?php foreach ( $blocks as $b ) : ?>
					<option value="<?= htmlspecialchars( $b , ENT_QUOTES ) ?>"><?= htmlspecialchars( $b ) ?></option>
				<?php endforeach ?>
			</select>
		</label>

		<label class="checkbox">
			<input type="checkbox" name="enable" value="1" checked>
			Включить в <code>site.config</code>
			(<code>fillers[domain][allias][block] = true</code>)
		</label>

		<button type="submit">Создать</button>
		<a href="?p=allias:list&domain=<?= urlencode( $domain ) ?>" class="btn-cancel">Отмена</a>
	</form>
<?php endif ?>