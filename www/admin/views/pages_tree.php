<?PHP
if ( ! function_exists( 'render_page_node' ) ) {
	function render_page_node ( array $node , string $domain , string $allias ) : void {
		$meta = $node[ 'meta' ] ?? [ 'available' => [] , 'auth' => false , 'search' => true , 'default' => false ];
		?>
		<li class="tree-item" data-search="<?= htmlspecialchars( mb_strtolower( $node[ 'name' ] ) ) ?>">
			<div class="tree-row">
				<div class="tree-left">
					<div class="tree-title">
						<a class="tree-name" href="?p=page:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $node[ 'path' ] ) ?>">
							<?= htmlspecialchars( $node[ 'name' ] ) ?>
						</a>
						<?PHP if ( $node[ 'has_file' ] ) : ?><span class="tree-badge tree-badge-page">страница</span><?PHP else : ?><span class="tree-badge tree-badge-orphan">без файла</span><?PHP endif ?>
						<code class="tree-cls"><?= htmlspecialchars( $node[ 'class' ] ) ?></code>
						<?PHP if ( ! empty( $meta[ 'available' ] ) ) : ?>
							<span class="tree-avail">
								<?PHP foreach ( $meta[ 'available' ] as $a ) : ?><code><?= htmlspecialchars( $a === '' ? '/' : $a ) ?></code><?PHP endforeach ?>
							</span>
						<?PHP endif ?>
					</div>
					<div class="tree-sub">
						<a href="?p=page:visual&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $node[ "path" ] ) ?>" class="btn-sub" style="color:#1a7a3b; font-weight:bold;">Визуально</a> &nbsp;|&nbsp;
						<a href="?p=page:code&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $node[ "path" ] ) ?>" class="btn-sub">Редактировать код</a> &nbsp;|&nbsp;
						<a href="?p=page:new&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&parent=<?= urlencode( $node[ "path" ] ) ?>" class="btn-sub">+ Подстраница</a>
					</div>
				</div>
				<?PHP if ( $node[ 'has_file' ] ) : ?>
					<span class="tree-flags">
						<?PHP foreach ( [ 'auth' => 'auth' , 'search' => 'search' , 'default' => 'default' ] as $key => $label ) : ?>
							<form method="post" action="?p=page:toggle"><?= Csrf::field( ) ?><input type="hidden" name="domain" value="<?= htmlspecialchars( $domain , ENT_QUOTES ) ?>"><input type="hidden" name="allias" value="<?= htmlspecialchars( $allias , ENT_QUOTES ) ?>"><input type="hidden" name="path" value="<?= htmlspecialchars( $node[ 'path' ] , ENT_QUOTES ) ?>"><input type="hidden" name="key" value="<?= $key ?>"><input type="hidden" name="value" value="<?= ! empty( $meta[ $key ] ) ? '0' : '1' ?>"><button type="submit" class="flag <?= ! empty( $meta[ $key ] ) ? 'flag-on' : 'flag-off' ?>"><?= $label ?></button></form>
						<?PHP endforeach ?>
					</span>
				<?PHP endif ?>
			</div>
			<?PHP if ( ! empty( $node[ 'children' ] ) ) : ?><ul class="tree-children"><?PHP foreach ( $node[ 'children' ] as $child ) render_page_node( $child , $domain , $allias ); ?></ul><?PHP endif ?>
		</li>
		<?PHP
	}
}
?>

<div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <input type="text" id="tree-search" placeholder="Поиск по страницам..." style="padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); width: 300px; font: inherit; background: var(--surface);">
    <span class="hint" style="margin:0;">Введите название для фильтрации</span>
</div>

<?PHP if ( ! $has_dir ) : ?>
	<div class="error-msg">Каталог не найден: <code>app/<?= htmlspecialchars( $logic ) ?>/domain/<?= htmlspecialchars( $domain ) ?>/allias/<?= htmlspecialchars( $allias ) ?>/algoritm/</code></div>
<?PHP elseif ( empty( $tree ) ) : ?>
	<p>Страниц нет.</p>
<?PHP else : ?>
	<ul class="tree" id="pages-tree"><?PHP foreach ( $tree as $node ) render_page_node( $node , $domain , $allias ); ?></ul>
<?PHP endif ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tree-search');
    const treeItems = document.querySelectorAll('#pages-tree .tree-item');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const val = e.target.value.toLowerCase().trim();
            
            treeItems.forEach(item => {
                const text = item.getAttribute('data-search') || '';
                // Если поле пустое или текст совпадает
                if (val === '' || text.includes(val)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>