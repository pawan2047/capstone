<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-language Code Editor (VS Code-Like)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/monaco-editor@latest/min/vs/loader.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Multi-language Code Editor</h1>

        <!-- Language Selector -->
        <div class="flex justify-between items-center mb-4">
            <label for="language-select" class="text-lg font-medium text-gray-800">Select Language:</label>
            <select id="language-select" class="p-2 border border-gray-300 rounded-md">
                <option value="javascript">JavaScript</option>
                <option value="python">Python</option>
                <option value="php">PHP</option>
                <option value="cpp">C++</option>
            </select>
        </div>

        <!-- Monaco Code Editor -->
        <div id="editor" class="border border-gray-300 rounded-md" style="height: 400px; width: 100%;"></div>

        <!-- Buttons -->
        <div class="flex justify-between mt-4">
            <button id="run-code" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Run Code
            </button>
        </div>

        <!-- Output Section -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800">Output:</h3>
            <pre id="code-output" class="mt-2 bg-gray-900 text-white p-4 rounded-md overflow-auto"></pre>
        </div>
    </div>

<script>
    require.config({ paths: { vs: "https://unpkg.com/monaco-editor@latest/min/vs" } });

    require(["vs/editor/editor.main"], function () {
        let editor = monaco.editor.create(document.getElementById("editor"), {
            value: "// Write your code here...",
            language: "javascript",
            theme: "vs-dark",
            automaticLayout: true,
            lineNumbers: "on", // Enable line numbers
            minimap: { enabled: true }, // Show minimap like VS Code
            wordWrap: "on",
            fontSize: 14,
            scrollBeyondLastLine: false,
        });

        const languageMap = {
            javascript: "javascript",
            python: "python",
            php: "php",
            cpp: "cpp"
        };

        document.getElementById("language-select").addEventListener("change", function () {
            let selectedLanguage = this.value;
            monaco.editor.setModelLanguage(editor.getModel(), languageMap[selectedLanguage] || "javascript");
        });

        window.getCodeFromEditor = function () {
            return editor.getValue();
        };

        document.getElementById("run-code").addEventListener("click", function () {
            let code = getCodeFromEditor();
            console.log("Running Code:\n", code);
            alert("Running code:\n" + code);
        });
    });
</script>

<script>

require.config({ paths: { vs: "https://unpkg.com/monaco-editor@latest/min/vs" } });

require(["vs/editor/editor.main"], function () {
    let editor = monaco.editor.create(document.getElementById("editor"), {
        value: "// Write your code here...",
        language: "javascript",
        theme: "vs-dark",
        automaticLayout: true,
        lineNumbers: "on", // Always display line numbers
        minimap: { enabled: true }, // Enable minimap for a VS Code-like experience
        wordWrap: "on",
        fontSize: 14,
        scrollBeyondLastLine: false,
        renderLineNumbers: "on", // Explicitly render line numbers
        glyphMargin: true, // Add margin space for breakpoints/debugging
    });

    </script>


</body>
</html>
