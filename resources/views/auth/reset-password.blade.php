<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTrack - Reset Password</title>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --text-dark: #222;
            --bg-gray: #f3f4f6;
            --danger: #e3342f;
            --success: #10b981;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: var(--bg-gray);
            height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* LEFT SECTION */
        .left {
            flex: 1.25;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            text-align: center;
            background: linear-gradient(135deg, #a5b4fc, #818cf8);
            position: relative;
            overflow: hidden;
            color: white;
        }

        .left::before,
        .left::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.5;
        }

        .left::before {
            width: 320px;
            height: 320px;
            background: #6366f1;
            top: -50px;
            left: -40px;
        }

        .left::after {
            width: 380px;
            height: 380px;
            background: #4f46e5;
            bottom: -60px;
            right: -30px;
        }

        .left-divider {
            position: absolute;
            right: 0;
            top: 0;
            width: 2px;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
        }

        .icon {
            font-size: 5rem;
            text-shadow: 0px 4px 10px rgba(0, 0, 0, 0.25);
        }

        .left h1 {
            font-size: 3.8rem;
            font-weight: 900;
            margin-top: 10px;
            letter-spacing: -1px;
        }

        .left p {
            font-size: 1.25rem;
            max-width: 70%;
            line-height: 1.6;
            margin-top: 20px;
            opacity: 0.95;
        }

        /* RIGHT SECTION */
        .right {
            flex: 0.9;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .reset-card {
            width: 420px;
            padding: 40px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reset-card h2 {
            text-align: center;
            font-size: 2.1rem;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .reset-card p {
            text-align: center;
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
        }
        
        .error-message {
            color: var(--danger);
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 5px;
            display: block;
        }

        .success-message {
            color: var(--success);
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 5px;
            display: block;
        }

        .form-group input.is-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 1px var(--danger);
        }

        input {
            width: 100%;
            padding: 14px 16px;
            margin-top: 8px;
            border-radius: 12px;
            border: 1px solid #ccc;
            font-size: 1rem;
            background: #fafafa;
            transition: 0.2s;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.25);
            outline: none;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.65;
            width: 22px;
            height: 22px;
        }

        .password-toggle:hover {
            opacity: 1;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            font-size: 1.05rem;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.25s;
        }

        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .bottom-text {
            margin-top: 20px;
            text-align: center;
            font-size: 0.95rem;
            color: #555;
        }

        .bottom-text a {
            color: var(--primary);
            font-weight: bold;
            text-decoration: none;
        }

        .bottom-text a:hover {
            text-decoration: underline;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="left">
        <div class="left-divider"></div>
        <div class="icon">🔐</div>
        <h1>Reset Password</h1>
        <p>Enter your email and new password to reset your account password.</p>
    </div>

    <div class="right">
        <div class="reset-card">

            <h2>Reset Your Password</h2>
            <p>Please enter your email address and new password</p>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('password.reset.submit') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="e.g., john@school.edu" required 
                           value="{{ old('email') }}" 
                           class="@error('email') is-invalid @enderror">

                    @error('email')
                        <span class="error-message">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group password-wrapper">
                    <label>New Password</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Enter your new password" required>
                </div>

                <div class="form-group password-wrapper">
                    <label>Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           placeholder="Confirm your new password" required>
                </div>

                @error('password')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror

                <button type="submit" class="btn">Reset Password</button>

                <div class="bottom-text">
                    <a href="{{ route('login') }}">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const field = document.getElementById("password");
    field.type = field.type === "password" ? "text" : "password";
}
</script>

</body>
</html>

