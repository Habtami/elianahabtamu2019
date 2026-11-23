<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password | EPSS Contract Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

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
            color: #000000 !important;
        }

        h1, h2, h3, h4, h5, h6,
        p, span, label, li, div, strong {
            color: #000000;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .8rem;
            color: #6b7280;
            transition: color .2s ease;
        }

        .requirement.met {
            color: #047857;
            font-weight: 500;
        }

        .requirement i {
            width: 1rem;
            text-align: center;
        }

        .requirement.met i::before {
            content: "\f058"; /* fa-check-circle */
        }

        @media (max-width: 640px) {
            input, button {
                font-size: 16px !important;
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                    boxShadow: {
                        'elevation-xl': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)'
                    }
                }
            }
        }
    </script>
</head>
<body>
    <div class="page-wrapper">
        <div class="w-full max-w-lg">
            <div class="bg-white rounded-2xl shadow-elevation-xl overflow-hidden">

                <!-- Brand header -->
                <div class="bg-gradient-to-r from-epss to-epss-dark px-6 py-6 text-center">
                    <img src="{{ asset('images/epss_logo_amharic.jpg') }}"
                         alt="Ethiopian Pharmaceutical Supply Service"
                         class="h-16 w-auto mx-auto mb-3">
                    <h1 class="text-lg font-bold text-white">CMS | Change Password</h1>
                    <p class="text-sm text-white/90">የፓስዎርድ መቀየሪያ</p>
                </div>

                <div class="p-6 md:p-8 space-y-5">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <script>
                            setTimeout(function() {
                                window.location.href = "{{ route('login') }}";
                            }, 3000);
                        </script>
                    @endif

                    <!-- Warning -->
                    @if($user->must_change_password || session('warning'))
                        <div class="flex items-start gap-3 bg-amber-50 border border-amber-300 rounded-xl p-4">
                            <i class="fas fa-shield-alt text-amber-500 mt-0.5"></i>
                            <div class="text-sm">
                                <p class="font-semibold text-amber-800">Password change required</p>
                                <p class="text-amber-700">እባክዎ የመጀመሪያ ጊዜ ፓስዎርድዎን ይቀይሩ።</p>
                                <p class="text-amber-700 mt-1">
                                    You are signing in with a temporary password. Choose a new password to continue to the system.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Errors -->
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                                <div class="text-sm text-red-700">
                                    <p class="font-semibold text-red-800">Please fix the following:</p>
                                    <ul class="list-disc pl-4 mt-1 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Signed-in user -->
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                        <div class="h-10 w-10 rounded-full bg-epss text-white flex items-center justify-center font-semibold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="text-sm leading-tight">
                            <p class="font-semibold">{{ $user->name }}</p>
                            <p class="text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.change.update') }}" class="space-y-5" id="changeForm">
                        @csrf

                        <!-- Current password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-900 mb-2">
                                <i class="fas fa-key mr-1 text-gray-400"></i>
                                Current Password / የአሁኑ ፓስዎርድ
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="current_password"
                                       id="current_password"
                                       required
                                       autocomplete="current-password"
                                       autofocus
                                       class="block w-full px-3 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-epss focus:border-transparent @error('current_password') border-red-500 @enderror"
                                       placeholder="Enter your current password">
                                <button type="button"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 toggle-visibility"
                                        data-target="current_password"
                                        aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- New password -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-900 mb-2">
                                <i class="fas fa-lock mr-1 text-gray-400"></i>
                                New Password / አዲስ ፓስዎርድ
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="new_password"
                                       id="new_password"
                                       required
                                       autocomplete="new-password"
                                       class="block w-full px-3 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-epss focus:border-transparent @error('new_password') border-red-500 @enderror"
                                       placeholder="Create a new password">
                                <button type="button"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 toggle-visibility"
                                        data-target="new_password"
                                        aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            <!-- Requirements -->
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                <div class="requirement" data-rule="length">
                                    <i class="fas fa-circle" style="font-size:.45rem"></i>
                                    <span>At least 8 characters</span>
                                </div>
                                <div class="requirement" data-rule="letter">
                                    <i class="fas fa-circle" style="font-size:.45rem"></i>
                                    <span>At least one letter</span>
                                </div>
                                <div class="requirement" data-rule="number">
                                    <i class="fas fa-circle" style="font-size:.45rem"></i>
                                    <span>At least one number</span>
                                </div>
                                <div class="requirement" data-rule="symbol">
                                    <i class="fas fa-circle" style="font-size:.45rem"></i>
                                    <span>At least one special character</span>
                                </div>
                                <div class="requirement" data-rule="match">
                                    <i class="fas fa-circle" style="font-size:.45rem"></i>
                                    <span>Passwords match</span>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation -->
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">
                                <i class="fas fa-check-double mr-1 text-gray-400"></i>
                                Confirm New Password / ማረጋገጫ
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="new_password_confirmation"
                                       id="new_password_confirmation"
                                       required
                                       autocomplete="new-password"
                                       class="block w-full px-3 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-epss focus:border-transparent"
                                       placeholder="Repeat the new password">
                                <button type="button"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 toggle-visibility"
                                        data-target="new_password_confirmation"
                                        aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                                id="submitBtn"
                                class="w-full bg-gradient-to-r from-epss to-epss-dark text-white font-semibold py-3.5 px-4 rounded-lg hover:from-epss-dark hover:to-epss-dark focus:outline-none focus:ring-2 focus:ring-epss focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="submitLabel"><i class="fas fa-save mr-2"></i>አስቀምጥ</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 px-1">
                <p class="text-xs text-gray-500">
                    &copy; 2026 Ethiopian Pharmaceutical Supply Service
                </p>
                <a href="{{ route('logout.get') }}" class="text-xs text-gray-600 hover:text-epss">
                    <i class="fas fa-sign-out-alt mr-1"></i>Sign out
                </a>
            </div>
        </div>
    </div>

    <script>
        (function () {
            'use strict';

            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('new_password_confirmation');
            const form = document.getElementById('changeForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitLabel = document.getElementById('submitLabel');

            function setRule(name, met) {
                const el = document.querySelector('.requirement[data-rule="' + name + '"]');
                if (!el) return;
                el.classList.toggle('met', met);
            }

            function validate() {
                const value = newPassword.value;
                setRule('length', value.length >= 8);
                setRule('letter', /[A-Za-z]/.test(value));
                setRule('number', /[0-9]/.test(value));
                setRule('symbol', /[^A-Za-z0-9]/.test(value));
                setRule('match', value !== '' && value === confirmPassword.value);
            }

            newPassword.addEventListener('input', validate);
            confirmPassword.addEventListener('input', validate);

            document.querySelectorAll('.toggle-visibility').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.dataset.target);
                    if (!input) return;
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    button.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
                });
            });

            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitLabel.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            });

            validate();
        })();
    </script>
</body>
</html>
