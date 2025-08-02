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

        input,
        select {
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

        .success {
            background: #d1fae5;
            color: #065f46;
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

        #admin-fields,
        #user-fields {
            display: none;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Register</h2>

        @if(session('success'))
        <div class="success">{{ session('success') }}</div>
        @endif

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

            <label>Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>

            <label>Role</label>
            <select name="role" required onchange="toggleAdminFields(this.value)">
                <option value="user" {{ old('role')==='user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Admin</option>
            </select>

            <!-- Admin Fields -->
            <div id="admin-fields">
                <label>Admin Secret Key</label>
                <input type="text" name="admin_secret_key" value="{{ old('admin_secret_key') }}">

                <label>Admin Invitation Code (Optional)</label>
                <input type="text" name="admin_invitation_code" value="{{ old('admin_invitation_code') }}">
            </div>

            <!-- User Field -->
            <div id="user-fields">
                <label>Invitation Code</label>
                <input type="text" name="invitation_code" id="invitation_code_input"
                    value="{{ old('invitation_code', $invitationCode ?? '') }}">
            </div>

            <button type="submit">Register</button>
        </form>

        <a href="{{ route('login') }}">Already have an account? Login</a>
    </div>

    <script>
        function getCodeFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const codeFromQuery = urlParams.get('code');
            const codeFromPath = window.location.pathname.split('/').pop();
            return codeFromQuery || (codeFromPath.startsWith('jyoti_') ? codeFromPath : '');
        }

        function toggleAdminFields(role) {
            const adminFields = document.getElementById('admin-fields');
            const userFields = document.getElementById('user-fields');
            const invitationInput = document.getElementById('invitation_code_input');

            if (role === 'admin') {
                adminFields.style.display = 'block';
                userFields.style.display = 'none';
            } else {
                adminFields.style.display = 'none';
                userFields.style.display = 'block';

                if (invitationInput && !invitationInput.value) {
                    invitationInput.value = getCodeFromURL();
                }
            }
        }

        window.onload = function () {
            toggleAdminFields("{{ old('role', 'user') }}");
        };
    </script>
</body>

</html>