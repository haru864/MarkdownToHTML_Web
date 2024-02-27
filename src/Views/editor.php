<?php

use Settings\Settings;

$baseURL = Settings::env('BASE_URL');
?>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>HTML Sample</title>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.4.0/styles/default.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $baseURL ?>/css/style" />
</head>

<body>
    <div class="container">
        <div id="editor-container" style="width:800px;height:600px;border:1px solid grey"></div>
        <div id="html-preview" style="width:800px;height:600px;border:1px solid grey"></div>
    </div>
    <button type="button" id="preview-btn">Preview</button>
    <button type="button" id="html-btn">HTML</button>
    <button type="button" id="highlight-btn">Highlight</button>
    <button type="button" id="download-btn">Download</button>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.4.0/highlight.min.js"></script>
    <script>
        const BASE_URL = '<?= $baseURL ?>';
    </script>
    <script src="<?= $baseURL ?>/js/editor-logic"></script>
</body>

</html>