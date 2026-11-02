<?php
// Inicjalizacja zmiennych
$errors = [];
$formSubmitted = false;

$imie = '';
$klasa = '';
$sila = '';
$zrecznosc = '';
$inteligencja = '';
$hp = 0;

// Odbiór i walidacja danych w PHP (Backend)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $imie = isset($_POST['imie']) ? trim($_POST['imie']) : '';
    $klasa = isset($_POST['klasa']) ? trim($_POST['klasa']) : '';
    $sila = filter_input(INPUT_POST, 'sila', FILTER_VALIDATE_INT);
    $zrecznosc = filter_input(INPUT_POST, 'zrecznosc', FILTER_VALIDATE_INT);
    $inteligencja = filter_input(INPUT_POST, 'inteligencja', FILTER_VALIDATE_INT);

    // Walidacja imienia
    if (empty($imie)) {
        $errors[] = "Imię postaci nie może być puste.";
    }

    // Walidacja klasy
    $dozwoloneKlasy = ['Wojownik', 'Mag', 'Łotrzyk'];
    if (!in_array($klasa, $dozwoloneKlasy)) {
        $errors[] = "Wybierz poprawną klasę postaci.";
    }

    // Walidacja zakresów statystyk (1 - 20)
    if ($sila === false || $sila < 1 || $sila > 20) {
        $errors[] = "Siła musi być liczbą w zakresie od 1 do 20.";
    }
    if ($zrecznosc === false || $zrecznosc < 1 || $zrecznosc > 20) {
        $errors[] = "Zręczność musi być liczbą w zakresie od 1 do 20.";
    }
    if ($inteligencja === false || $inteligencja < 1 || $inteligencja > 20) {
        $errors[] = "Inteligencja musi być liczbą w zakresie od 1 do 20.";
    }

    // Jeśli brak błędów -> oblicz HP
    if (empty($errors)) {
        $formSubmitted = true;
        // Wzór: $hp = ($sila * 10) + ($zrecznosc * 2);
        $hp = ($sila * 10) + ($zrecznosc * 2);
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karta Postaci RPG</title>
    <style>
        :root {
            --bg-color: #1a1a1a;
            --card-bg: #2b2b2b;
            --text-color: #e0e0e0;
            --accent-color: #777;
            --border-color: #444;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: var(--card-bg);
            border: 4px solid var(--accent-color);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
            transition: border-color 0.3s ease;
        }

        h1, h2 {
            text-align: center;
            margin-top: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #f1c40f;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select {
            padding: 10px;
            background-color: #1c1c1c;
            border: 1px solid var(--border-color);
            color: #fff;
            border-radius: 5px;
            font-size: 1rem;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #f1c40f;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        button {
            flex: 1;
            padding: 12px;
            background-color: #c0392b;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            transition: background 0.2s ease;
        }

        button:hover {
            background-color: #e74c3c;
        }

        button.btn-random {
            background-color: #f39c12;
        }

        button.btn-random:hover {
            background-color: #d35400;
        }

        .error-box {
            background-color: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #ff6b6b;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .error-box ul {
            margin: 0;
            padding-left: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .stat-item {
            background-color: #1f1f1f;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #f1c40f;
            text-align: center;
        }

        .stat-item.full-width {
            grid-column: span 2;
            border-left-color: #e74c3c;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
            margin-top: 5px;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #f1c40f;
            text-decoration: none;
            font-weight: bold;
        }

        .back-btn:hover {
            text-decoration: underline;
        }

        .border-wojownik { border-color: #e74c3c !important; }
        .border-mag { border-color: #3498db !important; }
        .border-lotrzyk { border-color: #2ecc71 !important; }
    </style>
</head>
<body>

<div class="container" id="mainContainer">

    <?php if (!$formSubmitted): ?>
        <h1>Stwórz Postać RPG</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" id="rpgForm">
            <div class="form-group">
                <label for="imie">Imię postaci:</label>
                <input type="text" id="imie" name="imie" required value="<?= htmlspecialchars($imie) ?>">
            </div>

            <div class="form-group">
                <label for="klasa">Klasa:</label>
                <select id="klasa" name="klasa" required>
                    <option value="" disabled selected>-- Wybierz klasę --</option>
                    <option value="Wojownik" <?= $klasa === 'Wojownik' ? 'selected' : '' ?>>Wojownik</option>
                    <option value="Mag" <?= $klasa === 'Mag' ? 'selected' : '' ?>>Mag</option>
                    <option value="Łotrzyk" <?= $klasa === 'Łotrzyk' ? 'selected' : '' ?>>Łotrzyk</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sila">Siła (1 - 20):</label>
                <input type="number" id="sila" name="sila" min="1" max="20" required value="<?= htmlspecialchars($sila) ?>">
            </div>

            <div class="form-group">
                <label for="zrecznosc">Zręczność (1 - 20):</label>
                <input type="number" id="zrecznosc" name="zrecznosc" min="1" max="20" required value="<?= htmlspecialchars($zrecznosc) ?>">
            </div>

            <div class="form-group">
                <label for="inteligencja">Inteligencja (1 - 20):</label>
                <input type="number" id="inteligencja" name="inteligencja" min="1" max="20" required value="<?= htmlspecialchars($inteligencja) ?>">
            </div>

            <div class="button-group">
                <button type="button" class="btn-random" id="btnRandom">🎲 Losuj</button>
                <button type="submit">Generuj Kartę</button>
            </div>
        </form>

    <?php else: ?>
        <h2>Karta Postaci</h2>

        <div class="stats-grid">
            <div class="stat-item full-width">
                <div>Imię i Klasa</div>
                <div class="stat-value"><?= htmlspecialchars($imie) ?> (<?= htmlspecialchars($klasa) ?>)</div>
            </div>

            <div class="stat-item full-width">
                <div>Punkty Życia (HP)</div>
                <div class="stat-value" style="color: #e74c3c;"><?= $hp ?> HP</div>
            </div>

            <div class="stat-item">
                <div>Siła</div>
                <div class="stat-value"><?= $sila ?></div>
            </div>

            <div class="stat-item">
                <div>Zręczność</div>
                <div class="stat-value"><?= $zrecznosc ?></div>
            </div>

            <div class="stat-item full-width">
                <div>Inteligencja</div>
                <div class="stat-value"><?= $inteligencja ?></div>
            </div>
        </div>

        <a href="index.php" class="back-btn">← Stwórz nową postać</a>
    <?php endif; ?>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectKlasa = document.getElementById('klasa');
        const container = document.getElementById('mainContainer');
        const btnRandom = document.getElementById('btnRandom');
        const inputSila = document.getElementById('sila');
        const inputZrecznosc = document.getElementById('zrecznosc');
        const inputInteligencja = document.getElementById('inteligencja');

        function updateBorderColor() {
            if (!container || !selectKlasa) return;
            container.classList.remove('border-wojownik', 'border-mag', 'border-lotrzyk');
            const selectedValue = selectKlasa.value;

            if (selectedValue === 'Wojownik') {
                container.classList.add('border-wojownik');
            } else if (selectedValue === 'Mag') {
                container.classList.add('border-mag');
            } else if (selectedValue === 'Łotrzyk') {
                container.classList.add('border-lotrzyk');
            }
        }

        if (selectKlasa) {
            selectKlasa.addEventListener('change', updateBorderColor);
            updateBorderColor();
        }

        if (btnRandom) {
            btnRandom.addEventListener('click', () => {
                const getRandomInt = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
                
                inputSila.value = getRandomInt(1, 20);
                inputZrecznosc.value = getRandomInt(1, 20);
                inputInteligencja.value = getRandomInt(1, 20);

                const klasy = ['Wojownik', 'Mag', 'Łotrzyk'];
                const losowaKlasa = klasy[Math.floor(Math.random() * klasy.length)];
                selectKlasa.value = losowaKlasa;
                
                updateBorderColor();
            });
        }
    });
</script>

</body>
</html>