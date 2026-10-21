<?php 
$active_tab = $active_tab ?? ''; 
$actions = $actions ?? ''; 
?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h1 style="margin:0;">Аллиас: <code><?= htmlspecialchars( $allias ) ?></code> <span style="font-size:16px; color:#888; font-weight:normal;">(<?= htmlspecialchars( $domain ) ?>)</span></h1>
    <div class="td-actions">
        <?= $actions ?>
        <a href="?p=dashboard" class="btn-cancel">← Назад</a>
    </div>
</div>

<div class="nav-tabs" style="margin-bottom:20px;">
    <a href="?p=allias:edit&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&tab=main" class="<?= $active_tab === 'main' ? 'active' : '' ?>">Настройки</a>
    <a href="?p=pages:tree&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&tab=pages" class="<?= $active_tab === 'pages' ? 'active' : '' ?>">Страницы</a>
    <a href="?p=allias:fillers&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&tab=fillers" class="<?= $active_tab === 'fillers' ? 'active' : '' ?>">Филлеры</a>
    <a href="?p=scheme:edit&domain=<?= urlencode($domain) ?>&scheme=<?= urlencode($allias) ?>&tab=scheme" class="<?= $active_tab === 'scheme' ? 'active' : '' ?>">Схема</a>
</div>
