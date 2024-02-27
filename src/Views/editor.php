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
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.4.0/highlight.min.js"></script>
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
                document.getElementById('html-preview').innerHTML = await convertMarkdownToHTML();
            });
        });
        async function convertMarkdownToHTML() {
            try {
                let details = {
                    'markdown': window.editor.getValue()
                };
                let formBody = [];
                for (let property in details) {
                    let encodedKey = encodeURIComponent(property);
                    let encodedValue = encodeURIComponent(details[property]);
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
        document.getElementById("preview-btn").addEventListener("click", async function() {
            document.getElementById('html-preview').innerHTML = await convertMarkdownToHTML();
        });
        document.getElementById("html-btn").addEventListener("click", async function() {
            document.getElementById('html-preview').innerHTML = await convertMarkdownToHTML();
            let htmlSrc = document.getElementById('html-preview').innerHTML;
            document.getElementById('html-preview').textContent = htmlSrc;
        });
        document.getElementById("highlight-btn").addEventListener("click", async function() {
            hljs.highlightAll();
        });
        document.getElementById("download-btn").addEventListener("click", async function() {
            let htmlSrc = document.getElementById('html-preview').innerHTML;
            let blob = new Blob([htmlSrc], {
                type: "text/html"
            });
            let url = URL.createObjectURL(blob);
            let a = document.createElement('a');
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