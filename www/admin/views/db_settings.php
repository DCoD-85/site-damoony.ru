<?PHP if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Настройки БД сохранены</div>
<?PHP endif ?>

<div class="hint" style="margin-bottom:15px;">
    Файл конфигурации: <code>secure/db.config</code>
</div>

<form method="post" action="?p=db:settings" class="form" style="max-width: 600px;">
    <?= Csrf::field() ?>
    
    <label>Идентификатор базы (localname)
        <input type="text" name="localname" value="<?= htmlspecialchars($db['localname'] ?? 'visuo_damoony') ?>" required>
        <small style="color:#888;">Должен совпадать с <code>domain_db</code> в site.config</small>
    </label>
    
    <label>Хост (Host)
        <input type="text" name="host" value="<?= htmlspecialchars($db['dbhost'] ?? 'localhost') ?>" required>
    </label>
    
    <label>Имя базы (Database)
        <input type="text" name="name" value="<?= htmlspecialchars($db['dbname'] ?? '') ?>" required>
    </label>
    
    <label>Пользователь (User)
        <input type="text" name="user" value="<?= htmlspecialchars($db['username'] ?? '') ?>" required>
    </label>
    
    <label>Пароль (Password)
        <input type="password" name="pass" value="" autocomplete="new-password" placeholder="<?= ! empty( $db['password'] ) ? '•••••••• (оставьте пустым, чтобы не менять)' : 'Пароль не задан' ?>">
        <small style="color:#888;">Оставьте поле пустым, чтобы сохранить текущий пароль.</small>
    </label>

    <div style="display:flex; gap:10px; margin-top:20px;">
        <button type="submit" class="btn-primary">Сохранить</button>
        <a href="?p=settings" class="btn-cancel">Назад</a>
    </div>
</form>