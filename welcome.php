<?php
session_start();

if($_COOKIE['username']){
    $user = $_COOKIE['username'];
    $_SESSION['username'] = $user;
}
$user = $_SESSION['username'];
if(!$user) {
    header("Location: http://localhost");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-language Code Editor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Prettier.js for Auto Formatting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.0/beautify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.0/beautify-html.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.0/beautify-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.0/beautify-css.min.js"></script>

<script src="https://unpkg.com/prettier/standalone.js"></script>
<script src="https://unpkg.com/@prettier/plugin-php/standalone.js"></script>
<script src="https://unpkg.com/monaco-editor@latest/min/vs/loader.js"></script>
</head>
<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Select all <code> blocks inside <pre>
            document.querySelectorAll("pre code").forEach((block) => {
                // Get the language from the data-language attribute
                const lang = block.getAttribute("data-language");
                if (lang) {
                    block.classList.add(`language-${lang}`);
                }
                // Apply Highlight.js syntax highlighting
                hljs.highlightElement(block);
            });
        });
    </script>

<body class="bg-gray-100 min-h-screen">

<nav class="bg-white border-gray-200 dark:bg-gray-900">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto   p-4">

        <!-- Website Logo -->
        <a href="" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="" class="h-8" alt="Your Logo" />
            <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">CODINGMANIA</span>
        </a>

        <!-- Profile & Mobile Menu -->
        <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse gap-8  relative">
            
            <!-- Profile Button -->
            <button id="profile-btn" type="button"
                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                <span class="sr-only">Open user menu</span>
                <div class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
    <svg class="absolute w-12 h-12 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
</div>

<button id="dropdownMenuIconButton" data-dropdown-toggle="dropdownDots" class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600 " type="button">
<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
<path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
</svg>
</button>

                
            

            





            <!-- Dropdown Menu (Initially Hidden) -->
            <div id="profile-dropdown"
                class="absolute right-0 mt-2 w-48 bg-white divide-y divide-gray-100 rounded-lg shadow-lg hidden dark:bg-gray-700 dark:divide-gray-600">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-900 dark:text-white">John Doe</span>
                    <span class="block text-sm text-gray-500 truncate dark:text-gray-400">john.doe@example.com</span>
                </div>
                <ul class="py-2">
                <li>
                        <a href="#"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                            👤 <?= $user ?>
                        </a>
                    </li>
                    <li>
                        <a href="dashboard.html"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                            🏠 Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="badges.html"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                            🏅 Badges
                        </a>
                    </li>


                    <li>
                        <a href="settings.html"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                            ⚙️ Settings
                        </a>
                    </li>
                    <li>
                        <a href="default.php"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                            🚪 Sign Out
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</nav>


<div class="w-full max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Multi-language Code Editor</h1>

    <!-- Language Selector -->
    <div class="flex justify-between items-center mb-6">
        <label for="language-select" class="text-lg font-medium text-gray-800">Select Language:</label>
        <select id="language-select" class="p-2 border border-gray-300 rounded-md">
            <option value="javascript">JavaScript</option>
            <option value="python">Python</option>
            <option value="php">PHP</option>
            <option value="cpp">C++</option>
        </select>
    </div>

    <!-- Question Section -->
    <div class="bg-blue-100 p-4 rounded-md mb-6">
        <h2 class="text-lg font-semibold text-blue-700">Question:</h2>
       
        <?php
        include 'question.php';
        ?>
    </div>

    <!-- Code Input Section -->
   <!-- <div class="flex flex-col space-y-4">
        <label for="code-input" class="text-gray-800 font-medium">Write your code:</label>
        <textarea id="code-input" rows="10"
            class="w-full p-4 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
            placeholder="Write your code here..."></textarea>
    </div>-->
    <!-- Monaco Code Editor (Renamed ID to 'code-input') -->
    <div id="code-input" class="border border-gray-300 rounded-md" style="height: 400px; width: 100%;"></div>

    <!-- Buttons and Chatbot Section -->
    <div class="flex items-center justify-between mt-4 space-x-4">

        <div class="flex-grow flex justify-center">
            <button id="chatbot"
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none">
                Chat with us 
            </button>
        </div>

        <!-- Run Code Button -->
        <div class="flex-grow flex justify-center">
            <button id="run-code"
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none">
                Run Code
            </button>
        </div>

        <!-- Show Answer Button -->
        <button id="show-answer"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none">
            Show Answer
        </button>

         <!-- Next Question Button -->
        <button id="next-question"
        class="px-6 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none">
        Next Question
    </button>

    



    </div>

    <!-- Output Section -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-800">Output:</h3>
        <pre id="code-output" class="mt-2 bg-gray-900 text-white p-4 rounded-md overflow-auto"></pre>
    </div>

    <!-- Chatbox Section -->
    <div id="chatbox" class="fixed top-0 left-0 h-full w-1/5 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 p-6 z-50">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Chat with Us</h2>
            <button id="close-chatbox" class="text-gray-600 hover:text-gray-900 text-2xl font-bold">&times;</button>
        </div>
        <div class="flex flex-col space-y-4 h-full">
            <div class="flex-grow overflow-y-auto bg-gray-100 p-4 rounded-md" id="chat-content">
                <!-- Chat messages will appear here -->
            </div>
            <input type="text" id="chat-input" class="border border-gray-300 p-2 rounded-md" placeholder="Type your message...">
            <button id="send-message" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mt-2 mb-8">Send</button>
        </div>
    </div>

