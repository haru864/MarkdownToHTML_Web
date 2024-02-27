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
    </style>
</head>

<body>
    <div class="container">
        <div id="editor-container" style="width:800px;height:600px;border:1px solid grey"></div>
        <div id="htmlPreview" style="width:800px;height:600px;border:1px solid grey"></div>
    </div>
    <button type="button" id="download-btn">Download</button>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.min.js"></script>
    <script>
        require.config({
            paths: {
                'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs'
            }
        });
        require(['vs/editor/editor.main'], function () {
            window.editor = monaco.editor.create(document.getElementById('editor-container'), {
                value: '',
                language: 'markdown'
            });
            window.editor.onDidChangeModelContent(async function () {
                var markdownText = editor.getValue();
                var htmlContent = await convertMarkdownToHTML();
                document.getElementById('htmlPreview').innerHTML = htmlContent;
            });
        });
        async function convertMarkdownToHTML() {
            try {
                var details = { 'md_src': window.editor.getValue() };
                var formBody = [];
                for (var property in details) {
                    var encodedKey = encodeURIComponent(property);
                    var encodedValue = encodeURIComponent(details[property]);
                    formBody.push(encodedKey + "=" + encodedValue);
                }
                formBody = formBody.join("&");
                const response = await fetch('http://localhost:8081', {
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
        document.getElementById("download-btn").addEventListener("click", async function () {
            try {
                var details = { 'action': 'download', 'md_src': window.editor.getValue() };
                var formBody = [];
                for (var property in details) {
                    var encodedKey = encodeURIComponent(property);
                    var encodedValue = encodeURIComponent(details[property]);
                    formBody.push(encodedKey + "=" + encodedValue);
                }
                formBody = formBody.join("&");
                const response = await fetch('http://localhost:8081', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formBody
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'MarkdownToHTML.html';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
</body>

</html>