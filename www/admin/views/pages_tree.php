<?php
if ( ! function_exists( 'render_page_node' ) ) {
	function render_page_node ( array $node , string $domain , string $allias ) : void {
		$meta = $node[ 'meta' ] ?? [ 'available' => [] , 'auth' => false , 'search' => true , 'default' => false ];
		?>
		<li class="tree-item">
			<div class="tree-row">
				<div class="tree-left">
					<div class="tree-title">
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
					</div>

					<div class="tree-sub">
						<a href="?p=page:visual&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $node[ 'path' ] ) ?>" class="btn-sub" style="color:#1a7a3b; font-weight:bold;">Визуально</a> &nbsp;|&nbsp; 
							<a href="?p=page:code&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $node[ 'path' ] ) ?>" class="btn-sub">Редактировать код</a> &nbsp;|&nbsp; 
							<a href="?p=page:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&parent=<?= urlencode( $node[ 'path' ] ) ?>"
							class="btn-sub">+ Подстраница</a>
					</div>
				</div>

				<?php if ( $node[ 'has_file' ] ) : ?>
					<span class="tree-flags">
						<?php foreach ( [ 'auth' => 'auth' , 'search' => 'search' , 'default' => 'default' ] as $key => $label ) : ?>
							<form method="post" action="?p=page:toggle">
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

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
	<h1 style="margin:0;">Страницы: <code><?= htmlspecialchars( $domain ) ?></code> / <code><?= htmlspecialchars( $allias ) ?></code></h1>
	<div class="td-actions">
		<a href="?p=page:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>" class="btn-edit">+ Новая страница</a>
		<a href="?p=allias:list&domain=<?= urlencode( $domain ) ?>" class="btn-cancel">← К аллиасам</a>
	</div>
</div>

<?php if ( ! $has_dir ) : ?>
	<div class="error-msg">
		Каталог не найден: <code>app/<?= htmlspecialchars( $logic ) ?>/domain/<?= htmlspecialchars( $domain ) ?>/allias/<?= htmlspecialchars( $allias ) ?>/algoritm/</code>
	</div>
<?php elseif ( empty( $tree ) ) : ?>
	<p>Страниц нет.</p>
<?php else : ?>
	<ul class="tree">
		<?php foreach ( $tree as $node ) render_page_node( $node , $domain , $allias ); ?>
	</ul>
<?php endif ?>
