<?php

use Settings\Settings;

$baseURL = Settings::env('BASE_URL');
?>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>HTML Sample</title>
    <style>
        .container {
            display: flex;
            align-items: center;
        }

        .container>div,
        .buttons>button {
            margin-bottom: 10px;
        }

        #html-preview {
            overflow: auto;
        }
    </style>
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
    <script>
        require.config({
            paths: {
                'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs'
            }
        });
        require(['vs/editor/editor.main'], function() {
            window.editor = monaco.editor.create(document.getElementById('editor-container'), {
                value: '',
                language: 'markdown'
            });
            window.editor.onDidChangeModelContent(async function() {
                var markdownText = editor.getValue();
                var htmlContent = await convertMarkdownToHTML();
                document.getElementById('html-preview').innerHTML = htmlContent;
            });
        });
        async function convertMarkdownToHTML() {
            try {
                var details = {
                    'markdown': window.editor.getValue()
                };
                var formBody = [];
                for (var property in details) {
                    var encodedKey = encodeURIComponent(property);
                    var encodedValue = encodeURIComponent(details[property]);
                    formBody.push(encodedKey + "=" + encodedValue);
                }
                formBody = formBody.join("&");
                const response = await fetch('<?= $baseURL ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formBody
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                return data["html"];
            } catch (error) {
                console.error('Error:', error);
            }
        }
        document.getElementById("download-btn").addEventListener("click", async function() {
            let htmlSrc = document.getElementById('html-preview').innerHTML;
            var blob = new Blob([htmlSrc], {
                type: "text/html"
            });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = "download.html";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    </script>
</body>

</html>