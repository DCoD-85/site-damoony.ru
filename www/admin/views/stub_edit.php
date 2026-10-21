<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h1 style="margin:0;">Заглушка: <code><?= htmlspecialchars( $domain ) ?></code></h1>
    <a href="?p=dashboard" class="btn-cancel">← К доменам</a>
</div>

<?php if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<form method="post" action="?p=stub:edit&domain=<?= urlencode( $domain ) ?>" class="form">
    <?= Csrf::field() ?>

    <label class="checkbox">
        <input type="checkbox" name="stub_enabled" value="1" <?= !empty($stub["enabled"]) ? "checked" : "" ?>>
        <b>Включить заглушку</b> (если сайт выключен или на тех. работах)
    </label>

    <label>Заголовок
        <input type="text" name="stub_title" value="<?= htmlspecialchars($stub["title"] ?? "Технические работы") ?>" required>
    </label>

    <label>Текст
        <textarea name="stub_text" rows="4" required><?= htmlspecialchars($stub["text"] ?? "Мы обновляем наш сайт, чтобы сделать его лучше. Скоро вернемся!") ?></textarea>
    </label>

    <label>Время окончания (когда сайт снова заработает)
        <input type="datetime-local" name="stub_until_date" value="<?= !empty($stub["until"]) ? date("Y-m-d\TH:i", $stub["until"]) : "" ?>">
        <small style="color:#888;">Оставьте пустым, если заглушка должна работать бессрочно.</small>
    </label>

    <button type="submit" class="btn-edit">Сохранить заглушку</button>
</form>
