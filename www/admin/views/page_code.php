<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
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

<?php if ( $tab === 'data' ) : ?>
    <form method="post" style="display:flex; flex-direction:column;">
        <?= Csrf::field() ?>
        <input type="hidden" name="save_json" value="1">
        
        <label style="font-weight:bold; margin-bottom:5px; color:#555;">JSON (Данные роутинга) — <code><?= $cls_short ?>.data.json</code></label>
        <textarea name="json_code" class="code-editor" rows="20" spellcheck="false"><?= htmlspecialchars( $json_code ) ?></textarea>
        
        <div style="margin-top:20px;">
            <button type="submit" class="btn-edit" style="padding:10px 24px; font-size:15px;">Сохранить данные</button>
        </div>
    </form>
<?php else : ?>
    <div style="display:flex; gap:20px;">
        <!-- Сайдбар с функциями -->
        <div style="width:250px; flex-shrink:0;">
            <div style="font-weight:bold; margin-bottom:10px; color:#555;">Функции класса</div>
            <div class="method-list">
                <?php foreach ( $methods as $name => $m ) : ?>
                    <a href="?p=page:code&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&path=<?= urlencode($path) ?>&tab=logic&method=<?= urlencode($name) ?>" 
                       class="method-link <?= $method_name === $name ? 'active' : '' ?>">
                        <?= htmlspecialchars( $name ) ?>()
                    </a>
                <?php endforeach ?>
            </div>
        </div>

        <!-- Редактор функции -->
        <div style="flex:1;">
            <?php if ( $method_name && isset( $methods[ $method_name ] ) ) : ?>
                <form method="post" style="display:flex; flex-direction:column;">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="save_method" value="1">
                    
                    <label style="font-weight:bold; margin-bottom:5px; color:#555;">Код функции <code><?= htmlspecialchars($method_name) ?>()</code></label>
                    <textarea name="method_code" class="code-editor" rows="22" spellcheck="false"><?= htmlspecialchars( $method_code ) ?></textarea>
                    
                    <div style="margin-top:20px;">
                        <button type="submit" class="btn-edit" style="padding:10px 24px; font-size:15px;">Сохранить функцию</button>
                    </div>
                </form>
            <?php else : ?>
                <div class="error-msg" style="background:#f5f6f8; color:#666; border:1px dashed #ccc;">
                    Выберите функцию слева для редактирования. <br><br>
                    <em>Совет:</em> Если нужной функции нет, вы можете добавить её в «Настройках» (кнопка сверху) через полный редактор кода. (Скоро мы и это перенесем сюда).
                </div>
            <?php endif ?>
        </div>
    </div>
<?php endif ?>

<style>
.code-editor {
    width: 100%;
    background: #1e1e1e;
    color: #d4d4d4;
    font-family: ui-monospace, 'Cascadia Code', 'Consolas', monospace;
    font-size: 14px;
    line-height: 1.5;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #333;
    resize: vertical;
    tab-size: 4;
}
.code-editor:focus { outline: none; border-color: #2a63d6; }

.method-list { display: flex; flex-direction: column; gap: 4px; }
.method-link { 
    padding: 8px 12px; background: #fff; border: 1px solid #e0e4eb; border-radius: 4px; 
    color: #333; text-decoration: none; font-family: ui-monospace, Consolas, monospace; font-size: 13px;
}
.method-link:hover { background: #f0f4ff; border-color: #c6d6f5; }
.method-link.active { background: #2a63d6; color: #fff; border-color: #2a63d6; font-weight: bold; }
</style>
