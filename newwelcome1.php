<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Code Playground</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

  <!-- Navigation -->
  <nav class="bg-blue-600 p-4 mb-6">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
      <a href="#" class="flex items-center space-x-2 text-white font-semibold text-2xl">
        <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Logo" />
        <span>CodingMania</span>
      </a>
    </div>
  </nav>

  <!-- Main Container -->
  <main class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Multi-language Code Editor</h1>

    <!-- Language Select -->
    <div class="flex justify-between items-center mb-4">
      <label for="language" class="font-medium text-gray-800">Language:</label>
      <select id="language" class="border border-gray-300 rounded-md p-2">
        <option value="javascript">JavaScript</option>
        <option value="python">Python</option>
        <option value="php">PHP</option>
        <option value="cpp">C++</option>
      </select>
    </div>

    <!-- Question -->
    <div class="bg-blue-100 rounded-md p-4 mb-6">
      <h2 class="text-blue-700 font-semibold">Question:</h2>
      <p class="text-gray-700 mt-2">Write a program to print <code>Hello, World!</code>.</p>
    </div>

    <!-- Code Editor -->
    <label for="code-input" class="font-medium text-gray-800 block mb-2">Your Code:</label>
    <textarea id="code-input" rows="10"
      class="w-full border border-gray-300 rounded-md p-4 focus:ring-2 focus:ring-blue-400 mb-4"
      placeholder="Write your code here..."></textarea>

    <!-- Buttons -->
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
      <button id="chatbot" class="flex-1 bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Chat with us</button>
      <button id="run-code" class="flex-1 bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Run Code</button>
      <button id="show-answer" class="bg-green-600 text-white py-2 rounded-md hover:bg-green-700">Show Answer</button>
    </div>

    <!-- Output -->
    <div>
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Output:</h3>
      <pre id="output"
        class="bg-gray-900 text-white p-4 rounded-md overflow-auto h-48 whitespace-pre-wrap"></pre>
    </div>
  </main>

  <!-- Chatbox -->
  <div id="chatbox"
    class="fixed top-0 left-0 w-1/5 h-full bg-white shadow transform -translate-x-full transition-transform duration-300 z-50 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-gray-800">Chat with Us</h2>
      <button id="close-chatbox" class="text-2xl font-bold text-gray-600 hover:text-gray-900">&times;</button>
    </div>
    <div class="flex flex-col h-full">
      <div id="chat-content" class="flex-grow bg-gray-100 p-3 rounded-md overflow-y-auto mb-4"></div>
      <input id="chat-input" type="text" placeholder="Type your message..."
        class="border border-gray-300 p-2 rounded-md mb-2" />
      <button id="send-message" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Send</button>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    const langMap = {
      javascript: 'nodejs',
      python: 'python3',
      php: 'php',
      cpp: 'cpp'
    };

    const answerMap = {
      javascript: 'console.log("Hello, World!");',
      python: 'print("Hello, World!")',
      php: '<?php echo "Hello, World!"; ?>',
      cpp: '#include <iostream>\nusing namespace std;\nint main() {\n  cout << "Hello, World!";\n  return 0;\n}'
    };

    const outputEl = document.getElementById('output');
    const chatbox = document.getElementById('chatbox');

    // Chatbox toggle
    document.getElementById('chatbot').onclick = () => chatbox.classList.toggle('-translate-x-full');
    document.getElementById('close-chatbox').onclick = () => chatbox.classList.add('-translate-x-full');

    // Send chat message
    document.getElementById('send-message').onclick = sendMessage;
    document.getElementById('chat-input').addEventListener('keypress', e => {
      if (e.key === 'Enter') {
        e.preventDefault();
        sendMessage();
      }
    });

    function sendMessage() {
      const input = document.getElementById('chat-input');
      const msg = input.value.trim();
      if (!msg) return;

      const chatContent = document.getElementById('chat-content');
      chatContent.innerHTML += `<div class="bg-blue-500 text-white p-2 rounded-lg mb-2 self-end">${msg}</div>`;

      fetch('get_response.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt: msg })
      })
        .then(res => res.json())
        .then(data => {
          chatContent.innerHTML += `<div class="bg-gray-300 text-black p-2 rounded-lg mb-2 self-start">${data.response}</div>`;
        })
        .catch(() => {
          chatContent.innerHTML += `<div class="bg-red-400 text-white p-2 rounded-lg mb-2">Error getting response.</div>`;
        });

      input.value = '';
    }

    // Run Code
    document.getElementById('run-code').onclick = () => {
      const lang = document.getElementById('language').value;
      const code = document.getElementById('code-input').value;

      const body = JSON.stringify({
        language: langMap[lang],
        version: 'latest',
        code: code,
        input: null
      });

      fetch('https://online-code-compiler.p.rapidapi.com/v1/', {
        method: 'POST',
        headers: {
          'content-type': 'application/json',
          'x-rapidapi-key': 'd3dfff1ac0msh311292519da91c4p17dab9jsna2ce47125585',
          'x-rapidapi-host': 'online-code-compiler.p.rapidapi.com'
        },
        body
      })
        .then(res => res.json())
        .then(data => {
          outputEl.textContent = data.output || '✅ Code executed but no output.';
        })
        .catch(() => {
          outputEl.textContent = '❌ Error running code.';
        });
    };

    // Show correct answer
    document.getElementById('show-answer').onclick = () => {
      const lang = document.getElementById('language').value;
      outputEl.textContent = `Correct Answer:\n${answerMap[lang]}`;
    };
  </script>

</body>

</html>