</div>

<!-- Sidebar for Table of Contents (initially hidden) -->
<div id="toc-sidebar" class="fixed top-0 right-0 w-96 h-full bg-white shadow-lg transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
    <!-- The Table of Contents content will be injected here -->
     <!-- Close Button -->
    <button id="toc-close" class="absolute top-2 right-2 p-2 text-gray-700 z-10">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>

  <script>
  // Declare global variables for reuse in all event listeners
  const sidebar = document.getElementById('toc-sidebar');
  const dropdownButton = document.getElementById('dropdownMenuIconButton');

  // Toggle the sidebar when the dropdown menu icon button is clicked
  dropdownButton.addEventListener('click', function(e) {
    // Prevent click from bubbling up to the document listener
    e.stopPropagation();
    
    if (sidebar.classList.contains('translate-x-full')) {
      // Load the table of contents if not already loaded
      if (!sidebar.innerHTML.trim()) {
        fetch('tableofcontent.php')
          .then(response => response.text())
          .then(html => {
            sidebar.innerHTML = html;
            
            // Attach event listener to the close button inside the fetched content if it exists
            const tocClose = document.getElementById('toc-close');
            if (tocClose) {
              tocClose.addEventListener('click', function() {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('translate-x-full');
              });
            }
          })
          .catch(error => console.error('Error loading table of contents:', error));
      }
      // Show the sidebar
      sidebar.classList.remove('translate-x-full');
      sidebar.classList.add('translate-x-0');
    } else {
      // Hide the sidebar
      sidebar.classList.remove('translate-x-0');
      sidebar.classList.add('translate-x-full');
    }
  });

  // Hide sidebar when clicking outside of it
  document.addEventListener('click', function(e) {
    // Check if click is outside both the sidebar and the dropdown button
    if (!sidebar.contains(e.target) && !dropdownButton.contains(e.target)) {
      sidebar.classList.remove('translate-x-0');
      sidebar.classList.add('translate-x-full');
    }
  });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
    const profileBtn = document.getElementById("profile-btn");
    const profileDropdown = document.getElementById("profile-dropdown");

    // Toggle dropdown visibility on click
    profileBtn.addEventListener("click", () => {
        profileDropdown.classList.toggle("hidden");
    });

    // Close dropdown if clicked outside
    document.addEventListener("click", (event) => {
        if (!profileBtn.contains(event.target) && !profileDropdown.contains(event.target)) {
            profileDropdown.classList.add("hidden");
        }
    });
});

    </script>

    <script>
    require.config({ paths: { vs: "https://unpkg.com/monaco-editor@latest/min/vs" } });

    require(["vs/editor/editor.main"], function () {
        let editor = monaco.editor.create(document.getElementById("code-input"), { // ✅ Using ID 'code-input'
            value: "// Write your code here...",
            language: "javascript",
            theme: "vs-dark",
            automaticLayout: true,
            lineNumbers: "on", // Always display line numbers
            minimap: { enabled: true }, // Show minimap
            wordWrap: "on", 
            fontSize: 14,
            scrollBeyondLastLine: false,
            glyphMargin: true, // Extra space for debugging markers
        });

        // Language mapping for API
        const languageMap = {
            javascript: "nodejs",
            python: "python3",
            php: "php",
            cpp: "cpp"
        };

        // Change language in Monaco Editor
        document.getElementById("language-select").addEventListener("change", function () {
            let selectedLanguage = this.value;
            monaco.editor.setModelLanguage(editor.getModel(), languageMap[selectedLanguage] || "javascript");
        });
        

        // Run Code Button Event (Using API)
        document.getElementById("run-code").addEventListener("click", () => {
            console.log("Run Code button clicked!"); // Debugging Message

            const language = document.getElementById("language-select").value;
            const codeInput = editor.getValue(); // ✅ Fetch code from Monaco Editor
            const codeOutput = document.getElementById("code-output");

            if (!codeInput) {
                codeOutput.textContent = "⚠️ Please enter some code to run.";
                return;
            }

            // Show loading message
            codeOutput.textContent = "⏳ Running code...";

            // Define the correct language mapping for API
            const requestData = JSON.stringify({
                language: languageMap[language],
                version: "6" ,
                code: codeInput,
                input: ""
            });

            console.log("🟢 Sending Request to API:", requestData);

            // Call the API
            fetch('https://online-code-compiler.p.rapidapi.com/v1/', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "x-rapidapi-key": "d3dfff1ac0msh311292519da91c4p17dab9jsna2ce47125585", // Replace with your actual API Key
                    "x-rapidapi-host": "online-code-compiler.p.rapidapi.com"
                },
                body: requestData
            })
            .then(response => response.json()) // Convert response to JSON
            .then(data => {
                console.log("✅ API Response:", data);

                if (data.output) {
                    codeOutput.innerHTML = `<pre style="white-space: pre-wrap; word-wrap: break-word; background-color: #1e1e1e; color: #ffffff; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace;">${data.output}</pre>`;
                } else {
                    codeOutput.textContent = "⚠️ No output or execution error.";
                }
            })
            .catch(error => {
                console.error('❌ Error calling API:', error);
                codeOutput.textContent = "⚠️ Error running code.";
            });
        });
    });
