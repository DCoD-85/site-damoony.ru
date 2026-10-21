<?php
// Читаем конфиг языков
$lang_cfg_path = PROJECT_ROOT . '/config/lang.config';
$lang_cfg = is_file($lang_cfg_path) ? json_decode(file_get_contents($lang_cfg_path), true) : [];
$lang_default = $lang_cfg['default'] ?? 'ru-ru';

// Строим URL для предпросмотра
$host = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'www.damoony2.ru');
$url_path = ($allias === 'www' || $allias === '') ? '' : $allias . '/';
$url_path .= ($path === '') ? $lang_default . '/index' : str_replace('lang', $lang_default, $path);
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
    <a href="?p=page:code&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&path=<?= urlencode($path) ?>&tab=data" class="<?= $tab === 'data' ? 'active' : '' ?>">Данные (JSON)</a>
</div>

<div style="display:flex; gap:15px; height:75vh;">
    <!-- ЛЕВАЯ КОЛОНКА (Редактор) -->
    <div style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
        <?php if ( $tab === 'data' ) : ?>
            <form method="post" style="display:flex; flex-direction:column; height:100%;">
                <?= Csrf::field() ?>
                <input type="hidden" name="save_json" value="1">
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
                    <!-- Генератор сложных компонентов -->
                    <div style="background:#f8f9fa; border:1px solid #ddd; border-radius:6px; padding:12px; margin-bottom:10px;">
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <select id="comp-type" style="padding:5px; border-radius:4px; border:1px solid #ccc;" onchange="renderBuilder()">
                                <option value="table">Таблица (visuoel_table)</option>
                                <option value="form">Форма (visuoel_form)</option>
                            </select>
                            <select id="comp-block" style="padding:5px; border-radius:4px; border:1px solid #ccc;">
                                <option value="center">center</option>
                                <option value="top">top</option>
                                <option value="left">left</option>
                                <option value="right">right</option>
                                <option value="bottom">bottom</option>
                            </select>
                        </div>

                        <div id="builder-ui"></div>

                        <button type="button" class="btn-edit" onclick="insertComponentCode()" style="padding:5px 15px; margin-top:10px;">Вставить код</button>
                    </div>

                    <!-- Редактор кода -->
                    <form method="post" style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="save_method" value="1">
                        <textarea name="method_code" id="method_code_editor" class="code-editor" style="flex:1;" spellcheck="false"><?= htmlspecialchars( $method_code ) ?></textarea>
                        <button type="submit" class="btn-edit" style="margin-top:10px; padding:10px;">Сохранить функцию</button>
                    </form>
                <?php else : ?>
                    <div class="error-msg" style="flex:1; display:flex; align-items:center; justify-content:center;">Выберите функцию.</div>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>

    <!-- ПРАВАЯ КОЛОНКА (Live Preview) -->
    <div style="flex:1; display:flex; flex-direction:column; background:#fff; border-radius:6px; overflow:hidden; border:1px solid #ccc;">
        <div style="background:#f0f2f6; padding:8px 12px; font-size:12px; border-bottom:1px solid #ccc; display:flex; gap:10px; align-items:center;">
            <button type="button" onclick="document.getElementById('preview-frame').src = document.getElementById('preview-url').value + '?t=' + Date.now();" style="cursor:pointer; padding:4px 10px;">Обновить</button>
            <input type="text" id="preview-url" value="<?= htmlspecialchars( $preview_url ) ?>" style="flex:1; padding:4px 8px; border:1px solid #ccc; border-radius:4px; font-family:monospace;">
        </div>
        <iframe id="preview-frame" src="<?= htmlspecialchars( $preview_url ) ?>?t=<?= time() ?>" style="flex:1; width:100%; border:none;"></iframe>
    </div>
</div>

