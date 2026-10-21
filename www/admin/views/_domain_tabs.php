<?php 
$active_tab = $active_tab ?? ''; 
$actions = $actions ?? ''; 
?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h1 style="margin:0;">Домен: <code><?= htmlspecialchars( $domain ) ?></code></h1>
    <div class="td-actions">
        <?= $actions ?>
        <a href="?p=dashboard" class="btn-cancel">← Назад</a>
    </div>
</div>

<div class="nav-tabs" style="margin-bottom:20px;">
    <a href="?p=domain:edit&domain=<?= urlencode($domain) ?>&tab=main" class="<?= $active_tab === 'main' ? 'active' : '' ?>">Настройки</a>
    <a href="?p=allias:list&domain=<?= urlencode($domain) ?>&tab=allias" class="<?= $active_tab === 'allias' ? 'active' : '' ?>">Аллиасы</a>
    <a href="?p=blocks:list&domain=<?= urlencode($domain) ?>&tab=blocks" class="<?= $active_tab === 'blocks' ? 'active' : '' ?>">Блоки</a>
    <a href="?p=schemes:list&domain=<?= urlencode($domain) ?>&tab=schemes" class="<?= $active_tab === 'schemes' ? 'active' : '' ?>">Схемы</a>
    <a href="?p=stub:edit&domain=<?= urlencode($domain) ?>&tab=stub" class="<?= $active_tab === 'stub' ? 'active' : '' ?>">Заглушка</a>
</div>
