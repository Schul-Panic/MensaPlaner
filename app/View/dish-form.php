<?php

use App\Model\Dish;

require __DIR__ . '/layout/header.php';

$isEdit = isset($dish);
$action = $isEdit ? "/speiseplan/verwaltung/{$dish['id']}" : '/speiseplan/verwaltung';
$selectedCategory = $isEdit ? $dish['category'] : ($prefillCategory ?? null);
?>

<h1><?php echo $isEdit ? 'Gericht bearbeiten' : 'Gericht hinzufügen'; ?></h1>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
<?php endif; ?>

<div class="card">
    <form class="login-form" method="post" action="<?php echo $action; ?>">
        <label>
            Kategorie
            <select name="category" required>
            <?php foreach (Dish::ROW_LABELS as $value => $label): ?>
                <option value="<?php echo htmlspecialchars($value); ?>" <?php echo ($selectedCategory === $value) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($label); ?>
                </option>
            <?php endforeach; ?>
            </select>
        </label>
        <label>
            Variante
            <select name="variant" required>
            <?php foreach (Dish::COLUMN_LABELS as $value => $label): ?>
                <option value="<?php echo htmlspecialchars($value); ?>" <?php echo ($isEdit && $dish['variant'] === $value) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($label); ?>
                </option>
            <?php endforeach; ?>
            </select>
        </label>
        <label>
            Name
            <input type="text" name="name" placeholder="z. B. Gulasch mit Spätzle" value="<?php echo htmlspecialchars($dish['name'] ?? ''); ?>" required>
        </label>
        <label>
            Preis
            <input type="text" name="price" placeholder="z. B. 3,50 oder 3.5" value="<?php echo htmlspecialchars($dish['price'] ?? ''); ?>" required>
        </label>
        <label>
            Allergene
            <div class="label-picker" id="label-picker">
            <?php foreach ($allAllergens as $allergen): ?>
                <button type="button" class="label-pill<?php echo in_array($allergen, $selectedAllergens, true) ? ' label-pill--active' : ''; ?>" data-label="<?php echo htmlspecialchars($allergen); ?>">
                    <?php echo htmlspecialchars($allergen); ?>
                </button>
            <?php endforeach; ?>
                <button type="button" class="label-pill label-pill--add" id="label-add-button">+</button>
            </div>
            <input type="hidden" name="labels" id="labels-hidden" value="<?php echo htmlspecialchars(implode(',', $selectedAllergens)); ?>">
        </label>
        <button type="submit"><?php echo $isEdit ? 'Speichern' : 'Hinzufügen'; ?></button>
    </form>

    <p class="switch-auth"><a href="/speiseplan/verwaltung">Zurück zur Übersicht</a></p>
</div>

<script>
(function () {
    var picker = document.getElementById('label-picker');
    var hidden = document.getElementById('labels-hidden');
    var addButton = document.getElementById('label-add-button');

    function syncHidden() {
        var active = Array.prototype.slice.call(picker.querySelectorAll('.label-pill--active')).map(function (el) {
            return el.dataset.label;
        });
        hidden.value = active.join(',');
    }

    function addNewLabel(value) {
        value = value.trim();
        if (!value) {
            return;
        }

        var existing = Array.prototype.slice.call(picker.querySelectorAll('.label-pill:not(.label-pill--add)')).find(function (el) {
            return el.dataset.label.toLowerCase() === value.toLowerCase();
        });

        if (existing) {
            existing.classList.add('label-pill--active');
        } else {
            var pill = document.createElement('button');
            pill.type = 'button';
            pill.className = 'label-pill label-pill--active';
            pill.dataset.label = value;
            pill.textContent = value;
            picker.insertBefore(pill, addButton);
        }

        syncHidden();
    }

    function openAddInput() {
        var input = document.createElement('input');
        input.type = 'text';
        input.className = 'label-pill-input';
        input.placeholder = 'Label…';

        addButton.style.display = 'none';
        picker.insertBefore(input, addButton);
        input.focus();

        function closeInput() {
            addNewLabel(input.value);
            input.remove();
            addButton.style.display = '';
        }

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                closeInput();
            } else if (event.key === 'Escape') {
                input.value = '';
                closeInput();
            }
        });
        input.addEventListener('blur', closeInput);
    }

    picker.addEventListener('click', function (event) {
        var pill = event.target.closest('.label-pill');
        if (!pill) {
            return;
        }

        if (pill === addButton) {
            openAddInput();
        } else {
            pill.classList.toggle('label-pill--active');
            syncHidden();
        }
    });
})();
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
