<?PHP if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?PHP endif ?>

<form method="post" action="?p=allias:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>" class="form" style="max-width: 600px;">
    <?= Csrf::field() ?>
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
        <label class="switch" style="margin:0;">
            <input type="checkbox" name="available" value="1" <?= $avail ? 'checked' : '' ?>>
            <span class="slider"></span>
        </label>
        <span>Аллиас включен (доступен для посещения)</span>
    </div>
    <button type="submit" class="btn-primary" style="margin-top:15px;">Сохранить</button>
</form>

<div style="margin-top: 40px; padding: 20px; background: var(--danger-bg); border: 1px solid #fecaca; border-radius: var(--radius); max-width: 600px;">
    <h3 style="color:var(--danger-h); margin-top:0;">Опасная зона</h3>
    <p style="font-size:13px; color:var(--text-dim);">Удаление аллиаса приведет к удалению всех его страниц и филлеров.</p>
    <a href="?p=allias:remove&domain=<?= urlencode($domain) ?>&name=<?= urlencode($allias) ?>" class="btn-danger-solid" onclick="return confirm('Удалить аллиас?');">Удалить аллиас</a>
</div>