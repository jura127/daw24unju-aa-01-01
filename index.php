<?php
// ... [Mantenemos tu lógica PHP intacta al principio del archivo] ...
require_once 'vendor/autoload.php';

function is_text($text, $min, $max) {
    $length = strlen($text);
    return $length >= $min && $length <= $max;
}

$hizkuntzak = [
    'euskera'   => 'Euskera',
    'gaztelera' => 'Gaztelera',
    'ingelesa'  => 'Ingelesa',
];

$data = ['testua' => '', 'hizkuntza' => ''];
$errors = ['testua' => '', 'hizkuntza' => ''];
$message = '';
$translation = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data['testua'] = $_POST['testua'] ?? '';
    $data['hizkuntza'] = $_POST['hizkuntza'] ?? '';

    $errors['testua'] = is_text(trim($data['testua']), 1, 500) ? '' : 'Itzuli beharreko testua falta da.';
    $errors['hizkuntza'] = array_key_exists($data['hizkuntza'], $hizkuntzak) ? '' : 'Aukeratu hizkuntza bat.';

    if (!implode($errors)) {
        try {
            $client = OpenAI::client(getenv('OPENAI_API_KEY'));
            $target_lang = match ($data['hizkuntza']) {
                'gaztelera' => 'Spanish',
                'euskera' => 'Basque',
                'ingelesa' => 'English'
            };

            $result = $client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Translate the following text into ' . $target_lang . '. Respond with just the translated text.'],
                    ['role' => 'user', 'content' => $data['testua']],
                ],
            ]);

            $translation = $result->choices[0]->message->content;
        } catch (\Exception $e) {
            $message = 'Errorea: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testuen itzultzailea</title>
    <style>
        /* Estilos para que se vea igual a la foto */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f7f8fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 500px;
            border: 1px solid #e1e4e8;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 40px;
            margin-right: 15px;
        }

        .header h1 {
            color: #007bff;
            font-size: 32px;
            margin: 0;
        }

        .input-group {
            margin-bottom: 15px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        select {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f1f3f4;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 40px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .output-box {
            width: 100%;
            height: 200px;
            border: 1px solid #eee;
            border-radius: 4px;
            background-color: #fafafa;
            padding: 15px;
            box-sizing: border-box;
            font-size: 16px;
            color: #333;
            overflow-y: auto;
        }

        .error-msg {
            color: #d93025;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="https://upload.wikimedia.org/wikipedia/commons/d/d7/Google_Translate_logo.svg" alt="logo">
        <h1>Testuen itzultzailea</h1>
    </div>

    <?php if ($message || implode($errors)): ?>
        <div class="error-msg">
            <?= $message ?: 'Mesedez, bete eremu guztiak zuzen.' ?>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <div class="input-group">
            <input type="text" name="testua" placeholder="Zer nahi duzu itzuli?" value="<?= htmlspecialchars($data['testua']) ?>">
        </div>

        <div class="controls">
            <select name="hizkuntza">
                <option value="" disabled <?= empty($data['hizkuntza']) ? 'selected' : '' ?>>Aukeratu hizkuntza</option>
                <?php foreach ($hizkuntzak as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($data['hizkuntza'] === $key) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit">Itzuli</button>
        </div>
    </form>

    <div class="output-box">
        <?= nl2br(htmlspecialchars($translation)) ?>
    </div>
</div>

</body>
</html>
