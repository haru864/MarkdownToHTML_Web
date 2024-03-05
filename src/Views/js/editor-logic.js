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
        const response = await fetch(BASE_URL, {
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
document.getElementById("preview-btn").addEventListener("click", async function () {
    document.getElementById('html-preview').innerHTML = await convertMarkdownToHTML();
});
document.getElementById("html-btn").addEventListener("click", async function () {
    document.getElementById('html-preview').innerHTML = await convertMarkdownToHTML();
    let htmlSrc = document.getElementById('html-preview').innerHTML;
    document.getElementById('html-preview').innerText = htmlSrc;
});
document.getElementById("highlight-btn").addEventListener("click", async function () {
    hljs.highlightAll();
});
document.getElementById("download-btn").addEventListener("click", async function () {
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