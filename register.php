<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign in Page</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 font-sans relative">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('codding.png');">
        <div class="absolute inset-0 bg-black opacity-50"></div> <!-- Dimming Overlay -->
    </div>

    <!-- Registration Form Section -->
    <div class="relative flex flex-col justify-center sm:h-screen p-4">
        <div class="max-w-md w-full mx-auto border border-gray-300 rounded-2xl p-8 bg-white bg-opacity-90 shadow-lg">
            <div class="text-center mb-12">
            <span class="self-center text-2xl font-semibold whitespace-nowrap text-white">CodingMania</span>
            </div>

            <form action="add.php" method="POST">
    <div class="space-y-6">
        <div>
            <label class="text-gray-800 text-sm mb-2 block">Email Id</label>
            <input name="email" type="text"
                class="text-gray-800 bg-white border border-gray-300 w-full text-sm px-4 py-3 rounded-md outline-blue-500"
                placeholder="Enter email" required />
        </div>
        <div>
            <label class="text-gray-800 text-sm mb-2 block">Password</label>
            <input name="password" id="password" type="password"
                class="text-gray-800 bg-white border border-gray-300 w-full text-sm px-4 py-3 rounded-md outline-blue-500"
                placeholder="Enter password" required />
        </div>
        <div>
            <label class="text-gray-800 text-sm mb-2 block">Confirm Password</label>
            <input name="cpassword" id="confirm-password" type="password"
                class="text-gray-800 bg-white border border-gray-300 w-full text-sm px-4 py-3 rounded-md outline-blue-500"
                placeholder="Enter confirm password" required />
            <!-- Error Message -->
            <p id="password-error" class="text-red-500 text-sm mt-1 hidden">Passwords do not match.</p>
        </div>

        <div class="flex items-center">
            <input id="remember-me" name="remember-me" type="checkbox"
                class="h-4 w-4 shrink-0 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required />
            <label for="remember-me" class="text-gray-800 ml-3 block text-sm">
                I accept the
                <a href="javascript:void(0);" class="text-blue-600 font-semibold hover:underline ml-1">Terms and
                    Conditions</a>
            </label>
        </div>
    </div>

    <div class="mt-12">
        <button type="submit" id="submit-btn"
            class="w-full py-3 px-4 text-sm tracking-wider font-semibold rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
            disabled>
            Create an account
        </button>
    </div>
    <p class="text-gray-800 text-sm mt-6 text-center">Already have an account?
        <a href="javascript:void(0);" class="text-blue-600 font-semibold hover:underline ml-1">Login here</a>
    </p>
</form>

<script>
    // Password and Confirm Password Fields
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm-password');
    const errorMessage = document.getElementById('password-error');
    const submitButton = document.getElementById('submit-btn');

    // Add event listeners to both password fields
    confirmPassword.addEventListener('input', validatePasswords);
    password.addEventListener('input', validatePasswords);

    function validatePasswords() {
        if (password.value === confirmPassword.value) {
            errorMessage.classList.add('hidden');  // Hide error message
            submitButton.disabled = false;         // Enable submit button
        } else {
            errorMessage.classList.remove('hidden');  // Show error message
            submitButton.disabled = true;             // Disable submit button
        }
    }
</script>

        </div>
    </div>
</body>

</html>
