<?php
// Узнаем статус сайта для подсказки
$site_enabled = ! empty( \Paths::real( $domain ) ) && ( new \Config( \Paths::config() ) )->getKeys( [ 'domains_available' , \Paths::real( $domain ) ] );
?>

<?php $active_tab = 'stub'; require __DIR__ . '/_domain_tabs.php'; ?>

<?php if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<div style="background:#eef1f5; padding:15px; border-radius:6px; margin-bottom:20px; border-left:4px solid #2a63d6;">
    <p style="margin:0; font-size:14px;">
        <b>Статус домена:</b> 
        <?php if ( $site_enabled ) : ?>
            <span style="color:#1a7a3b; font-weight:bold;">ВКЛЮЧЕН</span>
            <br><small style="color:#666;">Внимание: Пока сайт включен, заглушка не показывается посетителям. Чтобы она заработала, выключите домен на главной странице.</small>
        <?php else : ?>
            <span style="color:#a11; font-weight:bold;">ВЫКЛЮЧЕН</span>
            <br><small style="color:#666;">Сейчас посетители видят эту заглушку (если она настроена ниже).</small>
        <?php endif ?>
    </p>
</div>

<form method="post" action="?p=stub:edit&domain=<?= urlencode( $domain ) ?>" class="form" style="max-width:600px;">
    <?= Csrf::field() ?>

    <label class="checkbox">
        <input type="checkbox" name="stub_enabled" value="1" <?= !empty($stub["enabled"]) ? "checked" : "" ?>>
        <b>Включить заглушку</b>
    </label>

    <label>Заголовок
        <input type="text" name="stub_title" value="<?= htmlspecialchars($stub["title"] ?? "Технические работы") ?>" required>
    </label>

    <label>Текст заглушки
        <textarea name="stub_text" rows="4" required><?= htmlspecialchars($stub["text"] ?? "Мы обновляем наш сайт, чтобы сделать его лучше. Скоро вернемся!") ?></textarea>
    </label>

    <label>Время окончания
        <input type="datetime-local" name="stub_until_date" value="<?= !empty($stub["until"]) ? date("Y-m-d\TH:i", $stub["until"]) : "" ?>">
        <small style="color:#888;">Оставьте пустым, если заглушка должна работать бессрочно. Если время пройдет, заглушка исчезнет.</small>
    </label>

    <button type="submit" class="btn-edit" style="margin-top:10px; padding:10px 20px; font-size:15px;">Сохранить заглушку</button>
</form>