</script>



<script>
    // Toggle chatbox visibility
    const chatbox = document.getElementById('chatbox');
    document.getElementById('chatbot').addEventListener('click', () => {
        chatbox.classList.toggle('-translate-x-full');
    });

    // Close chatbox when close button is clicked
    document.getElementById('close-chatbox').addEventListener('click', () => {
        chatbox.classList.add('-translate-x-full');
    });

    // Function to send the message

    // Function to send the message along with the code and language context
    function sendMessage() {
    const chatInput = document.getElementById('chat-input');
    const chatContent = document.getElementById('chat-content');
    if (chatInput.value.trim()) {
        // Display the user's message in the chatbox
        const message = document.createElement('div');
        message.className = 'bg-blue-500 text-white p-2 rounded-lg mb-2 self-end';
        message.textContent = chatInput.value;
        chatContent.appendChild(message);
        // Send the message to PHP to get the ChatGPT response
        fetch('get_response.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ prompt: chatInput.value })
        })
        .then(response => response.json())
        .then(data => {
            const botMessage = document.createElement('div');
            botMessage.className = 'bg-gray-300 text-black p-2 rounded-lg mb-2 self-start';
            botMessage.textContent = data.response;
            chatContent.appendChild(botMessage);
        })
        .catch(error => {
            console.error('Error:', error);
        });
        // Clear the input
        chatInput.value = '';
    }
}
    // Send message when clicking the "Send" button
    document.getElementById('send-message').addEventListener('click', sendMessage);
    // Send message when pressing Enter in the chat input
    document.getElementById('chat-input').addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent newline in the input field
            sendMessage();
        }
    });

    
</script>

