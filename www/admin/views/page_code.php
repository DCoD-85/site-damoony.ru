<?php
// Читаем конфиг языков
$lang_cfg_path = PROJECT_ROOT . '/config/lang.config';
$lang_cfg = is_file($lang_cfg_path) ? json_decode(file_get_contents($lang_cfg_path), true) : [];
$lang_default = $lang_cfg['default'] ?? 'ru-ru';

// Строим умный URL для предпросмотра
$host = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'www.damoony2.ru');
$url_path = ($allias === 'www' || $allias === '') ? '' : $allias . '/';

if ($path === '') {
    $url_path .= $lang_default . '/index';
} else {
    $url_path .= str_replace('lang', $lang_default, $path);
}

$preview_url = rtrim($host . '/' . $url_path, '/') . '/';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h1 style="margin:0;">Редактор кода: <code><?= htmlspecialchars( $cls_short ) ?></code></h1>
    <div class="td-actions">
        <a href="?p=page:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $path ) ?>" class="btn-edit">Настройки</a>
        <a href="?p=pages:tree&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>" class="btn-cancel">← К страницам</a>
    </div>
</div>

<?php if ( !empty($_GET['saved']) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<div class="nav-tabs" style="margin-bottom:20px;">
    <a href="?p=page:code&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&path=<?= urlencode($path) ?>&tab=logic" class="<?= $tab === 'logic' ? 'active' : '' ?>">Логика (PHP)</a>
    <a href="?p=page:code&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&path=<?= urlencode($path) ?>&tab=data" class="<?= $tab === 'data' ? 'active' : '' ?>">Данные (Визуализатор)</a>
</div>

<div style="display:flex; gap:15px; height:75vh;">
    <!-- ЛЕВАЯ КОЛОНКА (Редактор) -->
    <div style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
        <?php if ( $tab === 'data' ) : ?>
            <form method="post" style="display:flex; flex-direction:column; height:100%;">
                <?= Csrf::field() ?>
                <input type="hidden" name="save_json" value="1">
                <label style="font-weight:bold; margin-bottom:5px; color:#555;">JSON (Данные роутинга)</label>
                <textarea name="json_code" class="code-editor" style="flex:1;" spellcheck="false"><?= htmlspecialchars( $json_code ) ?></textarea>
                <button type="submit" class="btn-edit" style="margin-top:10px; padding:10px;">Сохранить данные</button>
            </form>
        <?php else : ?>
            <div style="display:flex; flex-direction:column; height:100%;">
                <div class="method-list" style="display:flex; flex-direction:row; flex-wrap:wrap; gap:6px; margin-bottom:10px;">
                    <?php foreach ( $methods as $name => $m ) : ?>
                        <a href="?p=page:code&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&path=<?= urlencode($path) ?>&tab=logic&method=<?= urlencode($name) ?>" 
                           class="method-link <?= $method_name === $name ? 'active' : '' ?>" style="padding:4px 10px; font-size:12px;">
                            <?= htmlspecialchars( $name ) ?>()
                        </a>
                    <?php endforeach ?>
                </div>

                <?php if ( $method_name && isset( $methods[ $method_name ] ) ) : ?>
                    <form method="post" style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="save_method" value="1">
                        <textarea name="method_code" class="code-editor" style="flex:1;" spellcheck="false"><?= htmlspecialchars( $method_code ) ?></textarea>
                        <button type="submit" class="btn-edit" style="margin-top:10px; padding:10px;">Сохранить функцию</button>
                    </form>
                <?php else : ?>
                    <div class="error-msg" style="background:#f5f6f8; color:#666; border:1px dashed #ccc; flex:1; display:flex; align-items:center; justify-content:center;">
                        Выберите функцию сверху для редактирования.
                    </div>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>

    <!-- ПРАВАЯ КОЛОНКА (Live Preview) -->
    <div style="flex:1; display:flex; flex-direction:column; background:#fff; border-radius:6px; overflow:hidden; border:1px solid #ccc;">
        <div style="background:#f0f2f6; padding:8px 12px; font-size:12px; border-bottom:1px solid #ccc; display:flex; gap:10px; align-items:center;">
            <span style="font-weight:bold; color:#555; white-space:nowrap;">Предпросмотр:</span>
            <input type="text" id="preview-url" value="<?= htmlspecialchars( $preview_url ) ?>" style="flex:1; padding:4px 8px; border:1px solid #ccc; border-radius:4px; font-family:monospace;">
            <button onclick="document.getElementById('preview-frame').src = document.getElementById('preview-url').value + '?t=' + Date.now();" style="cursor:pointer; padding:4px 10px; white-space:nowrap;">Обновить</button>
            <a href="<?= htmlspecialchars( $preview_url ) ?>" target="_blank" style="color:#2a63d6; text-decoration:none; white-space:nowrap;">Открыть ↗</a>
        </div>
        <iframe id="preview-frame" src="<?= htmlspecialchars( $preview_url ) ?>?t=<?= time() ?>" style="flex:1; width:100%; border:none;"></iframe>
    </div>
</div>

<style>
.code-editor {
    width: 100%; background: #1e1e1e; color: #d4d4d4; font-family: ui-monospace, 'Cascadia Code', 'Consolas', monospace;
    font-size: 14px; line-height: 1.5; padding: 15px; border-radius: 6px; border: 1px solid #333; resize: none; tab-size: 4;
}
.code-editor:focus { outline: none; border-color: #2a63d6; }
.method-list { border-bottom:1px dashed #ccc; padding-bottom:10px; }
.method-link { background: #fff; border: 1px solid #e0e4eb; border-radius: 4px; color: #333; text-decoration: none; font-family: ui-monospace, Consolas, monospace; }
.method-link:hover { background: #f0f4ff; border-color: #c6d6f5; }
.method-link.active { background: #2a63d6; color: #fff; border-color: #2a63d6; font-weight: bold; }
</style>
