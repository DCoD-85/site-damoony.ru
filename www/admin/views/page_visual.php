<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h1 style="margin:0;">Визуальный редактор: <code><?= htmlspecialchars( $cls_short ) ?></code></h1>
    <div class="td-actions">
        <a href="?p=page:code&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>&path=<?= urlencode( $path ) ?>" class="btn-edit">Код PHP</a>
        <a href="?p=pages:tree&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>" class="btn-cancel">← К страницам</a>
    </div>
</div>

<?php if ( !empty($_GET['saved']) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<form method="post" id="visual-form" style="display:flex; gap:15px; height:75vh;">
    <?= Csrf::field() ?>
    <input type="hidden" name="json_code" id="json_code">
    
    <!-- ЛЕВАЯ КОЛОНКА (Дерево) -->
    <div style="flex:1; background:#fff; padding:15px; border:1px solid #ccc; border-radius:6px; display:flex; flex-direction:column; overflow-y:auto;">
        <div style="display:flex; gap:5px; margin-bottom:15px; flex-wrap:wrap;">
            <button type="button" class="btn-edit" onclick="addNode('h1', 'Заголовок')">+ H1</button>
            <button type="button" class="btn-edit" onclick="addNode('p', 'Абзац текста')">+ P</button>
            <button type="button" class="btn-edit" onclick="addNode('div', '')">+ DIV</button>
            <button type="button" class="btn-edit" onclick="addNode('a', 'Ссылка')">+ A</button>
        </div>
        
        <div id="tree-container" style="flex:1;"></div>
        
        <button type="submit" class="btn-edit" style="margin-top:15px; padding:12px; font-weight:bold;">Сохранить и Опубликовать</button>
    </div>

    <!-- ПРАВАЯ КОЛОНКА (Preview) -->
    <div style="flex:2; background:#fff; border:1px solid #ccc; border-radius:6px; overflow:hidden; display:flex; flex-direction:column;">
        <div style="background:#f0f2f6; padding:8px 12px; font-size:12px; border-bottom:1px solid #ccc; display:flex; gap:10px; align-items:center;">
            <span style="font-weight:bold; color:#555;">Предпросмотр:</span>
            <input type="text" id="preview-url" value="<?= htmlspecialchars( $preview_url ) ?>" style="flex:1; padding:4px 8px; border:1px solid #ccc; border-radius:4px; font-family:monospace;">
            <button type="button" onclick="document.getElementById('preview-frame').src = document.getElementById('preview-url').value + '?t=' + Date.now();" style="cursor:pointer; padding:4px 10px;">Обновить</button>
        </div>
        <iframe id="preview-frame" src="<?= htmlspecialchars( $preview_url ) ?>?t=<?= time() ?>" style="flex:1; width:100%; border:none;"></iframe>
    </div>
</form>

<style>
.visual-node { margin: 4px 0; padding: 8px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; position:relative; }
.visual-node:hover { border-color: #2a63d6; }
.visual-node-header { display:flex; gap:10px; align-items:center; font-family:monospace; font-size:13px; }
.visual-node-header b { color: #2a63d6; }
.visual-node input { flex:1; padding:4px; border:1px solid #ccc; border-radius:3px; font-size:12px; }
.btn-del { cursor:pointer; color:#a11; background:none; border:none; font-size:16px; padding:0 5px; }
.visual-children { margin-left:20px; border-left:2px dashed #ccc; padding-left:10px; margin-top:8px; }
</style>

<script>
let treeData = JSON.parse(<?= json_encode( $json_data ) ?>);
if (!treeData.children) treeData.children = [];

function renderTree(container, nodes) {
    container.innerHTML = '';
    nodes.forEach((node, index) => {
        let div = document.createElement('div');
        div.className = 'visual-node';
        
        let header = document.createElement('div');
        header.className = 'visual-node-header';
        
        let tagBadge = document.createElement('b');
        tagBadge.textContent = node.tag ? node.tag.toUpperCase() : 'DIV';
        header.appendChild(tagBadge);
        
        // Поле для контента (только для h1, p, a, span)
        if (['h1','h2','h3','p','a','span','button'].includes(node.tag)) {
            let input = document.createElement('input');
            input.value = node.content || '';
            input.placeholder = 'Текст...';
            input.oninput = (e) => { node.content = e.target.value; };
            header.appendChild(input);
        }
        
        // Кнопка удаления
        let del = document.createElement('button');
        del.className = 'btn-del';
        del.innerHTML = '✖';
        del.onclick = () => { nodes.splice(index, 1); renderTree(container, nodes); };
        header.appendChild(del);
        
        div.appendChild(header);
        
        // Дети (рекурсия)
        if (node.children) {
            let childContainer = document.createElement('div');
            childContainer.className = 'visual-children';
            div.appendChild(childContainer);
            renderTree(childContainer, node.children);
        }
        
        container.appendChild(div);
    });
}

function addNode(tag, defaultContent) {
    // Добавляем в корень для простоты
    treeData.children.push({ tag: tag, class: '', content: defaultContent, children: [] });
    renderTree(document.getElementById('tree-container'), treeData.children);
}

document.getElementById('visual-form').addEventListener('submit', function() {
    document.getElementById('json_code').value = JSON.stringify(treeData, null, 2);
});

// Инициализация
renderTree(document.getElementById('tree-container'), treeData.children);
</script>
