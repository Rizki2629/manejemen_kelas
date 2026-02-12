<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    
    <!-- DNS Prefetch -->
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    
    <!-- Preload critical CSS -->
    <link rel="preload" href="/css/tailwind.min.css" as="style">
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="/css/tailwind.min.css">
    
    <!-- Font Awesome - lazy loaded -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
</head>
<body class="h-full flex items-center justify-center font-sans bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="w-full max-w-sm sm:max-w-md lg:max-w-lg xl:max-w-xl bg-white p-6 sm:p-8 lg:p-12 rounded-2xl shadow-2xl border border-gray-200">
        <!-- Logo Section -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="mx-auto w-16 h-16 sm:w-20 sm:h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-graduation-cap text-blue-600 text-2xl sm:text-3xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-blue-700 tracking-wide">
                Login
            </h1>
            <p class="text-gray-600 text-sm sm:text-base mt-2">Masuk ke sistem SDN Grogol Utara 09</p>
        </div>

        <!-- Error Messages -->
        <?php if(session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-3 sm:p-4 rounded-lg mb-4 sm:mb-6" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                    <span class="text-sm sm:text-base"><?= session()->getFlashdata('error') ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if(isset($validation)): ?>
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-3 sm:p-4 rounded-lg mb-4 sm:mb-6" role="alert">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5"></i>
                    <div class="text-sm sm:text-base"><?= $validation->listErrors() ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="/login" method="post" class="space-y-4 sm:space-y-6">
            <?= csrf_field() ?>
            
            <!-- Username Field -->
            <div>
                <label for="username" class="block text-sm sm:text-base font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-blue-600"></i>Username atau Email
                </label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="<?= set_value('username') ?>" 
                    required
                    placeholder="Masukkan username atau email"
                    class="w-full h-12 sm:h-14 px-4 sm:px-6 text-sm sm:text-base rounded-xl border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-300 hover:border-gray-400" 
                />
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm sm:text-base font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-600"></i>Password
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        placeholder="Masukkan password"
                        class="w-full h-12 sm:h-14 px-4 sm:px-6 text-sm sm:text-base rounded-xl border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-300 hover:border-gray-400 pr-12 sm:pr-14" 
                    />
                    <button 
                        type="button" 
                        id="togglePassword" 
                        aria-label="Toggle password visibility"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 sm:pr-4 text-gray-400 hover:text-blue-600 focus:outline-none transition-colors duration-200">
                        <i id="eyeIcon" class="fas fa-eye text-lg"></i>
                        <i id="eyeOffIcon" class="fas fa-eye-slash text-lg hidden"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                    <i class="fas fa-user-check ml-2 mr-1 text-blue-600 text-sm"></i>
                    <span class="text-sm text-gray-600">Ingat saya</span>
                </label>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">
                    Lupa password?
                </a>
            </div>

            <!-- Login Button -->
            <div class="pt-2">
                <button 
                    type="submit"
                    class="w-full h-12 sm:h-14 rounded-xl bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 text-white text-sm sm:text-base font-bold shadow-lg hover:from-blue-700 hover:via-blue-800 hover:to-blue-900 focus:ring-4 focus:ring-blue-200 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </div>
        </form>


    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('hidden');
            eyeOffIcon.classList.toggle('hidden');
        });

        // Add loading state to form submission
        const loginForm = document.querySelector('form');
        const submitButton = loginForm.querySelector('button[type="submit"]');
        
        loginForm.addEventListener('submit', function() {
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitButton.disabled = true;
        });

        // Auto-focus on username field
        document.getElementById('username').focus();
    </script>
</body>
</html>
