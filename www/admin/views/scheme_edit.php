<?PHP
function render_node ( $n, $domain, $is_root = false ) {
    $class   = $n['class']   ?? 'node';
    $layout  = $n['layout']  ?? 'vertical';
    $name    = str_replace('dt_', '', $class) ?: $class;
    $enabled = $n['enabled'] ?? true;
    
    $checked   = $enabled ? 'checked' : '';
    $sel_v     = $layout === 'vertical'   ? 'selected' : '';
    $sel_h     = $layout === 'horizontal' ? 'selected' : '';
    $draggable = $is_root ? 'false' : 'true';
    $handle    = $is_root ? '<span style="width:16px; display:inline-block;"></span>' : '<span class="drag-handle">≡</span>';
    
    $root_class = $is_root ? ' tree-root' : '';
    $badge      = $is_root ? ' <span class="root-badge">(Контейнер)</span>' : '';
    
    $is_layout = ($n['type'] ?? 'block') === 'layout';
    $block_exists = true;
    if ( $is_layout ) {
        $badge = ' <span style="color:#888; font-size:11px;">(Визуальный)</span>';
    } elseif ( ! $is_root && $class !== 'dt' ) {
        $block_name = str_replace('dt_', '', $class);
        if ( ! is_dir( Paths::block( $domain , $block_name ) ) ) {
            $block_exists = false;
            $badge = ' <span style="color:#a11; font-size:11px;">(удалён)</span>';
        }
    }

    echo "<li class='tree-node{$root_class} " . (!$block_exists ? 'block-missing' : '') . "' data-class='{$class}'>";
    echo "  <div class='node-header' draggable='{$draggable}'>";
    echo "    {$handle}";
    echo "    <label><input type='checkbox' class='node-enabled' {$checked} " . ($is_root ? 'disabled' : '') . "> <b>{$name}</b>{$badge}</label>";
    echo "    <select class='node-layout' style='margin-left:10px; font-size:12px; padding:2px; border-radius:3px; border:1px solid #ccc;'>";
    echo "      <option value='vertical' {$sel_v}>↓ Вертикально</option>";
    echo "      <option value='horizontal' {$sel_h}>→ Горизонтально</option>";
    echo "    </select>";
    echo "    <code class='node-code'>{$class}</code>";
    echo "  </div>";
    
    if ( !empty($n['children']) ) {
        echo "<ul class='scheme-tree'>";
        foreach ( $n['children'] as $child ) {
            render_node( $child, $domain, false );
        }
        echo "</ul>";
    } else {
        echo "<ul class='scheme-tree' style='min-height:10px;'></ul>";
    }
    echo "</li>";
}

