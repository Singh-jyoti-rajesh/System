<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Color Trading Game - Menu</title>
    <!-- Google Fonts (optional for nicer appearance) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #f4f4f4);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .menu-container {
            text-align: center;
            max-width: 800px;
            width: 100%;
        }

        .menu-title {
            font-size: 40px;
            margin-bottom: 50px;
            color: #007bff;
            font-weight: 700;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 30px;
            padding: 0 20px;
        }

        .menu-card {
            background-color: #ffffff;
            padding: 30px 20px;
            border-radius: 15px;
            text-decoration: none;
            color: #007bff;
            font-size: 20px;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .menu-card:hover {
            background-color: #007bff;
            color: #fff;
            transform: translateY(-5px);
        }

        @media (max-width: 500px) {
            .menu-title {
                font-size: 28px;
            }

            .menu-card {
                font-size: 18px;
                padding: 20px 15px;
            }
        }
    </style>
</head>

<body>

    <div class="menu-container">
        <div class="menu-title">Color Trading Game</div>

        <div class="menu-grid">
            <a href="{{ route('user.dashboard30') }}" class="menu-card">Game</a>
            <a href="{{ route('user.wallet') }}" class="menu-card">Wallet</a>
            <a href="{{ route('user.account') }}" class="menu-card">Account</a>
            <a href="{{ route('user.promotion') }}" class="menu-card">Promotion</a>
        </div>
    </div>

</body>

</html>