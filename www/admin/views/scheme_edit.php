<?php
// Рекурсивная отрисовка структуры дерева в виде списка
function render_tree ( $nodes, $parent = 'root' ) {
    echo "<ul class='scheme-tree' data-parent='$parent'>";
    foreach ( $nodes as $n ) {
        $class = $n['class'] ?? 'node';
        $name  = str_replace('dt_', '', $class);
        $name  = $name ?: $class;
        $enabled = $n['enabled'] ?? true;
        
        $checked = $enabled ? 'checked' : '';
        echo "<li class='tree-node' data-class='{$class}'>";
        echo "  <div class='node-header' draggable='true'>";
        echo "    <span class='drag-handle'>≡</span>";
        echo "    <label><input type='checkbox' class='node-enabled' {$checked}> {$name}</label>";
        echo "    <code class='node-code'>$class</code>";
        echo "  </div>";
        
        if ( !empty($n['children']) ) {
            render_tree( $n['children'], $class );
        }
        echo "</li>";
    }
    echo "</ul>";
}

// Рекурсивная отрисовка Wireframe (вложенные рамки)
function render_wireframe ( $nodes ) {
    foreach ( $nodes as $n ) {
        $class = $n['class'] ?? '';
        if ( !$class ) continue;
        $enabled = $n['enabled'] ?? true;
        
        $name  = str_replace('dt_', '', $class);
        $name  = $name ?: $class;

        if ( $enabled ) {
            echo "<div style='display:flex; flex-direction:column; flex:1; border:2px dashed #2a63d6; background:rgba(42, 99, 214, 0.05); padding:10px; border-radius:6px; margin:4px; min-width:80px; text-align:center;'>";
            echo "<div style='font-size:12px; color:#2a63d6; font-weight:bold; margin-bottom:8px;'>$name</div>";
            
            if ( !empty($n['children']) ) {
                echo "<div style='display:flex; gap:8px; flex:1; align-items:stretch;'>";
                render_wireframe( $n['children'] );
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div style='display:flex; flex-direction:column; flex:1; border:2px solid #ddd; background:#fafafa; padding:10px; border-radius:6px; margin:4px; color:#aaa; font-size:12px; text-align:center;'>$name (выкл)</div>";
        }
    }
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h1 style="margin:0;">Схема: <code><?= htmlspecialchars( $scheme ) ?></code></h1>
    <a href="?p=blocks:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">← Назад</a>
</div>

<?php if ( !empty($_GET['saved']) ) : ?><div class="ok-msg">✓ Сохранено</div><?php endif ?>

<form method="post" id="scheme-form" style="display:flex; gap:20px; align-items:flex-start;">
    <?= Csrf::field() ?>
    <input type="hidden" name="scheme_json" id="scheme_json">
    
    <div style="flex:1; background:#fff; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <h3 style="margin-top:0;">Дерево блоков</h3>
        <p class="hint" style="font-size:12px; color:#666; margin-bottom:15px;">Перетаскивайте блоки за иконку <b>≡</b>, чтобы менять вложенность.</p>
        
        <?php render_tree( $scheme_data['children'] ); ?>
        
        <button type="submit" style="margin-top:20px;">Сохранить схему</button>
    </div>

    <div style="flex:2; background:#fff; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <h3 style="margin-top:0;">Визуализация</h3>
        <div style="display:flex; gap:10px; min-height:200px;">
            <?php render_wireframe( $scheme_data['children'] ); ?>
        </div>
    </div>
</form>

<style>
/* Стили для дерева */
.scheme-tree { list-style: none; padding-left: 20px; margin: 0; border-left: 1px dashed #ccc; }
.scheme-tree[data-parent="root"] { padding-left: 0; border-left: none; }
.tree-node { margin: 6px 0; }
.node-header { display: flex; align-items: center; gap: 8px; background: #f8f9fa; padding: 8px 12px; border: 1px solid #e0e4eb; border-radius: 4px; cursor: grab; }
.node-header:active { cursor: grabbing; }
.drag-handle { color: #999; font-size: 16px; cursor: grab; }
.node-code { font-size: 11px; color: #888; margin-left: auto; }
.dragging { opacity: 0.5; border: 2px dashed #2a63d6; }
.drag-over > .node-header { background: #e3f0ff; border-color: #2a63d6; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let dragged = null;

    const nodes = document.querySelectorAll('.node-header');
    nodes.forEach(header => {
        header.addEventListener('dragstart', function(e) {
            dragged = this.parentNode;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        header.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            dragged = null;
        });

        header.parentNode.addEventListener('dragover', function(e) {
            e.preventDefault(); // Разрешаем drop
            if (this === dragged || this.contains(dragged)) return;
            this.classList.add('drag-over');
        });

        header.parentNode.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });

        header.parentNode.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            if (this !== dragged && !this.contains(dragged)) {
                // Перемещаем DOM-элемент
                let ul = this.querySelector('.scheme-tree');
                if (!ul) {
                    ul = document.createElement('ul');
                    ul.className = 'scheme-tree';
                    ul.setAttribute('data-parent', this.dataset.class || '');
                    this.appendChild(ul);
                }
                ul.appendChild(dragged);
            }
        });
    });

    // Сборка JSON перед отправкой
    document.getElementById('scheme-form').addEventListener('submit', function(e) {
        // Восстанавливаем исходную структуру JSON из DOM
        function buildTree(ul) {
            let arr = [];
            ul.querySelectorAll(':scope > .tree-node').forEach(li => {
                let classStr = li.dataset.class;
                let enabled = li.querySelector('.node-enabled').checked;
                let node = { tag: 'div', class: classStr, return: true, enabled: enabled };
                
                let childUl = li.querySelector(':scope > .scheme-tree');
                if (childUl) node.children = buildTree(childUl);
                arr.push(node);
            });
            return arr;
        }

        let rootUl = document.querySelector('.scheme-tree[data-parent="root"]');
        let children = buildTree(rootUl);
        
        // Формируем итоговый массив как в data.php
        let finalData = {
            tag: 'div',
            class: 'dt',
            return: true,
            children: children
        };
        
        // Проставляем корректный return (false для листьев, true для родителей)
        function fixReturn(nodes) {
            nodes.forEach(n => {
                if (n.children) {
                    n.return = true;
                    fixReturn(n.children);
                } else {
                    n.return = false;
                }
            });
        }
        fixReturn(finalData.children);

        document.getElementById('scheme_json').value = JSON.stringify(finalData);
    });
});
</script>
