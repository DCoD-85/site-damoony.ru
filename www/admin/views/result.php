<?PHP ?>
<h1 class="page-title" style="margin-top:0; margin-bottom:20px;"><?= htmlspecialchars( $title ?? 'Результат' ) ?></h1>

<?= WebRenderer::html( $result ) ?>

<div style="margin-top: 24px;">
    <a href="<?= htmlspecialchars( $back_url ?? '?p=dashboard' , ENT_QUOTES ) ?>" class="btn-cancel" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; font-size:14px; text-decoration:none;">
        ← <?= htmlspecialchars( $back_label ?? 'К списку домен' ) ?>
    </a>
</div>