<script>
    // Mapping frontend language selector values to RapidAPI's expected values
  /*  const languageMap = {
        javascript: 'nodejs',
        python: 'python3',
        php: 'php',
        cpp: 'cpp'
    };

    // Placeholder answers for different languages
    const answers = {
    javascript: `console.log("Hello, World!");`,
    python: `print("Hello, World!")`,
    php: `<?php echo "Hello, World!"; ?>`,
    cpp: `#include <iostream>\nusing namespace std;\nint main() {\n    cout << "Hello, World!";\n    return 0;\n}`
};*/


// Debugging "Run Code" Button
/*document.getElementById('run-code').addEventListener('click', () => {
    console.log("Run Code button clicked!"); // Debugging Message

    const language = document.getElementById('language-select').value; 
    const codeInput = document.getElementById('code-input').value;
    const codeOutput = document.getElementById('code-output');

    if (!codeInput) {
        codeOutput.textContent = "⚠️ Please enter some code to run.";
        return;
    }

    // Show loading message
    codeOutput.textContent = "⏳ Running code...";

    // Define the correct language mapping for API
    const languageMap = {
        javascript: "nodejs",
        python: "python3",
        php: "php",
        cpp: "cpp"
    };

    const requestData = JSON.stringify({
        language: languageMap[language],
        version: "latest",
        code: codeInput,
        input: ""
    });

    console.log("🟢 Sending Request to API:", requestData);

    // Call the API
    fetch('https://online-code-compiler.p.rapidapi.com/v1/', {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "x-rapidapi-key": "d3dfff1ac0msh311292519da91c4p17dab9jsna2ce47125585",
            "x-rapidapi-host": "online-code-compiler.p.rapidapi.com"
        },
        body: requestData
    })
    .then(response => response.json()) // Convert response to JSON
    .then(data => {
        console.log("✅ API Response:", data);

        if (data.output) {
            codeOutput.innerHTML = `<pre style="white-space: pre-wrap; word-wrap: break-word; background-color: #1e1e1e; color: #ffffff; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace;">${data.output}</pre>`;
        } else {
            codeOutput.textContent = "⚠️ No output or execution error.";
        }
    })
    .catch(error => {
        console.error('❌ Error calling API:', error);
        codeOutput.textContent = "⚠️ Error running code.";
    });
});
uncomment it
    
*/

// Debugging "Show Answer" Button
/*document.getElementById('show-answer').addEventListener('click', () => {
    console.log("Show Answer button clicked!"); // Debugging Message

    const language = document.getElementById('language-select').value;
    const codeOutput = document.getElementById('code-output');

    if (answers[language]) {
        codeOutput.textContent = `✅ Correct Answer:\n${answers[language]}`;
    } else {
        codeOutput.textContent = "⚠️ No answer available for this language.";
    }
});*/


    // Run Code Button Event
    /*document.getElementById('run-code').addEventListener('click', () => {
        const language = document.getElementById('language-select').value; // Get selected language
        const codeInput = document.getElementById('code-input').value;
        const codeOutput = document.getElementById('code-output');
    //const question = document.querySelector(".bg-blue-100 p").textContent.trim(); // Get the question text
    const codeOutput = document.getElementById('code-output'); // Output element

    // Show loading message
    codeOutput.textContent = "Loading...";

        const data = JSON.stringify({
            language: languageMap[language],
            version: 'latest',
            code: codeInput,
            input: null
        });

        const xhr = new XMLHttpRequest();
        xhr.withCredentials = true;

        xhr.addEventListener('readystatechange', function () {
            if (this.readyState === this.DONE) {
                try {
                    const response = JSON.parse(this.responseText);
                    codeOutput.textContent = response.output || 'No output or execution error.';
                } catch (error) {
                    codeOutput.textContent = 'Error parsing response.';
                }
            }
        });

       
    });


    // Show Answer Button Event
    
    document.getElementById('show-answer').addEventListener('click', () => {
        const language = document.getElementById('language-select').value;
        const codeOutput = document.getElementById('code-output');
        codeOutput.textContent = `Correct Answer:\n${answers[language]}`;
    });*/

</script>

<script>

let currentId = 1; // Start with question ID 1

