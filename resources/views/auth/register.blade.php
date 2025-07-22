<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            background-color: #4f46e5;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #4338ca;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #4f46e5;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        #admin-key-section {
            display: none;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Register</h2>

    @if ($errors->any())
        <div class="error">
            <ul style="padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Confirm Password:</label>
        <input type="password" name="password_confirmation" required>

        <label>Role:</label>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <div id="admin-key-section">
            <label>Admin Secret Key:</label>
            <input type="text" name="admin_secret_key">
        </div>

        <button type="submit">Register</button>
    </form>

    <a href="{{ route('login') }}">Already have an account? Login</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleField = document.querySelector('[name="role"]');
        const keySection = document.getElementById('admin-key-section');

        roleField.addEventListener('change', function () {
            keySection.style.display = this.value === 'admin' ? 'block' : 'none';
        });

        // Preserve admin section on form resubmission
        if (roleField.value === 'admin') {
            keySection.style.display = 'block';
        }
    });
</script>

</body>
</html>
