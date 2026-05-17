<!--
    Register page for new Nexus users.
    Validates user input in the browser and forwards registration details to the form handler.
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Nexus</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900">Nexus</h1>
            <p class="text-gray-600 mt-2">Room & Player Management</p>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-center mb-6">Register</h2>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="core/formhandler.php" method="post" class="space-y-6">
                <input type="hidden" name="action" value="register">

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                        Username
                    </label>
                    <input type="text" id="username" name="username" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                    Register
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account?
                    <a href="login.php" class="text-blue-600 hover:text-blue-800 font-medium">Login here</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Client-side registration validation.
        // This script enforces password strength and ensures both password fields match
        // before the form is submitted to the server.
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Password strength validation
            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                e.preventDefault();
                return;
            }

            if (!/[a-z]/.test(password)) {
                alert('Password must contain at least one lowercase letter');
                e.preventDefault();
                return;
            }

            if (!/[A-Z]/.test(password)) {
                alert('Password must contain at least one uppercase letter');
                e.preventDefault();
                return;
            }

            if (!/[0-9]/.test(password)) {
                alert('Password must contain at least one number');
                e.preventDefault();
                return;
            }

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>