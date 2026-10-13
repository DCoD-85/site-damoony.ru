<?php
if ( ! function_exists( 'render_page_node' ) ) {
	function render_page_node ( array $node , string $domain , string $allias ) : void {
		$meta = $node[ 'meta' ] ?? [ 'available' => [] , 'auth' => false , 'search' => true , 'default' => false ];
		?>
		<li class="tree-item">
			<div class="tree-row">
				<a class="tree-name" href="?p=page:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $node[ 'path' ] ) ?>">
					<?= htmlspecialchars( $node[ 'name' ] ) ?>
				</a>

				<?php if ( $node[ 'has_file' ] ) : ?>
					<span class="tree-badge tree-badge-page">страница</span>
				<?php else : ?>
					<span class="tree-badge tree-badge-orphan">без файла</span>
				<?php endif ?>

				<code class="tree-cls"><?= htmlspecialchars( $node[ 'class' ] ) ?></code>

				<?php if ( ! empty( $meta[ 'available' ] ) ) : ?>
					<span class="tree-avail">
						<?php foreach ( $meta[ 'available' ] as $a ) : ?>
							<code><?= htmlspecialchars( $a === '' ? '/' : $a ) ?></code>
						<?php endforeach ?>
					</span>
				<?php endif ?>

				<?php if ( $node[ 'has_file' ] ) : ?>
					<span class="tree-flags">
						<?php foreach ( [ 'auth' => 'auth' , 'search' => 'search' , 'default' => 'default' ] as $key => $label ) : ?>
							<form method="post" action="?p=page:toggle" class="inline">
								<?= Csrf::field( ) ?>
								<input type="hidden" name="domain" value="<?= htmlspecialchars( $domain , ENT_QUOTES ) ?>">
								<input type="hidden" name="allias" value="<?= htmlspecialchars( $allias , ENT_QUOTES ) ?>">
								<input type="hidden" name="path"   value="<?= htmlspecialchars( $node[ 'path' ] , ENT_QUOTES ) ?>">
								<input type="hidden" name="key"    value="<?= $key ?>">
								<input type="hidden" name="value"  value="<?= ! empty( $meta[ $key ] ) ? '0' : '1' ?>">
								<button type="submit"
									class="flag <?= ! empty( $meta[ $key ] ) ? 'flag-on' : 'flag-off' ?>"
									title="<?= ! empty( $meta[ $key ] ) ? 'Включено — клик чтобы выключить' : 'Выключено — клик чтобы включить' ?>">
									<?= $label ?>
								</button>
							</form>
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
	<a href="?p=page:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>">+ Страница</a>
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