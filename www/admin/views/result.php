<h1><?= htmlspecialchars( $title ?? 'Результат' ) ?></h1>

<?= WebRenderer::html( $result ) ?>

<p>
	<a href="<?= htmlspecialchars( $back_url ?? '?p=dashboard' , ENT_QUOTES ) ?>">
		← <?= htmlspecialchars( $back_label ?? 'К списку домен' ) ?>
	</a>
</p>