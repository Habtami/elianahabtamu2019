<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | EPSS Contract Management</title>
    <!-- Favicon placeholder – replace with actual asset if needed -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    
    <!-- Critical CSS (reduced to essentials, keeping black text and spacing removal) -->
    <style>
        :root {
            --epss-primary: #1C75BC;
            --epss-primary-dark: #1664a0;
            --epss-secondary: #2B8CDB;
            --epss-light: #e8f4fd;
            --gradient-primary: linear-gradient(135deg, #1C75BC 0%, #2B8CDB 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #000000 !important; /* Force all text to black */
        }
        
        /* Force black text everywhere (except icons) */
        h1, h2, h3, h4, h5, h6,
        p, span, a, label, li, div,
        .text-gray-900, .text-gray-600, .text-gray-500, .text-gray-400,
        .text-red-800, .text-red-700, .text-red-600, .text-red-500,
        .text-green-800, .text-green-700, .text-green-500,
        .text-blue-100, .text-yellow-600, .text-purple-500,
        .text-epss, .text-epss-dark {
            color: #000000 !important;
        }
        
        i, .fa {
            color: inherit !important;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .spinner {
            animation: spin 1s linear infinite;
        }
        
        .focus-ring {
            outline: 2px solid var(--epss-primary);
            outline-offset: 2px;
        }
        
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        @media (max-width: 640px) {
            input, button, select, textarea {
                font-size: 16px !important;
            }
            .touch-target {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* --- SPACING REMOVAL: eliminate extra gaps around the login card --- */
        /* The outer wrapper should center content without extra top/bottom margin */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* Remove extra margin from logo header */
        .login-wrapper .logo-header {
            margin-bottom: 0 !important;
        }

        /* Remove margin-top from footer inside wrapper */
        .login-wrapper footer {
            margin-top: 0.5rem !important;
        }

        /* Remove any residual bottom margin from the form container */
        .login-wrapper .card-body {
            padding-bottom: 0 !important;
        }

        /* Ensure form fields don't add extra vertical space (optional) */
        .login-wrapper form {
            margin-bottom: 0 !important;
        }

        /* For the card itself: remove any bottom margin if present */
        .login-wrapper .login-card {
            margin-bottom: 0 !important;
        }
    </style>
    
    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind (CDN) with custom config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'epss': {
                            DEFAULT: '#1C75BC',
                            dark: '#1664a0',
                            light: '#e8f4fd'
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'pulse-subtle': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: 0 },
                            '100%': { opacity: 1 }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: 0 },
                            '100%': { transform: 'translateY(0)', opacity: 1 }
                        }
                    },
                    boxShadow: {
                        'elevation': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
                        'elevation-md': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                        'elevation-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                        'elevation-xl': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                        'inner': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.06)'
                    }
                }
            },
            corePlugins: {
                container: false
            }
        }
    </script>
</head>
<body>
    <!-- Skip navigation -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 z-50 bg-white px-4 py-2 rounded-lg shadow-elevation-lg text-epss font-medium">
        Skip to main content
    </a>

    <!-- MAIN WRAPPER – spacing removed via custom class -->
    <div class="login-wrapper" id="main-content">
        <!-- Logo Header: removed extra bottom margin (0) and kept minimal -->
        <div class="text-center logo-header">
            <!-- No extra content – just for spacing if needed; actually we can omit, but keep for structure -->
        </div>

