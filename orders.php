<?php
require "config/connect.php";

// Pokud je odeslán POST požadavek pro aktualizaci pořadí
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'update_position' && isset($data['order'])) {
        try {
            $pdo->beginTransaction();
            foreach ($data['order'] as $index => $hobby) {
                $stmt = $pdo->prepare("UPDATE golemos_hobbies SET position = :position WHERE id = :id");
                $stmt->execute([
                    ':position' => $index + 1,
                    ':id' => $hobby['id']
                ]);
            }
            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }
}

include "header.php";
?><main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <h2>Správa koníčků</h2>
    <table class="table table-striped">
        <tbody id="hobby-table">
        <?php
        $stmt = $pdo->query("SELECT * FROM golemos_hobbies ORDER BY position ASC");
        while ($hobby = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr class='hobby-item' data-id='{$hobby['id']}' draggable='true'>
                    <td>{$hobby['name']}</td>
                    <td class='position'>Pozice: {$hobby['position']}</td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.getElementById('hobby-table');
        let draggedRow = null;

        tableBody.addEventListener('dragstart', (e) => {
            if (e.target.classList.contains('hobby-item')) {
                draggedRow = e.target;
                e.dataTransfer.setData("text/plain", draggedRow.dataset.id);
                setTimeout(() => draggedRow.classList.add('dragging'), 0);
            }
        });

        tableBody.addEventListener('dragover', (e) => {
            e.preventDefault();
            const afterElement = getDragAfterElement(tableBody, e.clientY);
            if (afterElement == null) {
                tableBody.appendChild(draggedRow);
            } else {
                tableBody.insertBefore(draggedRow, afterElement);
            }
        });

        tableBody.addEventListener('dragend', () => {
            draggedRow.classList.remove('dragging');
            updateOrder();
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.hobby-item:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                return offset < 0 && offset > closest.offset ? { offset, element: child } : closest;
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        function updateOrder() {
            const newOrder = [];
            document.querySelectorAll('.hobby-item').forEach((row, index) => {
                row.querySelector('.position').textContent = `Pozice: ${index + 1}`;
                newOrder.push({ id: row.dataset.id, position: index + 1 });
            });

            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update_position', order: newOrder })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Chyba: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Chyba při odesílání:', error);
                    alert('Chyba při odesílání');
                });
        }
    });
</script>

</body>
</html>