// Event Listener for the Next Question Button
document.getElementById('next-question').addEventListener('click', () => {
    currentId++; // Increment the question ID

    // Fetch the next question from question.php
    fetch(`question.php?id=${currentId}`)
        .then(response => response.text()) // Expect text response
        .then(data => {
            const questionContainer = document.querySelector('.bg-blue-100');
            if (questionContainer) {
                questionContainer.innerHTML = `<h2 class="text-lg font-semibold text-blue-700">Question:</h2><p>${data}</p>`;
            } else {
                console.error("Error: Question container not found.");
            }
        })
        .catch(error => {
            console.error('Error fetching the next question:', error);
        });


    // Send current question ID to getanswer.php
    fetch(`getanswer.php?id=${currentId}&language=${selectedLanguage}`)
        .then(response => response.text()) // Expect text response
        .then(answer => {
            alert(`✅ Successfully received answer for ID ${currentId}: ${answer}`);
            // Display answer on the page (optional)
            const codeOutput = document.getElementById('code-output');
            codeOutput.textContent = `Answer: ${answer}`;
        })
        .catch(error => {
            alert('❌ Error sending question ID to getanswer.php: ' + error);
        });


});


// Event Listener for Show Answer Button (Fetches getanswer.php with stored currentId)
document.getElementById('show-answer').addEventListener('click', () => {
    console.log("Show Answer button clicked!"); // Debugging message

    const codeOutput = document.getElementById('code-output'); // Output box
    const questionId = currentId || 1; // If no ID is set, default to 1
    const selectedLanguage = document.getElementById('language-select').value; // Get selected language

    console.log(`🟢 Fetching answer for Question ID: ${questionId}`);
    // ✅ Clear previous output
    codeOutput.textContent = ""; 

    // Fetch the answer from getanswer.php with the stored question ID
    fetch(`getanswer.php?id=${questionId}&language=${selectedLanguage}`)
        .then(response => response.text()) // Get response as text
        .then(async function (answer){
            console.log(`✅ Received answer for ID ${questionId}: ${answer}`);
            const formattedAnswer = answer
                .replace(/</g, "&lt;") // Escape "<"
                .replace(/>/g, "&gt;") // Escape ">"
                .replace(/\n/g, "<br>"); // Preserve new lines
            var prettierPlugins = [];
            // Update the output box with properly formatted text inside a <pre> tag
            // codeOutput.innerHTML = `<pre style="white-space: pre-wrap; word-wrap: break-word; background-color: #1e1e1e; color: #ffffff; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace;">${answer.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</pre>`;
            
            function beautifyCode(code, language) {
                let formattedCode = '';
                 if (language === 'js' || language === 'javascript') {
                    formattedCode = js_beautify(code, { indent_size: 4 });
                } else if (language === 'html') {
                    formattedCode = html_beautify(code, { indent_size: 4 });
                } else {
                    return code;
                }
                    return formattedCode;
                }
            var code = await beautifyCode(answer, selectedLanguage);

            codeOutput.insertAdjacentHTML('beforeend',code)
            //codeOutput.innerText = `✅ Answer in ${selectedLanguage}:\n${answer}`;
        })
        .catch(error => {
            console.error('❌ Error fetching answer:', error);
            codeOutput.textContent = "⚠️ Error retrieving answer.";
        });
});

   /* let currentId = 1; // Start with question ID 1

// Event Listener for the Next Question Button
document.getElementById('next-question').addEventListener('click', () => {
    currentId++; // Increment the question ID

    // Fetch the next question from the server
    fetch(`question.php?id=${currentId}`) // ✅ Corrected string interpolation
        .then(response => response.text()) // Expect text response
        .then(data => {
            // ✅ Ensure there's an element with class 'bg-blue-100'
            const questionContainer = document.querySelector('.bg-blue-100');
            if (questionContainer) {
                questionContainer.innerHTML = `<h2 class="text-lg font-semibold text-blue-700">Question:</h2><p>${data}</p>`;
            } else {
                console.error("Error: Question container not found.");
            }
        })
        .catch(error => {
            console.error('Error fetching the next question:', error);
        });
});*/

// Show Answer Button Event

</script>




</body>

</html>