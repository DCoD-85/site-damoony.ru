<?php
if ( ! function_exists( 'render_page_node' ) ) {
	function render_page_node ( array $node , string $domain , string $allias ) : void {
		?>
		<li class="tree-item">
			<div class="tree-row">
				<span class="tree-name"><?= htmlspecialchars( $node[ 'name' ] ) ?></span>

				<?php if ( $node[ 'has_file' ] ) : ?>
					<span class="tree-badge tree-badge-page">страница</span>
				<?php else : ?>
					<span class="tree-badge tree-badge-orphan">без файла</span>
				<?php endif ?>

				<?php if ( ! empty( $node[ 'available' ] ) ) : ?>
					<span class="tree-avail">
						<?php foreach ( $node[ 'available' ] as $a ) : ?>
							<code><?= htmlspecialchars( $a === '' ? '/' : $a ) ?></code>
						<?php endforeach ?>
					</span>
				<?php endif ?>

				<span class="tree-actions">
					<a href="?p=page:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&parent=<?= urlencode( $node[ 'path' ] ) ?>"
						class="btn-edit">+ Подстраница</a>
				</span>
			</div>

			<?php if ( ! empty( $node[ 'children' ] ) ) : ?>
				<ul class="tree-children">
					<?php foreach ( $node[ 'children' ] as $child ) render_page_node( $child , $domain , $allias ); ?>
				</ul>
			<?php endif ?>
		</li>
		<?php
	}
}
?>

<h1>Страницы: <code><?= htmlspecialchars( $domain ) ?></code> / <code><?= htmlspecialchars( $allias ) ?></code></h1>

<p class="hint">
	<a href="?p=allias:list&domain=<?= urlencode( $domain ) ?>">← К аллиасам</a>
	&nbsp;·&nbsp;
	<a href="?p=page:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>">+ Корневая страница</a>
</p>

<?php if ( ! $has_dir ) : ?>
	<div class="error-msg">Каталог <code>algoritm/lang/</code> не найден: <?= htmlspecialchars( $logic ) ?> / <?= htmlspecialchars( $domain ) ?> / <?= htmlspecialchars( $allias ) ?></div>
<?php elseif ( empty( $tree ) ) : ?>
	<p>Страниц нет. Создайте первую.</p>
<?php else : ?>
	<ul class="tree">
		<?php foreach ( $tree as $node ) render_page_node( $node , $domain , $allias ); ?>
	</ul>
<?php endif ?>