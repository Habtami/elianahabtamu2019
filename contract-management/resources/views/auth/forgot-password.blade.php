<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>የይለፍ ቃል መርሳት | EPSS Contract Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+Ethiopic:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --epss-primary: #1C75BC;
            --epss-primary-dark: #1664a0;
            --navy: #0b2545;
            --gold: #c6a15b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Ethiopic', 'Inter', 'Segoe UI', 'Nyala', system-ui, sans-serif;
            background: linear-gradient(145deg, #f0f3fa 0%, #e2e8f5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #1f2937;
        }

        .forgot-card {
            max-width: 520px;
            width: 100%;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            border-radius: 28px;
            box-shadow: 0 30px 50px rgba(0, 20, 40, 0.25);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
            animation: rise 0.45s ease-out;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .forgot-header {
            background: linear-gradient(135deg, var(--epss-primary) 0%, var(--epss-primary-dark) 100%);
            padding: 1.75rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 3px solid var(--gold);
        }

        .forgot-header .logo {
            height: 64px;
            width: auto;
            background: #fff;
            padding: 6px 12px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
            margin-bottom: 0.9rem;
        }

        .forgot-header .system-name {
            color: #eaf2fb;
            font-size: 0.86rem;
            font-weight: 500;
            letter-spacing: 0.02em;
            margin-bottom: 0.35rem;
        }

        .forgot-header h1 {
            color: #fff;
            font-size: 1.45rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
        }

        .forgot-header h1 i {
            color: var(--gold);
            font-size: 1.1rem;
        }

        .forgot-body {
            padding: 2rem;
        }

        .alert {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            padding: 0.95rem 1.1rem;
            border-radius: 12px;
            font-size: 0.9rem;
            line-height: 1.55;
            margin-bottom: 1.25rem;
            animation: fadeIn 0.25s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .alert i {
            font-size: 1.05rem;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-left: 4px solid #10b981;
            color: #065f46;
        }

        .alert-success i { color: #10b981; }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .alert-error i { color: #ef4444; }

        .alert strong { display: block; font-weight: 700; margin-bottom: 0.15rem; }

        .alert ul { margin: 0.25rem 0 0 1.1rem; }

        .info-box {
            background: #e8f4fd;
            border-left: 4px solid var(--epss-primary);
            padding: 1rem 1.15rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.92rem;
            color: #14507e;
            line-height: 1.6;
            display: flex;
            gap: 0.65rem;
        }

        .info-box i {
            color: var(--epss-primary);
            margin-top: 3px;
            flex-shrink: 0;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.45rem;
            font-size: 0.95rem;
        }

        .input-group {
            display: flex;
            margin-bottom: 0.4rem;
        }

        .input-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            background: #f8f9fa;
            border: 2px solid #e0e4ea;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #64748b;
        }

        .input-group input {
            flex: 1;
            font-family: inherit;
            font-size: 1rem;
            padding: 0.8rem 0.85rem;
            border: 2px solid #e0e4ea;
            border-left: none;
            border-radius: 0 12px 12px 0;
            outline: none;
            color: #1f2937;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-group input::placeholder { color: #9aa4b2; }

        .input-group input:focus {
            border-color: var(--epss-primary);
            box-shadow: 3px 0 0 0 rgba(28, 117, 188, 0.08);
        }

        .input-group.has-error input,
        .input-group.has-error .input-icon {
            border-color: #ef4444;
        }

        .field-hint {
            font-size: 0.78rem;
            color: #6b7280;
            margin-bottom: 1.3rem;
        }

        .btn-send {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            width: 100%;
            border: none;
            cursor: pointer;
            color: #fff;
            font-family: inherit;
            font-size: 1.05rem;
            font-weight: 600;
            padding: 0.95rem;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--epss-primary) 0%, var(--epss-primary-dark) 100%);
            border-bottom: 4px solid var(--gold);
            transition: transform 0.15s, box-shadow 0.15s;
            margin: 0.35rem 0 1.1rem;
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(28, 117, 188, 0.32);
        }

        .btn-send:active { transform: translateY(0); }

        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--epss-primary-dark);
            font-weight: 600;
            font-size: 0.93rem;
            text-decoration: none;
        }

        .btn-login:hover { text-decoration: underline; }

        .back-link { text-align: center; }

        .card-footer {
            padding: 0 2rem 1.4rem;
            text-align: center;
            font-size: 0.72rem;
            color: #8a94a3;
            line-height: 1.6;
        }

        .card-footer .sep {
            display: inline-block;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #c3cad5;
            margin: 0 0.5rem;
            vertical-align: middle;
        }

        @media (max-width: 480px) {
            body { padding: 1rem; }
            .forgot-header { padding: 1.4rem 1.25rem 1.2rem; }
            .forgot-body { padding: 1.5rem 1.25rem; }
            .card-footer { padding: 0 1.25rem 1.2rem; }
            .forgot-header h1 { font-size: 1.25rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-card">
        <div class="forgot-header">
            <img src="{{ asset('images/epss_logo_amharic.jpg') }}"
                 alt="Ethiopian Pharmaceutical Supply Service"
                 class="logo">
            <p class="system-name">የኮንትራት አስተዳደር ሥርዓት · Contract Management System</p>
            <h1>
                <i class="fas fa-key"></i>
                የይለፍ ቃል መርሳት
            </h1>
        </div>

        <div class="forgot-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>ጊዜያዊ ፓስዎርድ ተልኳል።</strong>
                        {{ session('status') }}
                        <div style="margin-top:0.35rem; font-size:0.84rem;">
                            ኢሜይልዎን ይመልከቱ፤ ጊዜያዊ ፓስዎርድውን ተጠቅመው ይግቡና ወዲያውኑ ፓስዎርድዎን ይቀይሩ። ኢሜይሉ ካልደረሰዎ Spam ወይም Junk ፎልደርዎን ያረጋግጡ።
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>ስህተት ተፈጥሯል።</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <span>ኢሜይልዎን ያስገቡ፤ ጊዜያዊ ፓስዎርድ እንልክልዎታለን።</span>
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <label for="email" class="form-label">ኢሜይል አድራሻ</label>
                <div class="input-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email"
                           placeholder="name@epss.gov.et">
                </div>
                <p class="field-hint">ለምሳሌ፦ user@epss.gov.et</p>

                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i>
                    ላክ
                </button>

                <div class="back-link">
                    <a href="{{ route('login') }}" class="btn-login">
                        <i class="fas fa-arrow-left"></i>
                        ወደ መግቢያ ተመለስ
                    </a>
                </div>
            </form>
        </div>

        <div class="card-footer">
            &copy; 2026 Ethiopian Pharmaceutical Supply Service
            <br>
            CMS v2.5.2 <span class="sep"></span> Updated: Jan 2018 E.C
        </div>
    </div>
</body>
</html>
