<?php
$active_tab = 'stub';
$actions = '';
require __DIR__ . '/_domain_tabs.php';
?>

<?php if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<form method="post" action="?p=stub:edit&domain=<?= urlencode( $domain ) ?>" class="form" style="max-width:600px;">
    <?= Csrf::field() ?>

    <label>Заголовок
        <input type="text" name="stub_title" value="<?= htmlspecialchars($stub["title"] ?? "Технические работы") ?>" required>
    </label>

    <label>Текст заглушки
        <textarea name="stub_text" rows="4" required><?= htmlspecialchars($stub["text"] ?? "Мы обновляем наш сайт, чтобы сделать его лучше. Скоро вернемся!") ?></textarea>
    </label>

    <button type="submit" class="btn-edit" style="margin-top:10px;">Сохранить</button>
</form>