<!-- Login Card -->
<div class="w-full max-w-md animate-fade-in login-card">
    <div class="bg-white rounded-2xl shadow-elevation-xl overflow-hidden animate-slide-up p-6 md:p-8">
        <!-- Gradient header (logo centered and enlarged) -->
        <div class="bg-gradient-to-r from-epss to-epss-dark p-6 md:p-8 -mx-6 md:-mx-8 -mt-6 md:-mt-8 mb-6">
            <div class="flex flex-col items-center justify-center text-center">
                <img src="{{ asset('images/epss_logo_amharic.jpg') }}" 
                     alt="Ethiopian Pharmaceutical Supply Service" 
                     class="h-16 md:h-20 w-auto mb-4" 
                     loading="eager">
                <h3 class="text-xl font-bold text-white" style="color: #000000 !important;">CMS</h3>
                <p class="text-sm text-white/90" style="color: #000000 !important;">Contract Management System</p>
            </div>
        </div>
        <!-- ... rest of card body ... -->

                <!-- Card Body (p-6 md:p-8 inherited from parent but we adjust padding to remove extra gaps) -->
                <div class="card-body">
                    <!-- Status Messages -->
                    @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 animate-pulse-subtle">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-red-800">Sign in failed</h4>
                                <div class="mt-1 text-sm text-red-700">
                                    <ul class="list-disc pl-4 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.post') }}" class="space-y-6" id="loginForm">
                        @csrf
                        
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email"
                                       autofocus
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-epss focus:border-transparent transition-colors duration-200 placeholder-gray-400"
                                       placeholder="name@epss.gov.et"
                                       aria-describedby="email-helper">
                            </div>
                            <p id="email-helper" class="mt-2 text-xs text-gray-500">
                                Use your official EPSS email
                            </p>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="password" class="block text-sm font-medium text-gray-900">
                                    Password
                                </label>
                                <button type="button" 
                                        id="passwordToggle" 
                                        class="text-xs text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-epss focus:ring-offset-2 rounded px-2 py-1"
                                        aria-label="Toggle password visibility">
                                    <i class="fas fa-eye mr-1"></i> Show
                                </button>
                            </div>
                            
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       required 
                                       autocomplete="current-password"
                                       class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-epss focus:border-transparent transition-colors duration-200 placeholder-gray-400"
                                       placeholder="Enter your password">
                                <button type="button" 
                                        id="passwordVisibility" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                        aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            
                            <!-- Password Strength (optional – kept for completeness) -->
                            <div class="mt-4 space-y-2">
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">Password strength</span>
                                    <span id="strengthText" class="font-medium">--</span>
                                </div>
                                <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div id="strengthMeter" class="h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="remember" 
                                   id="remember" 
                                   class="h-4 w-4 text-epss focus:ring-epss border-gray-300 rounded">
                            <label for="remember" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                Keep me signed in
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                id="submitBtn"
                                class="w-full bg-gradient-to-r from-epss to-epss-dark text-white font-semibold py-3.5 px-4 rounded-lg hover:from-epss-dark hover:to-epss-dark focus:outline-none focus:ring-2 focus:ring-epss focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] shadow-elevation-md hover:shadow-elevation-lg touch-target disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            <span id="buttonText" class="flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-3"></i>
                                Sign In to Continue
                            </span>
                            <i id="loadingIcon" class="fas fa-spinner spinner ml-3 hidden"></i>
                        </button>

                        <!-- Forgot Password -->
                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-sm text-epss hover:text-epss-dark font-medium inline-flex items-center">
                                <i class="fas fa-key mr-2"></i>
                                Forgot your password?
                            </a>
                        </div>
                    </form>
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div> <!-- end max-w-md -->

        <!-- Footer (reduced top margin) -->
        <footer class="mt-4 text-center w-full max-w-md">
            <p class="text-xs text-gray-500 mb-2">
                &copy; 2026 Ethiopian Pharmaceutical Supply Service
            </p>
            <div class="flex items-center justify-center space-x-4 text-xs text-gray-400">
                <span>CMS v2.5.2</span>
                <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                <span>Updated: Jan 2018 E.C</span>
                <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                
            </div>
            <!-- Mobile Quick Actions (keeping minimal) -->
            <div class="mt-3 flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-3">
                <span class="hidden sm:inline text-gray-300">•</span>
                <a href="#" class="text-xs text-gray-500 hover:text-epss flex items-center">
                    <i class="fas fa-phone mr-2"></i>
                    Contact Support
                </a>
				  <span class="hidden sm:inline text-gray-300">•</span>
                <a href="#" class="text-xs text-gray-500 hover:text-epss flex items-center">
                    <i class="fas fa-phone mr-2"></i>
                    Developed By EPSS ICT Developer Team
                </a>
            </div>
        </footer>
    </div> <!-- end login-wrapper -->

    <!-- Support Modal (Hidden by default) -->
    <div id="supportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 animate-slide-up">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Support Contact</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                    <i class="fas fa-envelope text-epss mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Email Support</p>
                        <p class="font-medium">support@epss.gov.et</p>
                    </div>
                </div>
                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <i class="fas fa-phone text-epss mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Phone Support</p>
                        <p class="font-medium">+251 11 123 4567</p>
                    </div>
                </div>
                <div class="flex items-center p-3 bg-purple-50 rounded-lg">
                    <i class="fas fa-clock text-epss mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Support Hours</p>
                        <p class="font-medium">Mon-Fri, 8:30 AM - 5:30 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript (unchanged, with small tweak for "pageshow" state) -->
    <script>
        (function() {
            'use strict';
            
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const buttonText = document.getElementById('buttonText');
            const loadingIcon = document.getElementById('loadingIcon');
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordVisibility = document.getElementById('passwordVisibility');
            const strengthMeter = document.getElementById('strengthMeter');
            const strengthText = document.getElementById('strengthText');
            const supportModal = document.getElementById('supportModal');
            const closeModal = document.getElementById('closeModal');
            
            function checkPasswordStrength(password) {
                if (!password) return { level: '--', width: 0, color: 'transparent' };
                let score = 0;
                if (password.length >= 8) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[a-z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[^A-Za-z0-9]/.test(password)) score++;
                const levels = [
                    { level: 'Weak', width: 20, color: '#ef4444' },
                    { level: 'Fair', width: 40, color: '#f59e0b' },
                    { level: 'Good', width: 60, color: '#10b981' },
                    { level: 'Strong', width: 80, color: '#059669' },
                    { level: 'Very Strong', width: 100, color: '#047857' }
                ];
                return levels[Math.min(score, levels.length - 1)];
            }
            
            function togglePassword() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                const icon = passwordVisibility.querySelector('i');
                const textIcon = passwordToggle.querySelector('i');
                if (type === 'text') {
                    icon.className = 'fas fa-eye-slash';
                    textIcon.className = 'fas fa-eye-slash mr-1';
                    passwordToggle.innerHTML = '<i class="fas fa-eye-slash mr-1"></i> Hide';
                } else {
                    icon.className = 'fas fa-eye';
                    textIcon.className = 'fas fa-eye mr-1';
                    passwordToggle.innerHTML = '<i class="fas fa-eye mr-1"></i> Show';
                }
            }
            
            function handleSubmit(e) {
                e.preventDefault();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value.trim();
                if (!email || !password) {
                    const inputs = [document.getElementById('email'), document.getElementById('password')];
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            input.classList.add('border-red-500');
                            input.addEventListener('input', function() {
                                this.classList.remove('border-red-500');
                            }, { once: true });
                        }
                    });
                    if (!email && !password) alert('Please enter your email and password');
                    else if (!email) alert('Please enter your email address');
                    else alert('Please enter your password');
                    return;
                }
                submitBtn.disabled = true;
                buttonText.innerHTML = '<span>Authenticating...</span>';
                loadingIcon.classList.remove('hidden');
                setTimeout(() => { loginForm.submit(); }, 300);
            }
            
            function init() {
                loginForm.addEventListener('submit', handleSubmit);
                passwordToggle.addEventListener('click', togglePassword);
                passwordVisibility.addEventListener('click', togglePassword);
                
                passwordInput.addEventListener('input', function() {
                    const { level, width, color } = checkPasswordStrength(this.value);
                    strengthMeter.style.width = `${width}%`;
                    strengthMeter.style.backgroundColor = color;
                    strengthText.textContent = level;
                    strengthText.className = 'font-medium';
                    if (level === 'Weak') strengthText.classList.add('text-red-600');
                    else if (level === 'Fair') strengthText.classList.add('text-yellow-600');
                    else if (level === 'Good') strengthText.classList.add('text-green-600');
                    else if (level === 'Strong') strengthText.classList.add('text-green-700');
                    else if (level === 'Very Strong') strengthText.classList.add('text-green-800');
                });
                
                document.querySelectorAll('a[href="#"]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        if (this.textContent.includes('Support') || this.textContent.includes('Help')) {
                            e.preventDefault();
                            supportModal.classList.remove('hidden');
                        }
                    });
                });
                
                closeModal.addEventListener('click', () => supportModal.classList.add('hidden'));
                supportModal.addEventListener('click', (e) => {
                    if (e.target === supportModal) supportModal.classList.add('hidden');
                });
                
                const rememberCheckbox = document.getElementById('remember');
                rememberCheckbox.addEventListener('change', function() {
                    const label = this.nextElementSibling;
                    if (this.checked) label.innerHTML = '<i class="fas fa-check-circle text-epss mr-1"></i> Keep me signed in';
                    else label.innerHTML = 'Keep me signed in';
                });
                
                const emailField = document.getElementById('email');
                if (!emailField.value) emailField.focus();
                
                window.addEventListener('pageshow', function() {
                    submitBtn.disabled = false;
                    buttonText.innerHTML = '<i class="fas fa-sign-in-alt mr-3"></i>Sign In to Continue';
                    loadingIcon.classList.add('hidden');
                });
                
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !submitBtn.disabled) {
                        const active = document.activeElement;
                        if (active && (active.type === 'email' || active.type === 'password')) {
                            handleSubmit(new Event('submit'));
                        }
                    }
                });
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
</body>
</html>