function render_wireframe ( $nodes ) {
    foreach ( $nodes as $n ) {
        $class   = $n['class']   ?? '';
        if ( !$class ) continue;
        $enabled = $n['enabled'] ?? true;
        $layout  = $n['layout']  ?? 'vertical';
        $name    = str_replace('dt_', '', $class) ?: $class;

        if ( $enabled ) {
            echo "<div style='display:flex; flex-direction:column; flex:1; box-sizing:border-box; border:2px dashed #2a63d6; background:rgba(42, 99, 214, 0.05); padding:10px; border-radius:6px; margin:4px; min-width:80px; text-align:center;'>";
            echo "<div style='font-size:12px; color:#2a63d6; font-weight:bold; margin-bottom:8px;'>$name</div>";
            
            if ( !empty($n['children']) ) {
                $flex_dir = $layout === 'horizontal' ? 'row' : 'column';
                echo "<div style='display:flex; flex-direction:$flex_dir; gap:8px; flex:1; align-items:stretch;'>";
                render_wireframe( $n['children'] );
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div style='display:flex; flex-direction:column; flex:1; box-sizing:border-box; border:2px solid #ddd; background:#fafafa; padding:10px; border-radius:6px; margin:4px; color:#aaa; font-size:12px; text-align:center;'>$name (выкл)</div>";
        }
    }
}
?>

<?PHP if ( !empty($_GET['saved']) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?PHP endif ?>

<div style="display: flex; gap: 20px; align-items: flex-start; margin-top: 20px;">
    
    <!-- Сайдбар со списком схем -->
    <div style="width: 220px; flex-shrink: 0; background: var(--surface); padding: 15px; border-radius: var(--radius); box-shadow: var(--shadow-sm);">
        <h3 style="margin-top: 0; font-size: 14px; text-transform: uppercase; color: var(--text-dim); margin-bottom: 10px;">Все схемы</h3>
        <ul class="scheme-list">
            <?PHP foreach ( $schemes as $s ) : ?>
                <li>
                    <a href="?p=scheme:edit&domain=<?= urlencode($domain) ?>&scheme=<?= urlencode($s) ?>" 
                       class="<?= $s === $scheme ? 'active' : '' ?>">
                        <?= htmlspecialchars($s) ?>
                    </a>
                </li>
            <?PHP endforeach ?>
        </ul>
    </div>

    <!-- Редактор и Превью -->
    <div style="flex: 1; min-width: 0;">
        <form method="post" id="scheme-form" style="display:flex; gap:20px; align-items:flex-start;">
            <?= Csrf::field() ?>
            <input type="hidden" name="scheme_json" id="scheme_json">
            
            <div style="flex:1; background:var(--surface); padding:20px; box-shadow:var(--shadow-sm); border-radius:var(--radius);">
                <h3 style="margin-top:0;">Дерево блоков</h3>
                <p class="hint" style="margin-bottom:15px;">
                    Перетаскивайте за <b>≡</b>. Верхняя/нижняя часть — вставка рядом. Центр — вложение внутрь.
                </p>
                
                <ul class="scheme-tree-root">
                    <?PHP render_node( $scheme_data, $domain, true ); ?>
                </ul>
                
                <button type="submit" class="btn-primary" style="margin-top:20px; padding:10px 20px; font-size:15px;">Сохранить схему</button>
            </div>

            <div style="flex:1; background:var(--surface); padding:20px; box-shadow:var(--shadow-sm); border-radius:var(--radius);">
                <h3 style="margin-top:0;">Визуализация</h3>
                <div style="display:flex; flex-direction:column; flex:1; min-height:200px; box-sizing:border-box;">
                    <?PHP render_wireframe( [ $scheme_data ] ); ?>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Стили для списка схем */
.scheme-list { list-style: none; padding: 0; margin: 0; }
.scheme-list li { margin-bottom: 4px; }
.scheme-list a { display: block; padding: 8px 12px; border-radius: 4px; color: var(--text); text-decoration: none; font-weight: 500; font-size: 14px; transition: background 0.15s; }
.scheme-list a:hover { background: #f1f5f9; }
.scheme-list a.active { background: var(--primary-bg); color: var(--primary); font-weight: 600; }

/* Стили для дерева схемы */
.scheme-tree-root { list-style: none; padding: 0; margin: 0; }
.tree-root { background: #eef2f6; border: 2px solid #c9cdd6; border-radius: 8px; padding: 15px; margin-top: 0; }
.tree-root > .scheme-tree { padding-left: 10px; border-left: none; margin-top: 10px; }
.tree-root > .node-header { background: #fff; border-bottom: 3px solid #2a63d6; cursor: default; }
.root-badge { font-size: 11px; color: #555; font-weight: normal; margin-left: 5px; background: #dde3ec; padding: 2px 6px; border-radius: 4px; }
.scheme-tree { list-style: none; padding-left: 20px; margin: 0; border-left: 2px dashed #c9cdd6; min-height: 10px; }
.tree-node { margin: 6px 0; position: relative; }
.node-header { display: flex; align-items: center; gap: 8px; background: #fff; padding: 8px 12px; border: 1px solid #e0e4eb; border-radius: 4px; cursor: grab; }
.node-header:active { cursor: grabbing; }
.drag-handle { color: #999; font-size: 16px; cursor: grab; }
.node-code { font-size: 11px; color: #888; margin-left: auto; }
.dragging { opacity: 0.5; }
.block-missing > .node-header { background: #fdf2f2; border-color: #f5c6c6; color: #a11; }
.drop-above { border-top: 3px solid #2a63d6 !important; }
.drop-below { border-bottom: 3px solid #2a63d6 !important; }
.drop-inside > .node-header { background: #e3f0ff !important; border-color: #2a63d6 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let dragged = null;
    document.querySelectorAll('.node-header[draggable="true"]').forEach(header => {
        const handle = header.querySelector('.drag-handle');
        if (handle) {
            handle.addEventListener('mousedown', () => { header.setAttribute('draggable', 'true'); });
            header.addEventListener('mouseup', () => { if(!dragged) header.setAttribute('draggable', 'false'); });
            header.addEventListener('mouseleave', () => { if(!dragged) header.setAttribute('draggable', 'false'); });
        }
        header.addEventListener('dragstart', function(e) {
            dragged = this.closest('.tree-node');
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', 'drag');
        });
        header.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            dragged = null;
            document.querySelectorAll('.tree-node').forEach(el => el.classList.remove('drop-above', 'drop-below', 'drop-inside'));
        });
    });
    document.querySelectorAll('.node-header').forEach(header => {
        header.addEventListener('dragover', function(e) {
            e.preventDefault();
            const li = this.closest('.tree-node');
            if (li === dragged || li.contains(dragged)) return;
            const rect = this.getBoundingClientRect();
            const offset = e.clientY - rect.top;
            document.querySelectorAll('.tree-node').forEach(el => el.classList.remove('drop-above', 'drop-below', 'drop-inside'));
            if (offset < rect.height * 0.3) li.classList.add('drop-above');
            else if (offset > rect.height * 0.7) li.classList.add('drop-below');
            else li.classList.add('drop-inside');
        });
        header.addEventListener('dragleave', function(e) {
            const li = this.closest('.tree-node');
            if (!li.contains(e.relatedTarget)) li.classList.remove('drop-above', 'drop-below', 'drop-inside');
        });
        header.addEventListener('drop', function(e) {
            e.preventDefault();
            const li = this.closest('.tree-node');
            li.classList.remove('drop-above', 'drop-below', 'drop-inside');
            if (li === dragged || li.contains(dragged)) return;
            const rect = this.getBoundingClientRect();
            const offset = e.clientY - rect.top;
            if (offset < rect.height * 0.3) li.parentNode.insertBefore(dragged, li);
            else if (offset > rect.height * 0.7) li.parentNode.insertBefore(dragged, li.nextSibling);
            else {
                let ul = li.querySelector(':scope > .scheme-tree');
                if (!ul) { ul = document.createElement('ul'); ul.className = 'scheme-tree'; li.appendChild(ul); }
                ul.appendChild(dragged);
            }
            dragged = null;
        });
    });
    document.getElementById('scheme-form').addEventListener('submit', function() {
        function buildChildren(ul) {
            let arr = [];
            if (!ul) return arr;
            ul.querySelectorAll(':scope > .tree-node').forEach(li => {
                let classStr = li.dataset.class;
                let enabled = li.querySelector('.node-enabled').checked;
                let layout = li.querySelector('.node-layout').value;
                let node = { tag: 'div', class: classStr, layout: layout, enabled: enabled };
                let childUl = li.querySelector(':scope > .scheme-tree');
                let children = buildChildren(childUl);
                if (children.length > 0) node.children = children;
                arr.push(node);
            });
            return arr;
        }
        function fixReturn(nodes) {
            nodes.forEach(n => {
                if (n.children) { n.return = true; fixReturn(n.children); } else { n.return = false; }
            });
        }
        let rootNode = document.querySelector('.tree-root');
        let finalData = { tag: 'div', class: rootNode.dataset.class, layout: rootNode.querySelector('.node-layout').value, children: buildChildren(rootNode.querySelector(':scope > .scheme-tree')) };
        fixReturn(finalData.children);
        document.getElementById('scheme_json').value = JSON.stringify(finalData);
    });
});
</script>