<style>
.code-editor { width: 100%; background: #1e1e1e; color: #d4d4d4; font-family: monospace; font-size: 14px; padding: 15px; border-radius: 6px; border: 1px solid #333; resize: none; }
.method-link { background: #fff; border: 1px solid #e0e4eb; border-radius: 4px; color: #333; padding: 4px 10px; font-size: 12px; text-decoration: none; font-family: monospace; }
.method-link.active { background: #2a63d6; color: #fff; border-color: #2a63d6; }
.grid-input { width: 100%; border: 1px solid #ccc; padding: 5px; border-radius: 3px; box-sizing: border-box; }
</style>

<script>
function renderBuilder() {
    let type = document.getElementById('comp-type').value;
    let ui = document.getElementById('builder-ui');

    if (type === 'table') {
        ui.innerHTML = `
            <table style="width:100%; border-collapse:collapse; margin-bottom:10px;" id="table-grid">
                <tr>
                    <td><input class="grid-input" value="Заголовок 1"></td>
                    <td><input class="grid-input" value="Заголовок 2"></td>
                </tr>
                <tr>
                    <td><input class="grid-input" value="Значение 1"></td>
                    <td><input class="grid-input" value="Значение 2"></td>
                </tr>
            </table>
            <button type="button" onclick="addTableRow()" style="padding:2px 8px; font-size:12px;">+ Строка</button>
        `;
    } else {
        ui.innerHTML = `
            <div style="margin-bottom:10px;">
                <input class="grid-input" id="form-name" placeholder="Имя формы (например, login)" style="margin-bottom:5px;" value="my_form">
                <input class="grid-input" placeholder="Поля через запятую (например: login, password, submit)">
            </div>
        `;
    }
}

function addTableRow() {
    let table = document.getElementById('table-grid');
    let cols = table.rows[0].cells.length;
    let tr = table.insertRow(-1);
    for (let i=0; i<cols; i++) {
        let td = tr.insertCell(i);
        td.innerHTML = '<input class="grid-input" value="">';
    }
}

function insertComponentCode() {
    let type = document.getElementById('comp-type').value;
    let block = document.getElementById('comp-block').value;
    let code = "\n\t\t";

    if (type === 'table') {
        let table = document.getElementById('table-grid');
        
        code += `$table = new \\visuoel();\n\t\t`;
        code += `$table = $table->table()->table();\n\t\t`;
        code += `$table->html_mode(true);\n\t\t`;
        code += `$head = $table->head();\n\t\t`;
        code += `$body = $table->body();\n\t\t`;
        
        for (let r = 0; r < table.rows.length; r++) {
            let row = table.rows[r];
            if (r === 0) code += `$row = $head->row();\n\t\t`;
            else code += `$row = $body->row();\n\t\t`;
            
            for (let c = 0; c < row.cells.length; c++) {
                let val = row.cells[c].querySelector('input').value;
                val = val.replace(/'/g, "\\'");
                code += `$row->cell()->set('${val}');\n\t\t`;
            }
        }
    } else {
        let formName = document.getElementById('form-name').value || 'my_form';
        code += `$form = new \\visuoel();\n\t\t`;
        code += `$form = $form->form()->form();\n\t\t`;
        code += `$form->data()->set( '${formName}' );\n\t\t`;
    }
    
    // Сборка контейнера visuotag и вставка в блок
    let elVar = (type === 'table') ? '$table' : '$form';
    code += `$vt = new \\visuotag();\n\t\t`;
    code += `$vt->el( ${elVar} );\n\t\t`;
    
    code += `$center = $main->alliasglobal->${block};\n\t\t`;
    code += `$center->wrapper_set( 'div' );\n\t\t`;
    code += `$center->section_add( 'auto_${Date.now()}' , $vt );\n\t`;

    // Поиск места вставки (перед return или перед закрывающей скобкой)
    let textarea = document.getElementById('method_code_editor');
    let val = textarea.value;
    let pos = val.lastIndexOf('return');
    
    if (pos === -1) {
        // Если return нет, вставляем перед последней закрывающей скобкой
        pos = val.lastIndexOf('}');
    }
    
    if (pos !== -1) {
        textarea.value = val.slice(0, pos) + code + '\n\t\t' + val.slice(pos);
    } else {
        textarea.value += code;
    }
}

renderBuilder();
</script>
