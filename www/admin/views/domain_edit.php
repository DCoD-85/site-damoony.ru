<?PHP if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?PHP endif ?>

<?PHP if ( ! empty( $errors ) ) : ?>
    <div class="error-msg">
        <?PHP foreach ( $errors as $e ) : ?>
            <div>✘ <?= htmlspecialchars( $e ) ?></div>
        <?PHP endforeach ?>
    </div>
<?PHP endif ?>

<form method="post" action="?p=domain:edit&domain=<?= urlencode( $domain ) ?>" class="form" style="max-width: 600px;">
    <?= Csrf::field() ?>

    <label>Заголовок (title)
        <input type="text" name="title" maxlength="100" required
            value="<?= htmlspecialchars( $current[ 'title' ] , ENT_QUOTES ) ?>">
    </label>

    <hr style="margin: 20px 0; border-top: 1px dashed #ccc;">
    <h3>Статус сайта</h3>

    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
        <label class="switch" style="margin:0;">
            <input type="checkbox" name="domain_available" value="1" <?= $current[ 'avail' ] ? 'checked' : '' ?>>
            <span class="slider"></span>
        </label>
        <span>Сайт включен (если выключить — покажется заглушка)</span>
    </div>

    <label>Выберите заглушку
        <select name="stub_current">
            <?PHP foreach ( $stubs as $sid => $s ) : ?>
                <option value="<?= htmlspecialchars($sid) ?>" <?= $sid === $current[ 'stub' ] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['title']) ?>
                </option>
            <?PHP endforeach ?>
        </select>
    </label>

    <label>Время авто-включения
        <input type="datetime-local" name="stub_until_date" value="<?= !empty($current['until']) ? date('Y-m-d\TH:i', $current['until']) : '' ?>">
        <small style="color:#888;">Если указать время, сайт автоматически включится в этот момент.</small>
    </label>

    <button type="submit" class="btn-primary" style="margin-top:15px;">Сохранить</button>
</form>

<div style="margin-top: 40px; padding: 20px; background: var(--danger-bg); border: 1px solid #fecaca; border-radius: var(--radius); max-width: 600px;">
    <h3 style="color:var(--danger-h); margin-top:0;">Опасная зона</h3>
    <p style="font-size:13px; color:var(--text-dim);">Удаление домена приведет к удалению всех аллиасов, блоков и настроек.</p>
    <a href="?p=domain:remove&domain=<?= urlencode($domain) ?>" class="btn-danger-solid" onclick="return confirm('Удалить домен?');">Удалить домен</a>
</div>