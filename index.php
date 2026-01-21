<?php
// --- KONFIGURAZIOA ---
// GARRANTZITSUA: Ez konpartitu gako hau publikoki, norbaitek zure kreditua gastatu dezake eta.
$apiKey = "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"; 
$res = $txt = $lang = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $txt = $_POST['testua'] ?? '';
    $lang = $_POST['idioma'] ?? '';

    // Baldintza sinplifikatua akatsak ekiditeko
    if (!empty($txt) && !empty($lang)) {
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        $data = [
            'model' => 'gpt-4o', 
            'messages' => [
                ['role' => 'system', 'content' => "Eres un traductor experto. Traduce el texto al idioma solicitado ($lang). Solo devuelve el texto traducido, sin comentarios."],
                ['role' => 'user', 'content' => $txt]
            ],
            'temperature' => 0.2
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $apiResponse = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $res = "CURL Errorea: " . curl_error($ch);
        } else {
            $responseArray = json_decode($apiResponse, true);
            if (isset($responseArray['choices'][0]['message']['content'])) {
                $res = $responseArray['choices'][0]['message']['content'];
            } else {
                // Errore mezu zehatzagoa OpenAI-k zerbait itzultzen badu (adibidez, krediturik gabe)
                $res = "Errorea: " . ($responseArray['error']['message'] ?? "Ezezaguna");
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Testuen itzultzailea</title>
</head>
<body>

    <h1>Testuen itzultzailea</h1>

    <form method="post">
        <table border="1" cellspacing="4" cellpadding="0">
            <tr>
                <td><font size="5">Testua</font></td>
                <td>
                    <textarea name="testua" style="width:300px; height:80px;" required><?=htmlspecialchars($txt)?></textarea>
                </td>
            </tr>
            <tr>
                <td align="right">
                    <input type="radio" name="idioma" value="Euskera" required <?=($lang=='Euskera')?'checked':''?>>
                </td>
                <td><font size="5">Euskera</font></td>
            </tr>
            <tr>
                <td align="right">
                    <input type="radio" name="idioma" value="Castellano" <?=($lang=='Castellano')?'checked':''?>>
                </td>
                <td><font size="5">Castellano</font></td>
            </tr>
            <tr>
                <td align="right">
                    <input type="radio" name="idioma" value="Inglés" <?=($lang=='Inglés')?'checked':''?>>
                </td>
                <td><font size="5">Inglés</font></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" value="Itzuli">
                </td>
            </tr>
        </table>
    </form>

    <?php if ($res): ?>
        <div style="margin-top:20px; border:1px solid #333; padding:15px; width:400px; background:#f9f9f9;">
            <font size="4"><strong>Emaitza:</strong></font><br>
            <font size="5" color="black"><?=htmlspecialchars($res)?></font>
        </div>
    <?php endif; ?>

</body>
</html>