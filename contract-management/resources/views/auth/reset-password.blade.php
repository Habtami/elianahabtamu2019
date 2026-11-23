<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | EPSS Contract Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <style>
        :root {
            --epss-primary: #1C75BC;
            --epss-primary-dark: #1664a0;
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
        p, span, a, label, li, div {
            color: #000000 !important;
        }

        i, .fa {
            color: inherit !important;
        }

        .reset-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .reset-wrapper .card-body {
            padding-bottom: 0 !important;
        }

        .reset-wrapper form {
            margin-bottom: 0 !important;
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
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
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
                        'elevation-md': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                        'elevation-xl': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
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
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 z-50 bg-white px-4 py-2 rounded-lg shadow-elevation-xl text-epss font-medium">
        Skip to main content
    </a>

    <div class="reset-wrapper" id="main-content">
        <div class="w-full max-w-md animate-fade-in">
            <div class="bg-white rounded-2xl shadow-elevation-xl overflow-hidden animate-slide-up p-6 md:p-8">
                <!-- Gradient header -->
                <div class="bg-gradient-to-r from-epss to-epss-dark p-6 md:p-8 -mx-6 md:-mx-8 -mt-6 md:-mt-8 mb-6">
                    <div class="flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('images/epss_logo_amharic.jpg') }}"
                             alt="Ethiopian Pharmaceutical Supply Service"
                             class="h-16 md:h-20 w-auto mb-4"
                             loading="eager">
                        <h3 class="text-xl font-bold text-white" style="color: #000000 !important;">CMS</h3>
                        <p class="text-sm text-white/90" style="color: #000000 !important;">Set a New Password</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-lock text-epss mt-0.5"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    Enter your new password below.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                                </div>
                                <div class="ml-3">
                                    <ul class="text-sm text-red-700 list-disc pl-4 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                                       value="{{ old('email', $request->email) }}"
                                       required
                                       autocomplete="email"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-epss focus:border-transparent transition-colors duration-200 placeholder-gray-400 @error('email') border-red-300 @enderror"
                                       placeholder="name@epss.gov.et">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-900 mb-2">
                                New Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       required
                                       autocomplete="new-password"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-epss focus:border-transparent transition-colors duration-200 placeholder-gray-400 @error('password') border-red-300 @enderror"
                                       placeholder="At least 8 characters">
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       required
                                       autocomplete="new-password"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-epss focus:border-transparent transition-colors duration-200 placeholder-gray-400 @error('password_confirmation') border-red-300 @enderror"
                                       placeholder="Re-enter new password">
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-gradient-to-r from-epss to-epss-dark text-white font-semibold py-3.5 px-4 rounded-lg hover:from-epss-dark hover:to-epss-dark focus:outline-none focus:ring-2 focus:ring-epss focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] shadow-elevation-md">
                            <i class="fas fa-save mr-3"></i>
                            Reset Password
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-sm text-epss hover:text-epss-dark font-medium inline-flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Sign In
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <footer class="mt-4 text-center w-full">
                <p class="text-xs text-gray-500 mb-2">
                    &copy; 2026 Ethiopian Pharmaceutical Supply Service
                </p>
                <div class="flex items-center justify-center space-x-4 text-xs text-gray-400">
                    <span>CMS v2.5.2</span>
                    <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                    <span>Updated: Jan 2018 E.C</span>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
