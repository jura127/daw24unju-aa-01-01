<?php
require_once 'vendor/autoload.php';

// Configuración de Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

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
                'model' => 'gpt-5.2', // Nota: gpt-5.2 no existe todavía en el API público, asegúrate de usar gpt-4o o gpt-3.5-turbo
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

echo $twig->render('index.twig', [
    'hizkuntzak'  => $hizkuntzak,
    'data'        => $data,
    'errors'      => $errors,
    'message'     => $message,
    'translation' => $translation,
    'has_errors'  => (bool)implode($errors)
]); // Aquí faltaba cerrar el paréntesis antes del punto y coma