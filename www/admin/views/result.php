<h1><?= htmlspecialchars( $title ?? 'Результат' ) ?></h1>

<?= WebRenderer::html( $result ) ?>

<p><a href="?p=dashboard">← К списку домен</a></p>