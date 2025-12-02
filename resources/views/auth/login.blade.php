<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTrack - Login</title>

    <style>
        /* Define a consistent primary color palette */
        :root {
            --primary-color: #4f46e5; /* Indigo/Blue for accents */
            --primary-hover: #4338ca;
            --background-main: #d9d9d9; /* The requested main background color */
            --card-background: white;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        body {
            margin: 0;
            padding: 0;
            /* Use the system background color */
            background: var(--background-main);
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
        }

        .container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* --- LEFT SIDE (Branding) --- */
        .left {
            flex: 1.2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* The left side uses the main background color */
            background: var(--background-main); 
            color: #333; /* Darker text for contrast on gray */
            padding: 30px;
        }
        
        .left h1 {
            font-size: 4.5rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 10px;
            color: var(--primary-color); /* Use primary color for the logo */
        }
        
        .left p {
            font-size: 1.25rem;
            font-weight: 400;
            opacity: 0.7;
            text-align: center;
            max-width: 80%;
        }
        
        .icon {
            font-size: 5rem;
            margin-bottom: 20px;
            color: var(--primary-color); /* Use primary color for the icon */
        }

        /* --- RIGHT SIDE (Login Form) --- */
        .right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            /* The right side background is also the main background color */
            background: var(--background-main);
        }

        .login-card {
            width: 400px;
            background: var(--card-background); /* Card remains white for focus */
            padding: 50px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            z-index: 10;
        }

        .login-card h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            color: #333;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 14px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            background: white;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }
        
        input:focus, select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .btn {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            background: var(--primary-color); /* Use blue for the action button */
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.1s;
        }

        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .btn:active {
            transform: translateY(0);
        }

        .bottom-text {
            margin-top: 25px;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
        }

        .bottom-text a {
            font-weight: 600;
            text-decoration: none;
            color: var(--primary-color);
            transition: color 0.2s;
        }
        
        .bottom-text a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
    </style>

</head>
<body>

<div class="container">

    <div class="left">
        <div class="icon">🎓</div>
        <h1>EduTrack</h1>
        <p>Your centralized platform for academic excellence and progress monitoring.</p>
    </div>

    <div class="right">
        <div class="login-card">
            <h2>Sign In to Your Account</h2>

            <form action="/login" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="e.g., john.doe@school.edu" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your secret password" required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="student">Student</option>
                    </select>
                </div>

                <button class="btn" type="submit">Sign In</button>

                <div class="bottom-text">
                    Need to create an account? <a href="/register">Sign Up Here</a>
                </div>
            </form>
        </div>
    </div>

</div>

</body>
</html>